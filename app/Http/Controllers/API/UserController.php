<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the users with professional filtering.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Add back the filter logic that was missing:
        if ($request->filled('name')) {
            $name = $request->name;
            $query->where(function ($q) use ($name) {
                $q->where('first_name', 'ilike', "%$name%")
                  ->orWhere('middle_name', 'ilike', "%$name%")
                  ->orWhere('last_name', 'ilike', "%$name%");
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('marital_status')) {
            $query->where('marital_status', $request->marital_status);
        }

        if ($request->filled('employment_status')) {
            $query->where('employment_status', $request->employment_status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('phone_number')) {
            $query->where('phone_number', 'like', "%{$request->phone_number}%");
        }

        if ($request->filled('dob_from')) {
            $query->whereDate('dob', '>=', $request->dob_from);
        }

        if ($request->filled('dob_to')) {
            $query->whereDate('dob', '<=', $request->dob_to);
        }

        $users = $query->orderBy('last_name', 'asc')->paginate(20)->withQueryString();

        // Check if any search/filter was applied
        $hasSearch = $request->anyFilled(['name', 'gender', 'marital_status', 'employment_status', 'type', 'phone_number', 'dob_from', 'dob_to']);

        return view('users.index', compact('users', 'hasSearch'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'gender' => ['nullable', Rule::in(['Male', 'Female'])],
            'marital_status' => 'nullable|string|max:50',
            'employment_status' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:50|in:Stakeholder,Employee,Admin,Customer,Partner',
            
            // Make identification fields optional
            'identification_id' => 'nullable|string|max:50|unique:users,identification_id',
            'passport_number' => 'nullable|string|max:50|unique:users,passport_number',
            'register_number' => 'nullable|string|max:50',
            'register_place' => 'nullable|string|max:255',
        ];

        $request->validate($rules);
        
        // Set default type if not provided
        $userData = $request->all();
        if (empty($userData['type'])) {
            $userData['type'] = 'Stakeholder';
        }
        
        User::create($userData);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($user_id)
    {
        $user = User::where('user_id', $user_id)->firstOrFail();
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $user_id)
    {
        $user = User::where('user_id', $user_id)->firstOrFail();

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'gender' => ['nullable', Rule::in(['Male', 'Female'])],
            'marital_status' => 'nullable|string|max:50',
            'employment_status' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:50|in:Stakeholder,Employee,Admin,Customer,Partner',
            
            'identification_id' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('users', 'identification_id')->ignore($user->user_id, 'user_id'),
            ],
            'passport_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('users', 'passport_number')->ignore($user->user_id, 'user_id'),
            ],
            'register_number' => 'nullable|string|max:50',
            'register_place' => 'nullable|string|max:255',
        ];

        $request->validate($rules);

        $user->update($request->all());

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($user_id)
    {
        $user = User::where('user_id', $user_id)->firstOrFail();
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Display user dashboard
     */
    public function dashboard()
    {
        $totalUsers = User::count();
        return view('users.dashboard', compact('totalUsers'));
    }

    /**
     * Display user statistics
     */
    public function statistics()
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'gender_distribution' => User::groupBy('gender')
                    ->select('gender', DB::raw('COUNT(*) as count'))
                    ->get(),
                'employment_distribution' => User::groupBy('employment_status')
                    ->select('employment_status', DB::raw('COUNT(*) as count'))
                    ->get(),
                'type_distribution' => User::groupBy('type')
                    ->select('type', DB::raw('COUNT(*) as count'))
                    ->get(),
                'marital_status_distribution' => User::groupBy('marital_status')
                    ->select('marital_status', DB::raw('COUNT(*) as count'))
                    ->get(),
                'registration_trends' => $this->getRegistrationTrends(),
                'new_this_week' => User::where('created_at', '>=', now()->subWeek())->count(),
                'new_this_month' => User::where('created_at', '>=', now()->subMonth())->count(),
                'avg_daily_registrations' => $this->getAverageRegistrationsPerDay(),
            ];

            return view('users.statistics', compact('stats'));

        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'Failed to load statistics: ' . $e->getMessage());
        }
    }

    /**
     * Display user reports
     */
    public function reports()
    {
        try {
            $reports = [
                'detailed_users' => User::orderBy('created_at', 'desc')->get(),
                'demographic_breakdown' => $this->getDemographicBreakdown(),
                'registration_patterns' => $this->getRegistrationPatterns(),
                'export_data' => $this->getExportData(),
            ];

            return view('users.reports', compact('reports'));

        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'Failed to generate reports: ' . $e->getMessage());
        }
    }

    /**
     * Export users to CSV
     */
    public function exportExcel(Request $request)
    {
        try {
            $query = User::query();

            // Apply the same filters as your index method
            if ($request->filled('name')) {
                $name = $request->name;
                $query->where(function ($q) use ($name) {
                    $q->where('first_name', 'ilike', "%$name%")
                      ->orWhere('middle_name', 'ilike', "%$name%")
                      ->orWhere('last_name', 'ilike', "%$name%");
                });
            }

            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }

            if ($request->filled('marital_status')) {
                $query->where('marital_status', $request->marital_status);
            }

            if ($request->filled('employment_status')) {
                $query->where('employment_status', $request->employment_status);
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('phone_number')) {
                $query->where('phone_number', 'like', "%{$request->phone_number}%");
            }

            if ($request->filled('dob_from')) {
                $query->whereDate('dob', '>=', $request->dob_from);
            }

            if ($request->filled('dob_to')) {
                $query->whereDate('dob', '<=', $request->dob_to);
            }

            $users = $query->orderBy('last_name', 'asc')->get();
            
            $filename = 'users-export-' . now()->format('Y-m-d-H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($users) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8 to handle special characters
                fwrite($file, "\xEF\xBB\xBF");
                
                // Headers
                fputcsv($file, [
                    'User ID',
                    'First Name', 
                    'Middle Name', 
                    'Last Name', 
                    'Gender',
                    'Date of Birth', 
                    'Phone Number', 
                    'Marital Status',
                    'Employment Status', 
                    'User Type',
                    'Identification ID',
                    'Passport Number',
                    'Register Number',
                    'Register Place',
                    'Mother Name',
                    'Created Date'
                ], ',');
                
                // Data rows
                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->user_id,
                        $user->first_name ?? '',
                        $user->middle_name ?? '',
                        $user->last_name ?? '',
                        $user->gender ?? '',
                        $user->dob ? $user->dob->format('Y-m-d') : '', // Fixed null check
                        $user->phone_number ?? '',
                        $user->marital_status ?? '',
                        $user->employment_status ?? '',
                        $user->type ?? '',
                        $user->identification_id ?? '',
                        $user->passport_number ?? '',
                        $user->register_number ?? '',
                        $user->register_place ?? '',
                        $user->mother_name ?? '',
                        $user->created_at->format('Y-m-d H:i:s')
                    ], ',');
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'Failed to export users: ' . $e->getMessage());
        }
    }

    /**
     * Show import form
     */
    public function showImportForm()
    {
        return view('users.import');
    }

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        $filename = 'users-import-template-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // BOM for UTF-8
            
            // Template headers
            fputcsv($file, [
                'first_name', 
                'last_name', 
                'middle_name', 
                'dob',
                'phone_number', 
                'gender',
                'marital_status',
                'employment_status', 
                'type',
                'mother_name',
                'identification_id',
                'passport_number',
                'register_number',
                'register_place'
            ], ',');
            
            // Example data row
            fputcsv($file, [
                'John',
                'Doe',
                'Smith',
                '1990-05-15',
                '1234567890',
                'Male',
                'Married',
                'Employed',
                'Stakeholder',
                'Mary Doe',
                'ID123456',
                'P1234567',
                'REG001',
                'City Hall'
            ], ',');
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Process imported users
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx'
        ]);

        $results = [
            'total' => 0,
            'successful' => 0,
            'failed' => 0,
            'errors' => []
        ];

        try {
            $file = $request->file('file');
            $handle = fopen($file->getPathname(), 'r');
            
            // Skip header row
            $header = fgetcsv($handle);
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                $results['total']++;
                
                try {
                    // Map CSV columns to database fields
                    $cleanedData = $this->cleanImportData([
                        'first_name' => $data[0] ?? null,
                        'last_name' => $data[1] ?? null,
                        'middle_name' => $data[2] ?? null,
                        'dob' => $data[3] ?? null,
                        'phone_number' => $data[4] ?? null,
                        'gender' => $data[5] ?? null,
                        'marital_status' => $data[6] ?? null,
                        'employment_status' => $data[7] ?? null,
                        'type' => $data[8] ?? 'Stakeholder', // Default value
                        'mother_name' => $data[9] ?? null,
                        'identification_id' => $data[10] ?? null,
                        'passport_number' => $data[11] ?? null,
                        'register_number' => $data[12] ?? null,
                        'register_place' => $data[13] ?? null,
                    ]);
                    
                    // Validate required fields
                    if (empty($cleanedData['first_name']) || empty($cleanedData['last_name'])) {
                        throw new \Exception('First name and last name are required');
                    }
                    
                    User::create($cleanedData);
                    $results['successful']++;
                    
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Row {$results['total']}: " . $e->getMessage();
                }
            }
            
            fclose($handle);
            
        } catch (\Exception $e) {
            return redirect()->route('users.import.form')
                ->with('error', 'Failed to process file: ' . $e->getMessage());
        }

        return $this->handleImportResults($results);
    }

    /**
     * Get registration trends data
     */
    private function getRegistrationTrends()
    {
        return User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    /**
     * Get average registrations per day
     */
    private function getAverageRegistrationsPerDay()
    {
        $firstUser = User::orderBy('created_at', 'asc')->first();
        
        if (!$firstUser) {
            return 0;
        }

        $totalUsers = User::count();
        $days = now()->diffInDays($firstUser->created_at);
        
        return $days > 0 ? round($totalUsers / $days, 2) : $totalUsers;
    }

    /**
     * Get demographic breakdown
     */
    private function getDemographicBreakdown()
    {
        return [
            'by_gender' => User::groupBy('gender')
                ->select('gender', DB::raw('COUNT(*) as count'))
                ->get(),
            'by_marital_status' => User::groupBy('marital_status')
                ->select('marital_status', DB::raw('COUNT(*) as count'))
                ->get(),
            'by_employment' => User::groupBy('employment_status')
                ->select('employment_status', DB::raw('COUNT(*) as count'))
                ->get(),
        ];
    }

    /**
     * Get registration patterns (Database-agnostic version)
     */
    private function getRegistrationPatterns()
    {
        return [
            'by_type' => User::groupBy('type')
                ->select('type', DB::raw('COUNT(*) as count'))
                ->get(),
            'by_day_of_week' => User::selectRaw('
                DAYNAME(created_at) as day, 
                COUNT(*) as count'
            )
            ->groupBy(DB::raw('DAYNAME(created_at)'))
            ->orderBy(DB::raw('MIN(created_at)'))
            ->get(),
        ];
    }

    /**
     * Get export data
     */
    private function getExportData()
    {
        return User::orderBy('created_at', 'desc')->limit(100)->get();
    }

    /**
     * Clean import data
     */
    private function cleanImportData($data)
    {
        $cleaned = [];
        
        foreach ($data as $key => $value) {
            $cleanValue = is_string($value) ? trim($value) : $value;
            
            // Convert empty strings to null
            if ($cleanValue === '') {
                $cleanValue = null;
            }
            
            // Handle date format
            if ($key === 'dob' && $cleanValue) {
                try {
                    $cleanValue = Carbon::parse($cleanValue)->format('Y-m-d');
                } catch (\Exception $e) {
                    $cleanValue = null;
                }
            }
            
            $cleaned[$key] = $cleanValue;
        }
        
        return $cleaned;
    }

    /**
     * Handle import results and redirect
     */
    private function handleImportResults($results)
    {
        $message = "Import completed: {$results['successful']} successful, {$results['failed']} failed out of {$results['total']} total records.";
        
        if ($results['failed'] > 0) {
            $errorDetails = implode('<br>', array_slice($results['errors'], 0, 10));
            if (count($results['errors']) > 10) {
                $errorDetails .= '<br>... and ' . (count($results['errors']) - 10) . ' more errors';
            }
            
            return redirect()
                ->route('users.import.form')
                ->with('warning', $message)
                ->with('error_details', $errorDetails);
        }
        
        return redirect()
            ->route('users.index')
            ->with('success', $message);
    }
}