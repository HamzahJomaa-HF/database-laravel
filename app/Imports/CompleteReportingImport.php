<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\Log;

class CompleteReportingImport implements WithMultipleSheets
{
    public $hierarchyImport;
    public $activitiesImport;
    
    /**
     * Specify which sheets to import
     */
    public function sheets(): array
    {
        // Create instances
        $this->hierarchyImport = new HierarchyImport();
        $this->activitiesImport = new ActivitiesImport();
        
        return [
            0 => $this->hierarchyImport,      // First sheet
            1 => $this->activitiesImport,     // Second sheet
        ];
    }
}