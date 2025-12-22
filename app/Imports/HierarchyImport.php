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

    public function collection(Collection $rows)
    {
        Log::info('=== STARTING COMPLETE HIERARCHY IMPORT ===');

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $this->results['processed']++;

            try {
                // Convert row to array for easier handling
                $rowArray = $row->toArray();
                
                // Debug: Log first few rows
                if ($index < 3) {
                    Log::debug("Row {$rowNumber} raw data:", array_keys($rowArray));
                }

                // Use flexible column name resolution
                $componentCode = $this->findColumnValue($rowArray, [
                    'component code',
                    'component_code',
                    'component'
                ]);
                
                $componentName = $this->findColumnValue($rowArray, [
                    'component',
                    'component_name',
                    'component name'
                ]);
                
                $programCode = $this->findColumnValue($rowArray, [
                    'program code',
                    'program_code',
                    'program'
                ]);
                
                $programName = $this->findColumnValue($rowArray, [
                    'program',
                    'program_name',
                    'program name'
                ]);
                
                $unitCode = $this->findColumnValue($rowArray, [
                    'unit code',
                    'unit_code',
                    'unit'
                ]);
                
                $unitName = $this->findColumnValue($rowArray, [
                    'unit',
                    'unit_name',
                    'unit name'
                ]);
                
                $actionCode = $this->findColumnValue($rowArray, [
                    'action code',
                    'action_code',
                    'action'
                ]);
                
                $actionName = $this->findColumnValue($rowArray, [
                    'action',
                    'action_name',
                    'action name'
                ]);
                
                $actionObjective = $this->findColumnValue($rowArray, [
                    'action objective',
                    'action_objective',
                    'objective'
                ]);
                
                $actionTargets = $this->findColumnValue($rowArray, [
                    'action targets & beneficiaries',
                    'targets_beneficiaries',
                    'targets',
                    'beneficiaries'
                ]);

                // Log parsed values for debugging
                Log::debug("Row {$rowNumber} parsed:", [
                    'component' => $componentCode,
                    'program' => $programCode,
                    'unit' => $unitCode,
                    'action' => $actionCode
                ]);

                // Skip if no action code (this is the leaf node)
                if (!$actionCode) {
                    Log::warning("Row {$rowNumber}: Skipping - no action code found");
                    $this->results['skipped']++;
                    continue;
                }

                // ========== COMPONENT ==========
                $componentId = null;
                if ($componentCode) {
                    if (isset($this->componentMap[$componentCode])) {
                        $componentId = $this->componentMap[$componentCode];
                        $this->results['details']['components']['existing']++;
                    } else {
                        $existing = DB::table('rp_components')
                            ->where('code', $componentCode)
                            ->first(['rp_components_id']);

                        if ($existing) {
                            $componentId = $existing->rp_components_id;
                            $this->componentMap[$componentCode] = $componentId;
                            $this->results['details']['components']['existing']++;
                            Log::info("Found existing component: {$componentCode}");
                        } else {
                            $componentId = Str::uuid();
                            DB::table('rp_components')->insert([
                                'rp_components_id' => $componentId,
                                'external_id' => Str::uuid(),
                                'name' => $componentName ?: $componentCode,
                                'code' => $componentCode,
                                'description' => null,
                                'created_at' => now(),
                                'updated_at' => now(),
                                'deleted_at' => null
                            ]);
                            $this->componentMap[$componentCode] = $componentId;
                            $this->results['details']['components']['new']++;
                            Log::info("Created new component: {$componentCode}");
                        }
                    }
                }

                // ========== PROGRAM ==========
                $programId = null;
                if ($componentId && $programCode) {
                    $programKey = $componentId . '|' . $programCode;

                    if (isset($this->programMap[$programKey])) {
                        $programId = $this->programMap[$programKey];
                        $this->results['details']['programs']['existing']++;
                    } else {
                        $existing = DB::table('rp_programs')
                            ->where('code', $programCode)
                            ->where('rp_components_id', $componentId)
                            ->first(['rp_programs_id']);

                        if ($existing) {
                            $programId = $existing->rp_programs_id;
                            $this->programMap[$programKey] = $programId;
                            $this->results['details']['programs']['existing']++;
                            Log::info("Found existing program: {$programCode} under component {$componentCode}");
                        } else {
                            $programId = Str::uuid();
                            DB::table('rp_programs')->insert([
                                'rp_programs_id' => $programId,
                               'rp_components_id' => $componentId,
                                'external_id' => Str::uuid(),
                                'name' => $programName ?: $programCode,
                                'code' => $programCode,
                                'description' => null,
                                'created_at' => now(),
                                'updated_at' => now(),
                                'deleted_at' => null
                            ]);
                            $this->programMap[$programKey] = $programId;
                            $this->results['details']['programs']['new']++;
                            Log::info("Created new program: {$programCode} under component {$componentCode}");
                        }
                    }
                }

                // ========== UNIT ==========
                $unitId = null;
                if ($programId && $unitCode) {
                    $unitKey = $programId . '|' . $unitCode;

                    if (isset($this->unitMap[$unitKey])) {
                        $unitId = $this->unitMap[$unitKey];
                        $this->results['details']['units']['existing']++;
                    } else {
                        $existing = DB::table('rp_units')
                            ->where('code', $unitCode)
                            ->where('rp_programs_id', $programId)
                            ->first(['rp_units_id']);

                        if ($existing) {
                            $unitId = $existing->rp_units_id;
                            $this->unitMap[$unitKey] = $unitId;
                            $this->results['details']['units']['existing']++;
                            Log::info("Found existing unit: {$unitCode} under program {$programCode}");
                        } else {
                            $unitId = Str::uuid();
                            DB::table('rp_units')->insert([
                                'rp_units_id' => $unitId,
                                'external_id' => Str::uuid(),
                                'rp_programs_id' => $programId,
                                'name' => $unitName ?: $unitCode,
                                'code' => $unitCode,
                                'description' => null,
                                'created_at' => now(),
                                'updated_at' => now(),
                                'deleted_at' => null
                            ]);
                            $this->unitMap[$unitKey] = $unitId;
                            $this->results['details']['units']['new']++;
                            Log::info("Created new unit: {$unitCode} under program {$programCode}");
                        }
                    }
                }

                // ========== ACTION ==========
                if ($unitId && $actionCode) {
                    $actionKey = $unitId . '|' . $actionCode;

                    if (isset($this->actionMap[$actionKey])) {
                        $this->results['details']['actions']['existing']++;
                        Log::info("Action already exists: {$actionCode} under unit {$unitCode}");
                    } else {
                        $existing = DB::table('rp_actions')
                            ->where('code', $actionCode)
                            ->where('rp_units_id', $unitId)
                            ->first(['rp_actions_id']);

                        if ($existing) {
                            $this->actionMap[$actionKey] = true;
                            $this->results['details']['actions']['existing']++;
                            Log::info("Found existing action: {$actionCode} under unit {$unitCode}");
                        } else {
                            DB::table('rp_actions')->insert([
                                'rp_actions_id' => Str::uuid(),
                                'external_id' => Str::uuid(),
                               'rp_units_id' => $unitId,
                                'name' => $actionName ?: $actionCode,
                                'code' => $actionCode,
                                'objectives' => $actionObjective,
                                'targets_beneficiaries' => $actionTargets,
                                'created_at' => now(),
                                'updated_at' => now(),
                                'deleted_at' => null
                            ]);
                            $this->actionMap[$actionKey] = true;
                            $this->results['details']['actions']['new']++;
                            Log::info("Created new action: {$actionCode} under unit {$unitCode}");
                        }
                    }
                } else {
                    // Log warning if we have action but missing parent hierarchy
                    if ($actionCode && (!$componentCode || !$programCode || !$unitCode)) {
                        Log::warning("Row {$rowNumber}: Action '{$actionCode}' has incomplete hierarchy (Component: {$componentCode}, Program: {$programCode}, Unit: {$unitCode})");
                        $this->results['errors'][] = "Row {$rowNumber}: Incomplete hierarchy for action '{$actionCode}'";
                    }
                }

            } catch (\Exception $e) {
                $this->results['errors'][] = "Row {$rowNumber}: " . $e->getMessage();
                Log::error("HIERARCHY IMPORT ERROR Row {$rowNumber}", [
                    'error' => $e->getMessage(),
                    'data' => $row->toArray()
                ]);
            }
        }

        Log::info('=== HIERARCHY IMPORT COMPLETED ===', $this->results);
    }

    /**
     * Flexible column value finder
     */
    private function findColumnValue(array $rowArray, array $possibleNames, $default = null)
    {
        foreach ($possibleNames as $name) {
            // Try exact match
            if (isset($rowArray[$name])) {
                return $this->nullify($rowArray[$name]);
            }
            
            // Try case-insensitive match
            $lowerName = strtolower($name);
            foreach ($rowArray as $key => $value) {
                if (strtolower($key) === $lowerName) {
                    return $this->nullify($value);
                }
            }
            
            // Try removing special characters and spaces
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

    /**
     * Handle null/empty values properly
     */
    private function nullify($value)
    {
        if ($value === null) {
            return null;
        }
        
        $trimmed = trim((string)$value);
        return ($trimmed === '') ? null : $trimmed;
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