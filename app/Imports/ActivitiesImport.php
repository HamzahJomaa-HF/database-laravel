<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ActivitiesImport implements ToCollection, WithHeadingRow
{
    private $results = [
        'processed' => 0,
        'created' => ['activities' => 0, 'indicators' => 0, 'focalpoints' => 0],
        'updated' => ['activities' => 0],
        'errors' => []
    ];

    private $hierarchyMap = [];
    
    
    

    public function collection(Collection $rows)
    {
        Log::info('=== STARTING ACTIVITIES IMPORT WITH MAPPING ===');
        Log::info('Total rows: ' . $rows->count());

        // Pre-load hierarchy mapping
        $this->loadHierarchyMap();

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            try {
                $rowArray = $row->toArray();

                // Get values from Excel
                $actionReference = $this->findColumnValue($rowArray, [
                    'Action Reference',
                    'action reference', 
                    'action_reference', 
                    'reference'
                ]);
                
                $activityCode = $this->findColumnValue($rowArray, [
                    'Activity Code',
                    'activity_code', 
                    'activity'
                ]);
                
                $activityName = $this->findColumnValue($rowArray, [
                    'Activity', 
                    'activity_name', 
                    'activity name'
                ]);
                
                $status = $this->findColumnValue($rowArray, [
                    'status (classification)',
                    'status', 
                    'status_classification'
                ], 'ongoing');
                
                $indicatorsText = $this->findColumnValue($rowArray, [
                    'activity indicators (meal properties?)',
                    'activity indicators', 
                    'activity_indicators', 
                    'indicators'
                ]);
                
                $focalPointsText = $this->findColumnValue($rowArray, [
                    'focal point(s)',
                    'focal points', 
                    'focal_points', 
                    'focal point'
                ]);

                // Skip if no activity code
                if (!$activityCode) {
                    Log::warning("Row {$rowNumber}: Skipping - no activity code");
                    continue;
                }

                $this->results['processed']++;

                // If no action reference, log error
                if (!$actionReference) {
                    $errorMsg = "Row {$rowNumber}: No action reference for activity '{$activityCode}'";
                    $this->results['errors'][] = $errorMsg;
                    Log::warning($errorMsg);
                    continue;
                }

                Log::info("Row {$rowNumber}: Processing reference '{$actionReference}'");

                // Parse the full hierarchy from reference
                $parsedHierarchy = $this->parseActionReference($actionReference, $rowNumber);
                
                if (!$parsedHierarchy) {
                    $errorMsg = "Row {$rowNumber}: Could not parse action reference '{$actionReference}'";
                    $this->results['errors'][] = $errorMsg;
                    Log::warning($errorMsg);
                    continue;
                }

                // APPLY PROGRAM MAPPING if needed
                $mappedHierarchy = $this->applyProgramMapping($parsedHierarchy, $rowNumber);
                
                // Find the action using MAPPED hierarchy
                $action = $this->findActionByHierarchy($mappedHierarchy, $rowNumber);
                
                if (!$action) {
                    // Try one more time with original hierarchy (in case mapping is wrong)
                    $action = $this->findActionByHierarchy($parsedHierarchy, $rowNumber);
                    
                    if (!$action) {
                        $errorMsg = "Row {$rowNumber}: Action not found for reference '{$actionReference}'";
                        $this->results['errors'][] = $errorMsg;
                        Log::warning($errorMsg);
                        
                        // Debug: List actual available programs
                        $this->debugActualPrograms($parsedHierarchy['component_code'], $rowNumber);
                        continue;
                    }
                }

                Log::info("Row {$rowNumber}: Found action '{$action->action_code}' for reference '{$actionReference}'");

                // Create unique activity code: action_code + activity_code
                $uniqueActivityCode = $this->generateUniqueActivityCode($action->action_code, $activityCode, $action->rp_actions_id);
                
                Log::debug("Row {$rowNumber}: Original activity code '{$activityCode}' → Unique code '{$uniqueActivityCode}'");

                // Create/Update Activity
                $activity = $this->createOrUpdateActivity($action, $uniqueActivityCode, $activityName, $status, $rowNumber);
                
                if (!$activity) {
                    continue;
                }
                
                $activityId = $activity->rp_activities_id;

                // Process indicators
                if ($indicatorsText) {
                    $indicatorsCount = $this->processIndicators($activityId, $indicatorsText);
                    Log::info("Row {$rowNumber}: Linked {$indicatorsCount} indicators");
                }

                // Process focal points
                if ($focalPointsText) {
                    $focalPointsCount = $this->processFocalPoints($activityId, $focalPointsText);
                    Log::info("Row {$rowNumber}: Linked {$focalPointsCount} focal points");
                }

            } catch (\Exception $e) {
                $errorMsg = "Row {$rowNumber}: " . $e->getMessage();
                $this->results['errors'][] = $errorMsg;
                Log::error($errorMsg, [
                    'error' => $e->getMessage(),
                    'data' => $row->toArray(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        Log::info('=== ACTIVITIES IMPORT COMPLETED ===', $this->results);
    }

    /**
     * Apply program code mapping
     */
    private function applyProgramMapping(array $hierarchy, int $rowNumber): array
    {
        $originalProgram = $hierarchy['program_code'];
        
        if (isset($this->programMapping[$originalProgram])) {
            $mappedProgram = $this->programMapping[$originalProgram];
            Log::info("Row {$rowNumber}: Mapped program '{$originalProgram}' → '{$mappedProgram}'");
            
            // Update the hierarchy with mapped program
            $hierarchy['program_code'] = $mappedProgram;
            $hierarchy['original_program'] = $originalProgram; 
        } else {
            Log::info("Row {$rowNumber}: No mapping found for program '{$originalProgram}'");
        }
        
        return $hierarchy;
    }

    /**
     * Debug: Show actual programs in database
     */
    private function debugActualPrograms(string $componentCode, int $rowNumber): void
    {
        Log::warning("Row {$rowNumber}: DEBUG - Checking actual programs in component '{$componentCode}'");
        
        $actualPrograms = DB::table('rp_programs AS p')
            ->join('rp_components AS c', 'p.rp_components_id', '=', 'c.rp_components_id')
            ->where('c.code', $componentCode)
            ->whereNull('p.deleted_at')
            ->select('p.code', 'p.name')
            ->orderBy('p.code')
            ->get();
        
        if ($actualPrograms->count() > 0) {
            Log::warning("Row {$rowNumber}: Actual programs in component '{$componentCode}':");
            foreach ($actualPrograms as $program) {
                Log::warning("  - {$program->code}: {$program->name}");
            }
            
            // Also check for any programs starting with similar letters
            $firstLetter = substr($componentCode, -1); // Get last letter (A, B, C, D, E)
            $similarPrograms = DB::table('rp_programs AS p')
                ->join('rp_components AS c', 'p.rp_components_id', '=', 'c.rp_components_id')
                ->where('c.code', 'like', 'AD%')
                ->where('p.code', 'like', $firstLetter . '%')
                ->whereNull('p.deleted_at')
                ->select('c.code as component', 'p.code as program', 'p.name')
                ->orderBy('c.code')
                ->orderBy('p.code')
                ->get();
            
            if ($similarPrograms->count() > 0) {
                Log::warning("Row {$rowNumber}: Programs starting with '{$firstLetter}' across all AD components:");
                foreach ($similarPrograms as $program) {
                    Log::warning("  - {$program->component}.{$program->program}: {$program->name}");
                }
            }
        } else {
            Log::warning("Row {$rowNumber}: No programs found in component '{$componentCode}'");
        }
    }

    /**
     * Parse action reference like "AD.A1.iv.1"
     */
    private function parseActionReference(string $reference, int $rowNumber): ?array
{
    $reference = trim($reference);
    Log::info("DEBUG Row {$rowNumber}: Parsing reference '{$reference}'");
    
    $reference = preg_replace('/\s+/', '', $reference);
    
   
    if (preg_match('/^AD\.(A\d+|B\d+)\.([ivx]+)\.(\d+)$/i', $reference, $matches)) {
        // Programs A1-A8, B1-B3 are in component AD.A or AD.B
        $program = $matches[1];
        $unit = $matches[2];
        $action = $matches[3];
        
        // Determine which component based on program
        $component = str_starts_with($program, 'A') ? 'AD.A' : 'AD.B';
        
        Log::info("DEBUG Row {$rowNumber}: Pattern 1 - C='{$component}', P='{$program}', U='{$unit}', A='{$action}'");
        return $this->buildHierarchyArray($component, $program, $unit, $action, $reference);
    }
    
   
    if (preg_match('/^AD\.(C0|D0|E0)\.([ivx]+)\.(\d+)$/i', $reference, $matches)) {
        $program = $matches[1]; 
        $unit = $matches[2];
        $action = $matches[3];
        
        
        $componentMap = [
            'C0' => 'AD.C',
            'D0' => 'AD.D', 
            'E0' => 'AD.E'
        ];
        
        $component = $componentMap[$program];
        
        Log::info("DEBUG Row {$rowNumber}: Pattern 2 - C='{$component}', P='{$program}', U='{$unit}', A='{$action}'");
        return $this->buildHierarchyArray($component, $program, $unit, $action, $reference);
    }
    
    Log::error("Row {$rowNumber}: Cannot parse reference '{$reference}'");
    return null;
}

   private function buildHierarchyArray(string $component, string $program, string $unit, string $action, string $originalReference): array
{
  
    $unitCode = strtoupper($unit); 
    
    return [
        'component_code' => $component,
        'program_code' => $program,
        'unit_code' => $unitCode, 
        'unit_original' => $unit, 
        'action_code' => $action,
        'original_reference' => $originalReference,
        'original_program' => $program
    ];
}

    /**
     * Load hierarchy map from database
     */
    private function loadHierarchyMap(): void
    {
        Log::info("Loading hierarchy map from database...");
        
        // Load ALL components starting with AD
        $hierarchy = DB::table('rp_actions AS a')
            ->join('rp_units AS u', 'a.rp_units_id', '=', 'u.rp_units_id')
            ->join('rp_programs AS p', 'u.rp_programs_id', '=', 'p.rp_programs_id')
            ->join('rp_components AS c', 'p.rp_components_id', '=', 'c.rp_components_id')
            ->where('c.code', 'like', 'AD%') // Get all AD components
            ->whereNull('a.deleted_at')
            ->whereNull('u.deleted_at')
            ->whereNull('p.deleted_at')
            ->whereNull('c.deleted_at')
            ->select(
                'a.rp_actions_id',
                'a.code AS action_code',
                'a.name AS action_name',
                'u.code AS unit_code',
                'u.name AS unit_name',
                'p.code AS program_code',
                'p.name AS program_name',
                'c.code AS component_code',
                'c.name AS component_name'
            )
            ->get();
        
        foreach ($hierarchy as $item) {
            // Create key for exact lookup: Component.Program.Unit.Action
            $key = "{$item->component_code}.{$item->program_code}.{$item->unit_code}.{$item->action_code}";
            $this->hierarchyMap[$key] = $item;
            
            // Also store with roman numerals
            $unitRoman = $this->numberToRoman($item->unit_code);
            if ($unitRoman) {
                $keyRoman = "{$item->component_code}.{$item->program_code}.{$unitRoman}.{$item->action_code}";
                $this->hierarchyMap[$keyRoman] = $item;
            }
        }
        
        Log::info("Loaded " . $hierarchy->count() . " actions from AD components into hierarchy map");
        
        // Log how many from each component
        $componentCounts = [];
        foreach ($hierarchy as $item) {
            $componentCounts[$item->component_code] = ($componentCounts[$item->component_code] ?? 0) + 1;
        }
        
        foreach ($componentCounts as $component => $count) {
            Log::info("  - Component {$component}: {$count} actions");
        }
    }

    /**
     * Convert number to roman numeral (for reverse lookup)
     */
    private function numberToRoman(string $number): ?string
    {
        $number = trim($number);
        if (!is_numeric($number)) return null;
        
        $intVal = intval($number);
        $romanNumerals = [
            1 => 'i', 2 => 'ii', 3 => 'iii', 4 => 'iv', 5 => 'v',
            6 => 'vi', 7 => 'vii', 8 => 'viii', 9 => 'ix', 10 => 'x'
        ];
        
        return $romanNumerals[$intVal] ?? null;
    }

    /**
     * Find action by full hierarchy
     */
    private function findActionByHierarchy(array $hierarchy, int $rowNumber)
    {
        $component = $hierarchy['component_code'];
        $program = $hierarchy['program_code'];
        $unit = $hierarchy['unit_code'];
        $action = $hierarchy['action_code'];
        $unitOriginal = $hierarchy['unit_original'];
        
        Log::info("DEBUG Row {$rowNumber}: Looking for C='{$component}', P='{$program}', U='{$unit}', A='{$action}'");
        
        // First, try exact match as before
        $exactMatch = $this->findExactAction($component, $program, $unit, $unitOriginal, $action, $rowNumber);
        if ($exactMatch) {
            return $exactMatch;
        }
        
        // If not found, try to find any action with the same unit and action codes
        // This handles cases where the program code might be wrong
        $similarActions = DB::table('rp_actions AS a')
            ->join('rp_units AS u', 'a.rp_units_id', '=', 'u.rp_units_id')
            ->join('rp_programs AS p', 'u.rp_programs_id', '=', 'p.rp_programs_id')
            ->join('rp_components AS c', 'p.rp_components_id', '=', 'c.rp_components_id')
            ->where('c.code', 'like', 'AD%')
            ->where('u.code', $unit)
            ->where('a.code', $action)
            ->whereNull('a.deleted_at')
            ->whereNull('u.deleted_at')
            ->whereNull('p.deleted_at')
            ->whereNull('c.deleted_at')
            ->select(
                'a.rp_actions_id',
                'a.code AS action_code',
                'a.name AS action_name',
                'u.code AS unit_code',
                'p.code AS program_code',
                'c.code AS component_code'
            )
            ->first();
        
        if ($similarActions) {
            Log::warning("Row {$rowNumber}: Found similar action (different program): C='{$similarActions->component_code}', P='{$similarActions->program_code}', U='{$similarActions->unit_code}', A='{$similarActions->action_code}'");
            return $similarActions;
        }
        
        Log::warning("DEBUG Row {$rowNumber}: NOT FOUND for any component variation");
        return null;
    }

    /**
     * Helper method to find exact action match
     */
   private function findExactAction($component, $program, $unit, $unitOriginal, $action, $rowNumber)
{
    $componentVariations = $this->getComponentVariations($component, $program);
    
    // Create ALL possible unit code variations
    $unitVariations = [
        $unit,                    // Uppercase from buildHierarchyArray
        strtoupper($unit),        // Force uppercase
        strtolower($unit),        // Lowercase
        $unitOriginal,            // Original from reference (lowercase)
        strtoupper($unitOriginal) // Original in uppercase
    ];
    
    // Also try Roman numeral to number conversion (just in case)
    $romanToNumber = [
        'i' => '1', 'ii' => '2', 'iii' => '3', 'iv' => '4', 'v' => '5',
        'vi' => '6', 'vii' => '7', 'viii' => '8', 'ix' => '9', 'x' => '10',
        'I' => '1', 'II' => '2', 'III' => '3', 'IV' => '4', 'V' => '5',
        'VI' => '6', 'VII' => '7', 'VIII' => '8', 'IX' => '9', 'X' => '10'
    ];
    
    $unitLower = strtolower($unit);
    if (isset($romanToNumber[$unitLower])) {
        $unitVariations[] = $romanToNumber[$unitLower]; // Number version
    }
    
    // Remove duplicates
    $unitVariations = array_unique($unitVariations);
    
    Log::info("DEBUG Row {$rowNumber}: Trying unit variations: " . implode(', ', $unitVariations));
    
    foreach ($componentVariations as $compVar) {
        foreach ($unitVariations as $unitVar) {
            // Try exact match in cache
            $key = "{$compVar}.{$program}.{$unitVar}.{$action}";
            if (isset($this->hierarchyMap[$key])) {
                Log::info("DEBUG Row {$rowNumber}: FOUND in cache with key '{$key}'");
                return $this->hierarchyMap[$key];
            }
            
            // Try database lookup
            $foundAction = DB::table('rp_actions AS a')
                ->join('rp_units AS u', 'a.rp_units_id', '=', 'u.rp_units_id')
                ->join('rp_programs AS p', 'u.rp_programs_id', '=', 'p.rp_programs_id')
                ->join('rp_components AS c', 'p.rp_components_id', '=', 'c.rp_components_id')
                ->where('c.code', $compVar)
                ->where('p.code', $program)
                ->where('u.code', $unitVar)
                ->where('a.code', $action)
                ->whereNull('a.deleted_at')
                ->whereNull('u.deleted_at')
                ->whereNull('p.deleted_at')
                ->whereNull('c.deleted_at')
                ->select(
                    'a.rp_actions_id',
                    'a.code AS action_code',
                    'a.name AS action_name',
                    'u.code AS unit_code',
                    'p.code AS program_code',
                    'c.code AS component_code'
                )
                ->first();
            
            if ($foundAction) {
                Log::info("DEBUG Row {$rowNumber}: FOUND in database with C='{$foundAction->component_code}', P='{$foundAction->program_code}', U='{$foundAction->unit_code}', A='{$foundAction->action_code}'");
                
                // Cache it
                $cacheKey = "{$foundAction->component_code}.{$foundAction->program_code}.{$foundAction->unit_code}.{$foundAction->action_code}";
                $this->hierarchyMap[$cacheKey] = $foundAction;
                
                return $foundAction;
            }
        }
    }
    
    return null;
}

    /**
     * Get component variations based on program
     */
    private function getComponentVariations(string $component, string $program): array
    {
        if ($component === 'AD') {
            $programPrefix = substr($program, 0, 1); // Get first letter of program
            
            $componentMap = [
                'A' => ['AD.A'],
                'B' => ['AD.B'],
                'C' => ['AD.C', 'AD.A'], 
                'D' => ['AD.D', 'AD.A'],
                'E' => ['AD.E', 'AD.A'], 
                'default' => ['AD.A', 'AD.B']
            ];
            
            $variations = $componentMap[$programPrefix] ?? $componentMap['default'];
            Log::info("Mapped 'AD' with program '{$program}' to component variations: " . implode(', ', $variations));
            
            // Also try case variations
            $caseVariations = [];
            foreach ($variations as $comp) {
                $caseVariations[] = strtoupper($comp);
                $caseVariations[] = strtolower($comp);
                $caseVariations[] = ucfirst(strtolower($comp));
            }
            
            return array_unique(array_merge($variations, $caseVariations));
        }
        
        return [$component];
    }

    /**
     * Generate unique activity code
     */
    private function generateUniqueActivityCode(string $actionCode, string $activityCode, string $actionId): string
    {
        $uniqueCode = "{$actionCode}-{$activityCode}";
        
        $existing = DB::table('rp_activities')
            ->where('rp_actions_id', $actionId)
            ->where('code', $uniqueCode)
            ->exists();
        
        if (!$existing) {
            return $uniqueCode;
        }
        
        $counter = 1;
        while (true) {
            $proposedCode = "{$actionCode}-{$activityCode}-{$counter}";
            $exists = DB::table('rp_activities')
                ->where('rp_actions_id', $actionId)
                ->where('code', $proposedCode)
                ->exists();
            
            if (!$exists) {
                Log::warning("Generated unique code with suffix: {$proposedCode} (original: {$activityCode})");
                return $proposedCode;
            }
            
            $counter++;
            if ($counter > 100) {
                $proposedCode = "{$actionCode}-{$activityCode}-" . time();
                Log::warning("Generated unique code with timestamp: {$proposedCode}");
                return $proposedCode;
            }
        }
    }

    /**
     * Create or update activity
     */
    private function createOrUpdateActivity($action, $activityCode, $activityName, $status, $rowNumber)
    {
        try {
            // Find existing activity with the unique code
            $existingActivity = DB::table('rp_activities')
                ->where('rp_actions_id', $action->rp_actions_id)
                ->where('code', $activityCode)
                ->first(['rp_activities_id', 'code', 'name']);
            
            $activityId = null;
            $isNew = false;

            if ($existingActivity) {
                // Update existing activity
                DB::table('rp_activities')
                    ->where('rp_activities_id', $existingActivity->rp_activities_id)
                    ->update([
                        'name' => $this->truncateForDb($activityName ?: "Activity {$activityCode}"),
                        'status' => $this->mapStatus($status),
                        'updated_at' => now(),
                    ]);
                $this->results['updated']['activities']++;
                $activityId = $existingActivity->rp_activities_id;
                Log::info("Updated activity: {$activityId} for action {$action->action_code}");
            } else {
                // Create new activity
                $activityId = (string) Str::uuid();
                
                DB::table('rp_activities')->insert([
                    'rp_activities_id' => $activityId,
                    'rp_actions_id' => $action->rp_actions_id,
                    'external_id' => (string) Str::uuid(),
                    'external_type' => null,
                    'name' => $this->truncateForDb($activityName ?: "Activity {$activityCode}"),
                    'code' => $activityCode,
                    'description' => $this->truncateForDb($activityName, 'text'),
                    'activity_type' => null,
                    'status' => $this->mapStatus($status),
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => null,
                ]);
                $this->results['created']['activities']++;
                $isNew = true;
                Log::info("Created new activity: {$activityId} for action {$action->action_code}");
            }
            
            return (object) [
                'rp_activities_id' => $activityId,
                'is_new' => $isNew
            ];
            
        } catch (\Exception $e) {
            $errorMsg = "Row {$rowNumber} Activity creation: " . $e->getMessage();
            $this->results['errors'][] = $errorMsg;
            Log::error($errorMsg, ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Process indicators
     */
    private function processIndicators(string $activityId, string $indicatorsText): int
    {
        $lines = preg_split('/[\n,;]/', $indicatorsText);
        $count = 0;
        
        foreach ($lines as $line) {
            $line = $this->nullify($line);
            if (!$line) continue;

            $line = trim($line);
            if (is_numeric($line)) continue;

            // Find or create indicator
            $indicator = DB::table('rp_indicators')
                ->where('name', $line)
                ->first(['rp_indicators_id']);

            if (!$indicator) {
                $indicatorId = (string) Str::uuid();
                
                DB::table('rp_indicators')->insert([
                    'rp_indicators_id' => $indicatorId,
                    'external_id' => (string) Str::uuid(),
                    'name' => $this->truncateForDb($line),
                    'description' => $this->truncateForDb($line, 'text'),
                    'indicator_type' => null,
                    'target_value' => null,
                    'data_source' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => null,
                ]);
                $this->results['created']['indicators']++;
            } else {
                $indicatorId = $indicator->rp_indicators_id;
            }

            // Check and create link
            $existingLink = DB::table('rp_activity_indicators')
                ->where('rp_activities_id', $activityId)
                ->where('rp_indicators_id', $indicatorId)
                ->first();

            if (!$existingLink) {
                $linkId = (string) Str::uuid();
                
                DB::table('rp_activity_indicators')->insert([
                    'rp_activity_indicators_id' => $linkId,
                    'rp_activities_id' => $activityId,
                    'rp_indicators_id' => $indicatorId,
                    'notes' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * Process focal points
     */
    private function processFocalPoints(string $activityId, string $focalPointsText): int
    {
        $names = preg_split('/[\n,;]/', $focalPointsText);
        $count = 0;
        
        foreach ($names as $name) {
            $name = $this->nullify($name);
            if (!$name) continue;

            $name = trim($name);

            // Find or create focal point
            $focalpoint = DB::table('rp_focalpoints')
                ->where('name', $name)
                ->first(['rp_focalpoints_id']);

            if (!$focalpoint) {
                $focalpointId = (string) Str::uuid();
                
                DB::table('rp_focalpoints')->insert([
                    'rp_focalpoints_id' => $focalpointId,
                    'external_id' => (string) Str::uuid(),
                    'name' => $this->truncateForDb($name),
                    'type' => 'rp_activity',
                    'employee_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => null,
                ]);
                $this->results['created']['focalpoints']++;
            } else {
                $focalpointId = $focalpoint->rp_focalpoints_id;
            }

            // Check and create link
            $existingLink = DB::table('rp_activity_focalpoints')
                ->where('rp_activities_id', $activityId)
                ->where('rp_focalpoints_id', $focalpointId)
                ->first();

            if (!$existingLink) {
                $linkId = (string) Str::uuid();
                
                DB::table('rp_activity_focalpoints')->insert([
                    'rp_activity_focalpoints_id' => $linkId,
                    'rp_activities_id' => $activityId,
                    'rp_focalpoints_id' => $focalpointId,
                    'role' => 'Focal Point',
                    'end_date' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * Helper methods
     */
    private function findColumnValue(array $rowArray, array $possibleNames, $default = null)
    {
        foreach ($possibleNames as $name) {
            if (isset($rowArray[$name])) {
                return $this->nullify($rowArray[$name]);
            }
            
            $lowerName = strtolower($name);
            foreach ($rowArray as $key => $value) {
                if (strtolower($key) === $lowerName) {
                    return $this->nullify($value);
                }
            }
            
            $cleanName = preg_replace('/[^a-z0-9]/', '', strtolower($name));
            foreach ($rowArray as $key => $value) {
                $cleanKey = preg_replace('/[^a-z0-9]/', '', strtolower($key));
                if ($cleanKey === $cleanName) {
                    return $this->nullify($value);
                }
            }
        }
        
        return $default;
    }

    private function nullify($value)
    {
        if ($value === null) return null;
        $trimmed = trim((string)$value);
        return ($trimmed === '') ? null : $trimmed;
    }

    private function truncateForDb(?string $value, string $type = 'varchar'): ?string
    {
        if (!$value) return $value;
        $value = trim($value);
        
        if ($type === 'text') return $value;
        if (mb_strlen($value) <= 255) return $value;
        
        $truncated = mb_substr($value, 0, 252) . '...';
        Log::warning("Truncated string from " . mb_strlen($value) . " to 255 characters");
        return $truncated;
    }

    private function mapStatus(?string $status): string
    {
        if (!$status) return 'ongoing';
        
        $status = strtolower(trim($status));
        
        $ongoingKeywords = ['ongoing', 'جاري', 'in progress', 'in-progress', 'active'];
        $completedKeywords = ['completed', 'done', 'مكتمل', 'منتهي', 'finished'];
        $plannedKeywords = ['planned', 'pending', 'مخطط', 'مستقبلي'];
        
        if (in_array($status, $ongoingKeywords)) return 'ongoing';
        if (in_array($status, $completedKeywords)) return 'completed';
        if (in_array($status, $plannedKeywords)) return 'planned';
        
        return 'ongoing';
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function getResults()
    {
        return [
            'processed' => $this->results['processed'],
            'created' => $this->results['created'],
            'updated' => $this->results['updated'],
            'errors' => $this->results['errors']
        ];
    }
}