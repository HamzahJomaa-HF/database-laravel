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
                'components_rows' => $results['total_hierarchy_rows'],
                'unique_components_in_file' => $results['unique_components_in_file'],
                'unique_programs_in_file' => $results['unique_programs_in_file'],
                'unique_units_in_file' => $results['unique_units_in_file'],
                'unique_actions_in_file' => $results['unique_actions_in_file'],
                'activities_rows' => $results['total_activity_rows'],
                'indicators_rows' => $results['total_indicator_rows_in_file'],
                'focalpoints_rows' => $results['total_focalpoint_rows_in_file']
            ]);

            $summary = [
                'components' => $actualInserted['components'] . ' new / ' . $results['unique_components_in_file'] . ' unique in file',
                'programs' => $actualInserted['programs'] . ' new / ' . $results['unique_programs_in_file'] . ' unique in file',
                'units' => $actualInserted['units'] . ' new / ' . $results['unique_units_in_file'] . ' unique in file',
                'actions' => $actualInserted['actions'] . ' new / ' . $results['unique_actions_in_file'] . ' unique in file',
                'activities' => $actualInserted['activities'] . ' new / ' . $results['total_activity_rows'] . ' rows in file',
                'indicators' => $actualInserted['indicators'] . ' new / ' . $results['total_indicator_rows_in_file'] . ' in file',
                'focalpoints' => $actualInserted['focalpoints'] . ' new / ' . $results['total_focalpoint_rows_in_file'] . ' in file',
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
                'total_hierarchy_rows' => count($hierarchyRows),
                'unique_components_in_file' => $hierarchyResults['unique_components_in_file'],
                'unique_programs_in_file' => $hierarchyResults['unique_programs_in_file'],
                'unique_units_in_file' => $hierarchyResults['unique_units_in_file'],
                'unique_actions_in_file' => $hierarchyResults['unique_actions_in_file'],
                'processed_components' => $hierarchyResults['processed_components'],
                'processed_programs' => $hierarchyResults['processed_programs'],
                'processed_units' => $hierarchyResults['processed_units'],
                'processed_actions' => $hierarchyResults['processed_actions'],
                'total_activity_rows' => count($activitiesData) - 1, // Exclude header
                'processed_activities' => $activityResults['processed_activities'],
                'processed_indicators' => $activityResults['processed_indicators'],
                'processed_focalpoints' => $activityResults['processed_focalpoints'],
                'total_indicator_rows_in_file' => $activityResults['total_indicators_in_file'],
                'total_focalpoint_rows_in_file' => $activityResults['total_focalpoints_in_file']
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
            'total_hierarchy_rows' => count($hierarchyData),
            'unique_components_in_file' => $hierarchyResults['unique_components_in_file'],
            'unique_programs_in_file' => $hierarchyResults['unique_programs_in_file'],
            'unique_units_in_file' => $hierarchyResults['unique_units_in_file'],
            'unique_actions_in_file' => $hierarchyResults['unique_actions_in_file'],
            'processed_components' => $hierarchyResults['processed_components'],
            'processed_programs' => $hierarchyResults['processed_programs'],
            'processed_units' => $hierarchyResults['processed_units'],
            'processed_actions' => $hierarchyResults['processed_actions'],
            'total_activity_rows' => count($activityData),
            'processed_activities' => $activityResults['processed_activities'],
            'processed_indicators' => $activityResults['processed_indicators'],
            'processed_focalpoints' => $activityResults['processed_focalpoints'],
            'total_indicator_rows_in_file' => $activityResults['total_indicators_in_file'],
            'total_focalpoint_rows_in_file' => $activityResults['total_focalpoints_in_file']
        ];
    }

    /**
     * Process hierarchy section - FIXED to count unique items in file correctly
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
        $componentCache = [];
        $programCache = [];
        $unitCache = [];
        
        // Track unique items in the file (not in database)
        $uniqueComponentsInFile = [];
        $uniqueProgramsInFile = [];
        $uniqueUnitsInFile = [];
        $uniqueActionsInFile = [];

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

            // Track unique items in the file
            if (!empty($componentCode)) {
                $uniqueComponentsInFile[$componentCode] = true;
            }
            if (!empty($programCode)) {
                $uniqueProgramsInFile[$programCode] = true;
            }
            if (!empty($unitCode)) {
                $uniqueUnitsInFile[$unitCode] = true;
            }
            if (!empty($actionCode)) {
                $uniqueActionsInFile[$actionCode] = true;
            }

            // Skip ENTIRE row if no component data
            if (empty($componentCode) && empty($componentName)) {
                Log::debug('Skipping row ' . ($index + 2) . ' - completely empty');
                continue;
            }

            // CREATE COMPONENT - Use cache to prevent duplicates
            if (!empty($componentCode)) {
                if (!isset($componentCache[$componentCode])) {
                    try {
                        $component = RpComponent::firstOrCreate(
                            ['code' => $componentCode],
                            [
                                'name' => $componentName ?: $componentCode,
                                'description' => null,
                                'is_active' => true
                            ]
                        );
                        $componentCache[$componentCode] = $component->rp_components_id;
                        $processedComponents++;
                        Log::debug('Created component: ' . $componentCode . ' -> ID: ' . $componentCache[$componentCode]);
                    } catch (\Exception $e) {
                        Log::error('Error creating component: ' . $e->getMessage());
                        continue;
                    }
                }
                $componentId = $componentCache[$componentCode];
            } else {
                // Skip if no component code
                Log::debug('Skipping row ' . ($index + 2) . ' - no component code');
                continue;
            }

            // CREATE PROGRAM - Only create if programCode exists
            $programId = null;
            if (!empty($programCode)) {
                $programKey = $componentId . '_' . $programCode;
                
                if (!isset($programCache[$programKey])) {
                    try {
                        $program = RpProgram::firstOrCreate(
                            ['code' => $programCode],
                            [
                                'component_id' => $componentId,
                                'name' => $programName ?: $programCode,
                                'description' => null,
                                'is_active' => true
                            ]
                        );
                        $programCache[$programKey] = $program->rp_programs_id;
                        $processedPrograms++;
                        Log::debug('Created program: ' . $programCode . ' -> ID: ' . $programCache[$programKey]);
                    } catch (\Exception $e) {
                        Log::error('Error creating program: ' . $e->getMessage());
                        continue;
                    }
                }
                $programId = $programCache[$programKey];
            } else {
                // Skip if no program code
                Log::debug('Skipping row ' . ($index + 2) . ' - no program code');
                continue;
            }

            // CREATE UNIT - Only create if unitCode exists
            $unitId = null;
            if (!empty($unitCode)) {
                $unitKey = $programId . '_' . $unitCode;
                
                if (!isset($unitCache[$unitKey])) {
                    try {
                        $unit = RpUnit::firstOrCreate(
                            ['code' => $unitCode],
                            [
                                'program_id' => $programId,
                                'name' => $unitName ?: $unitCode,
                                'unit_type' => 'department',
                                'description' => null,
                                'is_active' => true
                            ]
                        );
                        $unitCache[$unitKey] = $unit->rp_units_id;
                        $processedUnits++;
                        Log::debug('Created unit: ' . $unitCode . ' -> ID: ' . $unitCache[$unitKey]);
                    } catch (\Exception $e) {
                        Log::error('Error creating unit: ' . $e->getMessage());
                        continue;
                    }
                }
                $unitId = $unitCache[$unitKey];
            } else {
                // Skip if no unit code
                Log::debug('Skipping row ' . ($index + 2) . ' - no unit code');
                continue;
            }

            // CREATE ACTION - Only create if actionCode exists
            if (!empty($actionCode) && $unitId) {
                $actionKey = $unitId . '_' . $actionCode;
                
                if (!isset($actionMap[$actionKey])) {
                    try {
                        $action = RpAction::firstOrCreate(
                            ['code' => $actionCode],
                            [
                                'unit_id' => $unitId,
                                'name' => $actionName ?: $actionCode,
                                'description' => $actionObjective ?: 'No objective provided',
                                'planned_start_date' => now(),
                                'planned_end_date' => now()->addYear(),
                                'status' => 'planned',
                                'is_active' => true
                            ]
                        );
                        
                        // Create action reference - EXACTLY as in your Excel
                        $programNumber = $this->extractNumberFromProgramCode($programCode);
                        
                        // Reference format: AD.A.1.iv.1
                        $reference = $componentCode . '.' . $programNumber . '.' . $unitCode . '.' . $actionCode;
                        
                        $actionMap[$reference] = [
                            'id' => $action->rp_actions_id,
                            'component_code' => $componentCode,
                            'program_code' => $programCode,
                            'program_number' => $programNumber,
                            'unit_code' => $unitCode,
                            'action_code' => $actionCode,
                            'reference' => $reference
                        ];
                        
                        $processedActions++;
                        Log::info('Created action:', [
                            'id' => $action->rp_actions_id,
                            'code' => $action->code,
                            'reference' => $reference
                        ]);
                        
                    } catch (\Exception $e) {
                        Log::error('Error creating action ' . $actionCode . ': ' . $e->getMessage());
                    }
                }
            }
            
            // Progress logging
            if (($index + 1) % 50 === 0) {
                Log::info('Processed ' . ($index + 1) . ' hierarchy rows...');
            }
        }

        Log::info('Hierarchy section completed:', [
            'total_rows' => count($data),
            'unique_components_in_file' => count($uniqueComponentsInFile),
            'unique_programs_in_file' => count($uniqueProgramsInFile),
            'unique_units_in_file' => count($uniqueUnitsInFile),
            'unique_actions_in_file' => count($uniqueActionsInFile),
            'processed_components' => $processedComponents,
            'processed_programs' => $processedPrograms,
            'processed_units' => $processedUnits,
            'processed_actions' => $processedActions,
            'unique_components_in_db' => count($componentCache),
            'unique_programs_in_db' => count($programCache),
            'unique_units_in_db' => count($unitCache),
            'unique_actions_in_db' => count($actionMap)
        ]);

        return [
            'unique_components_in_file' => count($uniqueComponentsInFile),
            'unique_programs_in_file' => count($uniqueProgramsInFile),
            'unique_units_in_file' => count($uniqueUnitsInFile),
            'unique_actions_in_file' => count($uniqueActionsInFile),
            'processed_components' => $processedComponents,
            'processed_programs' => $processedPrograms,
            'processed_units' => $processedUnits,
            'processed_actions' => $processedActions,
            'actionMap' => $actionMap
        ];
    }

    /**
     * Extract number from program code (e.g., A1 -> 1, B2 -> 2, A -> 1)
     */
    private function extractNumberFromProgramCode(string $programCode): string
    {
        // Remove all non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $programCode);
        
        // If no number found, try to extract from position
        if (empty($number)) {
            // For codes like "A", "B", "C" etc, use position in alphabet
            $letter = strtoupper(substr($programCode, 0, 1));
            if (strlen($letter) == 1 && ctype_alpha($letter)) {
                $number = ord($letter) - ord('A') + 1;
            } else {
                $number = '1'; // Default
            }
        }
        
        return (string)$number;
    }

    /**
     * Process activities section - FIXED to count items in file correctly
     */
    private function processActivitySection(array $data, array $actionMap)
    {
        Log::info('=== PROCESSING ACTIVITIES SECTION ===');
        Log::info('Total activity rows to process: ' . count($data));
        Log::info('Available actions in map: ' . count($actionMap));
        
        $processedActivities = 0;
        $processedIndicators = 0;
        $processedFocalpoints = 0;
        
        $activityCodes = [];
        $focalpointCache = [];
        
        // Track items in the file (not in database)
        $totalIndicatorsInFile = 0;
        $totalFocalpointsInFile = 0;

        // Skip header row if present
        $headerRow = $data[0] ?? [];
        $isHeader = isset($headerRow[0]) && (stripos($headerRow[0], 'action') !== false || stripos($headerRow[0], 'مرجع') !== false);
        
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

            // Count indicators in this row
            if (!empty($indicatorsText) && $indicatorsText !== '""' && $indicatorsText !== 'N/A') {
                $indicatorCount = $this->countIndicatorsInText($indicatorsText);
                $totalIndicatorsInFile += $indicatorCount;
            }
            
            // Count focal points in this row
            if (!empty($focalPointsText) && $focalPointsText !== '""' && $focalPointsText !== 'N/A') {
                $focalpointCount = $this->countFocalPointsInText($focalPointsText);
                $totalFocalpointsInFile += $focalpointCount;
            }

            // Skip if no activity data at all
            if (empty($activityCode) && empty($activityName)) {
                Log::debug('Skipping activity row ' . ($index + 2) . ' - completely empty');
                continue;
            }

            // CREATE ACTIVITY
            $processedActivities++;
            
            // Generate activity code if empty
            if (empty($activityCode)) {
                $activityCode = 'ACTIVITY_' . ($index + 1);
            }
            if (empty($activityName)) {
                $activityName = 'Activity ' . ($index + 1);
            }
            
            // Ensure unique activity code
            $originalCode = $activityCode;
            $suffix = 1;
            while (isset($activityCodes[$activityCode])) {
                $activityCode = $originalCode . '_' . $suffix;
                $suffix++;
            }
            $activityCodes[$activityCode] = true;

            // Find action ID
            $actionId = $this->findActionIdByReference($actionReference, $actionMap);
            
            if (!$actionId) {
                Log::warning('Action not found for reference: ' . $actionReference . ', creating placeholder');
                $actionId = $this->createPlaceholderActionForReference($actionReference, $index);
            }

            // Create Activity
            try {
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
                
                Log::info('Activity saved:', ['id' => $activity->id, 'code' => $activity->code]);
                
                // Process Indicators
                if (!empty($indicatorsText) && $indicatorsText !== '""' && $indicatorsText !== 'N/A') {
                    $indicatorsResult = $this->processActivityIndicators($activity, $indicatorsText);
                    $processedIndicators += $indicatorsResult['processed'];
                }

                // Process Focal Points
                if (!empty($focalPointsText) && $focalPointsText !== '""' && $focalPointsText !== 'N/A') {
                    $focalpointsResult = $this->processActivityFocalPoints($activity, $focalPointsText, $focalpointCache);
                    $processedFocalpoints += $focalpointsResult['processed'];
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
            'total_activity_rows' => count($data),
            'total_indicators_in_file' => $totalIndicatorsInFile,
            'total_focalpoints_in_file' => $totalFocalpointsInFile,
            'processed_activities' => $processedActivities,
            'processed_indicators' => $processedIndicators,
            'processed_focalpoints' => $processedFocalpoints,
            'unique_activities' => count($activityCodes),
            'unique_focalpoints' => count($focalpointCache)
        ]);

        return [
            'processed_activities' => $processedActivities,
            'processed_indicators' => $processedIndicators,
            'processed_focalpoints' => $processedFocalpoints,
            'total_indicators_in_file' => $totalIndicatorsInFile,
            'total_focalpoints_in_file' => $totalFocalpointsInFile
        ];
    }

    /**
     * Count indicators in text
     */
    private function countIndicatorsInText(string $indicatorsText): int
    {
        $count = 0;
        
        // Clean the text
        $indicatorsText = trim($indicatorsText, '" \t\n\r\0\x0B');
        $indicatorsText = str_replace('""', '"', $indicatorsText);
        
        if (empty($indicatorsText)) {
            return 0;
        }
        
        // Replace full-width numbers with regular numbers
        $indicatorsText = str_replace(
            ['１', '２', '３', '４', '５', '６', '７', '８', '９', '０'],
            ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'],
            $indicatorsText
        );
        
        // Also replace Arabic numerals
        $indicatorsText = str_replace(
            ['١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', '٠'],
            ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'],
            $indicatorsText
        );
        
        // Count by numbered pattern
        if (preg_match_all('/[0-9]+\./', $indicatorsText, $matches)) {
            $count = count($matches[0]);
        } else {
            // Count by newlines
            $lines = explode("\n", $indicatorsText);
            foreach ($lines as $line) {
                if (trim($line) && trim($line) !== '-') {
                    $count++;
                }
            }
        }
        
        return $count;
    }

    /**
     * Count focal points in text
     */
    private function countFocalPointsInText(string $focalPointsText): int
    {
        $count = 0;
        
        // Clean the text
        $focalPointsText = trim($focalPointsText, '" \t\n\r\0\x0B');
        $focalPointsText = str_replace('""', '"', $focalPointsText);
        
        if (empty($focalPointsText)) {
            return 0;
        }
        
        // Normalize newlines
        $focalPointsText = str_replace(["\r\n", "\r"], "\n", $focalPointsText);
        
        // Split by various separators and count
        $focalPointNames = preg_split('/[\n,،\|\/\;]+/', $focalPointsText, -1, PREG_SPLIT_NO_EMPTY);
        
        foreach ($focalPointNames as $name) {
            $name = trim($name);
            if (!empty($name) && $name !== '-') {
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * Find action ID by reference
     */
    private function findActionIdByReference(string $reference, array $actionMap): ?string
    {
        $reference = trim($reference);
        
        if (empty($reference)) {
            return null;
        }
        
        // First try exact match
        if (isset($actionMap[$reference])) {
            return $actionMap[$reference]['id'];
        }
        
        // Try different variations
        $parts = explode('.', $reference);
        if (count($parts) >= 4) {
            $componentCode = $parts[0] ?? '';
            $programNumber = $parts[1] ?? '';
            $unitCode = $parts[2] ?? '';
            $actionCode = $parts[3] ?? '';
            
            // Search in action map for matching pattern
            foreach ($actionMap as $actionData) {
                if (isset($actionData['component_code']) && $actionData['component_code'] === $componentCode &&
                    isset($actionData['program_number']) && $actionData['program_number'] === $programNumber &&
                    isset($actionData['unit_code']) && $actionData['unit_code'] === $unitCode &&
                    isset($actionData['action_code']) && $actionData['action_code'] === $actionCode) {
                    return $actionData['id'];
                }
            }
        }
        
        Log::warning('No match found for reference: ' . $reference);
        return null;
    }

    /**
     * Create placeholder action for a reference
     */
    private function createPlaceholderActionForReference(string $reference, int $index): string
    {
        try {
            $parts = explode('.', $reference);
            
            if (count($parts) >= 4) {
                $componentCode = $parts[0] ?? 'COMP_' . $index;
                $programNumber = $parts[1] ?? '1';
                $unitCode = $parts[2] ?? 'UNIT_' . $index;
                $actionCode = $parts[3] ?? 'ACT_' . $index;
                
                // Create program code from number
                $programCode = 'A' . $programNumber;
                
                // Create component
                $component = RpComponent::firstOrCreate(
                    ['code' => $componentCode],
                    [
                        'name' => 'Component ' . $componentCode,
                        'is_active' => true
                    ]
                );
                
                // Create program
                $program = RpProgram::firstOrCreate(
                    ['code' => $programCode],
                    [
                        'component_id' => $component->rp_components_id,
                        'name' => 'Program ' . $programCode,
                        'is_active' => true
                    ]
                );
                
                // Create unit
                $unit = RpUnit::firstOrCreate(
                    ['code' => $unitCode],
                    [
                        'program_id' => $program->rp_programs_id,
                        'name' => 'Unit ' . $unitCode,
                        'unit_type' => 'department',
                        'is_active' => true
                    ]
                );
                
                // Create action
                $action = RpAction::firstOrCreate(
                    ['code' => $actionCode],
                    [
                        'unit_id' => $unit->rp_units_id,
                        'name' => 'Action ' . $actionCode,
                        'status' => 'planned',
                        'is_active' => true
                    ]
                );
                
                return $action->rp_actions_id;
            }
            
        } catch (\Exception $e) {
            Log::error('Error creating placeholder action: ' . $e->getMessage());
        }
        
        // Fallback
        return $this->createSimplePlaceholderAction($index);
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
            Log::error('Error creating simple placeholder action: ' . $e->getMessage());
            
            // Last resort: get any action or create a very simple one
            $defaultAction = RpAction::first();
            if ($defaultAction) {
                return $defaultAction->rp_actions_id;
            }
            
            // Create a minimal action
            $minimalAction = RpAction::create([
                'unit_id' => 1,
                'code' => 'MINIMAL_ACT_' . $index,
                'name' => 'Minimal Action',
                'status' => 'planned',
                'is_active' => true
            ]);
            
            return $minimalAction->rp_actions_id;
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
            'ج' => 'ongoing',
            'ق' => 'planned',
            'م' => 'completed'
        ];

        $lowerStatus = strtolower(trim($excelStatus));
        return $statusMap[$lowerStatus] ?? 'planned';
    }

    /**
     * Process indicators for an activity
     */
    private function processActivityIndicators(RpActivity $activity, string $indicatorsText): array
    {
        $processed = 0;
        
        // Clean the text
        $indicatorsText = trim($indicatorsText, '" \t\n\r\0\x0B');
        $indicatorsText = str_replace('""', '"', $indicatorsText);
        
        if (empty($indicatorsText) || $indicatorsText === 'N/A') {
            return ['processed' => 0];
        }
        
        // Replace full-width numbers with regular numbers
        $indicatorsText = str_replace(
            ['１', '２', '３', '４', '５', '６', '۷', '８', '۹', '０'],
            ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'],
            $indicatorsText
        );
        
        // Also replace Arabic numerals
        $indicatorsText = str_replace(
            ['١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', '٠'],
            ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'],
            $indicatorsText
        );
        
        // Try splitting by numbered pattern
        $indicatorLines = [];
        if (preg_match('/[0-9]+\./', $indicatorsText)) {
            $indicatorLines = preg_split('/[0-9]+\./', $indicatorsText, -1, PREG_SPLIT_NO_EMPTY);
        }
        // Try splitting by newlines
        elseif (strpos($indicatorsText, "\n") !== false) {
            $indicatorLines = explode("\n", $indicatorsText);
        }
        // Just use the whole text
        else {
            $indicatorLines = [$indicatorsText];
        }
        
        foreach ($indicatorLines as $index => $line) {
            $indicatorText = trim($line);
            
            // Skip empty lines
            if (empty($indicatorText) || $indicatorText === '-') {
                continue;
            }
            
            $processed++;
            
            // Create indicator code
            $indicatorCode = 'IND_' . $activity->code . '_' . ($index + 1);
            
            // Ensure unique code
            $originalCode = $indicatorCode;
            $suffix = 1;
            while (RpIndicator::where('indicator_code', $indicatorCode)->exists()) {
                $indicatorCode = $originalCode . '_' . $suffix;
                $suffix++;
            }
            
            try {
                $indicator = RpIndicator::firstOrCreate(
                    ['indicator_code' => $indicatorCode],
                    [
                        'name' => substr($indicatorText, 0, 200),
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
        
        return ['processed' => $processed];
    }

    /**
     * Process focal points for an activity - FIXED to use full names
     */
    private function processActivityFocalPoints(RpActivity $activity, string $focalPointsText, array &$focalpointCache): array
    {
        $processed = 0;
        
        // Clean the text
        $focalPointsText = trim($focalPointsText, '" \t\n\r\0\x0B');
        $focalPointsText = str_replace('""', '"', $focalPointsText);
        
        if (empty($focalPointsText) || $focalPointsText === 'N/A') {
            return ['processed' => 0];
        }
        
        // Map single letters to full names (based on your Excel data)
        $letterToNameMap = [
            'ن' => 'نادين زيدان',
            'م' => 'محمد بلطجي',
            'أ' => 'أسامة أرناؤوط',
            'ح' => 'حاتم عاصي',
            'ب' => 'بلال الحريري',
            'ع' => 'محمد إسماعيل',
            'مص' => 'مصطفى حريري',
            'أح' => 'أحمد شامي',
            'مع' => 'معادن الشريف',
            'ر' => 'رشاد أبو زينب',
            'أم' => 'أمير حجازي',
            'ت' => 'تهاني سنتينا',
            'ج' => 'جهاد عاكوم',
            'أحك' => 'أحمد كساب',
            'ط' => 'طارق أبو زينب',
            'محم' => 'محمد الحريري',
            'مرو' => 'مروة أبو عاصي',
            'من' => 'منى بخاري',
            'روب' => 'روبينا أبو زينب',
            'ك' => 'كريم سلامة',
            'أمين' => 'أمين الحريري',
            'ل' => 'ليليا شاهين',
            'أد' => 'أدهم قبرصلي'
        ];
        
        // Normalize newlines
        $focalPointsText = str_replace(["\r\n", "\r"], "\n", $focalPointsText);
        
        // Split by various separators
        $focalPointNames = preg_split('/[\n,،\|\/\;]+/', $focalPointsText, -1, PREG_SPLIT_NO_EMPTY);
        
        foreach ($focalPointNames as $name) {
            $name = trim($name);
            $name = preg_replace('/\s+/', ' ', $name);
            
            // Skip empty names
            if (empty($name) || $name === '-') {
                continue;
            }
            
            // Convert single letters to full names
            $fullName = $name;
            if (strlen($name) <= 3 && isset($letterToNameMap[$name])) {
                $fullName = $letterToNameMap[$name];
            } elseif (strlen($name) == 1) {
                // If it's a single letter not in our map, use a generic name
                $fullName = 'فريق ' . $name;
            }
            
            $processed++;
            
            // Create code from full name
            $code = 'FP_' . preg_replace('/[^A-Za-z0-9]/', '_', $this->transliterateArabic($fullName));
            $code = substr($code, 0, 50);
            
            // Ensure unique code
            $originalCode = $code;
            $suffix = 1;
            while (RpFocalpoint::where('focalpoint_code', $code)->exists()) {
                $code = $originalCode . '_' . $suffix;
                $suffix++;
            }
            
            // Use cache to prevent duplicates
            if (!isset($focalpointCache[$code])) {
                try {
                    $focalpoint = RpFocalpoint::firstOrCreate(
                        ['focalpoint_code' => $code],
                        [
                            'name' => $fullName,
                            'position' => 'Focal Point',
                            'department' => 'Operations',
                            'is_active' => true
                        ]
                    );
                    
                    $focalpointCache[$code] = $focalpoint->rp_focalpoints_id;
                    
                } catch (\Exception $e) {
                    Log::error('Failed to create focal point ' . $code . ': ' . $e->getMessage());
                    continue;
                }
            }
            
            // Attach focal point to activity
            try {
                $activity->focalpoints()->syncWithoutDetaching([$focalpointCache[$code]]);
            } catch (\Exception $e) {
                Log::error('Failed to attach focal point to activity: ' . $e->getMessage());
            }
        }
        
        return ['processed' => $processed];
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
            ['AD.A', 'برامج مؤسسة الحريري التربوية والإنمائية', 'A1', 'تحسين جودة التعليم في ثانوية رفيق الحريري', 'iv', 'بناء القدرات الفنية للمعلمين لمواصلة تقديم التعليم المتميز بما يتماشى مع ممارسات التعليم الحديثة', '1', 'إجراء تدريب للهيئات العاملة في المدرسة حول استراتيجيات وسبل التدريس', 'تطوير المهارات التربوية وتعزيز جودة العملية التعليمية', 'هيئة القيادة البيداغولوجية في الثانوية'],
        ], null, 'A1');
        
        // Sheet 2: Activities
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Activities');
        $sheet2->fromArray([
            ['Action Reference', 'Activity Code', 'Activity', 'Activity Indicators (MEAL properties?)', 'Focal Point(s)', 'Status (classification)', 'References (dates/partners/events)', 'Media Link (Comms properties?)', 'Reference to HF Program Engine (Hamza?) - joint folder/sheet'],
            ['AD.A.1.iv.1', '1', 'تدريبات لهيئة القيادة البيداغولوجية في المدرسة بالتعاون مع خبراء وجهات تدريبية متخصصة', "1. عدد التدريبات\n2. عدد الأساتذة المشاركين", 'نادين زيدان', 'Ongoing', '', '', ''],
        ], null, 'A1');
        
        // Save to file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($templateDir . '/reporting_import_template.xlsx');
    }
}