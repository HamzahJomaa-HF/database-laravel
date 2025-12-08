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
            'csv_file' => 'required|file|mimes:csv,txt,xlsx,xls'
        ]);

        try {
            $file = $request->file('csv_file');
            $filePath = $file->getRealPath();
            
            $data = $this->readCSV($filePath);
            
            if (empty($data)) {
                return redirect()->back()
                    ->with('error', 'CSV file is empty or invalid');
            }

            $results = $this->processCSVData($data);

            $summary = [
                'components' => $results['components_count'],
                'programs' => $results['programs_count'],
                'units' => $results['units_count'],
                'actions' => $results['actions_count'],
                'activities' => $results['activities_count'],
                'indicators' => $results['indicators_count'],
                'focalpoints' => $results['focalpoints_count'],
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
     * Download template CSV file
     */
    public function downloadTemplate()
    {
        $templatePath = storage_path('templates/reporting_import_template.csv');
        
        if (!file_exists($templatePath)) {
            $this->createTemplate();
        }
        
        return response()->download($templatePath, 'reporting_import_template.csv');
    }

    // ========== PRIVATE METHODS ==========
    
    /**
     * Read CSV file with better handling for multi-line cells
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
     * Process CSV Data
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
            'components_count' => $hierarchyResults['components_count'],
            'programs_count' => $hierarchyResults['programs_count'],
            'units_count' => $hierarchyResults['units_count'],
            'actions_count' => $hierarchyResults['actions_count'],
            'activities_count' => $activityResults['activities_count'],
            'indicators_count' => $activityResults['indicators_count'],
            'focalpoints_count' => $activityResults['focalpoints_count']
        ];
    }

    /**
     * Process hierarchy section
     */
    private function processHierarchySection(array $data)
    {
        Log::info('=== PROCESSING HIERARCHY SECTION ===');
        
        $components = [];
        $programs = [];
        $units = [];
        $actions = [];
        
        $componentMap = [];
        $programMap = [];
        $unitMap = [];
        $actionMap = [];

        foreach ($data as $index => $row) {
            Log::debug('Processing hierarchy row ' . ($index + 1) . ':', $row);
            
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

            // Skip if no component data
            if (empty($componentCode) || empty($componentName)) {
                continue;
            }

            // Process Component
            if (!isset($componentMap[$componentCode])) {
                $component = RpComponent::updateOrCreate(
                    ['code' => $componentCode],
                    [
                        'name' => $componentName,
                        'description' => null,
                        'is_active' => true
                    ]
                );
                $componentMap[$componentCode] = $component->rp_components_id;
                $components[] = $component->code;
            }

            // Process Program
            if (!empty($programCode) && !empty($programName)) {
                $programKey = $componentCode . '_' . $programCode;
                if (!isset($programMap[$programKey])) {
                    $program = RpProgram::updateOrCreate(
                        ['code' => $programCode],
                        [
                            'component_id' => $componentMap[$componentCode],
                            'name' => $programName,
                            'description' => null,
                            'is_active' => true
                        ]
                    );
                    $programMap[$programKey] = $program->rp_programs_id;
                    $programs[] = $program->code;
                }
            } else {
                // Create a default program if none provided
                $programCode = 'PROG_' . $componentCode;
                $programKey = $componentCode . '_' . $programCode;
                if (!isset($programMap[$programKey])) {
                    $program = RpProgram::updateOrCreate(
                        ['code' => $programCode],
                        [
                            'component_id' => $componentMap[$componentCode],
                            'name' => $componentName . ' Program',
                            'description' => null,
                            'is_active' => true
                        ]
                    );
                    $programMap[$programKey] = $program->rp_programs_id;
                    $programs[] = $program->code;
                }
            }

            // Process Unit
            if (!empty($unitCode) && !empty($unitName) && isset($programMap[$programKey])) {
                $unitKey = $programKey . '_' . $unitCode;
                if (!isset($unitMap[$unitKey])) {
                    $unit = RpUnit::updateOrCreate(
                        ['code' => $unitCode],
                        [
                            'program_id' => $programMap[$programKey],
                            'name' => $unitName,
                            'unit_type' => 'department',
                            'description' => null,
                            'is_active' => true
                        ]
                    );
                    $unitMap[$unitKey] = $unit->rp_units_id;
                    $units[] = $unit->code;
                }
            } else {
                // Create a default unit if none provided
                $unitCode = 'UNIT_' . ($programCode ?? 'DEFAULT');
                $unitKey = $programKey . '_' . $unitCode;
                if (!isset($unitMap[$unitKey])) {
                    $unit = RpUnit::updateOrCreate(
                        ['code' => $unitCode],
                        [
                            'program_id' => $programMap[$programKey],
                            'name' => ($programName ?? 'Default') . ' Unit',
                            'unit_type' => 'department',
                            'description' => null,
                            'is_active' => true
                        ]
                    );
                    $unitMap[$unitKey] = $unit->rp_units_id;
                    $units[] = $unit->code;
                }
            }

            // Process Action
            if (!empty($actionCode) && !empty($actionName) && isset($unitMap[$unitKey])) {
                $actionKey = $unitKey . '_' . $actionCode;
                if (!isset($actionMap[$actionKey])) {
                    $action = RpAction::updateOrCreate(
                        ['code' => $actionCode],
                        [
                            'unit_id' => $unitMap[$unitKey],
                            'name' => $actionName,
                            'description' => $actionObjective,
                            'planned_start_date' => now(),
                            'planned_end_date' => now()->addYear(),
                            'status' => 'planned',
                            'is_active' => true
                        ]
                    );
                    
                    // Create multiple reference formats for matching
                    $programNumber = preg_replace('/[^0-9]/', '', $programCode);
                    $actionMap[$actionKey] = [
                        'id' => $action->rp_actions_id,
                        'reference1' => $componentCode . '.' . $programCode . '.' . $unitCode . '.' . $actionCode,
                        'reference2' => $componentCode . '.' . $programNumber . '.' . $unitCode . '.' . $actionCode,
                        'reference3' => $componentCode . '.3.' . $unitCode . '.' . $actionCode
                    ];
                    $actions[] = $action->code;
                    
                    Log::info('Created action with references:', [
                        'id' => $action->rp_actions_id,
                        'references' => $actionMap[$actionKey]
                    ]);
                }
            }
        }

        Log::info('Hierarchy section processed:', [
            'components' => count($components),
            'programs' => count($programs),
            'units' => count($units),
            'actions' => count($actions),
            'action_map_size' => count($actionMap)
        ]);

        return [
            'components_count' => count($components),
            'programs_count' => count($programs),
            'units_count' => count($units),
            'actions_count' => count($actions),
            'actionMap' => $actionMap
        ];
    }

    /**
     * Process activities section
     */
    private function processActivitySection(array $data, array $actionMap)
    {
        Log::info('=== PROCESSING ACTIVITIES SECTION ===');
        
        $activities = [];
        $indicators = [];
        $focalpoints = [];

        // Skip header row if present
        $headerRow = $data[0] ?? [];
        $isHeader = isset($headerRow[0]) && (stripos($headerRow[0], 'action') !== false || stripos($headerRow[0], 'مرجع') !== false);
        
        if ($isHeader) {
            Log::info('Found header row in activities section, skipping it');
            array_shift($data);
        }

        Log::info('Processing ' . count($data) . ' activity rows');

        foreach ($data as $index => $row) {
            Log::debug('Processing activity row ' . ($index + 1) . ':', $row);
            
            // Ensure row has enough columns (at least 6 for basic data)
            $row = array_pad($row, 10, '');
            
            $actionReference = trim($row[0] ?? '');
            $activityCode = trim($row[1] ?? '');
            $activityName = trim($row[2] ?? '');
            $indicatorsText = trim($row[3] ?? '');
            $focalPointsText = trim($row[4] ?? '');
            $status = $this->mapStatus(trim($row[5] ?? ''));

            // Log raw data for debugging
            Log::debug('Raw activity data:', [
                'action_ref' => $actionReference,
                'activity_code' => $activityCode,
                'activity_name' => $activityName,
                'indicators' => $indicatorsText,
                'focal_points' => $focalPointsText,
                'status' => $status
            ]);

            // Skip if no activity data
            if (empty($activityCode) || empty($activityName)) {
                Log::warning('Skipping activity row - missing code or name');
                continue;
            }

            // Find action ID
            $actionId = $this->findActionId($actionReference, $actionMap);
            
            if (!$actionId) {
                Log::warning('Action not found, creating placeholder:', ['reference' => $actionReference]);
                $actionId = $this->createPlaceholderAction($actionReference);
                
                if (!$actionId) {
                    Log::error('Failed to create placeholder action:', ['reference' => $actionReference]);
                    continue;
                }
            }

            // Create Activity
            try {
                Log::info('Creating/updating activity:', [
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
                
                $activities[] = $activity->code;
                Log::info('Activity saved:', ['id' => $activity->id, 'code' => $activity->code]);
                
                // Process Indicators
                if (!empty($indicatorsText) && $indicatorsText !== '""') {
                    Log::info('Processing indicators for activity ' . $activityCode, ['text' => $indicatorsText]);
                    $indicatorsCount = $this->processActivityIndicators($activity, $indicatorsText, $indicators);
                    Log::info('Created ' . $indicatorsCount . ' indicators for activity ' . $activityCode);
                } else {
                    Log::info('No indicators to process for activity ' . $activityCode);
                }

                // Process Focal Points - FIXED HERE
                if (!empty($focalPointsText) && $focalPointsText !== '""') {
                    Log::info('Processing focal points for activity ' . $activityCode, ['text' => $focalPointsText]);
                    $focalPointsCount = $this->processActivityFocalPoints($activity, $focalPointsText, $focalpoints);
                    Log::info('Created ' . $focalPointsCount . ' focal points for activity ' . $activityCode);
                } else {
                    Log::info('No focal points to process for activity ' . $activityCode);
                }
                
            } catch (\Exception $e) {
                Log::error('Failed to save activity:', [
                    'code' => $activityCode,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                continue;
            }
        }

        Log::info('Activities section processed:', [
            'activities' => count($activities),
            'indicators' => count($indicators),
            'focalpoints' => count($focalpoints)
        ]);

        return [
            'activities_count' => count($activities),
            'indicators_count' => count($indicators),
            'focalpoints_count' => count($focalpoints)
        ];
    }

    /**
     * Find action ID from reference - IMPROVED
     */
    private function findActionId(string $reference, array $actionMap): ?string
    {
        Log::debug('Finding action for reference:', ['reference' => $reference]);
        
        // Clean the reference
        $reference = trim($reference);
        
        if (empty($reference)) {
            Log::warning('Empty action reference provided');
            return null;
        }
        
        // Try exact match first
        foreach ($actionMap as $actionData) {
            foreach (['reference1', 'reference2', 'reference3'] as $refKey) {
                if (isset($actionData[$refKey]) && $actionData[$refKey] === $reference) {
                    Log::debug('Exact match found:', ['ref_key' => $refKey, 'id' => $actionData['id']]);
                    return $actionData['id'];
                }
            }
        }
        
        // Try matching without dots or with different separators
        $normalizedRef = str_replace(['.', ' ', '-', '_'], '', $reference);
        foreach ($actionMap as $actionData) {
            foreach (['reference1', 'reference2', 'reference3'] as $refKey) {
                if (isset($actionData[$refKey])) {
                    $normalizedActionRef = str_replace(['.', ' ', '-', '_'], '', $actionData[$refKey]);
                    if ($normalizedRef === $normalizedActionRef) {
                        Log::debug('Normalized match found:', ['ref_key' => $refKey, 'id' => $actionData['id']]);
                        return $actionData['id'];
                    }
                }
            }
        }
        
        // Try partial match (reference might be longer)
        foreach ($actionMap as $actionData) {
            foreach (['reference1', 'reference2', 'reference3'] as $refKey) {
                if (isset($actionData[$refKey]) && strpos($reference, $actionData[$refKey]) !== false) {
                    Log::debug('Partial match found:', ['ref_key' => $refKey, 'id' => $actionData['id']]);
                    return $actionData['id'];
                }
            }
        }
        
        Log::warning('No action match found for reference:', [
            'reference' => $reference,
            'available_refs' => array_map(function($item) {
                return [
                    'ref1' => $item['reference1'] ?? null,
                    'ref2' => $item['reference2'] ?? null,
                    'ref3' => $item['reference3'] ?? null
                ];
            }, $actionMap)
        ]);
        
        return null;
    }

    /**
     * Create placeholder action if not found
     */
    private function createPlaceholderAction(string $reference): ?string
    {
        try {
            Log::info('Creating placeholder action:', ['reference' => $reference]);
            
            // Parse reference like AD.A.3.i.1
            $parts = explode('.', $reference);
            
            if (count($parts) < 4) {
                Log::warning('Invalid reference format:', ['reference' => $reference]);
                return null;
            }
            
            // Extract parts
            $componentPart1 = $parts[0] ?? '';
            $componentPart2 = $parts[1] ?? '';
            $componentCode = $componentPart1 . '.' . $componentPart2; // AD.A
            
            // Determine program code
            $programPart = $parts[2] ?? '';
            $programCode = is_numeric($programPart) ? 'A' . $programPart : $programPart;
            
            $unitCode = $parts[3] ?? 'i';
            $actionCode = $parts[4] ?? '1';
            
            // Create component
            $component = RpComponent::firstOrCreate(
                ['code' => $componentCode],
                [
                    'name' => $componentCode . ' Component',
                    'is_active' => true
                ]
            );
            
            // Create program
            $program = RpProgram::firstOrCreate(
                ['code' => $programCode],
                [
                    'component_id' => $component->rp_components_id,
                    'name' => $programCode . ' Program',
                    'is_active' => true
                ]
            );
            
            // Create unit
            $unit = RpUnit::firstOrCreate(
                ['code' => $unitCode],
                [
                    'program_id' => $program->rp_programs_id,
                    'name' => $unitCode . ' Unit',
                    'unit_type' => 'department',
                    'is_active' => true
                ]
            );
            
            // Create action
            $action = RpAction::firstOrCreate(
                ['code' => $actionCode],
                [
                    'unit_id' => $unit->rp_units_id,
                    'name' => $actionCode . ' Action',
                    'status' => 'planned',
                    'is_active' => true
                ]
            );
            
            Log::info('Placeholder action created:', ['id' => $action->rp_actions_id]);
            return $action->rp_actions_id;
            
        } catch (\Exception $e) {
            Log::error('Error creating placeholder action:', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);
            return null;
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
     * Process indicators for an activity - FIXED
     */
    private function processActivityIndicators(RpActivity $activity, string $indicatorsText, array &$indicators): int
    {
        $count = 0;
        
        // Clean the text - remove quotes and extra spaces
        $indicatorsText = trim($indicatorsText, '" \t\n\r\0\x0B');
        $indicatorsText = str_replace('""', '"', $indicatorsText);
        
        Log::debug('Processing indicators text:', ['text' => $indicatorsText]);
        
        if (empty($indicatorsText)) {
            return 0;
        }
        
        // Split by numbered indicators (1., 2., etc.)
        $indicatorLines = preg_split('/\d+\./', $indicatorsText, -1, PREG_SPLIT_NO_EMPTY);
        
        Log::debug('Split indicator lines:', ['lines' => $indicatorLines, 'count' => count($indicatorLines)]);
        
        foreach ($indicatorLines as $index => $line) {
            $indicatorText = trim($line);
            if (empty($indicatorText)) {
                continue;
            }
            
            $indicatorName = substr($indicatorText, 0, 200); // Increased length
            $indicatorCode = 'IND_' . $activity->code . '_' . ($index + 1);
            
            Log::info('Creating indicator:', [
                'code' => $indicatorCode,
                'name' => $indicatorName,
                'activity_code' => $activity->code
            ]);
            
            try {
                $indicator = RpIndicator::updateOrCreate(
                    ['indicator_code' => $indicatorCode],
                    [
                        'name' => $indicatorName,
                        'description' => $indicatorText,
                        'indicator_type' => 'output',
                        'unit_of_measure' => 'number',
                        'is_active' => true
                    ]
                );
                
                $indicators[] = $indicator->indicator_code;
                $activity->indicators()->syncWithoutDetaching([$indicator->rp_indicators_id]);
                $count++;
                
                Log::info('Indicator created and attached:', ['id' => $indicator->rp_indicators_id, 'code' => $indicator->indicator_code]);
                
            } catch (\Exception $e) {
                Log::error('Failed to create indicator:', [
                    'code' => $indicatorCode,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $count;
    }

    /**
     * Process focal points for an activity - FIXED VERSION
     */
    private function processActivityFocalPoints(RpActivity $activity, string $focalPointsText, array &$focalpoints): int
    {
        $count = 0;
        
        // Clean the text - remove quotes and extra spaces
        $focalPointsText = trim($focalPointsText, '" \t\n\r\0\x0B');
        $focalPointsText = str_replace('""', '"', $focalPointsText);
        
        Log::debug('Processing focal points text:', ['raw_text' => $focalPointsText]);
        
        if (empty($focalPointsText)) {
            Log::debug('Focal points text is empty after cleaning');
            return 0;
        }
        
        // Handle different formats:
        // 1. "بلال الحريري\nمحمد إسماعيل" (newline separated)
        // 2. "بلال الحريري، محمد إسماعيل" (Arabic comma separated)
        // 3. "بلال الحريري, محمد إسماعيل" (English comma separated)
        // 4. "بلال الحريري محمد إسماعيل" (space separated)
        
        // First, normalize newlines
        $focalPointsText = str_replace(["\r\n", "\r"], "\n", $focalPointsText);
        
        // Split by various separators
        $focalPointNames = preg_split('/[\n,،\|\/\;]+/', $focalPointsText, -1, PREG_SPLIT_NO_EMPTY);
        
        Log::debug('Split focal point names:', ['names' => $focalPointNames, 'count' => count($focalPointNames)]);
        
        foreach ($focalPointNames as $name) {
            $name = trim($name);
            
            // Additional cleaning
            $name = preg_replace('/\s+/', ' ', $name); // Replace multiple spaces with single space
            
            if (empty($name) || strlen($name) < 2) {
                Log::debug('Skipping empty or too short focal point name:', ['name' => $name]);
                continue;
            }
            
            // Create a code from the name (Latin characters only for code)
            $code = 'FP_' . preg_replace('/[^A-Za-z0-9]/', '_', $this->transliterateArabic($name));
            $code = substr($code, 0, 50); // Limit code length
            
            Log::info('Creating/updating focal point:', ['code' => $code, 'name' => $name]);
            
            try {
                $focalpoint = RpFocalpoint::updateOrCreate(
                    ['focalpoint_code' => $code],
                    [
                        'name' => $name,
                        'position' => 'Focal Point',
                        'department' => 'Operations',
                        'is_active' => true
                    ]
                );
                
                $focalpoints[] = $focalpoint->focalpoint_code;
                $activity->focalpoints()->syncWithoutDetaching([$focalpoint->rp_focalpoints_id]);
                $count++;
                
                Log::info('Focal point created and attached:', [
                    'id' => $focalpoint->rp_focalpoints_id,
                    'code' => $focalpoint->focalpoint_code,
                    'name' => $focalpoint->name
                ]);
                
            } catch (\Exception $e) {
                Log::error('Failed to create focal point:', [
                    'name' => $name,
                    'code' => $code,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $count;
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
     * Create template CSV file
     */
    private function createTemplate()
    {
        $templateDir = storage_path('templates');
        if (!file_exists($templateDir)) {
            mkdir($templateDir, 0777, true);
        }
        
        $csvContent = "Component Code,Component,Program Code,Program,Unit Code,Unit,Action Code,Action,Action Objective,Action Targets & Beneficiaries\n";
        $csvContent .= "AD.A,برامج مؤسسة الحريري التربوية والإنمائية,A3,تعزيز العدالة الصحية من خلال توسيع نطاق عمل مركز الحريري الطبي الاجتماعي,i,بناء قدرات الكوادر المتخصصة والمواطنين حول أفضل الممارسات الصحية,1,القيام بحملة للتوعية والتشخيص والوقاية الصحية,تعزيز الوقاية من الأمراض المزمنة عبر التوعية الصحية والتشخيص المبكر,\"السكان المحليين\n(نساء، أطفال، كبار السن، ذوي الاحتياجات الخاصة، مصابين بأمراض مزمنة)\"\n";
        $csvContent .= "\n";
        $csvContent .= "Action Reference,Activity Code,Activity,Activity Indicators,Focal Point(s),Status\n";
        $csvContent .= "AD.A.3.i.1,1,جلسات توعوية وأيام صحية في المركز وفي المجتمعات المحلية,\"1. عدد الجلسات المنفذة\n2. نسبة الحالات المشخّصة مبكراً\",\"بلال الحريري\nمحمد إسماعيل\",Ongoing\n";
        
        file_put_contents($templateDir . '/reporting_import_template.csv', $csvContent);
    }
}