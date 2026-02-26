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

foreach ($rows as $index => $row) {
    $rowNumber = $index + 2;

    // Counters
    $this->results['processed'] = ($this->results['processed'] ?? 0) + 1;
    $this->results['skipped']   = ($this->results['skipped'] ?? 0);
    $this->results['inserted']  = ($this->results['inserted'] ?? 0);
    $this->results['errors']    = ($this->results['errors'] ?? []);

    try {
        // Optional: UNIQUE ID for debugging
        $uniqueId = $this->getValue($row, ['unique id', 'unique_id'], 'NO_UNIQUE_ID');

        // Read values
        $componentCode = $this->getValue($row, ['component code', 'component_code']);
        $componentName = $this->getValue($row, ['component']);

        $programCode   = $this->getValue($row, ['program code', 'program_code']);
        $programName   = $this->getValue($row, ['program']);

        $unitCode      = $this->getValue($row, ['unit code', 'unit_code']);
        $unitName      = $this->getValue($row, ['unit']);

        $actionCode    = $this->getValue($row, ['action code', 'action_code']);
        $actionName    = $this->getValue($row, ['action']);

        $actionObjective = $this->getValue($row, ['action objective', 'action_objective']);
        $actionTargets   = $this->getValue($row, ['action targets & beneficiaries', 'action_targets_beneficiaries']);

        Log::debug("Row {$rowNumber} parsed values:", [
            'unique_id'       => $uniqueId,
            'component_code'  => $componentCode,
            'component'       => $componentName,
            'program_code'    => $programCode,
            'program'         => $programName,
            'unit_code'       => $unitCode,
            'unit'            => $unitName,
            'action_code'     => $actionCode,
            'action'          => $actionName,
        ]);

        // 1) Skip completely empty rows (disregard)
        $allEmpty = !$componentCode && !$componentName
            && !$programCode && !$programName
            && !$unitCode && !$unitName
            && !$actionCode && !$actionName
            && !$actionObjective && !$actionTargets;

        if ($allEmpty) {
            Log::info("Row {$rowNumber}: Empty row skipped", ['unique_id' => $uniqueId]);
            $this->results['skipped']++;
            continue;
        }

        // 2) Required fields (NO fallback)
        // Adjust these rules if your DB requires program/unit too.
        if (!$componentCode || !$componentName) {
            Log::warning("Row {$rowNumber}: Missing component code/name - skipped", [
                'unique_id' => $uniqueId,
                'component_code' => $componentCode,
                'component' => $componentName,
            ]);
            $this->results['skipped']++;
            continue;
        }

        if (!$actionCode) {
            Log::warning("Row {$rowNumber}: Missing action code - skipped", [
                'unique_id' => $uniqueId,
                'action_code' => $actionCode,
                'action' => $actionName,
            ]);
            $this->results['skipped']++;
            continue;
        }

        // ========== COMPONENT ==========
        $componentId = $this->handleComponent($componentCode, $componentName, $this->actionplanId);
        if (!$componentId) {
            Log::warning("Row {$rowNumber}: Component create/find failed - skipped", [
                'unique_id' => $uniqueId,
                'component_code' => $componentCode,
                'component' => $componentName,
            ]);
            $this->results['skipped']++;
            continue;
        }

        // ========== PROGRAM ==========
        $programId = null;
        $hasProgramData = ($programCode || $programName);

        if ($hasProgramData) {
            // If program is present, require both code+name (NO fallback)
            if (!$programCode || !$programName) {
                Log::warning("Row {$rowNumber}: Partial program data (need code+name) - skipped", [
                    'unique_id' => $uniqueId,
                    'program_code' => $programCode,
                    'program' => $programName,
                ]);
                $this->results['skipped']++;
                continue;
            }

            $programId = $this->handleProgram($componentId, $programCode, $programName, $rowNumber);
            if (!$programId) {
                Log::warning("Row {$rowNumber}: Program create/find failed - skipped", [
                    'unique_id' => $uniqueId,
                    'program_code' => $programCode,
                    'program' => $programName,
                ]);
                $this->results['skipped']++;
                continue;
            }
        }

        // ========== UNIT ==========
        $unitId = null;
        $hasUnitData = ($unitCode || $unitName);

        if ($hasUnitData) {
            // If unit is present, require both code+name (NO fallback)
            if (!$unitCode || !$unitName) {
                Log::warning("Row {$rowNumber}: Partial unit data (need code+name) - skipped", [
                    'unique_id' => $uniqueId,
                    'unit_code' => $unitCode,
                    'unit' => $unitName,
                ]);
                $this->results['skipped']++;
                continue;
            }

            // If your schema requires a program for units, enforce it:
            if (!$programId) {
                Log::warning("Row {$rowNumber}: Unit provided but no program present - skipped", [
                    'unique_id' => $uniqueId,
                    'unit_code' => $unitCode,
                    'unit' => $unitName,
                ]);
                $this->results['skipped']++;
                continue;
            }

            $unitId = $this->handleUnit($programId, $unitCode, $unitName);
            if (!$unitId) {
                Log::warning("Row {$rowNumber}: Unit create/find failed - skipped", [
                    'unique_id' => $uniqueId,
                    'unit_code' => $unitCode,
                    'unit' => $unitName,
                ]);
                $this->results['skipped']++;
                continue;
            }
        }

        // ========== ACTION ==========
        // If your actions MUST belong to a unit, enforce it here:
        // if (!$unitId) { ... skip ... }
        $this->handleAction(
            $unitId,
            $actionCode,
            $actionName,
            $actionObjective,
            $actionTargets
        );

        $this->results['inserted']++;
    } catch (\Exception $e) {
        $this->results['errors'][] = "Row {$rowNumber}: " . $e->getMessage();
        Log::error("HIERARCHY IMPORT ERROR Row {$rowNumber}", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}

    }

    /**
     * Handle component creation/update - NO CHANGES NEEDED
     */
private function handleComponent(string $code, string $name, ?string $action_plan_id = null)
{
    $cleanCode = $this->cleanCode($code);

    // Cache key must include action_plan_id (null-safe)
    $mapKey = $cleanCode . '|' . ($action_plan_id ?? 'NULL');

    if (isset($this->componentMap[$mapKey])) {
        $this->results['details']['components']['existing']++;
        return $this->componentMap[$mapKey];
    }

    // Reuse only if BOTH code and action_plan_id match (and not soft-deleted)
    $existing = DB::table('rp_components')
        ->where('code', $cleanCode)
        ->whereNull('deleted_at')
        ->when($action_plan_id === null, function ($q) {
            $q->whereNull('action_plan_id');
        }, function ($q) use ($action_plan_id) {
            $q->where('action_plan_id', $action_plan_id);
        })
        ->first(['rp_components_id']);

    if ($existing) {
        $this->componentMap[$mapKey] = $existing->rp_components_id;
        $this->results['details']['components']['existing']++;
        return $existing->rp_components_id;
    }

    // If same code exists under another action plan, we intentionally create a new row
    $componentId = Str::uuid();

    DB::table('rp_components')->insert([
        'rp_components_id' => $componentId,
        'external_id' => Str::uuid(),
        'name' => $this->truncateForDb($name),
        'code' => $cleanCode,
        'description' => null,
        'action_plan_id' => $action_plan_id,
        'created_at' => now(),
        'updated_at' => now(),
        'deleted_at' => null,
    ]);

    $this->componentMap[$mapKey] = $componentId;
    $this->results['details']['components']['new']++;

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
        }

        $existing = DB::table('rp_programs')
            ->where('code', $cleanCode)
            ->where('rp_components_id', $componentId)
            ->first(['rp_programs_id']);

        if ($existing) {
            $this->programMap[$programKey] = $existing->rp_programs_id;
            $this->results['details']['programs']['existing']++;
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
        
        return $unitId;
    }

   
    private function handleAction(string $unitId, string $code, ?string $name, ?string $objective, ?string $targets)
    {
        $actionKey = $unitId . '|' . $code;
        
        if (isset($this->actionMap[$actionKey])) {
            $this->results['details']['actions']['existing']++;
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
        return $originalCode; // Return original instead of null
    }
    
   
    if ($cleanCode !== $originalCode) {
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