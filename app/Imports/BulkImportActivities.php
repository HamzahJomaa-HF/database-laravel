<?php

namespace App\Imports;

use App\Models\Activity;
use App\Models\ProjectActivity;
use App\Models\PortfolioActivity;
use App\Models\RpActivityMapping;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BulkImportActivities implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    protected $importedCount = 0;
    protected $failedRows = [];
    protected $rowIndex = 0;

    // Cache for lookups to avoid repeated DB queries
    protected $projectCache = [];
    protected $portfolioCache = [];
    protected $rpActivityCache = [];

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $this->rowIndex++;
        
        try {
            // Convert date formats
            $startDate = $this->parseDate($row['start_date'] ?? null);
            $endDate = $this->parseDate($row['end_date'] ?? null);
            
            if (!$startDate) {
                throw new \Exception("Invalid start_date format: {$row['start_date']}");
            }
            
            // ============================================
            // FIX: Process operational_support FIRST
            // ============================================
            $operationalSupportArray = [];
            if (!empty($row['operational_support'])) {
                // Split by comma, trim each value, remove empty values
                $values = explode(',', $row['operational_support']);
                $operationalSupportArray = array_map('trim', $values);
                $operationalSupportArray = array_filter($operationalSupportArray);
                $operationalSupportArray = array_values($operationalSupportArray); // Re-index
            }
            
            // Prepare activity data
            $activityData = [
                'activity_id' => (string) Str::uuid(),
                'activity_title_en' => $row['activity_title_en'] ?? null,
                'activity_title_ar' => $row['activity_title_ar'] ?? null,
                'activity_type' => $row['activity_type'] ?? null,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'venue' => $row['venue'] ?? null,
                'content_network' => $row['content_network'] ?? null,
                'maximum_capacity' => $row['maximum_capacity'] ?? null,
                'operational_support' => !empty($operationalSupportArray) ? json_encode($operationalSupportArray) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Create the activity
            $activity = Activity::create($activityData);
            $this->importedCount++;

            // ============================================
            // Handle Projects - Look up by code/name
            // ============================================
            if (!empty($row['projects'])) {
                $projectCodes = array_filter(array_map('trim', explode(',', $row['projects'])));
                $projectCodes = array_unique($projectCodes);
                
                foreach ($projectCodes as $projectCode) {
                    // Try to find the project by its code or external_id
                    $projectId = $this->findProjectByCode($projectCode);
                    
                    if ($projectId) {
                        ProjectActivity::create([
                            'project_activity_id' => (string) Str::uuid(),
                            'activity_id' => $activity->activity_id,
                            'project_id' => $projectId,
                        ]);
                    } else {
                        Log::warning("Project not found for code: {$projectCode}");
                    }
                }
            }

            // ============================================
            // Handle Portfolios - Look up by code/name
            // ============================================
            if (!empty($row['portfolios'])) {
                $portfolioCodes = array_filter(array_map('trim', explode(',', $row['portfolios'])));
                $portfolioCodes = array_unique($portfolioCodes);
                
                foreach ($portfolioCodes as $portfolioCode) {
                    $portfolioId = $this->findPortfolioByCode($portfolioCode);
                    
                    if ($portfolioId) {
                        PortfolioActivity::create([
                            'activity_id' => $activity->activity_id,
                            'portfolio_id' => $portfolioId,
                        ]);
                    } else {
                        Log::warning("Portfolio not found for code: {$portfolioCode}");
                    }
                }
            }

            // ============================================
            // Handle RP Activities - Look up by code
            // ============================================
            if (!empty($row['rp_activities'])) {
                $rpActivityCodes = array_filter(array_map('trim', explode(',', $row['rp_activities'])));
                $rpActivityCodes = array_unique($rpActivityCodes);
                
                foreach ($rpActivityCodes as $rpActivityCode) {
                    $rpActivityId = $this->findRpActivityByCode($rpActivityCode);
                    
                    if ($rpActivityId) {
                        DB::table('rp_activity_mappings')->insert([
                            'rp_activity_mappings_id' => (string) Str::uuid(),
                            'rp_activities_id' => $rpActivityId,
                            'activity_id' => $activity->activity_id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        Log::warning("RP Activity not found for code: {$rpActivityCode}");
                    }
                }
            }

            // ============================================
            // Handle Focal Points - Use employee IDs directly
            // ============================================
            if (!empty($row['focal_points'])) {
                $employeeIds = array_filter(array_map('trim', explode(',', $row['focal_points'])));
                $employeeIds = array_unique($employeeIds);
                
                $rpFocalpoints = [];
                foreach ($employeeIds as $employeeId) {
                    $exists = DB::table('rp_focalpoints')
                        ->where('employee_id', $employeeId)
                        ->whereNull('deleted_at')
                        ->first();
                    
                    if (!$exists) {
                        $employee = DB::table('employees')
                            ->where('employee_id', $employeeId)
                            ->first();
                        
                        if ($employee) {
                            $focalpointId = (string) Str::uuid();
                            DB::table('rp_focalpoints')->insert([
                                'rp_focalpoints_id' => $focalpointId,
                                'name' => trim($employee->first_name . ' ' . $employee->last_name),
                                'type' => 'employee',
                                'employee_id' => $employeeId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            $rpFocalpoints[] = $focalpointId;
                        }
                    } else {
                        $rpFocalpoints[] = $exists->rp_focalpoints_id;
                    }
                }
                
                // Create pivot records
                foreach ($rpFocalpoints as $rpFocalpointId) {
                    DB::table('activity_focal_points')->insert([
                        'activity_focal_point_id' => (string) Str::uuid(),
                        'activity_id' => $activity->activity_id,
                        'rp_focalpoints_id' => $rpFocalpointId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            return null;
            
        } catch (\Exception $e) {
            Log::error('Error importing row ' . $this->rowIndex, [
                'error' => $e->getMessage(),
                'row' => $row
            ]);
            
            $this->failedRows[] = [
                'row' => $this->rowIndex,
                'error' => $e->getMessage()
            ];
            
            return null;
        }
    }

    /**
     * Find project by code (external_id, name, or custom code)
     */
    private function findProjectByCode($code)
    {
        // Check cache first
        if (isset($this->projectCache[$code])) {
            return $this->projectCache[$code];
        }
        
        // Try to find by external_id first (most likely)
        $project = DB::table('projects')
            ->where('external_id', $code)
            ->orWhere('project_id', $code)
            ->orWhere('name', 'like', "%{$code}%")
            ->whereNull('deleted_at')
            ->first();
        
        $result = $project ? $project->project_id : null;
        $this->projectCache[$code] = $result;
        
        return $result;
    }

    /**
     * Find portfolio by code (external_id, name, or custom code)
     */
    private function findPortfolioByCode($code)
    {
        // Check cache first
        if (isset($this->portfolioCache[$code])) {
            return $this->portfolioCache[$code];
        }
        
        $portfolio = DB::table('portfolios')
            ->where('external_id', $code)
            ->orWhere('portfolio_id', $code)
            ->orWhere('name', 'like', "%{$code}%")
            ->whereNull('deleted_at')
            ->first();
        
        $result = $portfolio ? $portfolio->portfolio_id : null;
        $this->portfolioCache[$code] = $result;
        
        return $result;
    }

    /**
     * Find RP activity by code
     */
    private function findRpActivityByCode($code)
    {
        // Check cache first
        if (isset($this->rpActivityCache[$code])) {
            return $this->rpActivityCache[$code];
        }
        
        $rpActivity = DB::table('rp_activities')
            ->where('code', $code)
            ->orWhere('rp_activities_id', $code)
            ->orWhere('name', 'like', "%{$code}%")
            ->whereNull('deleted_at')
            ->first();
        
        $result = $rpActivity ? $rpActivity->rp_activities_id : null;
        $this->rpActivityCache[$code] = $result;
        
        return $result;
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }
        
        try {
            $formats = [
                'Y-m-d',
                'm/d/Y',
                'd/m/Y',
                'Y/m/d',
                'Y-m-d H:i:s',
                'm/d/Y H:i:s',
            ];
            
            foreach ($formats as $format) {
                try {
                    $parsed = Carbon::createFromFormat($format, $date);
                    if ($parsed) {
                        return $parsed;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
            
            return Carbon::parse($date);
            
        } catch (\Exception $e) {
            Log::warning("Failed to parse date: {$date}", ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'activity_title_en' => 'required_without:activity_title_ar|string|max:255',
            'activity_title_ar' => 'nullable|string|max:255',
            'activity_type' => 'required|string|max:100',
            'start_date' => 'required',
            'end_date' => 'nullable',
            'venue' => 'nullable|string|max:255',
            'content_network' => 'nullable|string',
            'maximum_capacity' => 'nullable|integer|min:0',
            'projects' => 'nullable|string',
            'portfolios' => 'nullable|string',
            'rp_activities' => 'nullable|string',
            'focal_points' => 'nullable|string',
            'operational_support' => 'nullable|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'activity_title_en.required_without' => 'Either English or Arabic title is required',
            'activity_type.required' => 'Activity type is required',
            'start_date.required' => 'Start date is required',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }

    public function getFailedRows()
    {
        return $this->failedRows;
    }
}