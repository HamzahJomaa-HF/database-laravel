<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class HierarchyImport implements ToCollection, WithHeadingRow
{
    private $componentMap = [];
    private $programMap = [];
    private $unitMap = [];
    private $actionMap = [];
    private string $actionplanId;

    private $results = [
        'processed' => 0,
        'skipped' => 0,
        'details' => [
            'components' => ['new' => 0, 'existing' => 0],
            'programs'   => ['new' => 0, 'existing' => 0],
            'units'      => ['new' => 0, 'existing' => 0],
            'actions'    => ['new' => 0, 'existing' => 0],
        ],
        'errors' => []
    ];

    public function __construct(string $actionplanId)
    {
        $this->actionplanId = $actionplanId;
    }

    public function collection(Collection $rows)
    {
        Log::info('=== STARTING HIERARCHY IMPORT ===');

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $this->results['processed']++;

            try {
                // Get the UNIQUE ID for debugging
                $uniqueId = $this->getValue($row, [
                    'unique id',
                    'unique_id',
                    'unique id'
                ], 'NO_UNIQUE_ID');
                
                Log::debug("Processing row {$rowNumber} - Unique ID: {$uniqueId}");

            
                $componentCode = $this->getValue($row, [
                    'component code',
                    'component_code'
                ]);
                
                $componentName = $this->getValue($row, [
                    'component'
                ]);
                
                $programCode = $this->getValue($row, [
                    'program code', 
                    'program_code'
                ]);
                
                $programName = $this->getValue($row, [
                    'program'
                ]);
                
                $unitCode = $this->getValue($row, [
                    'unit code', 
                    'unit_code'
                ]);
                
                $unitName = $this->getValue($row, [
                    'unit'
                ]);
                
                $actionCode = $this->getValue($row, [
                    'action code', 
                    'action_code'
                ]);
                
                $actionName = $this->getValue($row, [
                    'action'
                ]);
                
                $actionObjective = $this->getValue($row, [
                    'action objective',
                    'action_objective'
                ]);
                
                $actionTargets = $this->getValue($row, [
                    'action targets & beneficiaries',
                    'action_targets_beneficiaries'
                ]);

                Log::debug("Row {$rowNumber} parsed values:", [
                    'component_code' => $componentCode,
                    'component' => $componentName,
                    'program_code' => $programCode,
                    'program' => $programName,
                    'unit_code' => $unitCode,
                    'unit' => $unitName,
                    'action_code' => $actionCode,
                    'action' => $actionName
                ]);

                
                if (!$componentCode || !$componentName) {
                    Log::warning("Row {$rowNumber}: Missing component data - inserting with defaults");
                    // Use defaults instead of skipping
                    $componentCode = $componentCode ?: "UNKNOWN_{$rowNumber}";
                    $componentName = $componentName ?: "Unknown Component";
                }

                // Action code is required - create default if missing
                if (!$actionCode) {
                    Log::warning("Row {$rowNumber}: No action code - generating one");
                    $actionCode = "ACTION_{$rowNumber}";
                }

                // ========== COMPONENT ==========
                $componentId = $this->handleComponent($componentCode, $componentName, $this->actionplanId);
                if (!$componentId) {
                    Log::error("Row {$rowNumber}: Failed to create component - using fallback");
                    // Create a fallback component
                    // $componentId = $this->createFallbackComponent($rowNumber);
                }

                // ========== PROGRAM ==========
                $programId = null;
                if ($programCode || $programName) {
                    // Use program code if exists, otherwise generate from program name or row number
                    $programCodeToUse = $programCode ?: ($programName ? substr(md5($programName), 0, 8) : "PROG_{$rowNumber}");
                    $programNameToUse = $programName ?: "Program {$programCodeToUse}";
                    
                    $programId = $this->handleProgram($componentId, $programCodeToUse, $programNameToUse, $rowNumber);
                    
                    if (!$programId) {
                        Log::warning("Row {$rowNumber}: Program creation failed - creating fallback program");
                        $programId = $this->createFallbackProgram($componentId, $rowNumber);
                    }
                } else {
                    // No program data - create a default program
                    Log::info("Row {$rowNumber}: No program data - creating default program");
                    $programId = $this->createFallbackProgram($componentId, $rowNumber);
                }

                // ========== UNIT ==========
                $unitId = null;
                if ($unitCode || $unitName) {
                    // Use unit code if exists, otherwise generate
                    $unitCodeToUse = $unitCode ?: ($unitName ? substr(md5($unitName), 0, 8) : "UNIT_{$rowNumber}");
                    $unitNameToUse = $unitName ?: "Unit {$unitCodeToUse}";
                    
                    $unitId = $this->handleUnit($programId, $unitCodeToUse, $unitNameToUse);
                    
                    if (!$unitId) {
                        Log::warning("Row {$rowNumber}: Unit creation failed - creating fallback unit");
                        $unitId = $this->createFallbackUnit($programId, $rowNumber);
                    }
                } else {
                    // No unit data - create a default unit
                    Log::info("Row {$rowNumber}: No unit data - creating default unit");
                    $unitId = $this->createFallbackUnit($programId, $rowNumber);
                }

                // ========== ACTION ==========
                if ($unitId) {
                    // Use action name if exists, otherwise use action code
                    $actionNameToUse = $actionName ?: "Action {$actionCode}";
                    
                    $this->handleAction($unitId, $actionCode, $actionNameToUse, $actionObjective, $actionTargets);
                } else {
                    // This should never happen with fallbacks, but just in case
                    Log::error("Row {$rowNumber}: No unit ID for action '{$actionCode}' - creating standalone action");
                    $this->createStandaloneAction($componentId, $actionCode, $actionName, $actionObjective, $actionTargets, $rowNumber);
                }

            } catch (\Exception $e) {
                $this->results['errors'][] = "Row {$rowNumber}: " . $e->getMessage();
                Log::error("HIERARCHY IMPORT ERROR Row {$rowNumber}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        Log::info('=== HIERARCHY IMPORT COMPLETED ===', $this->results);
    }

    /**
     * Handle component creation/update - NO CHANGES NEEDED
     */
    private function handleComponent(string $code, string $name, ?string $action_plan_id = null)
    {
        if (isset($this->componentMap[$code])) {
            $this->results['details']['components']['existing']++;
            return $this->componentMap[$code];
        }

        $existing = DB::table('rp_components')
            ->where('code', $code)
            ->first(['rp_components_id']);

        if ($existing) {
            $this->componentMap[$code] = $existing->rp_components_id;
            $this->results['details']['components']['existing']++;
            Log::info("Found existing component: {$code}");
            return $existing->rp_components_id;
        }

        $componentId = Str::uuid();
        DB::table('rp_components')->insert([
            'rp_components_id' => $componentId,
            'external_id' => Str::uuid(),
            'name' => $this->truncateForDb($name),
            'code' => $this->cleanCode($code),
            'description' => null,
            'action_plan_id' => $action_plan_id,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null
        ]);
        
        $this->componentMap[$code] = $componentId;
        $this->results['details']['components']['new']++;
        Log::info("Created new component: {$code} - {$name}");
        
        return $componentId;
    }

    /**
     * Handle program creation/update - UPDATED to never return null
     */
    private function handleProgram(string $componentId, string $code, ?string $name, int $rowNumber)
    {
        $programKey = $componentId . '|' . $code;
        
        if (isset($this->programMap[$programKey])) {
            $this->results['details']['programs']['existing']++;
            return $this->programMap[$programKey];
        }

        // Clean the code - don't reject anything
        $cleanCode = $this->cleanProgramCode($code, $rowNumber);
        if (!$cleanCode) {
            // If cleaning fails, use original code or generate one
            $cleanCode = $code ?: "PROG_{$rowNumber}";
            Log::info("Row {$rowNumber}: Using program code: {$cleanCode}");
        }

        $existing = DB::table('rp_programs')
            ->where('code', $cleanCode)
            ->where('rp_components_id', $componentId)
            ->first(['rp_programs_id']);

        if ($existing) {
            $this->programMap[$programKey] = $existing->rp_programs_id;
            $this->results['details']['programs']['existing']++;
            Log::info("Found existing program: {$cleanCode}");
            return $existing->rp_programs_id;
        }

        $programId = Str::uuid();
        DB::table('rp_programs')->insert([
            'rp_programs_id' => $programId,
            'rp_components_id' => $componentId,
            'external_id' => Str::uuid(),
            'name' => $this->truncateForDb($name ?: $cleanCode),
            'code' => $cleanCode,
            'description' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null
        ]);
        
        $this->programMap[$programKey] = $programId;
        $this->results['details']['programs']['new']++;
        Log::info("Row {$rowNumber}: Created new program: {$cleanCode} - " . ($name ?: 'No name'));
        
        return $programId;
    }

    /**
     * Handle unit creation/update - UPDATED to never return null
     */
    private function handleUnit(string $programId, string $code, ?string $name)
    {
        $unitKey = $programId . '|' . $code;
        
        if (isset($this->unitMap[$unitKey])) {
            $this->results['details']['units']['existing']++;
            return $this->unitMap[$unitKey];
        }

        $cleanCode = $this->cleanCode($code);
        if (!$cleanCode) {
            // If cleaning fails, use original code
            $cleanCode = $code;
            Log::warning("Unit code '{$code}' cleaned to empty, using original");
        }

        $existing = DB::table('rp_units')
            ->where('code', $cleanCode)
            ->where('rp_programs_id', $programId)
            ->first(['rp_units_id']);

        if ($existing) {
            $this->unitMap[$unitKey] = $existing->rp_units_id;
            $this->results['details']['units']['existing']++;
            Log::info("Found existing unit: {$cleanCode}");
            return $existing->rp_units_id;
        }

        $unitId = Str::uuid();
        DB::table('rp_units')->insert([
            'rp_units_id' => $unitId,
            'rp_programs_id' => $programId,
            'external_id' => Str::uuid(),
            'name' => $this->truncateForDb($name ?: $cleanCode),
            'code' => $cleanCode,
            'description' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null
        ]);
        
        $this->unitMap[$unitKey] = $unitId;
        $this->results['details']['units']['new']++;
        Log::info("Created new unit: {$cleanCode} - " . ($name ?: 'No name'));
        
        return $unitId;
    }

   
    private function handleAction(string $unitId, string $code, ?string $name, ?string $objective, ?string $targets)
    {
        $actionKey = $unitId . '|' . $code;
        
        if (isset($this->actionMap[$actionKey])) {
            $this->results['details']['actions']['existing']++;
            Log::info("Action already exists: {$code}");
            return;
        }

        $cleanCode = $this->cleanCode($code);
        if (!$cleanCode) {
            // If cleaning fails, use original code
            $cleanCode = $code;
            Log::warning("Action code '{$code}' cleaned to empty, using original");
        }

        $existing = DB::table('rp_actions')
            ->where('code', $cleanCode)
            ->where('rp_units_id', $unitId)
            ->first(['rp_actions_id']);

        if ($existing) {
            $this->actionMap[$actionKey] = true;
            $this->results['details']['actions']['existing']++;
            Log::info("Found existing action: {$cleanCode}");
            return;
        }

        DB::table('rp_actions')->insert([
            'rp_actions_id' => Str::uuid(),
            'rp_units_id' => $unitId,
            'external_id' => Str::uuid(),
            'name' => $this->truncateForDb($name ?: $cleanCode),
            'code' => $cleanCode,
            'objectives' => $this->truncateForDb($objective, 'text'),
            'targets_beneficiaries' => $this->truncateForDb($targets, 'text'),
            'planned_start_date' => null,
            'planned_end_date' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null
        ]);
        
        $this->actionMap[$actionKey] = true;
        $this->results['details']['actions']['new']++;
        Log::info("Created new action: {$cleanCode} - " . ($name ?: 'No name'));
    }

    
    private function createFallbackComponent(int $rowNumber)
    {
        $code = "FALLBACK_COMP_{$rowNumber}";
        $name = "Fallback Component Row {$rowNumber}";
        
        $componentId = Str::uuid();
        DB::table('rp_components')->insert([
            'rp_components_id' => $componentId,
            'external_id' => Str::uuid(),
            'name' => $name,
            'code' => $code,
            'description' => 'Auto-created fallback component',
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null
        ]);
        
        $this->componentMap[$code] = $componentId;
        $this->results['details']['components']['new']++;
        Log::warning("Created fallback component for row {$rowNumber}: {$code}");
        
        return $componentId;
    }

   
    private function createFallbackProgram(string $componentId, int $rowNumber)
    {
        $code = "FALLBACK_PROG_{$rowNumber}";
        $name = "Fallback Program Row {$rowNumber}";
        $programKey = $componentId . '|' . $code;
        
        $programId = Str::uuid();
        DB::table('rp_programs')->insert([
            'rp_programs_id' => $programId,
            'rp_components_id' => $componentId,
            'external_id' => Str::uuid(),
            'name' => $name,
            'code' => $code,
            'description' => 'Auto-created fallback program',
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null
        ]);
        
        $this->programMap[$programKey] = $programId;
        $this->results['details']['programs']['new']++;
        Log::warning("Created fallback program for row {$rowNumber}: {$code}");
        
        return $programId;
    }

   
    private function createFallbackUnit(string $programId, int $rowNumber)
    {
        $code = "FALLBACK_UNIT_{$rowNumber}";
        $name = "Fallback Unit Row {$rowNumber}";
        $unitKey = $programId . '|' . $code;
        
        $unitId = Str::uuid();
        DB::table('rp_units')->insert([
            'rp_units_id' => $unitId,
            'rp_programs_id' => $programId,
            'external_id' => Str::uuid(),
            'name' => $name,
            'code' => $code,
            'description' => 'Auto-created fallback unit',
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null
        ]);
        
        $this->unitMap[$unitKey] = $unitId;
        $this->results['details']['units']['new']++;
        Log::warning("Created fallback unit for row {$rowNumber}: {$code}");
        
        return $unitId;
    }

 
    private function createStandaloneAction(string $componentId, string $code, ?string $name, ?string $objective, ?string $targets, int $rowNumber)
    {
        // First create a fallback program and unit
        $programId = $this->createFallbackProgram($componentId, $rowNumber);
        $unitId = $this->createFallbackUnit($programId, $rowNumber);
        
        // Now create the action
        $this->handleAction($unitId, $code, $name, $objective, $targets);
        Log::warning("Created standalone action with fallback hierarchy for row {$rowNumber}");
    }

    /**
     * Clean program code specifically - UPDATED to never reject
     */
    private function cleanProgramCode($code, int $rowNumber)
{
    if (!$code) return null;
    
    $originalCode = $code;
    $code = trim($code);
    
    // Check if it's an Excel formula and try to extract the intended value
    if (strpos($code, '_xlfn.CONCATRIGHTB') === 0) {
       
       
        if (preg_match('/CONCATRIGHTB(\d+)/', $code, $matches)) {
            $rowRef = $matches[1];
            
          
            
            $cleanCode = "FORMULA_{$rowRef}";
            Log::warning("Row {$rowNumber}: Formula detected '{$originalCode}', using '{$cleanCode}'");
            return $cleanCode;
        }
    }
  
    $cleanCode = $this->cleanCode($code);
    
    if (!$cleanCode) {
        Log::info("Row {$rowNumber}: Program code '{$originalCode}' resulted in empty after cleaning");
        return $originalCode; // Return original instead of null
    }
    
   
    if ($cleanCode !== $originalCode) {
        Log::info("Row {$rowNumber}: Program code cleaned: '{$originalCode}' → '{$cleanCode}'");
    }
    
    return $cleanCode;
}

  
    private function getValue(Collection $row, array $possibleNames, $default = null)
    {
        foreach ($possibleNames as $name) {
            // Try exact match
            if ($row->has($name)) {
                $value = $row->get($name);
                return $this->nullify($value);
            }
            
           
            $lowerName = strtolower($name);
            foreach ($row as $key => $value) {
                if (strtolower($key) === $lowerName) {
                    return $this->nullify($value);
                }
            }
        }
        
        return $default;
    }

  
    private function nullify($value)
    {
        if ($value === null) {
            return null;
        }
        
        $trimmed = trim((string)$value);
        return ($trimmed === '') ? null : $trimmed;
    }

   
    private function cleanCode($code)
    {
        if (!$code) return null;
        
        $clean = trim($code);
        
        
        if (preg_match('/^[ivxlcdm]+$/i', $clean)) {
            return strtoupper($clean);
        }
        
       
        $clean = preg_replace('/[^a-zA-Z0-9\-_\.]/', '', $clean);
        $clean = trim($clean);
        
        return $clean ?: null;
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

    public function headingRow(): int
    {
        return 1;
    }

    public function getResults()
    {
        return $this->results;
    }
}