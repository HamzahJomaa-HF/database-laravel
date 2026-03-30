<?php

namespace App\Exports;

use App\Models\Activity;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class ActivitiesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $filters;
    protected $rowNumber = 0;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Activity::query();

        // Apply filters
        if (isset($this->filters['title']) && !empty($this->filters['title'])) {
            $query->where(function ($q) {
                $q->where('activity_title_en', 'like', '%' . $this->filters['title'] . '%')
                    ->orWhere('activity_title_ar', 'like', '%' . $this->filters['title'] . '%');
            });
        }

        if (isset($this->filters['activity_type']) && !empty($this->filters['activity_type'])) {
            $query->where('activity_type', $this->filters['activity_type']);
        }

        if (isset($this->filters['venue']) && !empty($this->filters['venue'])) {
            $query->where('venue', 'like', '%' . $this->filters['venue'] . '%');
        }

        if (isset($this->filters['status']) && !empty($this->filters['status'])) {
            $now = now();
            if ($this->filters['status'] == 'upcoming') {
                $query->where('start_date', '>', $now);
            } elseif ($this->filters['status'] == 'ongoing') {
                $query->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
            } elseif ($this->filters['status'] == 'completed') {
                $query->where('end_date', '<', $now);
            }
        }

        if (isset($this->filters['start_date_from']) && !empty($this->filters['start_date_from'])) {
            $query->where('start_date', '>=', $this->filters['start_date_from']);
        }

        if (isset($this->filters['end_date_to']) && !empty($this->filters['end_date_to'])) {
            $query->where('end_date', '<=', $this->filters['end_date_to']);
        }

        return $query->orderBy('start_date', 'desc');
    }

    /**
     * Define the column headers
     */
    public function headings(): array
    {
        return [
            '#',                      // Serial number
            'Activity Name EN',       // Activity Name in English
            'Activity Name AR',       // Activity Name in Arabic
            'Start Date',             // Start date
            'End Date',               // End date
            'Venue',                  // Venue/location
            'Activity Type',          // Type of activity
            'Activity Scope',         // Scope (Local/Regional/International)
            'Activity Portfolio',     // Portfolio category
            'Project Name',           // Associated Project Name
            'Content / Network',      // Content and network description
            'Experts',                // Experts involved
            'Status',                 // Status (planned/upcoming/ongoing/completed/cancelled)
            'Focal Point',            // Focal point person
        ];
    }

    /**
     * Map each activity to the Excel row
     */
    public function map($activity): array
    {
        $this->rowNumber++;
        
        // Get portfolios (Activity Portfolio)
        $portfolios = DB::table('portfolio_activities')
            ->join('portfolios', 'portfolio_activities.portfolio_id', '=', 'portfolios.portfolio_id')
            ->where('portfolio_activities.activity_id', $activity->activity_id)
            ->pluck('portfolios.name')
            ->implode(', ');
        
        // GET PROJECT NAME FROM project_activities and projects tables
        $projectNames = DB::table('project_activities')
            ->join('projects', 'project_activities.project_id', '=', 'projects.project_id')
            ->where('project_activities.activity_id', $activity->activity_id)
            ->pluck('projects.name')
            ->implode(', ');
        
        // FOCAL POINTS - USING PIVOT TABLE
        $focalPoints = DB::table('activity_focal_points')
            ->join('rp_focalpoints', 'rp_focalpoints.rp_focalpoints_id', '=', 'activity_focal_points.rp_focalpoints_id')
            ->leftJoin('employees', 'employees.employee_id', '=', 'rp_focalpoints.employee_id')
            ->where('activity_focal_points.activity_id', $activity->activity_id)
            ->whereNull('activity_focal_points.deleted_at')
            ->selectRaw("CONCAT(employees.first_name, ' ', employees.last_name) as full_name")
            ->pluck('full_name')
            ->implode(', ');
        
        // If no employees found, use the focal point name
        if (empty($focalPoints)) {
            $focalPoints = DB::table('activity_focal_points')
                ->join('rp_focalpoints', 'rp_focalpoints.rp_focalpoints_id', '=', 'activity_focal_points.rp_focalpoints_id')
                ->where('activity_focal_points.activity_id', $activity->activity_id)
                ->whereNull('activity_focal_points.deleted_at')
                ->pluck('rp_focalpoints.name')
                ->implode(', ');
        }
        
        // DETERMINE STATUS - exactly as displayed in the application
        $status = $this->determineStatus($activity);
        
        // Format dates (26-Feb-26 format)
        $startDate = $activity->start_date ? date('d-M-y', strtotime($activity->start_date)) : '';
        $endDate = $activity->end_date ? date('d-M-y', strtotime($activity->end_date)) : '';
        
        // Determine activity scope
        $activityScope = $activity->activity_scope ?? 'Local';
        
        return [
            $this->rowNumber,                      // # column
            $activity->activity_title_en ?? '',    // Activity Name EN
            $activity->activity_title_ar ?? '',    // Activity Name AR
            $startDate,                            // Start Date
            $endDate,                              // End Date
            $activity->venue ?? '',                // Venue
            $activity->activity_type ?? '',        // Activity Type
            $activityScope,                        // Activity Scope
            $portfolios ?: '',                     // Activity Portfolio
            $projectNames ?: '',                   // Project Name
            $activity->content_network ?? '',      // Content / Network
            $activity->experts ?? '',              // Experts
            $status,                               // Status
            $focalPoints ?: '',                    // Focal Point
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Auto-size columns
                $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];
                foreach ($columns as $column) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                }
                
                // Freeze the header row
                $event->sheet->getDelegate()->freezePane('A2');
                
                // Add filter to the header
                $event->sheet->getDelegate()->setAutoFilter('A1:N1');
                
                // Set header background color
                $event->sheet->getDelegate()->getStyle('A1:N1')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE0E0E0');
            },
        ];
    }
    
    /**
     * Determine status based on dates - exactly as displayed in the application
     * Returns: 'planned', 'upcoming', 'ongoing', 'completed', or 'cancelled'
     */
   /**
 * Determine status based on dates - exactly as displayed in the application
 * Returns: 'planned', 'upcoming', 'ongoing', 'completed', 'cancelled', or 'unknown'
 */
private function determineStatus($activity)
{
    $now = now();
    
    // Check if activity is cancelled (if you have this field)
    if (isset($activity->is_cancelled) && $activity->is_cancelled) {
        return 'cancelled';
    }
    
    // If no start date, status is unknown
    if (empty($activity->start_date)) {
        return 'unknown';
    }
    
    // Check if completed (end date exists and is in the past)
    if (!empty($activity->end_date) && $activity->end_date < $now) {
        return 'completed';
    }
    
    // Check if ongoing (start date is now or in the past, and activity is not completed)
    if ($activity->start_date <= $now) {
        // If no end date, it's unknown (as per your requirement)
        if (empty($activity->end_date)) {
            return 'unknown';
        }
        // If end date exists and is now or in the future
        if ($activity->end_date >= $now) {
            return 'ongoing';
        }
    }
    
    // Check if upcoming (start date is in the future)
    if ($activity->start_date > $now) {
        // If no end date, it's upcoming
        if (empty($activity->end_date)) {
            return 'upcoming';
        }
        return 'upcoming';
    }
    
    // Default fallback
    return 'unknown';
}
}