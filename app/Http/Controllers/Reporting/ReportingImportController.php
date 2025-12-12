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
use App\Models\RpTargetAction;
use App\Models\RpActivityIndicator;
use App\Models\RpActivityFocalpoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;

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
                'target_actions' => $countsAfter['target_actions'] - $countsBefore['target_actions'],
                'activity_indicators' => $countsAfter['activity_indicators'] - $countsBefore['activity_indicators'],
                'activity_focalpoints' => $countsAfter['activity_focalpoints'] - $countsBefore['activity_focalpoints'],
            ];
            
            Log::info('Actual inserted counts (difference):', $actualInserted);
            Log::info('Rows processed from file:', [
                'components_rows' => $results['processed_components'],
                'programs_rows' => $results['processed_programs'],
                'units_rows' => $results['processed_units'],
                'actions_rows' => $results['processed_actions'],
                'activities_rows' => $results['processed_activities'],
                'indicators_rows' => $results['processed_indicators'],
                'focalpoints_rows' => $results['processed_focalpoints'],
                'target_actions_rows' => $results['processed_target_actions'],
                'activity_indicators_rows' => $results['processed_activity_indicators'],
                'activity_focalpoints_rows' => $results['processed_activity_focalpoints'],
            ]);

            $summary = [
                'components' => $actualInserted['components'] . ' new / ' . $results['processed_components'] . ' rows in file',
                'programs' => $actualInserted['programs'] . ' new / ' . $results['processed_programs'] . ' rows in file',
                'units' => $actualInserted['units'] . ' new / ' . $results['processed_units'] . ' rows in file',
                'actions' => $actualInserted['actions'] . ' new / ' . $results['processed_actions'] . ' rows in file',
                'activities' => $actualInserted['activities'] . ' new / ' . $results['processed_activities'] . ' rows in file',
                'indicators' => $actualInserted['indicators'] . ' new / ' . $results['processed_indicators'] . ' rows in file',
                'focalpoints' => $actualInserted['focalpoints'] . ' new / ' . $results['processed_focalpoints'] . ' rows in file',
                'target_actions' => $actualInserted['target_actions'] . ' new / ' . $results['processed_target_actions'] . ' rows in file',
                'activity_indicators' => $actualInserted['activity_indicators'] . ' new / ' . $results['processed_activity_indicators'] . ' rows in file',
                'activity_focalpoints' => $actualInserted['activity_focalpoints'] . ' new / ' . $results['processed_activity_focalpoints'] . ' rows in file',
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
            'target_actions' => RpTargetAction::count(),
            'activity_indicators' => RpActivityIndicator::count(),
            'activity_focalpoints' => RpActivityFocalpoint::count(),
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
                'processed_target_actions' => $hierarchyResults['processed_target_actions'],
                'processed_activities' => $activityResults['processed_activities'],
                'processed_indicators' => $activityResults['processed_indicators'],
                'processed_focalpoints' => $activityResults['processed_focalpoints'],
                'processed_activity_indicators' => $activityResults['processed_activity_indicators'],
                'processed_activity_focalpoints' => $activityResults['processed_activity_focalpoints'],
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
            'processed_target_actions' => $hierarchyResults['processed_target_actions'],
            'processed_activities' => $activityResults['processed_activities'],
            'processed_indicators' => $activityResults['processed_indicators'],
            'processed_focalpoints' => $activityResults['processed_focalpoints'],
            'processed_activity_indicators' => $activityResults['processed_activity_indicators'],
            'processed_activity_focalpoints' => $activityResults['processed_activity_focalpoints'],
        ];
    }

    /**
     * Process hierarchy section - RESPECTS EMPTY CELLS
     */
    private function processHierarchySection(array $data)
    {
        Log::info('=== PROCESSING HIERARCHY SECTION ===');
        Log::info('Total rows to process: ' . count($data));
        
        $processedComponents = 0;
        $processedPrograms = 0;
        $processedUnits = 0;
        $processedActions = 0;
        $processedTargetActions = 0;
        
        $actionMap = [];
        $componentCache = [];
        $programCache = [];
        $unitCache = [];
        $actionCache = [];

        // Track last valid values for each level
        $lastComponentId = null;
        $lastProgramId = null;
        $lastUnitId = null;

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

            // Skip row if ALL fields are empty
            if (empty($componentCode) && empty($componentName) && 
                empty($programCode) && empty($programName) &&
                empty($unitCode) && empty($unitName) &&
                empty($actionCode) && empty($actionName) &&
                empty($actionObjective) && empty($actionTargets)) {
                Log::debug('Skipping row ' . ($index + 2) . ' - completely empty');
                continue;
            }

            // ========== COMPONENT ==========
            $componentId = null;
            if (!empty($componentCode) || !empty($componentName)) {
                // Only create component if there's data
                if (empty($componentCode)) {
                    $componentCode = 'COMP_' . Str::random(8);
                }
                if (empty($componentName)) {
                    $componentName = 'Component - ' . $componentCode;
                }
                
                $componentKey = strtoupper($componentCode);
                
                if (!isset($componentCache[$componentKey])) {
                    try {
                        $component = RpComponent::firstOrCreate(
                            ['code' => $componentCode],
                            [
                                'rp_components_id' => (string) Str::uuid(),
                                'name' => $componentName,
                                'description' => null,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                        $componentCache[$componentKey] = $component->rp_components_id;
                        $processedComponents++;
                        Log::debug('Component created/retrieved:', [
                            'id' => $component->rp_components_id,
                            'code' => $componentCode,
                            'name' => $componentName
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Error creating component: ' . $e->getMessage());
                        continue;
                    }
                }
                $componentId = $componentCache[$componentKey];
                $lastComponentId = $componentId;
            } else {
                // If no component data, use last valid component
                $componentId = $lastComponentId;
                if (!$componentId) {
                    Log::warning('No component data found and no previous component available');
                    continue;
                }
            }

            // ========== PROGRAM ==========
            $programId = null;
            if (!empty($programCode) || !empty($programName)) {
                // Only create program if there's data
                if (empty($programCode)) {
                    $programCode = 'PROG_' . Str::random(6);
                }
                if (empty($programName)) {
                    $programName = 'Program - ' . $programCode;
                }
                
                $programKey = ($componentId ?: '') . '|' . strtoupper($programCode);
                
                if (!isset($programCache[$programKey])) {
                    try {
                        $program = RpProgram::firstOrCreate(
                            ['code' => $programCode],
                            [
                                'rp_programs_id' => (string) Str::uuid(),
                                'component_id' => $componentId,
                                'name' => $programName,
                                'description' => null,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                        $programCache[$programKey] = $program->rp_programs_id;
                        $processedPrograms++;
                        Log::debug('Program created/retrieved:', [
                            'id' => $program->rp_programs_id,
                            'code' => $programCode,
                            'name' => $programName
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Error creating program: ' . $e->getMessage());
                        continue;
                    }
                }
                $programId = $programCache[$programKey];
                $lastProgramId = $programId;
            } else {
                // If no program data, use last valid program for this component
                $programId = $lastProgramId;
                if (!$programId) {
                    Log::warning('No program data found and no previous program available');
                    continue;
                }
            }

            // ========== UNIT ==========
            $unitId = null;
            if (!empty($unitCode) || !empty($unitName)) {
                // Only create unit if there's data
                if (empty($unitCode)) {
                    $unitCode = 'UNIT_' . Str::random(4);
                }
                if (empty($unitName)) {
                    $unitName = 'Unit - ' . $unitCode;
                }
                
                $unitKey = ($programId ?: '') . '|' . strtoupper($unitCode);
                
                if (!isset($unitCache[$unitKey])) {
                    try {
                        $unit = RpUnit::firstOrCreate(
                            ['code' => $unitCode],
                            [
                                'rp_units_id' => (string) Str::uuid(),
                                'program_id' => $programId,
                                'name' => $unitName,
                                'unit_type' => 'department',
                                'description' => null,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                        $unitCache[$unitKey] = $unit->rp_units_id;
                        $processedUnits++;
                        Log::debug('Unit created/retrieved:', [
                            'id' => $unit->rp_units_id,
                            'code' => $unitCode,
                            'name' => $unitName
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Error creating unit: ' . $e->getMessage());
                        continue;
                    }
                }
                $unitId = $unitCache[$unitKey];
                $lastUnitId = $unitId;
            } else {
                // If no unit data, use last valid unit for this program
                $unitId = $lastUnitId;
                if (!$unitId) {
                    Log::warning('No unit data found and no previous unit available');
                    continue;
                }
            }

            // ========== ACTION ==========
            $actionId = null;
            if (!empty($actionCode) || !empty($actionName) || !empty($actionObjective)) {
                // Only create action if there's data
                if (empty($actionCode)) {
                    $actionCode = 'ACT_' . ($processedActions + 1);
                }
                if (empty($actionName)) {
                    $actionName = 'Action - ' . $actionCode;
                }
                
                $actionKey = ($unitId ?: '') . '|' . strtoupper($actionCode);
                
                if (!isset($actionCache[$actionKey])) {
                    try {
                        $action = RpAction::firstOrCreate(
                            ['code' => $actionCode],
                            [
                                'rp_actions_id' => (string) Str::uuid(),
                                'unit_id' => $unitId,
                                'name' => $actionName,
                                'description' => !empty($actionObjective) ? $actionObjective : null,
                                'planned_start_date' => now(),
                                'planned_end_date' => now()->addYear(),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                        $actionId = $action->rp_actions_id;
                        $actionCache[$actionKey] = $actionId;
                        $processedActions++;
                        
                        // Create reference for matching
                        if ($actionId) {
                            $actionMap[$actionId] = [
                                'id' => $actionId,
                                'references' => [$actionCode]
                            ];
                            
                            Log::debug('Action created/retrieved:', [
                                'id' => $actionId,
                                'code' => $actionCode,
                                'name' => $actionName,
                            ]);
                        }
                        
                        // ========== PROCESS ACTION TARGETS ==========
                        if (!empty($actionTargets) && $actionId) {
                            $targetsResult = $this->processActionTargets($actionId, $actionTargets);
                            $processedTargetActions += $targetsResult['processed'];
                            Log::debug('Processed targets for action:', [
                                'action_id' => $actionId,
                                'action_code' => $actionCode,
                                'targets_processed' => $targetsResult['processed']
                            ]);
                        }
                        
                    } catch (\Exception $e) {
                        Log::error('Error creating action ' . $actionCode . ': ' . $e->getMessage());
                    }
                } else {
                    $actionId = $actionCache[$actionKey];
                    
                    // Process Action Targets for existing action too
                    if (!empty($actionTargets) && $actionId) {
                        $targetsResult = $this->processActionTargets($actionId, $actionTargets);
                        $processedTargetActions += $targetsResult['processed'];
                        Log::debug('Processed targets for existing action:', [
                            'action_id' => $actionId,
                            'action_code' => $actionCode,
                            'targets_processed' => $targetsResult['processed']
                        ]);
                    }
                }
            } else {
                // No action data - skip creating action
                Log::debug('Skipping action creation - no action data in row');
            }
            
            // Progress logging
            if (($index + 1) % 100 === 0) {
                Log::info('Processed ' . ($index + 1) . ' hierarchy rows...');
            }
        }

        Log::info('Hierarchy section completed:', [
            'total_rows' => count($data),
            'unique_components' => count($componentCache),
            'unique_programs' => count($programCache),
            'unique_units' => count($unitCache),
            'unique_actions' => count($actionCache),
            'processed_components' => $processedComponents,
            'processed_programs' => $processedPrograms,
            'processed_units' => $processedUnits,
            'processed_actions' => $processedActions,
            'processed_target_actions' => $processedTargetActions
        ]);

        return [
            'processed_components' => $processedComponents,
            'processed_programs' => $processedPrograms,
            'processed_units' => $processedUnits,
            'processed_actions' => $processedActions,
            'processed_target_actions' => $processedTargetActions,
            'actionMap' => $actionMap
        ];
    }

    /**
     * Process Action Targets into rp_target_actions table
     */
    private function processActionTargets(string $actionId, string $targetsText): array
    {
        $processed = 0;
        
        // Clean the text
        $targetsText = trim($targetsText, '" \t\n\r\0\x0B');
        $targetsText = str_replace('""', '"', $targetsText);
        
        if (empty($targetsText)) {
            return ['processed' => 0];
        }
        
        Log::debug('Processing action targets for action_id: ' . $actionId, ['text' => $targetsText]);
        
        // Split by newlines - based on Excel data format
        $beneficiaries = [];
        
        // Try different delimiters
        if (strpos($targetsText, "\n") !== false) {
            $beneficiaries = explode("\n", $targetsText);
        } elseif (strpos($targetsText, '\\n') !== false) {
            $beneficiaries = explode('\\n', $targetsText);
        } else {
            // Try to see if there are multiple entries with bullet points or numbers
            if (preg_match('/\d+\./', $targetsText)) {
                $beneficiaries = preg_split('/\d+\./', $targetsText, -1, PREG_SPLIT_NO_EMPTY);
            } elseif (preg_match('/[٠-٩]+\./', $targetsText)) {
                $beneficiaries = preg_split('/[٠-٩]+\./', $targetsText, -1, PREG_SPLIT_NO_EMPTY);
            } elseif (strpos($targetsText, '•') !== false) {
                $beneficiaries = explode('•', $targetsText);
            } else {
                // Treat as single beneficiary
                $beneficiaries = [$targetsText];
            }
        }
        
        foreach ($beneficiaries as $index => $beneficiary) {
            $beneficiary = trim($beneficiary);
            
            if (empty($beneficiary)) {
                continue;
            }
            
            // Clean up common bullet points or numbers
            $beneficiary = preg_replace('/^[\d٠-٩]+\.\s*/u', '', $beneficiary);
            $beneficiary = preg_replace('/^[•\-*]\s*/u', '', $beneficiary);
            $beneficiary = trim($beneficiary);
            
            if (empty($beneficiary)) {
                continue;
            }
            
            try {
                // Create target action record
                RpTargetAction::create([
                    'rp_target_actions_id' => (string) Str::uuid(),
                    'action_id' => $actionId,
                    'target_name' => mb_substr($beneficiary, 0, 255, 'UTF-8'),
                    'description' => $beneficiary,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $processed++;
                Log::debug('Created target action:', [
                    'action_id' => $actionId,
                    'beneficiary' => $beneficiary
                ]);
                
            } catch (\Exception $e) {
                Log::error('Failed to create target action for action ' . $actionId . ': ' . $e->getMessage());
            }
        }
        
        return ['processed' => $processed];
    }

    /**
     * Process activities section - RESPECTS EMPTY CELLS
     */
    private function processActivitySection(array $data, array $actionMap)
    {
        Log::info('=== PROCESSING ACTIVITIES SECTION ===');
        
        $processedActivities = 0;
        $processedIndicators = 0;
        $processedFocalpoints = 0;
        $processedActivityIndicators = 0;
        $processedActivityFocalpoints = 0;
        
        $activityCodes = [];
        $indicatorNames = [];
        $focalpointNames = [];

        // Skip header row if present
        $headerRow = $data[0] ?? [];
        $isHeader = isset($headerRow[0]) && (
            stripos($headerRow[0], 'action') !== false || 
            stripos($headerRow[0], 'مرجع') !== false ||
            stripos($headerRow[0], 'reference') !== false
        );
        
        if ($isHeader) {
            Log::info('Found header row in activities section, skipping it');
            array_shift($data);
        }

        Log::info('Processing ' . count($data) . ' activity rows');

        foreach ($data as $index => $row) {
            // Ensure row has enough columns
            $row = array_pad($row, 10, '');
            
            $actionReference = trim($row[0] ?? '');
            $activityCode = trim($row[1] ?? '');
            $activityName = trim($row[2] ?? '');
            $indicatorsText = trim($row[3] ?? '');
            $focalPointsText = trim($row[4] ?? '');
            $status = $this->mapStatus(trim($row[5] ?? ''));

            // Skip if no activity data at all
            if (empty($activityCode) && empty($activityName) && empty($actionReference)) {
                Log::debug('Skipping activity row ' . ($index + 2) . ' - completely empty');
                continue;
            }

            // ========== ACTIVITY ==========
            $processedActivities++;
            
            // Generate activity code if empty
            if (empty($activityCode)) {
                $activityCode = 'ACTIVITY_' . ($index + 1) . '_' . Str::random(4);
            }
            
            // Generate activity name if empty
            if (empty($activityName)) {
                $activityName = 'Activity ' . ($index + 1);
            }
            
            // Ensure unique activity code
            if (isset($activityCodes[$activityCode])) {
                $activityCode = $activityCode . '_' . ($index + 1);
            }
            $activityCodes[$activityCode] = true;

            // Find action ID - using enhanced matching
            $actionId = $this->findActionIdEnhanced($actionReference, $actionMap, $index);
            
            // Create Activity - even without action link if reference not found
            try {
                $activityData = [
                    'rp_activities_id' => (string) Str::uuid(),
                    'code' => $activityCode,
                    'name' => $activityName,
                    'description' => null,
                    'activity_type' => 'project_activity',
                    'status' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                if ($actionId) {
                    $activityData['action_id'] = $actionId;
                }
                
                $activity = RpActivity::updateOrCreate(
                    ['code' => $activityCode],
                    $activityData
                );
                
                Log::debug('Activity saved:', [
                    'id' => $activity->rp_activities_id,
                    'code' => $activity->code,
                    'name' => $activity->name,
                    'action_id' => $actionId
                ]);
                
                // ========== INDICATORS ==========
                // Only create indicators if there's text
                if (!empty($indicatorsText) && $indicatorsText !== '""') {
                    $indicatorsResult = $this->processActivityIndicators($activity, $indicatorsText, $indicatorNames);
                    $processedIndicators += $indicatorsResult['processed_indicators'];
                    $processedActivityIndicators += $indicatorsResult['processed_pivot'];
                    $indicatorNames = $indicatorsResult['names'];
                } else {
                    Log::debug('No indicators text for activity ' . $activity->code);
                }

                // ========== FOCAL POINTS ==========
                // Only create focal points if there's text
                if (!empty($focalPointsText) && $focalPointsText !== '""') {
                    $focalpointsResult = $this->processActivityFocalPoints($activity, $focalPointsText, $focalpointNames);
                    $processedFocalpoints += $focalpointsResult['processed_focalpoints'];
                    $processedActivityFocalpoints += $focalpointsResult['processed_pivot'];
                    $focalpointNames = $focalpointsResult['names'];
                } else {
                    Log::debug('No focal points text for activity ' . $activity->code);
                }
                
            } catch (\Exception $e) {
                Log::error('Failed to save activity ' . $activityCode . ': ' . $e->getMessage());
                continue;
            }
            
            // Progress logging
            if (($index + 1) % 100 === 0) {
                Log::info('Processed ' . ($index + 1) . ' activity rows...');
            }
        }

        Log::info('Activities section completed:', [
            'total_rows' => count($data),
            'processed_activities' => $processedActivities,
            'processed_indicators' => $processedIndicators,
            'processed_focalpoints' => $processedFocalpoints,
            'processed_activity_indicators' => $processedActivityIndicators,
            'processed_activity_focalpoints' => $processedActivityFocalpoints,
            'unique_activities' => count($activityCodes),
            'unique_indicators' => count($indicatorNames),
            'unique_focalpoints' => count($focalpointNames)
        ]);

        return [
            'processed_activities' => $processedActivities,
            'processed_indicators' => $processedIndicators,
            'processed_focalpoints' => $processedFocalpoints,
            'processed_activity_indicators' => $processedActivityIndicators,
            'processed_activity_focalpoints' => $processedActivityFocalpoints,
        ];
    }

    /**
     * Enhanced action ID finder with multiple matching strategies
     */
    private function findActionIdEnhanced(string $reference, array $actionMap, int $rowIndex): ?string
    {
        $reference = trim($reference);
        
        if (empty($reference)) {
            return null;
        }
        
        Log::debug('Looking for action reference:', ['reference' => $reference]);
        
        // Strategy 1: Direct match with any reference format
        foreach ($actionMap as $actionData) {
            if (isset($actionData['references'])) {
                foreach ($actionData['references'] as $ref) {
                    if ($ref === $reference) {
                        Log::debug('Found direct match:', ['reference' => $reference, 'action_id' => $actionData['id']]);
                        return $actionData['id'];
                    }
                }
            }
        }
        
        // Strategy 2: Partial match (contains)
        foreach ($actionMap as $actionData) {
            if (isset($actionData['references'])) {
                foreach ($actionData['references'] as $ref) {
                    if (strpos($reference, $ref) !== false || strpos($ref, $reference) !== false) {
                        Log::debug('Found partial match:', ['reference' => $reference, 'action_id' => $actionData['id']]);
                        return $actionData['id'];
                    }
                }
            }
        }
        
        // Strategy 3: Match by extracting codes from reference
        // Try to parse reference like "AD.A.1.iv.1"
        $parts = explode('.', $reference);
        if (count($parts) >= 4) {
            $componentCode = $parts[0] ?? '';
            $programPart = $parts[1] ?? '';
            $unitCode = $parts[2] ?? '';
            $actionCode = $parts[3] ?? '';
            
            // Try to match with extracted codes
            foreach ($actionMap as $actionData) {
                if (isset($actionData['references'][0])) {
                    $refParts = explode('.', $actionData['references'][0]);
                    if (count($refParts) >= 4) {
                        if ($refParts[0] === $componentCode && 
                            $refParts[2] === $unitCode && 
                            $refParts[3] === $actionCode) {
                            Log::debug('Found parsed match:', [
                                'reference' => $reference,
                                'action_id' => $actionData['id'],
                                'matched_parts' => [$refParts[0], $refParts[2], $refParts[3]]
                            ]);
                            return $actionData['id'];
                        }
                    }
                }
            }
        }
        
        // Strategy 4: Use first available action if none found
        if (!empty($actionMap)) {
            $firstAction = reset($actionMap);
            Log::warning('No match found for reference: ' . $reference . ', using first available action');
            return $firstAction['id'] ?? null;
        }
        
        Log::warning('No actions available for reference: ' . $reference);
        return null;
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
            'c' => 'completed',
            'd' => 'completed'
        ];

        $lowerStatus = strtolower(trim($excelStatus));
        return $statusMap[$lowerStatus] ?? 'planned';
    }

    /**
     * Process indicators for an activity - FIXED VERSION (using name instead of code)
     */
    private function processActivityIndicators(RpActivity $activity, string $indicatorsText, array $existingNames): array
    {
        $processedIndicators = 0;
        $processedPivot = 0;
        $indicatorNames = $existingNames;
        
        // Clean the text - preserve original formatting
        $indicatorsText = trim($indicatorsText, '" \t\n\r\0\x0B');
        $indicatorsText = str_replace('""', '"', $indicatorsText);
        
        if (empty($indicatorsText)) {
            return ['processed_indicators' => 0, 'processed_pivot' => 0, 'names' => $indicatorNames];
        }
        
        Log::debug('Processing indicators for activity ' . $activity->code, ['text' => $indicatorsText]);
        
        // Handle different indicator formats
        $indicatorLines = [];
        
        // Try splitting by numbered indicators (1., 2., etc.)
        if (preg_match('/\d+\./', $indicatorsText)) {
            $indicatorLines = preg_split('/\d+\./', $indicatorsText, -1, PREG_SPLIT_NO_EMPTY);
        } 
        // Try splitting by newlines
        elseif (strpos($indicatorsText, "\n") !== false) {
            $indicatorLines = explode("\n", $indicatorsText);
        }
        // Try splitting by Arabic numbers (١., ٢., etc.)
        elseif (preg_match('/[٠-٩]+\./', $indicatorsText)) {
            $indicatorLines = preg_split('/[٠-٩]+\./', $indicatorsText, -1, PREG_SPLIT_NO_EMPTY);
        }
        // If none of the above, treat as single indicator
        else {
            $indicatorLines = [$indicatorsText];
        }
        
        foreach ($indicatorLines as $index => $line) {
            $indicatorText = trim($line);
            if (empty($indicatorText)) {
                continue;
            }
            
            // Clean up common bullet points or numbers at the beginning
            $indicatorText = preg_replace('/^[\d٠-٩]+\.\s*/u', '', $indicatorText);
            $indicatorText = preg_replace('/^[•\-*]\s*/u', '', $indicatorText);
            $indicatorText = trim($indicatorText);
            
            if (empty($indicatorText)) {
                continue;
            }
            
            // Create indicator name (first 200 chars)
            $indicatorName = mb_substr($indicatorText, 0, 200, 'UTF-8');
            if (mb_strlen($indicatorText, 'UTF-8') > 200) {
                $indicatorName .= '...';
            }
            
            // Check if this name already exists (to avoid duplicates)
            $nameKey = md5($indicatorName);
            if (isset($indicatorNames[$nameKey])) {
                Log::debug('Indicator name already exists, skipping duplicate: ' . $indicatorName);
                // Still create the pivot relationship with existing indicator
                try {
                    $existingIndicator = RpIndicator::where('name', $indicatorName)->first();
                    if ($existingIndicator) {
                        RpActivityIndicator::firstOrCreate(
                            [
                                'activity_id' => $activity->rp_activities_id,
                                'indicator_id' => $existingIndicator->rp_indicators_id
                            ],
                            [
                                'rp_activity_indicators_id' => (string) Str::uuid(),
                                'notes' => null,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                        $processedPivot++;
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to create activity indicator pivot: ' . $e->getMessage());
                }
                continue;
            }
            
            $indicatorNames[$nameKey] = true;
            
            try {
                // Create indicator - using 'name' field since schema doesn't have 'code'
                $indicator = RpIndicator::create([
                    'rp_indicators_id' => (string) Str::uuid(),
                    'name' => $indicatorName,
                    'description' => $indicatorText,
                    'indicator_type' => 'output',
                    'target_value' => null,
                    'data_source' => 'manual',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $processedIndicators++;
                
                // Create pivot table entry in rp_activity_indicators
                RpActivityIndicator::create([
                    'rp_activity_indicators_id' => (string) Str::uuid(),
                    'activity_id' => $activity->rp_activities_id,
                    'indicator_id' => $indicator->rp_indicators_id,
                    'notes' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $processedPivot++;
                
                Log::debug('Activity indicator created:', [
                    'activity_id' => $activity->rp_activities_id,
                    'activity_code' => $activity->code,
                    'indicator_id' => $indicator->rp_indicators_id,
                    'indicator_name' => $indicatorName
                ]);
                
            } catch (\Exception $e) {
                Log::error('Failed to create activity indicator for activity ' . $activity->code . ': ' . $e->getMessage());
            }
        }
        
        return [
            'processed_indicators' => $processedIndicators,
            'processed_pivot' => $processedPivot,
            'names' => $indicatorNames
        ];
    }

    /**
     * Focal points processor - FIXED VERSION
     */
    private function processActivityFocalPoints(RpActivity $activity, string $focalPointsText, array $uniqueFocalpoints): array
    {
        $processedFocalpoints = 0;
        $processedPivot = 0;
        $focalpointNames = $uniqueFocalpoints;
        
        // Clean the text - remove quotes but keep everything else
        $focalPointsText = trim($focalPointsText, '" \t\n\r\0\x0B');
        $focalPointsText = str_replace('""', '"', $focalPointsText);
        
        if (empty($focalPointsText)) {
            return ['processed_focalpoints' => 0, 'processed_pivot' => 0, 'names' => $focalpointNames];
        }
        
        Log::debug('Processing focal points for activity ' . $activity->code, ['text' => $focalPointsText]);
        
        // Store the COMPLETE original text as a single focal point entry
        $completeText = $focalPointsText;
        
        // Clean up the text for the name
        $displayName = trim($completeText);
        
        // Check if this name already exists
        $nameKey = md5($displayName);
        if (isset($focalpointNames[$nameKey])) {
            Log::debug('Focal point name already exists: ' . $displayName);
            // Still create the pivot relationship with existing focal point
            try {
                $existingFocalpoint = RpFocalpoint::where('name', $displayName)->first();
                if ($existingFocalpoint) {
                    RpActivityFocalpoint::firstOrCreate(
                        [
                            'activity_id' => $activity->rp_activities_id,
                            'focalpoint_id' => $existingFocalpoint->rp_focalpoints_id
                        ],
                        [
                            'rp_activity_focalpoints_id' => (string) Str::uuid(),
                            'role' => 'Focal Point',
                            'end_date' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                    $processedPivot++;
                }
            } catch (\Exception $e) {
                Log::error('Failed to create activity focal point pivot: ' . $e->getMessage());
            }
            return [
                'processed_focalpoints' => 0,
                'processed_pivot' => $processedPivot,
                'names' => $focalpointNames
            ];
        }
        
        $focalpointNames[$nameKey] = true;
        $processedFocalpoints++;
        
        try {
            // Create focal point using name as identifier
            $focalpoint = RpFocalpoint::create([
                'rp_focalpoints_id' => (string) Str::uuid(),
                'name' => $displayName,
                'type' => 'internal',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Create pivot table entry in rp_activity_focalpoints
            RpActivityFocalpoint::create([
                'rp_activity_focalpoints_id' => (string) Str::uuid(),
                'activity_id' => $activity->rp_activities_id,
                'focalpoint_id' => $focalpoint->rp_focalpoints_id,
                'role' => 'Focal Point',
                'end_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $processedPivot++;
            
            Log::debug('Activity focal point created:', [
                'activity_id' => $activity->rp_activities_id,
                'activity_code' => $activity->code,
                'focalpoint_id' => $focalpoint->rp_focalpoints_id,
                'focalpoint_name' => $displayName
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to create activity focal point for activity ' . $activity->code . ': ' . $e->getMessage());
        }
        
        return [
            'processed_focalpoints' => $processedFocalpoints,
            'processed_pivot' => $processedPivot,
            'names' => $focalpointNames
        ];
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