<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the dashboard
     */
    public function index()
    {
        $employee = Auth::guard('employee')->user();
        
        return view('dashboard.index', [
            'employee' => $employee,
            'modules' => $employee->getAccessibleModules(),
        ]);
    }
}