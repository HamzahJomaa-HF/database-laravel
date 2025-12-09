<?php

namespace App\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RpComponent;
use App\Models\RpProgram;
use App\Models\RpUnit;
use App\Models\RpAction;
use App\Models\RpActivity;
use App\Models\RpIndicator;
use App\Models\RpFocalpoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ReportingImportController extends Controller
{
    /**
     * Handle both GET (show form) and POST (process import)
     */
    public function handleImport(Request $request)
    {
        if ($request->isMethod('GET')) {
            return view('reporting.import.create');
        }
        
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        try {
            // Set execution limits for large files
            set_time_limit(300);
            ini_set('max_execution_time', 300);
            ini_set('memory_limit', '512M');
            
            // Disable query logging for performance
            DB::disableQueryLog();
            
            $file = $request->file('excel_file');
            $extension = $file->getClientOriginalExtension();
            
            Log::info('Starting import with extension: ' . $extension);
            
            // Get counts BEFORE import
            $countsBefore = $this->getDatabaseCounts();
            Log::info('Database counts BEFORE import:', $countsBefore);
            
            if (in_array($extension, ['xlsx', 'xls'])) {
                // Process Excel file with multiple sheets
                $results = $this->processExcelWithSheets($file);
            } else {
                // Process CSV file (single sheet) - backward compatibility
                $filePath = $file->getRealPath();
                $data = $this->readCSV($filePath);
                $results = $this->processCSVData($data);
            }

            // Get counts AFTER import
            $countsAfter = $this->getDatabaseCounts();
            Log::info('Database counts AFTER import:', $countsAfter);
            
            // Calculate ACTUAL inserted counts (difference)
            $actualInserted = [
                'components' => $countsAfter['components'] - $countsBefore['components'],
                'programs' => $countsAfter['programs'] - $countsBefore['programs'],
                'units' => $countsAfter['units'] - $countsBefore['units'],
                'actions' => $countsAfter['actions'] - $countsBefore['actions'],
                'activities' => $countsAfter['activities'] - $countsBefore['activities'],
                'indicators' => $countsAfter['indicators'] - $countsBefore['indicators'],
                'focalpoints' => $countsAfter['focalpoints'] - $countsBefore['focalpoints'],
            ];
            
            Log::info('Actual inserted counts (difference):', $actualInserted);
            Log::info('Rows processed from file:', [
                'components_rows' => $results['processed_components'],
                'programs_rows' => $results['processed_programs'],
                'units_rows' => $results['processed_units'],
                'actions_rows' => $results['processed_actions'],
                'activities_rows' => $results['processed_activities'],
                'indicators_rows' => $results['processed_indicators'],
                'focalpoints_rows' => $results['processed_focalpoints']
            ]);

            $summary = [
                'components' => $actualInserted['components'] . ' new / ' . $results['processed_components'] . ' rows in file',
                'programs' => $actualInserted['programs'] . ' new / ' . $results['processed_programs'] . ' rows in file',
                'units' => $actualInserted['units'] . ' new / ' . $results['processed_units'] . ' rows in file',
                'actions' => $actualInserted['actions'] . ' new / ' . $results['processed_actions'] . ' rows in file',
                'activities' => $actualInserted['activities'] . ' new / ' . $results['processed_activities'] . ' rows in file',
                'indicators' => $actualInserted['indicators'] . ' new / ' . $results['processed_indicators'] . ' rows in file',
                'focalpoints' => $actualInserted['focalpoints'] . ' new / ' . $results['processed_focalpoints'] . ' rows in file',
            ];

            return view('reporting.import.create')
                ->with('success', 'Data imported successfully!')
                ->with('summary', $summary);

        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Get current database counts
     */
    private function getDatabaseCounts(): array
    {
        return [
            'components' => RpComponent::count(),
            'programs' => RpProgram::count(),
            'units' => RpUnit::count(),
            'actions' => RpAction::count(),
            'activities' => RpActivity::count(),
            'indicators' => RpIndicator::count(),
            'focalpoints' => RpFocalpoint::count(),
        ];
    }

    /**
     * Download template Excel file with 2 sheets
     */
    public function downloadTemplate()
    {
        $templatePath = storage_path('templates/reporting_import_template.xlsx');
        
        if (!file_exists($templatePath)) {
            $this->createExcelTemplate();
        }
        
        return response()->download($templatePath, 'reporting_import_template.xlsx');
    }

    /**
     * Process Excel file with multiple sheets using PhpSpreadsheet
     */
    private function processExcelWithSheets($file)
    {
        Log::info('=== PROCESSING EXCEL FILE WITH MULTIPLE SHEETS ===');
        
        $filePath = $file->getRealPath();
        
        try {
            // Load the Excel file
            $spreadsheet = IOFactory::load($filePath);
            
            // Get all sheet names
            $sheetNames = $spreadsheet->getSheetNames();
            Log::info('Sheets found:', $sheetNames);
            
            if (count($sheetNames) < 2) {
                throw new \Exception('Excel file must contain at least 2 sheets. Found: ' . count($sheetNames));
            }
            
            // Process Sheet 1: Hierarchy (Components, Programs, Units, Actions)
            $hierarchySheet = $spreadsheet->getSheetByName($sheetNames[0]);
            $hierarchyData = $hierarchySheet->toArray(null, true, true, false);
            Log::info('Sheet 1 loaded:', ['rows' => count($hierarchyData), 'name' => $sheetNames[0]]);
            
            // Process Sheet 2: Activities
            $activitiesSheet = $spreadsheet->getSheetByName($sheetNames[1]);
            $activitiesData = $activitiesSheet->toArray(null, true, true, false);
            Log::info('Sheet 2 loaded:', ['rows' => count($activitiesData), 'name' => $sheetNames[1]]);
            
            // Process hierarchy (skip header row)
            $hierarchyRows = array_slice($hierarchyData, 1);
            Log::info('Processing ' . count($hierarchyRows) . ' hierarchy rows (excluding header)');
            
            $hierarchyResults = $this->processHierarchySection($hierarchyRows);
            
            // Process activities
            $activityResults = $this->processActivitySection($activitiesData, $hierarchyResults['actionMap']);
            
            return [
                'processed_components' => $hierarchyResults['processed_components'],
                'processed_programs' => $hierarchyResults['processed_programs'],
                'processed_units' => $hierarchyResults['processed_units'],
                'processed_actions' => $hierarchyResults['processed_actions'],
                'processed_activities' => $activityResults['processed_activities'],
                'processed_indicators' => $activityResults['processed_indicators'],
                'processed_focalpoints' => $activityResults['processed_focalpoints']
            ];
            
        } catch (\Exception $e) {
            Log::error('Excel processing failed: ' . $e->getMessage());
            throw new \Exception('Excel processing error: ' . $e->getMessage());
        }
    }

    /**
     * Read CSV file (for backward compatibility)
     */
    private function readCSV(string $filePath): array
    {
        $data = [];
        
        // Use fopen with auto_detect_line_endings for better cross-platform compatibility
        ini_set('auto_detect_line_endings', true);
        $handle = fopen($filePath, 'r');
        
        if ($handle !== false) {
            // Read the entire file content to handle multi-line cells
            $lines = [];
            while (($line = fgets($handle)) !== false) {
                $lines[] = $line;
            }
            fclose($handle);
            
            // Parse CSV with str_getcsv for better control
            $data = [];
            $currentRow = [];
            $inQuotedField = false;
            $fieldValue = '';
            
            foreach ($lines as $line) {
                $chars = str_split($line);
                
                foreach ($chars as $char) {
                    if ($char === '"') {
                        $inQuotedField = !$inQuotedField;
                    } elseif ($char === ',' && !$inQuotedField) {
                        $currentRow[] = $fieldValue;
                        $fieldValue = '';
                    } elseif ($char === "\n" && !$inQuotedField) {
                        $currentRow[] = $fieldValue;
                        $data[] = $currentRow;
                        $currentRow = [];
                        $fieldValue = '';
                    } else {
                        $fieldValue .= $char;
                    }
                }
                
                // If still in quoted field, add line break and continue
                if ($inQuotedField) {
                    $fieldValue .= "\n";
                }
            }
            
            // Add the last row
            if (!empty($fieldValue) || !empty($currentRow)) {
                $currentRow[] = $fieldValue;
                $data[] = $currentRow;
            }
        }
        
        return $data;
    }

    /**
     * Process CSV Data (for backward compatibility)
     */
    private function processCSVData(array $data)
    {
        Log::info('=== STARTING CSV PROCESSING ===');
        Log::info('Total rows in CSV:', ['count' => count($data)]);
        
        // Find the empty row that separates the two sections
        $emptyRowIndex = null;
        foreach ($data as $index => $row) {
            $isEmpty = true;
            foreach ($row as $cell) {
                if (trim($cell) !== '') {
                    $isEmpty = false;
                    break;
                }
            }
            if ($isEmpty) {
                $emptyRowIndex = $index;
                break;
            }
        }
        
        if ($emptyRowIndex === null) {
            throw new \Exception('No empty row found to separate hierarchy and activities sections');
        }
        
        // Split data into two sections
        $hierarchyData = array_slice($data, 1, $emptyRowIndex - 1); // Skip header
        $activityData = array_slice($data, $emptyRowIndex + 1);
        
        Log::info('Split data:', [
            'hierarchy_rows' => count($hierarchyData),
            'activity_rows' => count($activityData)
        ]);
        
        // Process hierarchy section
        $hierarchyResults = $this->processHierarchySection($hierarchyData);
        
        // Process activities section
        $activityResults = $this->processActivitySection($activityData, $hierarchyResults['actionMap']);
        
        return [
            'processed_components' => $hierarchyResults['processed_components'],
            'processed_programs' => $hierarchyResults['processed_programs'],
            'processed_units' => $hierarchyResults['processed_units'],
            'processed_actions' => $hierarchyResults['processed_actions'],
            'processed_activities' => $activityResults['processed_activities'],
            'processed_indicators' => $activityResults['processed_indicators'],
            'processed_focalpoints' => $activityResults['processed_focalpoints']
        ];
    }

    /**
     * Process hierarchy section - SIMPLIFIED AND FORCED
     */
    private function processHierarchySection(array $data)
    {
        Log::info('=== PROCESSING HIERARCHY SECTION ===');
        Log::info('Total rows to process: ' . count($data));
        
        $processedComponents = 0;
        $processedPrograms = 0;
        $processedUnits = 0;
        $processedActions = 0;
        
        $actionMap = [];
        $componentIds = [];
        $programIds = [];
        $unitIds = [];

        foreach ($data as $index => $row) {
            // Ensure row has at least 10 columns
            $row = array_pad($row, 10, '');
            
            $componentCode = trim($row[0] ?? '');
            $componentName = trim($row[1] ?? '');
            $programCode = trim($row[2] ?? '');
            $programName = trim($row[3] ?? '');
            $unitCode = trim($row[4] ?? '');
            $unitName = trim($row[5] ?? '');
            $actionCode = trim($row[6] ?? '');
            $actionName = trim($row[7] ?? '');
            $actionObjective = trim($row[8] ?? '');
            $actionTargets = trim($row[9] ?? '');

            // Skip ENTIRE row if no component data
            if (empty($componentCode) && empty($componentName)) {
                Log::debug('Skipping row ' . ($index + 2) . ' - completely empty');
                continue;
            }

            // FORCE CREATE COMPONENT (even with minimal data)
            $processedComponents++;
            if (empty($componentCode)) {
                $componentCode = 'COMP_' . ($index + 1);
            }
            if (empty($componentName)) {
                $componentName = 'Component ' . ($index + 1);
            }
            
            try {
                $component = RpComponent::firstOrCreate(
                    ['code' => $componentCode],
                    [
                        'name' => $componentName,
                        'description' => null,
                        'is_active' => true
                    ]
                );
                $componentId = $component->rp_components_id;
                $componentIds[$componentCode] = $componentId;
                Log::debug('Component: ' . $componentCode . ' -> ID: ' . $componentId);
            } catch (\Exception $e) {
                Log::error('Error creating component: ' . $e->getMessage());
                continue;
            }

            // FORCE CREATE PROGRAM
            $processedPrograms++;
            if (empty($programCode)) {
                $programCode = 'PROG_' . $componentCode . '_' . ($index + 1);
            }
            if (empty($programName)) {
                $programName = 'Program for ' . $componentName;
            }
            
            try {
                $program = RpProgram::firstOrCreate(
                    ['code' => $programCode],
                    [
                        'component_id' => $componentId,
                        'name' => $programName,
                        'description' => null,
                        'is_active' => true
                    ]
                );
                $programId = $program->rp_programs_id;
                $programIds[$programCode] = $programId;
                Log::debug('Program: ' . $programCode . ' -> ID: ' . $programId);
            } catch (\Exception $e) {
                Log::error('Error creating program: ' . $e->getMessage());
                // Continue anyway - we'll create a default program
                $programCode = 'DEFAULT_PROG_' . $componentCode;
                $program = RpProgram::firstOrCreate(
                    ['code' => $programCode],
                    [
                        'component_id' => $componentId,
                        'name' => 'Default Program',
                        'is_active' => true
                    ]
                );
                $programId = $program->rp_programs_id;
            }

            // FORCE CREATE UNIT
            $processedUnits++;
            if (empty($unitCode)) {
                $unitCode = 'UNIT_' . $programCode . '_' . ($index + 1);
            }
            if (empty($unitName)) {
                $unitName = 'Unit for ' . $programName;
            }
            
            try {
                $unit = RpUnit::firstOrCreate(
                    ['code' => $unitCode],
                    [
                        'program_id' => $programId,
                        'name' => $unitName,
                        'unit_type' => 'department',
                        'description' => null,
                        'is_active' => true
                    ]
                );
                $unitId = $unit->rp_units_id;
                $unitIds[$unitCode] = $unitId;
                Log::debug('Unit: ' . $unitCode . ' -> ID: ' . $unitId);
            } catch (\Exception $e) {
                Log::error('Error creating unit: ' . $e->getMessage());
                // Continue anyway - we'll create a default unit
                $unitCode = 'DEFAULT_UNIT_' . $programCode;
                $unit = RpUnit::firstOrCreate(
                    ['code' => $unitCode],
                    [
                        'program_id' => $programId,
                        'name' => 'Default Unit',
                        'unit_type' => 'department',
                        'is_active' => true
                    ]
                );
                $unitId = $unit->rp_units_id;
            }

            // FORCE CREATE ACTION (if we have at least a code or name)
            if (!empty($actionCode) || !empty($actionName)) {
                $processedActions++;
                if (empty($actionCode)) {
                    $actionCode = 'ACT_' . $unitCode . '_' . ($index + 1);
                }
                if (empty($actionName)) {
                    $actionName = 'Action for ' . $unitName;
                }
                
                try {
                    $action = RpAction::firstOrCreate(
                        ['code' => $actionCode],
                        [
                            'unit_id' => $unitId,
                            'name' => $actionName,
                            'description' => $actionObjective ?: 'No objective provided',
                            'planned_start_date' => now(),
                            'planned_end_date' => now()->addYear(),
                            'status' => 'planned',
                            'is_active' => true
                        ]
                    );
                    
                    // Create action reference for matching
                    $actionKey = $componentCode . '.' . $programCode . '.' . $unitCode . '.' . $actionCode;
                    $actionMap[$actionKey] = [
                        'id' => $action->rp_actions_id,
                        'reference1' => $actionKey,
                        'reference2' => $componentCode . '.3.' . $unitCode . '.' . $actionCode,
                        'reference3' => preg_replace('/[^0-9]/', '', $programCode) ? 
                            $componentCode . '.' . preg_replace('/[^0-9]/', '', $programCode) . '.' . $unitCode . '.' . $actionCode :
                            $componentCode . '.3.' . $unitCode . '.' . $actionCode
                    ];
                    
                    Log::info('Created action:', [
                        'id' => $action->rp_actions_id,
                        'code' => $action->code,
                        'reference' => $actionKey
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error('Error creating action ' . $actionCode . ': ' . $e->getMessage());
                }
            }
            
            // Progress logging
            if (($index + 1) % 50 === 0) {
                Log::info('Processed ' . ($index + 1) . ' hierarchy rows...');
            }
        }

        Log::info('Hierarchy section completed:', [
            'total_rows' => count($data),
            'processed_components' => $processedComponents,
            'processed_programs' => $processedPrograms,
            'processed_units' => $processedUnits,
            'processed_actions' => $processedActions,
            'unique_components' => count($componentIds),
            'unique_programs' => count($programIds),
            'unique_units' => count($unitIds),
            'unique_actions' => count($actionMap)
        ]);

        return [
            'processed_components' => $processedComponents,
            'processed_programs' => $processedPrograms,
            'processed_units' => $processedUnits,
            'processed_actions' => $processedActions,
            'actionMap' => $actionMap
        ];
    }

    /**
     * Process activities section - SIMPLIFIED AND FORCED
     */
    private function processActivitySection(array $data, array $actionMap)
    {
        Log::info('=== PROCESSING ACTIVITIES SECTION ===');
        
        $processedActivities = 0;
        $processedIndicators = 0;
        $processedFocalpoints = 0;
        
        $activityCodes = [];
        $indicatorCodes = [];
        $focalpointCodes = [];

        // Skip header row if present
        $headerRow = $data[0] ?? [];
        $isHeader = isset($headerRow[0]) && (stripos($headerRow[0], 'action') !== false || stripos($headerRow[0], 'مرجع') !== false);
        
        if ($isHeader) {
            Log::info('Found header row in activities section, skipping it');
            array_shift($data);
        }

        Log::info('Processing ' . count($data) . ' activity rows');

        foreach ($data as $index => $row) {
            // Ensure row has enough columns (at least 6 for basic data)
            $row = array_pad($row, 10, '');
            
            $actionReference = trim($row[0] ?? '');
            $activityCode = trim($row[1] ?? '');
            $activityName = trim($row[2] ?? '');
            $indicatorsText = trim($row[3] ?? '');
            $focalPointsText = trim($row[4] ?? '');
            $status = $this->mapStatus(trim($row[5] ?? ''));

            // Skip if no activity data
            if (empty($activityCode) && empty($activityName)) {
                Log::debug('Skipping activity row ' . ($index + 2) . ' - completely empty');
                continue;
            }

            // FORCE CREATE ACTIVITY
            $processedActivities++;
            if (empty($activityCode)) {
                $activityCode = 'ACTIVITY_' . ($index + 1);
            }
            if (empty($activityName)) {
                $activityName = 'Activity ' . ($index + 1);
            }
            
            // Track unique activity codes
            if (isset($activityCodes[$activityCode])) {
                $activityCode = $activityCode . '_' . ($index + 1);
            }
            $activityCodes[$activityCode] = true;

            // Find action ID
            $actionId = $this->findActionId($actionReference, $actionMap);
            
            if (!$actionId) {
                Log::warning('Action not found for reference: ' . $actionReference . ', using first available action');
                // Use the first action if available
                if (!empty($actionMap)) {
                    $firstAction = reset($actionMap);
                    $actionId = $firstAction['id'];
                    Log::info('Using action ID: ' . $actionId . ' for activity: ' . $activityCode);
                } else {
                    // Create a placeholder action
                    $actionId = $this->createSimplePlaceholderAction($index);
                }
            }

            // Create Activity
            try {
                Log::debug('Creating/updating activity:', [
                    'code' => $activityCode,
                    'name' => $activityName,
                    'action_id' => $actionId
                ]);
                
                $activity = RpActivity::updateOrCreate(
                    ['code' => $activityCode],
                    [
                        'action_id' => $actionId,
                        'name' => $activityName,
                        'description' => null,
                        'activity_type' => 'project_activity',
                        'status' => $status,
                        'is_active' => true,
                        'needs_sync' => false
                    ]
                );
                
                Log::debug('Activity saved:', ['id' => $activity->id, 'code' => $activity->code]);
                
                // Process Indicators
                if (!empty($indicatorsText) && $indicatorsText !== '""') {
                    $indicatorsResult = $this->processActivityIndicators($activity, $indicatorsText, $indicatorCodes);
                    $processedIndicators += $indicatorsResult['processed'];
                    $indicatorCodes = $indicatorsResult['codes'];
                }

                // Process Focal Points
                if (!empty($focalPointsText) && $focalPointsText !== '""') {
                    $focalpointsResult = $this->processActivityFocalPoints($activity, $focalPointsText, $focalpointCodes);
                    $processedFocalpoints += $focalpointsResult['processed'];
                    $focalpointCodes = $focalpointsResult['codes'];
                }
                
            } catch (\Exception $e) {
                Log::error('Failed to save activity ' . $activityCode . ': ' . $e->getMessage());
                continue;
            }
            
            // Progress logging
            if (($index + 1) % 50 === 0) {
                Log::info('Processed ' . ($index + 1) . ' activity rows...');
            }
        }

        Log::info('Activities section completed:', [
            'total_rows' => count($data),
            'processed_activities' => $processedActivities,
            'processed_indicators' => $processedIndicators,
            'processed_focalpoints' => $processedFocalpoints,
            'unique_activities' => count($activityCodes),
            'unique_indicators' => count($indicatorCodes),
            'unique_focalpoints' => count($focalpointCodes)
        ]);

        return [
            'processed_activities' => $processedActivities,
            'processed_indicators' => $processedIndicators,
            'processed_focalpoints' => $processedFocalpoints
        ];
    }

    /**
     * Find action ID from reference - SIMPLIFIED
     */
    private function findActionId(string $reference, array $actionMap): ?string
    {
        $reference = trim($reference);
        
        if (empty($reference)) {
            return null;
        }
        
        // Try exact match
        foreach ($actionMap as $actionData) {
            foreach (['reference1', 'reference2', 'reference3'] as $refKey) {
                if (isset($actionData[$refKey]) && $actionData[$refKey] === $reference) {
                    return $actionData['id'];
                }
            }
        }
        
        return null;
    }

    /**
     * Create simple placeholder action
     */
    private function createSimplePlaceholderAction(int $index): string
    {
        try {
            $componentCode = 'PLACEHOLDER_COMP_' . $index;
            $programCode = 'PLACEHOLDER_PROG_' . $index;
            $unitCode = 'PLACEHOLDER_UNIT_' . $index;
            $actionCode = 'PLACEHOLDER_ACT_' . $index;
            
            $component = RpComponent::firstOrCreate(
                ['code' => $componentCode],
                [
                    'name' => 'Placeholder Component ' . $index,
                    'is_active' => true
                ]
            );
            
            $program = RpProgram::firstOrCreate(
                ['code' => $programCode],
                [
                    'component_id' => $component->rp_components_id,
                    'name' => 'Placeholder Program ' . $index,
                    'is_active' => true
                ]
            );
            
            $unit = RpUnit::firstOrCreate(
                ['code' => $unitCode],
                [
                    'program_id' => $program->rp_programs_id,
                    'name' => 'Placeholder Unit ' . $index,
                    'unit_type' => 'department',
                    'is_active' => true
                ]
            );
            
            $action = RpAction::firstOrCreate(
                ['code' => $actionCode],
                [
                    'unit_id' => $unit->rp_units_id,
                    'name' => 'Placeholder Action ' . $index,
                    'status' => 'planned',
                    'is_active' => true
                ]
            );
            
            return $action->rp_actions_id;
            
        } catch (\Exception $e) {
            Log::error('Error creating placeholder action: ' . $e->getMessage());
            // Return a default action ID if one exists
            $defaultAction = RpAction::first();
            return $defaultAction ? $defaultAction->rp_actions_id : null;
        }
    }

    /**
     * Map status
     */
    private function mapStatus(string $excelStatus): string
    {
        $statusMap = [
            'ongoing' => 'ongoing',
            'pending' => 'planned',
            'done' => 'completed',
            'completed' => 'completed',
            'in_progress' => 'ongoing',
            'active' => 'ongoing',
            'closed' => 'completed',
            'جاري' => 'ongoing',
            'قيد الانتظار' => 'planned',
            'منتهي' => 'completed',
            'مكتمل' => 'completed',
            'o' => 'ongoing',
            'p' => 'planned',
            'c' => 'completed'
        ];

        $lowerStatus = strtolower(trim($excelStatus));
        return $statusMap[$lowerStatus] ?? 'planned';
    }

    /**
     * Process indicators for an activity
     */
    private function processActivityIndicators(RpActivity $activity, string $indicatorsText, array $existingCodes): array
    {
        $processed = 0;
        $indicatorCodes = $existingCodes;
        
        // Clean the text
        $indicatorsText = trim($indicatorsText, '" \t\n\r\0\x0B');
        $indicatorsText = str_replace('""', '"', $indicatorsText);
        
        if (empty($indicatorsText)) {
            return ['processed' => 0, 'codes' => $indicatorCodes];
        }
        
        // Split by numbered indicators (1., 2., etc.)
        $indicatorLines = preg_split('/\d+\./', $indicatorsText, -1, PREG_SPLIT_NO_EMPTY);
        
        foreach ($indicatorLines as $index => $line) {
            $indicatorText = trim($line);
            if (empty($indicatorText)) {
                continue;
            }
            
            $processed++;
            $indicatorName = substr($indicatorText, 0, 200);
            $indicatorCode = 'IND_' . $activity->code . '_' . ($index + 1);
            
            // Ensure unique code
            if (isset($indicatorCodes[$indicatorCode])) {
                $indicatorCode = $indicatorCode . '_' . time();
            }
            $indicatorCodes[$indicatorCode] = true;
            
            try {
                $indicator = RpIndicator::firstOrCreate(
                    ['indicator_code' => $indicatorCode],
                    [
                        'name' => $indicatorName,
                        'description' => $indicatorText,
                        'indicator_type' => 'output',
                        'unit_of_measure' => 'number',
                        'is_active' => true
                    ]
                );
                
                $activity->indicators()->syncWithoutDetaching([$indicator->rp_indicators_id]);
                
            } catch (\Exception $e) {
                Log::error('Failed to create indicator ' . $indicatorCode . ': ' . $e->getMessage());
            }
        }
        
        return ['processed' => $processed, 'codes' => $indicatorCodes];
    }

    /**
     * Process focal points for an activity
     */
    private function processActivityFocalPoints(RpActivity $activity, string $focalPointsText, array $existingCodes): array
    {
        $processed = 0;
        $focalpointCodes = $existingCodes;
        
        // Clean the text
        $focalPointsText = trim($focalPointsText, '" \t\n\r\0\x0B');
        $focalPointsText = str_replace('""', '"', $focalPointsText);
        
        if (empty($focalPointsText)) {
            return ['processed' => 0, 'codes' => $focalpointCodes];
        }
        
        // Normalize newlines
        $focalPointsText = str_replace(["\r\n", "\r"], "\n", $focalPointsText);
        
        // Split by various separators
        $focalPointNames = preg_split('/[\n,،\|\/\;]+/', $focalPointsText, -1, PREG_SPLIT_NO_EMPTY);
        
        foreach ($focalPointNames as $nameIndex => $name) {
            $name = trim($name);
            $name = preg_replace('/\s+/', ' ', $name);
            
            if (empty($name) || strlen($name) < 2) {
                continue;
            }
            
            $processed++;
            
            // Create code from name
            $code = 'FP_' . preg_replace('/[^A-Za-z0-9]/', '_', $this->transliterateArabic($name));
            $code = substr($code, 0, 50);
            
            // Ensure unique code
            if (isset($focalpointCodes[$code])) {
                $code = $code . '_' . $nameIndex . '_' . time();
            }
            $focalpointCodes[$code] = true;
            
            try {
                $focalpoint = RpFocalpoint::firstOrCreate(
                    ['focalpoint_code' => $code],
                    [
                        'name' => $name,
                        'position' => 'Focal Point',
                        'department' => 'Operations',
                        'is_active' => true
                    ]
                );
                
                $activity->focalpoints()->syncWithoutDetaching([$focalpoint->rp_focalpoints_id]);
                
            } catch (\Exception $e) {
                Log::error('Failed to create focal point ' . $code . ': ' . $e->getMessage());
            }
        }
        
        return ['processed' => $processed, 'codes' => $focalpointCodes];
    }

    /**
     * Helper function to transliterate Arabic to Latin for codes
     */
    private function transliterateArabic(string $text): string
    {
        $transliteration = [
            'ا' => 'a', 'أ' => 'a', 'إ' => 'i', 'آ' => 'a', 'ى' => 'a', 'ة' => 'h',
            'ب' => 'b', 'ت' => 't', 'ث' => 'th', 'ج' => 'j', 'ح' => 'h', 'خ' => 'kh',
            'د' => 'd', 'ذ' => 'dh', 'ر' => 'r', 'ز' => 'z', 'س' => 's', 'ش' => 'sh',
            'ص' => 's', 'ض' => 'd', 'ط' => 't', 'ظ' => 'z', 'ع' => 'a', 'غ' => 'gh',
            'ف' => 'f', 'ق' => 'q', 'ك' => 'k', 'ل' => 'l', 'م' => 'm', 'ن' => 'n',
            'ه' => 'h', 'و' => 'w', 'ي' => 'y', 'ئ' => 'e', 'ؤ' => 'o', 'ء' => 'a',
            ' ' => '_'
        ];
        
        $result = '';
        $text = trim($text);
        
        for ($i = 0; $i < mb_strlen($text, 'UTF-8'); $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');
            if (isset($transliteration[$char])) {
                $result .= $transliteration[$char];
            } elseif (preg_match('/[A-Za-z0-9]/', $char)) {
                $result .= $char;
            } else {
                $result .= '_';
            }
        }
        
        // Remove duplicate underscores and trim
        $result = preg_replace('/_+/', '_', $result);
        $result = trim($result, '_');
        
        return $result ?: 'focalpoint';
    }

    /**
     * Create Excel template with 2 sheets
     */
    private function createExcelTemplate()
    {
        $templateDir = storage_path('templates');
        if (!file_exists($templateDir)) {
            mkdir($templateDir, 0777, true);
        }
        
        // Create spreadsheet with 2 sheets
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        
        // Sheet 1: Hierarchy
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Hierarchy');
        $sheet1->fromArray([
            ['Component Code', 'Component', 'Program Code', 'Program', 'Unit Code', 'Unit', 'Action Code', 'Action', 'Action Objective', 'Action Targets & Beneficiaries'],
            ['AD.A', 'برامج مؤسسة الحريري التربوية والإنمائية', 'A1', 'تحسين جودة التعليم في ثانوية رفيق الحريري', 'iv', 'بناء القدرات الفنية للمعلمين لمواصلة تقديم التعليم المتميز بما يتماشى مع ممارسات التعليم الحديثة', '1', 'إجراء تدريب للهيئات العاملة في المدرسة حول استراتيجيات وسبل التدريس', 'تطوير المهارات التربوية وتعزيز جودة العملية التعليمية', 'هيئة القيادة البيداغوجية في الثانوية'],
        ], null, 'A1');
        
        // Sheet 2: Activities
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Activities');
        $sheet2->fromArray([
            ['Action Reference', 'Activity Code', 'Activity', 'Activity Indicators', 'Focal Point(s)', 'Status'],
            ['AD.A.1.iv.1', '1', 'تدريبات لهيئة القيادة البيداغوجية في المدرسة بالتعاون مع خبراء وجهات تدريبية متخصصة', '1. عدد التدريبات\n2. عدد الأساتذة المشاركين', 'نادين زيدان', 'Ongoing'],
        ], null, 'A1');
        
        // Save to file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($templateDir . '/reporting_import_template.xlsx');
    }
}