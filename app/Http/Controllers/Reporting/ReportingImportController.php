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
use App\Models\RpActivityIndicator; // ADDED
use App\Models\RpActivityFocalpoint; // ADDED
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
                'activity_indicators' => $countsAfter['activity_indicators'] - $countsBefore['activity_indicators'], // ADDED
                'activity_focalpoints' => $countsAfter['activity_focalpoints'] - $countsBefore['activity_focalpoints'], // ADDED
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
                'activity_indicators_rows' => $results['processed_activity_indicators'], // ADDED
                'activity_focalpoints_rows' => $results['processed_activity_focalpoints'], // ADDED
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
                'activity_indicators' => $actualInserted['activity_indicators'] . ' new / ' . $results['processed_activity_indicators'] . ' rows in file', // ADDED
                'activity_focalpoints' => $actualInserted['activity_focalpoints'] . ' new / ' . $results['processed_activity_focalpoints'] . ' rows in file', // ADDED
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
            'activity_indicators' => RpActivityIndicator::count(), // ADDED
            'activity_focalpoints' => RpActivityFocalpoint::count(), // ADDED
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
                'processed_activity_indicators' => $activityResults['processed_activity_indicators'], // ADDED
                'processed_activity_focalpoints' => $activityResults['processed_activity_focalpoints'], // ADDED
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
            'processed_activity_indicators' => $activityResults['processed_activity_indicators'], // ADDED
            'processed_activity_focalpoints' => $activityResults['processed_activity_focalpoints'], // ADDED
        ];
    }

    /**
     * Process hierarchy section - UPDATED to include target actions
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
            $actionTargets = trim($row[9] ?? ''); // Column to process for rp_target_actions

            // Skip row if all hierarchy data is empty
            if (empty($componentCode) && empty($componentName) && 
                empty($programCode) && empty($programName) &&
                empty($unitCode) && empty($unitName)) {
                Log::debug('Skipping row ' . ($index + 2) . ' - all hierarchy data empty');
                continue;
            }

            // ========== COMPONENT ==========
            if (!empty($componentCode) || !empty($componentName)) {
                if (empty($componentCode)) {
                    $componentCode = 'COMP_' . Str::random(8);
                }
                if (empty($componentName)) {
                    $componentName = 'Component - ' . $componentCode;
                }
                
                $componentKey = $componentCode;
                
                if (!isset($componentCache[$componentKey])) {
                    try {
                        $component = RpComponent::firstOrCreate(
                            ['code' => $componentCode],
                            [
                                'name' => $componentName,
                                'description' => null,
                                'is_active' => true,
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
            } else {
                // If no component data in this row, use the last one
                if (empty($componentCache)) {
                    Log::warning('No component data found and no previous component available');
                    continue;
                }
                $componentId = end($componentCache);
                $componentKey = key($componentCache);
            }

            // ========== PROGRAM ==========
            if (!empty($programCode) || !empty($programName)) {
                if (empty($programCode)) {
                    $programCode = 'PROG_' . Str::random(6);
                }
                if (empty($programName)) {
                    $programName = 'Program - ' . $programCode;
                }
                
                $programKey = $componentKey . '|' . $programCode;
                
                if (!isset($programCache[$programKey])) {
                    try {
                        $program = RpProgram::firstOrCreate(
                            ['code' => $programCode],
                            [
                                'component_id' => $componentId,
                                'name' => $programName,
                                'description' => null,
                                'is_active' => true,
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
                        // Continue with default program
                        $programCode = 'DEFAULT_PROG';
                        $programKey = $componentKey . '|' . $programCode;
                        $program = RpProgram::firstOrCreate(
                            ['code' => $programCode],
                            [
                                'component_id' => $componentId,
                                'name' => 'Default Program',
                                'is_active' => true
                            ]
                        );
                        $programCache[$programKey] = $program->rp_programs_id;
                    }
                }
                $programId = $programCache[$programKey];
            } else {
                // If no program data in this row, use the last one for this component
                $lastProgramKey = null;
                foreach ($programCache as $key => $id) {
                    if (strpos($key, $componentKey . '|') === 0) {
                        $lastProgramKey = $key;
                    }
                }
                if ($lastProgramKey) {
                    $programId = $programCache[$lastProgramKey];
                    $programCode = explode('|', $lastProgramKey)[1];
                } else {
                    Log::warning('No program data found for component: ' . $componentKey);
                    continue;
                }
            }

            // ========== UNIT ==========
            if (!empty($unitCode) || !empty($unitName)) {
                if (empty($unitCode)) {
                    $unitCode = 'UNIT_' . Str::random(4);
                }
                if (empty($unitName)) {
                    $unitName = 'Unit - ' . $unitCode;
                }
                
                $unitKey = $programKey . '|' . $unitCode;
                
                if (!isset($unitCache[$unitKey])) {
                    try {
                        $unit = RpUnit::firstOrCreate(
                            ['code' => $unitCode],
                            [
                                'program_id' => $programId,
                                'name' => $unitName,
                                'unit_type' => 'department',
                                'description' => null,
                                'is_active' => true,
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
                        // Continue with default unit
                        $unitCode = 'DEFAULT_UNIT';
                        $unitKey = $programKey . '|' . $unitCode;
                        $unit = RpUnit::firstOrCreate(
                            ['code' => $unitCode],
                            [
                                'program_id' => $programId,
                                'name' => 'Default Unit',
                                'unit_type' => 'department',
                                'is_active' => true
                            ]
                        );
                        $unitCache[$unitKey] = $unit->rp_units_id;
                    }
                }
                $unitId = $unitCache[$unitKey];
            } else {
                // If no unit data in this row, use the last one for this program
                $lastUnitKey = null;
                foreach ($unitCache as $key => $id) {
                    if (strpos($key, $programKey . '|') === 0) {
                        $lastUnitKey = $key;
                    }
                }
                if ($lastUnitKey) {
                    $unitId = $unitCache[$lastUnitKey];
                    $unitCode = explode('|', $lastUnitKey)[2];
                } else {
                    Log::warning('No unit data found for program: ' . $programKey);
                    continue;
                }
            }

            // ========== ACTION ==========
            $actionId = null;
            if (!empty($actionCode) || !empty($actionName)) {
                if (empty($actionCode)) {
                    $actionCode = 'ACT_' . ($processedActions + 1);
                }
                if (empty($actionName)) {
                    $actionName = 'Action - ' . $actionCode;
                }
                
                $actionKey = $unitKey . '|' . $actionCode;
                
                if (!isset($actionCache[$actionKey])) {
                    try {
                        $action = RpAction::firstOrCreate(
                            ['code' => $actionCode],
                            [
                                'unit_id' => $unitId,
                                'name' => $actionName,
                                'description' => !empty($actionObjective) ? $actionObjective : null,
                                'planned_start_date' => now(),
                                'planned_end_date' => now()->addYear(),
                                'status' => 'planned',
                                'is_active' => true,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                        $actionId = $action->rp_actions_id;
                        $actionCache[$actionKey] = $actionId;
                        $processedActions++;
                        
                        // Create multiple reference formats for better matching
                        $actionMap[$actionId] = [
                            'id' => $actionId,
                            'references' => [
                                // Format 1: Component.Program.Unit.Action
                                $componentCode . '.' . $programCode . '.' . $unitCode . '.' . $actionCode,
                                // Format 2: Component.ProgramNumber.Unit.Action
                                $componentCode . '.' . preg_replace('/[^0-9]/', '', $programCode) . '.' . $unitCode . '.' . $actionCode,
                                // Format 3: Component.3.Unit.Action (default)
                                $componentCode . '.3.' . $unitCode . '.' . $actionCode,
                                // Format 4: Just the action code
                                $actionCode,
                            ]
                        ];
                        
                        Log::debug('Action created/retrieved:', [
                            'id' => $actionId,
                            'code' => $actionCode,
                            'name' => $actionName,
                            'references' => $actionMap[$actionId]['references']
                        ]);
                        
                        // ========== PROCESS ACTION TARGETS & BENEFICIARIES ==========
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
                    
                    // Process Action Targets & Beneficiaries for existing action too
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
     * Process Action Targets & Beneficiaries into rp_target_actions table
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
                    'target_name' => mb_substr($beneficiary, 0, 255, 'UTF-8'), // Store as target_name
                    'description' => $beneficiary, // Store full text in description
                    'target_value' => 1, // Default value
                    'unit_of_measure' => 'عدد', // Arabic for "number"
                    'target_date' => now()->addYear()->format('Y-m-d'),
                    'status' => 'pending',
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
     * Process activities section - UPDATED with pivot table support
     */
    private function processActivitySection(array $data, array $actionMap)
    {
        Log::info('=== PROCESSING ACTIVITIES SECTION ===');
        
        $processedActivities = 0;
        $processedIndicators = 0;
        $processedFocalpoints = 0;
        $processedActivityIndicators = 0; // ADDED
        $processedActivityFocalpoints = 0; // ADDED
        
        $activityCodes = [];
        $indicatorCodes = [];
        $focalpointCodes = [];

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
            
            if (!$actionId) {
                Log::warning('Action not found for reference: ' . $actionReference . ', creating standalone activity');
                // Create activity without action link
                $actionId = null;
            }

            // Create Activity
            try {
                $activityData = [
                    'code' => $activityCode,
                    'name' => $activityName,
                    'description' => null,
                    'activity_type' => 'project_activity',
                    'status' => $status,
                    'is_active' => true,
                    'needs_sync' => false,
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
                if (!empty($indicatorsText) && $indicatorsText !== '""') {
                    $indicatorsResult = $this->processActivityIndicators($activity, $indicatorsText, $indicatorCodes);
                    $processedIndicators += $indicatorsResult['processed'];
                    $processedActivityIndicators += $indicatorsResult['processed']; // Count pivot entries
                    $indicatorCodes = $indicatorsResult['codes'];
                }

                // ========== FOCAL POINTS ==========
                if (!empty($focalPointsText) && $focalPointsText !== '""') {
                    $focalpointsResult = $this->processActivityFocalPointsEnhanced($activity, $focalPointsText, $focalpointCodes);
                    $processedFocalpoints += $focalpointsResult['processed'];
                    $processedActivityFocalpoints += $focalpointsResult['processed']; // Count pivot entries
                    $focalpointCodes = $focalpointsResult['codes'];
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
            'processed_activity_indicators' => $processedActivityIndicators, // ADDED
            'processed_activity_focalpoints' => $processedActivityFocalpoints, // ADDED
            'unique_activities' => count($activityCodes),
            'unique_indicators' => count($indicatorCodes),
            'unique_focalpoints' => count($focalpointCodes)
        ]);

        return [
            'processed_activities' => $processedActivities,
            'processed_indicators' => $processedIndicators,
            'processed_focalpoints' => $processedFocalpoints,
            'processed_activity_indicators' => $processedActivityIndicators, // ADDED
            'processed_activity_focalpoints' => $processedActivityFocalpoints, // ADDED
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
            'c' => 'completed',
            'd' => 'completed'
        ];

        $lowerStatus = strtolower(trim($excelStatus));
        return $statusMap[$lowerStatus] ?? 'planned';
    }

    /**
     * Process indicators for an activity - UPDATED with pivot table creation
     */
    private function processActivityIndicators(RpActivity $activity, string $indicatorsText, array $existingCodes): array
    {
        $processed = 0;
        $indicatorCodes = $existingCodes;
        
        // Clean the text - preserve original formatting
        $indicatorsText = trim($indicatorsText, '" \t\n\r\0\x0B');
        $indicatorsText = str_replace('""', '"', $indicatorsText);
        
        if (empty($indicatorsText)) {
            return ['processed' => 0, 'codes' => $indicatorCodes];
        }
        
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
            
            $processed++;
            
            // Create indicator name (first 200 chars)
            $indicatorName = mb_substr($indicatorText, 0, 200, 'UTF-8');
            if (mb_strlen($indicatorText, 'UTF-8') > 200) {
                $indicatorName .= '...';
            }
            
            // Create unique code
            $indicatorCode = 'IND_' . $activity->code . '_' . ($index + 1) . '_' . Str::random(4);
            
            // Ensure unique code
            if (isset($indicatorCodes[$indicatorCode])) {
                $indicatorCode = $indicatorCode . '_' . time();
            }
            $indicatorCodes[$indicatorCode] = true;
            
            try {
                // Create or get indicator
                $indicator = RpIndicator::firstOrCreate(
                    ['indicator_code' => $indicatorCode],
                    [
                        'name' => $indicatorName,
                        'description' => $indicatorText,
                        'indicator_type' => 'output',
                        'unit_of_measure' => 'number',
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                
                // Create pivot table entry in rp_activity_indicators
                RpActivityIndicator::firstOrCreate(
                    [
                        'activity_id' => $activity->rp_activities_id,
                        'indicator_id' => $indicator->rp_indicators_id
                    ],
                    [
                        'rp_activity_indicators_id' => (string) Str::uuid(),
                        'target_value' => null,
                        'achieved_value' => null,
                        'achieved_date' => null,
                        'notes' => null,
                        'status' => 'planned',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                
                Log::debug('Activity indicator created in pivot table:', [
                    'activity_id' => $activity->rp_activities_id,
                    'indicator_id' => $indicator->rp_indicators_id,
                    'indicator_code' => $indicatorCode,
                    'indicator_name' => $indicatorName
                ]);
                
            } catch (\Exception $e) {
                Log::error('Failed to create activity indicator for activity ' . $activity->code . ': ' . $e->getMessage());
            }
        }
        
        return ['processed' => $processed, 'codes' => $indicatorCodes];
    }

    /**
     * Enhanced focal points processor - UPDATED with pivot table creation
     */
    private function processActivityFocalPointsEnhanced(RpActivity $activity, string $focalPointsText, array $uniqueFocalpoints): array
    {
        $processed = 0;
        $focalpointCodes = $uniqueFocalpoints;
        
        // Clean the text - remove quotes but keep EVERYTHING ELSE
        $focalPointsText = trim($focalPointsText, '" \t\n\r\0\x0B');
        $focalPointsText = str_replace('""', '"', $focalPointsText);
        
        if (empty($focalPointsText)) {
            return ['processed' => 0, 'codes' => $focalpointCodes];
        }
        
        Log::debug('Processing focal points text for activity ' . $activity->code . ':', ['text' => $focalPointsText]);
        
        // Store the COMPLETE original text as a single focal point entry
        $completeText = $focalPointsText;
        
        // Create a single focal point entry with the complete text
        $processed++;
        
        // Create a unique code for this complete focal point entry
        $code = 'FP_' . $activity->code . '_' . Str::random(8);
        
        // Ensure unique code
        if (isset($focalpointCodes[$code])) {
            $code = $code . '_' . time();
        }
        $focalpointCodes[$code] = true;
        
        try {
            // Store the COMPLETE text as the name - exactly as in the Excel cell
            $displayName = $completeText;
            
            $focalpoint = RpFocalpoint::firstOrCreate(
                ['focalpoint_code' => $code],
                [
                    'name' => $displayName,
                    'position' => 'Focal Point',
                    'department' => 'Operations',
                    'email' => null,
                    'phone' => null,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            
            // Create pivot table entry in rp_activity_focalpoints
            RpActivityFocalpoint::firstOrCreate(
                [
                    'activity_id' => $activity->rp_activities_id,
                    'focalpoint_id' => $focalpoint->rp_focalpoints_id
                ],
                [
                    'rp_activity_focalpoints_id' => (string) Str::uuid(),
                    'role' => 'Focal Point',
                    'responsibilities' => 'Primary contact for this activity',
                    'assigned_date' => now(),
                    'end_date' => null,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            
            Log::debug('Activity focal point created in pivot table:', [
                'activity_id' => $activity->rp_activities_id,
                'focalpoint_id' => $focalpoint->rp_focalpoints_id,
                'focalpoint_code' => $code,
                'focalpoint_name' => $displayName
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to create activity focal point for activity ' . $activity->code . ': ' . $e->getMessage());
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