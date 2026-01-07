<?php

namespace App\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\HierarchyImport;
use App\Imports\ActivitiesImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Models\ActionPlan;
use App\Models\RpComponent;
use Illuminate\Support\Str;


class ReportingImportController extends Controller
{
    public function index()
    {
        return view('reporting.import');
    }

    /**
     * Single import method - imports both hierarchy and activities
     */
    public function import(Request $request)
    {
        // ========== UPDATE VALIDATION ==========
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
            'action_plan_title' => 'required|string|max:255', // ADD THIS LINE
            'start_date' => 'nullable|date', // ADD THIS LINE
            'end_date' => 'nullable|date|after_or_equal:start_date', // ADD THIS LINE
        ]);
        // ========================================

        try {
            $file = $request->file('excel_file');

            // ========== ADD THIS SECTION: SAVE ACTION PLAN ==========
            Log::info('Step 1: Saving Action Plan to database');

            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('action_plans', $filename, 'public');
            $action_plan_id = (string) Str::uuid();


            // Save action plan WITHOUT component_id
            $actionPlan = ActionPlan::create([
                'action_plan_id' => $action_plan_id,
                'title' => $request->action_plan_title,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                // 'rp_components_id' => null, // Don't set it here
                'excel_filename' => $filename,
                'excel_url' => Storage::url($path),
                'excel_uploaded_at' => now(),
                'excel_metadata' => json_encode([
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]),
            ]);

            Log::info('Action Plan saved', [
                'id' => $actionPlan->action_plan_id,
                'title' => $actionPlan->title
            ]);
            // ========== END ADDITION ==========

            Log::info('=== STARTING COMPLETE IMPORT ===', [
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'action_plan_id' => $action_plan_id // ADD THIS
            ]);

            // REST OF YOUR EXISTING CODE STAYS THE SAME...
            // IMPORT HIERARCHY (SHEET 1)
            Log::info('--- IMPORTING HIERARCHY (Sheet 1) ---');

            $hierarchyImport = new HierarchyImport($action_plan_id);
            $hierarchyWrapper = new class($hierarchyImport) implements \Maatwebsite\Excel\Concerns\WithMultipleSheets {
                private $import;

                public function __construct($import)
                {
                    $this->import = $import;
                }

                public function sheets(): array
                {
                    return [0 => $this->import];
                }
            };

            Excel::import($hierarchyWrapper, $file, null, \Maatwebsite\Excel\Excel::XLSX);
            $hierarchyResults = $hierarchyImport->getResults();

            Log::info('Hierarchy import completed', $hierarchyResults);

            // IMPORT ACTIVITIES (SHEET 2)
            Log::info('--- IMPORTING ACTIVITIES (Sheet 2) ---');

            $activitiesImport = new ActivitiesImport();
            $activitiesWrapper = new class($activitiesImport) implements \Maatwebsite\Excel\Concerns\WithMultipleSheets {
                private $import;

                public function __construct($import)
                {
                    $this->import = $import;
                }

                public function sheets(): array
                {
                    return [1 => $this->import];
                }
            };

            Excel::import($activitiesWrapper, $file, null, \Maatwebsite\Excel\Excel::XLSX);
            $activitiesResults = $activitiesImport->getResults();

            Log::info('Activities import completed', $activitiesResults);

            // ========== ADD THIS: UPDATE ACTION PLAN WITH RESULTS ==========
            $actionPlan->update([
                'excel_processed_at' => now(),
                'excel_metadata' => json_encode(array_merge(
                    json_decode($actionPlan->excel_metadata, true) ?? [],
                    [
                        'import_results' => [
                            'hierarchy' => $hierarchyResults,
                            'activities' => $activitiesResults
                        ],
                        'processed_at' => now()->toDateTimeString(),
                    ]
                )),
            ]);
            Log::info('Action Plan updated with import results');
            // ========== END ADDITION ==========

            // Transform activities results
            $transformedActivities = $this->transformActivitiesResults($activitiesResults);

            // Get complete database counts
            $dbCounts = $this->getCompleteDatabaseCounts();

            // Log success
            Log::info('=== IMPORT COMPLETED ===', [
                'hierarchy_results' => $hierarchyResults,
                'activities_results' => $transformedActivities,
                'db_counts' => $dbCounts,
                'action_plan_id' => $actionPlan->action_plan_id // ADD THIS
            ]);

            // ========== UPDATE RETURN DATA ==========
            return back()->with([
                'success' => '✅ Action Plan created and data imported successfully!',
                'import_results' => $hierarchyResults,
                'activities_results' => $transformedActivities,
                'db_counts' => $dbCounts,
                'action_plan_created' => true, // ADD THIS
                'action_plan_id' => $actionPlan->action_plan_id, // ADD THIS
                'excel_filename' => $actionPlan->excel_filename, // ADD THIS
                'uploaded_at' => $actionPlan->excel_uploaded_at->format('Y-m-d H:i:s'), // ADD THIS
            ]);
            // ========================================

        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', '❌ Import failed: ' . $e->getMessage());
        }
    }
    /**
     * Transform activities results to match Blade view
     */
    private function transformActivitiesResults($results)
    {
        if (!is_array($results) || !isset($results['processed'])) {
            return [
                'processed' => 0,
                'details' => [
                    'activities' => ['new' => 0, 'existing' => 0],
                    'indicators' => ['new' => 0, 'existing' => 0],
                    'focalpoints' => ['new' => 0, 'existing' => 0],
                    'activity_indicators' => 0,
                    'activity_focalpoints' => 0
                ],
                'errors' => []
            ];
        }

        // Get recent activity-indicator and activity-focalpoint links
        $activityIndicatorsCount = DB::table('rp_activity_indicators')
            ->where('created_at', '>=', now()->subMinutes(10))
            ->count();

        $activityFocalpointsCount = DB::table('rp_activity_focalpoints')
            ->where('created_at', '>=', now()->subMinutes(10))
            ->count();

        return [
            'processed' => $results['processed'] ?? 0,
            'details' => [
                'activities' => [
                    'new' => $results['created']['activities'] ?? 0,
                    'existing' => $results['updated']['activities'] ?? 0
                ],
                'indicators' => [
                    'new' => $results['created']['indicators'] ?? 0,
                    'existing' => 0
                ],
                'focalpoints' => [
                    'new' => $results['created']['focalpoints'] ?? 0,
                    'existing' => 0
                ],
                'activity_indicators' => $activityIndicatorsCount,
                'activity_focalpoints' => $activityFocalpointsCount
            ],
            'errors' => $results['errors'] ?? []
        ];
    }

    /**
     * Get complete database counts
     */
    private function getCompleteDatabaseCounts()
    {
        try {
            return [
                'components' => DB::table('rp_components')->count(),
                'programs' => DB::table('rp_programs')->count(),
                'units' => DB::table('rp_units')->count(),
                'actions' => DB::table('rp_actions')->count(),
                'activities' => DB::table('rp_activities')->count(),
                'indicators' => DB::table('rp_indicators')->count(),
                'focalpoints' => DB::table('rp_focalpoints')->count(),
                'activity_indicators' => DB::table('rp_activity_indicators')->count(),
                'activity_focalpoints' => DB::table('rp_activity_focalpoints')->count(),
                'action_plans' => ActionPlan::count()

            ];
        } catch (\Exception $e) {
            Log::error('Failed to get database counts: ' . $e->getMessage());
            return [
                'components' => 0,
                'programs' => 0,
                'units' => 0,
                'actions' => 0,
                'activities' => 0,
                'indicators' => 0,
                'focalpoints' => 0,
                'activity_indicators' => 0,
                'activity_focalpoints' => 0,
                'action_plans' => 0
            ];
        }
    }

    /**
     * Download template
     */
    public function downloadTemplate()
    {
        $templateName = 'reporting_template_' . date('Y-m-d') . '.xlsx';
        $filePath = storage_path('app/templates/' . $templateName);

        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        if (!file_exists($filePath)) {
            $spreadsheet = new Spreadsheet();

            // Sheet 1 → Hierarchy
            $hierarchySheet = $spreadsheet->getActiveSheet();
            $hierarchySheet->setTitle('Hierarchy');
            $hierarchySheet->fromArray([
                ['component_code', 'component', 'program_code', 'program', 'unit_code', 'unit', 'action_code', 'action', 'action_objective', 'targets_beneficiaries']
            ], null, 'A1');

            // Sample data
            $hierarchySheet->fromArray([
                ['AD.A', 'برامج مؤسسة الحريري التربوية والإنمائية', 'A1', 'تحسين جودة التعليم في ثانوية رفيق الحريري', 'iv', 'بناء القدرات الفنية للمعلمين', '1', 'إجراء تدريب للهيئات العاملة في المدرسة', 'تطوير المهارات التربوية', 'هيئة القيادة البيداغولوجية في الثانوية'],
                ['AD.A', 'برامج مؤسسة الحريري التربوية والإنمائية', 'A1', 'تحسين جودة التعليم في ثانوية رفيق الحريري', 'v', 'تحقيق الاعتمادات الدولية', '1', 'مواصلة التقديم للحصول على اعتماد NEASC', 'ضمان الالتزام بالمعايير الدولية', 'أعضاء لجنة الاعتماد الدولي'],
            ], null, 'A2');

            // Sheet 2 → Activities
            $activitiesSheet = $spreadsheet->createSheet();
            $activitiesSheet->setTitle('Activities');
            $activitiesSheet->fromArray([
                ['action_reference', 'activity_code', 'activity', 'status', 'activity_indicators', 'focal_points']
            ], null, 'A1');

            // Sample data
            $activitiesSheet->fromArray([
                ['AD.A.1.iv.1', '1', 'تدريبات لهيئة القيادة البيداغوجية', 'ongoing', "1. عدد التدريبات\n2. عدد الأساتذة المشاركين", "نادين زيدان"],
                ['AD.A.1.v.1', '1', 'تغطية التكاليف اللوجستية للزيارة التفقدية', 'done', "1. نسبة استكمال متطلبات الاعتماد", "نادين زيدان\nمحمد بلطجي"],
            ], null, 'A2');

            // Auto-size columns
            foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
                foreach ($worksheet->getColumnIterator() as $column) {
                    $worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
            }

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($filePath);
        }

        return response()->download($filePath, 'reporting_template.xlsx');
    }

    /**
     * Clear all data
     */
    public function clearData(Request $request)
    {
        try {
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Clear in correct order (child to parent)
            $tables = [
                'rp_activity_focalpoints',
                'rp_activity_indicators',
                'rp_activities',
                'rp_focalpoints',
                'rp_indicators',
                'rp_actions',
                'rp_units',
                'rp_programs',
                'rp_components'
            ];

            foreach ($tables as $table) {
                DB::table($table)->truncate();
                Log::info("Cleared table: {$table}");
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return back()->with('success', 'All data cleared successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to clear data: ' . $e->getMessage());
            return back()->with('error', 'Failed to clear data: ' . $e->getMessage());
        }
    }
}
