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

    private $actionsMap = [];

    public function collection(Collection $rows)
    {
        Log::info('=== STARTING ACTIVITIES IMPORT ===');
        Log::info('Total rows: ' . $rows->count());

        // Debug: Log first row headers
        if ($rows->count() > 0) {
            $firstRow = $rows->first()->toArray();
            Log::info('First row headers:', array_keys($firstRow));
        }

        // Pre-load all actions from database
        $this->loadActions();

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            try {
                $rowArray = $row->toArray();

                // Get values from Excel
                $actionReference = $this->findColumnValue($rowArray, [
                    'acion reference',
                    'action reference', 
                    'action_reference', 
                    'reference', 
                    'action_code',
                    'action code'
                ]);
                
                $activityCode = $this->findColumnValue($rowArray, [
                    'activity code', 
                    'activity_code', 
                    'activity'
                ]);
                
                $activityName = $this->findColumnValue($rowArray, [
                    'activity', 
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

                // Extract action code from reference
                $actionCodeFromReference = $this->extractActionCode($actionReference);
                
                if (!$actionCodeFromReference) {
                    $errorMsg = "Row {$rowNumber}: Could not extract action code from reference '{$actionReference}'";
                    $this->results['errors'][] = $errorMsg;
                    Log::warning($errorMsg);
                    continue;
                }

                Log::debug("Row {$rowNumber}: Reference '{$actionReference}' → Extracted action code '{$actionCodeFromReference}'");

                // 1. Find Action using the extracted action code
                $action = $this->findActionByCode($actionCodeFromReference, $actionReference, $rowNumber);
                
                if (!$action) {
                    $errorMsg = "Row {$rowNumber}: Action not found for code '{$actionCodeFromReference}' (from reference '{$actionReference}')";
                    $this->results['errors'][] = $errorMsg;
                    Log::warning($errorMsg);
                    continue;
                }

                Log::info("Row {$rowNumber}: Found action '{$action->code}' for reference '{$actionReference}'");

                // Create unique activity code: action_code + activity_code
                $uniqueActivityCode = $this->generateUniqueActivityCode($action->code, $activityCode, $action->rp_actions_id);
                
                Log::debug("Row {$rowNumber}: Original activity code '{$activityCode}' → Unique code '{$uniqueActivityCode}'");

                // 2. Create/Update Activity
                $activity = $this->createOrUpdateActivity($action, $uniqueActivityCode, $activityName, $status, $rowNumber);
                
                if (!$activity) {
                    continue;
                }
                
                $activityId = $activity->rp_activities_id;

                // 3. Process indicators
                if ($indicatorsText) {
                    $indicatorsCount = $this->processIndicators($activityId, $indicatorsText);
                    Log::info("Row {$rowNumber}: Linked {$indicatorsCount} indicators");
                }

                // 4. Process focal points
                if ($focalPointsText) {
                    $focalPointsCount = $this->processFocalPoints($activityId, $focalPointsText);
                    Log::info("Row {$rowNumber}: Linked {$focalPointsCount} focal points");
                }

            } catch (\Exception $e) {
                $errorMsg = "Row {$rowNumber}: " . $e->getMessage();
                $this->results['errors'][] = $errorMsg;
                Log::error($errorMsg, [
                    'error' => $e->getMessage(),
                    'data' => $row->toArray()
                ]);
            }
        }

        Log::info('=== ACTIVITIES IMPORT COMPLETED ===', $this->results);
    }

    /**
     * Generate unique activity code
     */
    private function generateUniqueActivityCode(string $actionCode, string $activityCode, string $actionId): string
    {
        // Option 1: Simple concatenation
        $uniqueCode = "{$actionCode}-{$activityCode}";
        
        // Check if this code already exists for this action
        $existing = DB::table('rp_activities')
            ->where('action_id', $actionId)
            ->where('code', $uniqueCode)
            ->exists();
        
        if (!$existing) {
            return $uniqueCode;
        }
        
        // If exists, add incremental number
        $counter = 1;
        while (true) {
            $proposedCode = "{$actionCode}-{$activityCode}-{$counter}";
            $exists = DB::table('rp_activities')
                ->where('action_id', $actionId)
                ->where('code', $proposedCode)
                ->exists();
            
            if (!$exists) {
                Log::warning("Generated unique code with suffix: {$proposedCode} (original: {$activityCode})");
                return $proposedCode;
            }
            
            $counter++;
            if ($counter > 100) {
                // Fallback: add timestamp
                $proposedCode = "{$actionCode}-{$activityCode}-" . time();
                Log::warning("Generated unique code with timestamp: {$proposedCode}");
                return $proposedCode;
            }
        }
    }

    /**
     * Extract action code from reference
     */
    private function extractActionCode(string $actionReference): ?string
    {
        $cleanReference = trim($actionReference);
        $cleanReference = preg_replace('/\s+/', '', $cleanReference);
        
        // Split by dots
        $parts = explode('.', $cleanReference);
        
        // Get the last part
        $lastPart = end($parts);
        
        // Check if numeric
        if (is_numeric($lastPart)) {
            return $lastPart;
        }
        
        // Roman numerals
        $romanToNumber = [
            'i' => '1', 'ii' => '2', 'iii' => '3', 'iv' => '4', 'v' => '5',
            'vi' => '6', 'vii' => '7', 'viii' => '8', 'ix' => '9', 'x' => '10'
        ];
        
        $lowerLastPart = strtolower($lastPart);
        if (isset($romanToNumber[$lowerLastPart])) {
            return $romanToNumber[$lowerLastPart];
        }
        
        // Find any number
        if (preg_match('/\d+/', $cleanReference, $matches)) {
            return $matches[0];
        }
        
        // Try second last part
        if (count($parts) > 1) {
            $secondLast = $parts[count($parts) - 2];
            if (is_numeric($secondLast)) {
                return $secondLast;
            }
        }
        
        return null;
    }

    /**
     * Load all actions from database
     */
    private function loadActions(): void
    {
        $actions = DB::table('rp_actions')
            ->select('rp_actions_id', 'code', 'name')
            ->whereNotNull('code')
            ->get();
        
        Log::info("Found " . $actions->count() . " actions in database");
        
        foreach ($actions as $action) {
            $this->actionsMap['code:' . $action->code] = $action;
            $cleanedCode = trim($action->code);
            if ($cleanedCode !== $action->code) {
                $this->actionsMap['code_cleaned:' . $cleanedCode] = $action;
            }
        }
    }

    /**
     * Find action by code
     */
    private function findActionByCode(string $actionCode, string $originalReference, int $rowNumber)
    {
        Log::debug("Row {$rowNumber}: Searching action for code '{$actionCode}'");
        
        $cleanActionCode = trim($actionCode);
        
        // Try cached
        if (isset($this->actionsMap['code:' . $cleanActionCode])) {
            return $this->actionsMap['code:' . $cleanActionCode];
        }
        
        if (isset($this->actionsMap['code_cleaned:' . $cleanActionCode])) {
            return $this->actionsMap['code_cleaned:' . $cleanActionCode];
        }
        
        // Try database
        try {
            $action = DB::table('rp_actions')
                ->where('code', $cleanActionCode)
                ->select('rp_actions_id', 'code', 'name')
                ->first();
            
            if ($action) return $action;
            
            // Case-insensitive
            $action = DB::table('rp_actions')
                ->whereRaw('LOWER(code) = LOWER(?)', [$cleanActionCode])
                ->select('rp_actions_id', 'code', 'name')
                ->first();
            
            if ($action) return $action;
            
            // Partial match
            $action = DB::table('rp_actions')
                ->where('code', 'LIKE', '%' . $cleanActionCode . '%')
                ->select('rp_actions_id', 'code', 'name')
                ->first();
            
            if ($action) {
                Log::info("Found partial match: '{$action->code}' contains '{$cleanActionCode}'");
                return $action;
            }
            
        } catch (\Exception $e) {
            Log::error("Error searching for action: " . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Create or update activity
     */
    private function createOrUpdateActivity($action, $activityCode, $activityName, $status, $rowNumber)
    {
        try {
            // Find existing activity with the unique code
            $existingActivity = DB::table('rp_activities')
                ->where('action_id', $action->rp_actions_id)
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
                Log::info("Updated activity: {$activityId} for action {$action->code}");
            } else {
                // Create new activity
                $activityId = (string) Str::uuid();
                
                DB::table('rp_activities')->insert([
                    'rp_activities_id' => $activityId,
                    'action_id' => $action->rp_actions_id,
                    'external_id' => (string) Str::uuid(),
                    'external_type' => null,
                    'name' => $this->truncateForDb($activityName ?: "Activity {$activityCode}"),
                    'code' => $activityCode,
                    'description' => null,
                    'activity_type' => null,
                    'status' => $this->mapStatus($status),
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => null,
                ]);
                $this->results['created']['activities']++;
                $isNew = true;
                Log::info("Created new activity: {$activityId} for action {$action->code}");
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
                ->where('activity_id', $activityId)
                ->where('indicator_id', $indicatorId)
                ->first();

            if (!$existingLink) {
                $linkId = (string) Str::uuid();
                
                DB::table('rp_activity_indicators')->insert([
                    'rp_activity_indicators_id' => $linkId,
                    'activity_id' => $activityId,
                    'indicator_id' => $indicatorId,
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
                    'user_id' => null,
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
                ->where('activity_id', $activityId)
                ->where('focalpoint_id', $focalpointId)
                ->first();

            if (!$existingLink) {
                $linkId = (string) Str::uuid();
                
                DB::table('rp_activity_focalpoints')->insert([
                    'rp_activity_focalpoints_id' => $linkId,
                    'activity_id' => $activityId,
                    'focalpoint_id' => $focalpointId,
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