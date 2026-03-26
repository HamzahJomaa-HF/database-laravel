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
     * Define the column headers - EXACTLY matching your sample
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
            'Status',                 // Status (Planned/Complete/Cancelled/etc)
            'Focal Point',            // Focal point person
            'Data'                    // Additional data column
        ];
    }

    /**
 * Map each activity to the Excel row
 */
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
    
    // ============================================
    // FOCAL POINTS - CORRECT WAY USING PIVOT TABLE
    // ============================================
    // Get focal points from the pivot table structure
    $focalPoints = DB::table('activity_focal_points')
        ->join('rp_focalpoints', 'rp_focalpoints.rp_focalpoints_id', '=', 'activity_focal_points.rp_focalpoints_id')
        ->leftJoin('employees', 'employees.employee_id', '=', 'rp_focalpoints.employee_id')
        ->where('activity_focal_points.activity_id', $activity->activity_id)
        ->whereNull('activity_focal_points.deleted_at')
        ->selectRaw("CONCAT(employees.first_name, ' ', employees.last_name) as full_name")
        ->pluck('full_name')
        ->implode(', ');
    
    // If no employees found (focal points without employee association), use the focal point name
    if (empty($focalPoints)) {
        $focalPoints = DB::table('activity_focal_points')
            ->join('rp_focalpoints', 'rp_focalpoints.rp_focalpoints_id', '=', 'activity_focal_points.rp_focalpoints_id')
            ->where('activity_focal_points.activity_id', $activity->activity_id)
            ->whereNull('activity_focal_points.deleted_at')
            ->pluck('rp_focalpoints.name')
            ->implode(', ');
    }
    
    // Determine status based on dates if not explicitly set
    $status = $activity->status ?? $this->determineStatus($activity);
    
    // Format dates as per your sample (26-Feb-26 format)
    $startDate = $activity->start_date ? date('d-M-y', strtotime($activity->start_date)) : '';
    $endDate = $activity->end_date ? date('d-M-y', strtotime($activity->end_date)) : '';
    
    // Determine activity scope (you may need to adjust this based on your data)
    // If you have a scope field in your activities table, use it
    $activityScope = $activity->activity_scope ?? 'Local'; // Default to 'Local' if not specified
    
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
        ucfirst($status) ?? '',                // Status (Planned/Complete/etc)
        $focalPoints ?: '',                    // Focal Point (names from employees/focalpoints)
        ''                                     // Data column (empty as in sample)
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
                $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
                foreach ($columns as $column) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                }
                
                // Freeze the header row
                $event->sheet->getDelegate()->freezePane('A2');
                
                // Add filter to the header
                $event->sheet->getDelegate()->setAutoFilter('A1:L1');
                
                // Set header background color (optional)
                $event->sheet->getDelegate()->getStyle('A1:L1')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE0E0E0');
            },
        ];
    }
    
    /**
     * Determine status based on dates
     */
    private function determineStatus($activity)
    {
        $now = now();
        
        // Check if activity is cancelled
        if (isset($activity->is_cancelled) && $activity->is_cancelled) {
            return 'cancelled';
        }
        
        // Check if completed
        if ($activity->end_date && $activity->end_date < $now) {
            return 'complete';
        }
        
        // Check if ongoing
        if ($activity->start_date <= $now && $activity->end_date >= $now) {
            return 'ongoing';
        }
        
        // Check if upcoming/planned
        if ($activity->start_date > $now) {
            return 'planned';
        }
        
        return 'planned';
    }
}