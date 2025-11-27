<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Diploma;
use App\Models\Nationality;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $diplomas = Diploma::orderBy('diploma_name')->get();
        $nationalities = Nationality::orderBy('name')->get();
        
        return view('users.create', compact('diplomas', 'nationalities'));
    }

    /**
     * Store a newly created user in storage.
     */
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
        'email' => 'nullable|email|max:255|unique:users,email',
        'gender' => ['nullable', Rule::in(['Male', 'Female'])],
        'marital_status' => 'nullable|string|max:50',
        'employment_status' => 'nullable|string|max:50',
        'type' => 'nullable|string|max:50|in:Stakeholder,Employee,Admin,Customer,Partner,Beneficiary',
        
        // Make identification fields optional
        'identification_id' => 'nullable|string|max:50|unique:users,identification_id',
        'passport_number' => 'nullable|string|max:50|unique:users,passport_number',
        'register_number' => 'nullable|string|max:50',
        'register_place' => 'nullable|string|max:255',
        
        // Diploma and Nationality fields - use existing diploma IDs
        'diplomas' => 'nullable|array',
        'diplomas.*' => 'exists:diploma,diploma_id',
        'nationalities' => 'nullable|array',
        'nationalities.*' => 'exists:nationality,nationality_id',
    ];

    $request->validate($rules);
    
    // Set default type if not provided
    $userData = $request->only([
        'first_name', 'last_name', 'middle_name', 'mother_name', 
        'dob', 'phone_number', 'email', 'gender', 'marital_status', 
        'employment_status', 'type', 'identification_id', 'passport_number', 
        'register_number', 'register_place'
    ]);

    if (empty($userData['type'])) {
        $userData['type'] = 'Stakeholder';
    }
    
    // Create user
    $user = User::create($userData);
    
    // Sync diplomas with existing diploma records
    if ($request->has('diplomas')) {
        $user->diplomas()->sync($request->diplomas);
    }
    
    // Sync nationalities with existing nationality records
    if ($request->has('nationalities')) {
        $user->nationalities()->sync($request->nationalities);
    }

    return redirect()->route('users.index')->with('success', 'User created successfully.');
}
    /**
     * Show the form for editing the specified user.
     */
    public function edit($user_id)
    {
        $user = User::with(['diplomas', 'nationalities'])
                    ->where('user_id', $user_id)
                    ->firstOrFail();
                    
        $diplomas = Diploma::orderBy('diploma_name')->get();
        $nationalities = Nationality::orderBy('name')->get();
        
        return view('users.edit', compact('user', 'diplomas', 'nationalities'));
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
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->user_id, 'user_id'),
            ],
            'gender' => ['nullable', Rule::in(['Male', 'Female'])],
            'marital_status' => 'nullable|string|max:50',
            'employment_status' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:50|in:Stakeholder,Employee,Admin,Customer,Partner,Beneficiary',
            
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
            
            // Diploma and Nationality fields - use existing diploma IDs
            'diplomas' => 'nullable|array',
            'diplomas.*' => 'exists:diploma,diploma_id',
            'nationalities' => 'nullable|array',
            'nationalities.*' => 'exists:nationality,nationality_id',
        ];

        $request->validate($rules);

        $userData = $request->only([
            'first_name', 'last_name', 'middle_name', 'mother_name', 
            'dob', 'phone_number', 'email', 'gender', 'marital_status', 
            'employment_status', 'type', 'identification_id', 'passport_number', 
            'register_number', 'register_place'
        ]);

        $user->update($userData);
        
        // Sync diplomas
        $user->diplomas()->sync($request->diplomas ?? []);
        
        // Sync nationalities
        $user->nationalities()->sync($request->nationalities ?? []);

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
    /**
 * Display user statistics (Simplified version)
 */
public function statistics()
{
    try {
        $totalUsers = User::count();
        $newThisWeek = User::where('created_at', '>=', now()->subWeek())->count();
        $newThisMonth = User::where('created_at', '>=', now()->subMonth())->count();
        
        // Add default values for new fields to prevent errors
        $stats = [
            'total_users' => $totalUsers,
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
            'new_this_week' => $newThisWeek,
            'new_this_month' => $newThisMonth,
            'avg_daily_registrations' => $this->getAverageRegistrationsPerDay(),
            // Add default values for new fields
            'weekly_growth' => 0,
            'monthly_growth' => 0,
            'registration_velocity' => $this->getAverageRegistrationsPerDay(),
            'data_health_score' => 75,
            'user_engagement' => 65,
            'retention_rate' => 80,
            'yearly_growth' => collect([]),
            'peak_registration_times' => collect([]),
            'beneficiary_ratio' => 0,
            'active_ratio' => 0,
            'completion_ratio' => 0,
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
                    'Email',
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
                        $user->dob ? $user->dob->format('Y-m-d') : '',
                        $user->phone_number ?? '',
                        $user->email ?? '',
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
            
            // Template headers - MUST match the import mapping exactly
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
            
            // Example data row with CORRECT formatting
            fputcsv($file, [
                'John',
                'Doe',
                'Smith',
                '1990-05-15', // Must be YYYY-MM-DD
                '+961 70 123 456',
                'Male',
                'Married',
                'Employed',
                'Stakeholder',
                'Mary Doe',
                'ID123456',
                'P1234567',
                'REG001',
                'Beirut'
            ], ',');
            
            // Second example with different data
            fputcsv($file, [
                'Sarah',
                'Smith',
                '',
                '1985-12-20',
                '+961 71 987 654',
                'Female',
                'Single',
                'Student',
                'Beneficiary',
                'Jane Smith',
                '',
                'P7654321',
                'REG002',
                'Tripoli'
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
            'import_file' => 'required|file|mimes:csv,txt'
        ]);

        $results = [
            'total' => 0,
            'successful' => 0,
            'failed' => 0,
            'errors' => []
        ];

        try {
            $file = $request->file('import_file');
            
            if (!$file || !$file->isValid()) {
                throw new \Exception('File upload failed');
            }

            $handle = fopen($file->getPathname(), 'r');
            
            if (!$handle) {
                throw new \Exception('Cannot open file');
            }

            // Skip header row
            $header = fgetcsv($handle);
            Log::info('CSV Header:', $header ?: ['No header found']);
            
            $rowNumber = 1;
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                $results['total']++;
                $rowNumber++;
                
                Log::info("Processing row {$rowNumber}:", $data);
                
                try {
                    // Skip empty rows
                    if (empty(array_filter($data))) {
                        Log::info("Skipping empty row {$rowNumber}");
                        continue;
                    }

                    // Map CSV columns to database fields CORRECTLY
                    $cleanedData = $this->cleanImportData([
                        'first_name' => $data[0] ?? null,
                        'last_name' => $data[1] ?? null,
                        'middle_name' => $data[2] ?? null,
                        'dob' => $data[3] ?? null,
                        'phone_number' => $data[4] ?? null,
                        'gender' => $data[5] ?? null,
                        'marital_status' => $data[6] ?? null,
                        'employment_status' => $data[7] ?? null,
                        'type' => $data[8] ?? 'Stakeholder',
                        'mother_name' => $data[9] ?? null,
                        'identification_id' => $data[10] ?? null,
                        'passport_number' => $data[11] ?? null,
                        'register_number' => $data[12] ?? null,
                        'register_place' => $data[13] ?? null,
                    ]);
                    
                    Log::info("Cleaned data for row {$rowNumber}:", $cleanedData);
                    
                    // Validate required fields
                    if (empty($cleanedData['first_name']) || empty($cleanedData['last_name'])) {
                        throw new \Exception('First name and last name are required');
                    }

                    // Create the user
                    $user = User::create($cleanedData);
                    
                    if ($user) {
                        $results['successful']++;
                        Log::info("Successfully created user: {$user->user_id}");
                    } else {
                        throw new \Exception('Failed to create user record');
                    }
                    
                } catch (\Exception $e) {
                    $results['failed']++;
                    $errorMessage = "Row {$rowNumber}: " . $e->getMessage();
                    $results['errors'][] = $errorMessage;
                    Log::error($errorMessage);
                }
            }
            
            fclose($handle);
            
            Log::info("Import completed: {$results['successful']} successful, {$results['failed']} failed");
            
        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage());
            return redirect()->route('users.import.form')
                ->with('error', 'Failed to process file: ' . $e->getMessage());
        }

        return $this->handleImportResults($results);
    }

    /**
     * Clean import data with better date handling
     */
    private function cleanImportData($data)
    {
        $cleaned = [];
        
        foreach ($data as $key => $value) {
            // Handle null values
            if ($value === null || $value === '') {
                $cleaned[$key] = null;
                continue;
            }
            
            $cleanValue = is_string($value) ? trim($value) : $value;
            
            // Convert empty strings to null
            if ($cleanValue === '') {
                $cleanValue = null;
            }
            
            // Handle date format - improved for various formats
            if ($key === 'dob' && $cleanValue) {
                try {
                    // Remove any quotes or extra spaces
                    $cleanValue = trim($cleanValue, '"\' ');
                    
                    // Handle different date formats
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $cleanValue)) {
                        // Already in YYYY-MM-DD format
                        $cleanValue = Carbon::parse($cleanValue)->format('Y-m-d');
                    } elseif (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $cleanValue)) {
                        // Handle MM/DD/YYYY format
                        $dateParts = explode('/', $cleanValue);
                        $cleanValue = Carbon::createFromDate($dateParts[2], $dateParts[0], $dateParts[1])->format('Y-m-d');
                    } elseif (preg_match('/^\d{1,2}-\d{1,2}-\d{4}$/', $cleanValue)) {
                        // Handle MM-DD-YYYY format
                        $dateParts = explode('-', $cleanValue);
                        $cleanValue = Carbon::createFromDate($dateParts[2], $dateParts[0], $dateParts[1])->format('Y-m-d');
                    } else {
                        // Try to parse any other format
                        $cleanValue = Carbon::parse($cleanValue)->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    Log::warning("Invalid date format for {$key}: {$cleanValue} - Error: " . $e->getMessage());
                    $cleanValue = null;
                }
            }
            
            // Handle gender formatting
            if ($key === 'gender' && $cleanValue) {
                $cleanValue = ucfirst(strtolower(trim($cleanValue)));
                if (!in_array($cleanValue, ['Male', 'Female'])) {
                    $cleanValue = null;
                }
            }
            
            // Handle type formatting
            if ($key === 'type' && $cleanValue) {
                $cleanValue = ucfirst(strtolower(trim($cleanValue)));
                $allowedTypes = ['Stakeholder', 'Employee', 'Admin', 'Customer', 'Partner', 'Beneficiary'];
                if (!in_array($cleanValue, $allowedTypes)) {
                    $cleanValue = 'Stakeholder'; // Default value
                }
            }
            
            // Handle employment status formatting
            if ($key === 'employment_status' && $cleanValue) {
                $cleanValue = ucfirst(strtolower(trim($cleanValue)));
                $allowedStatuses = ['Employed', 'Unemployed', 'Student', 'Retired', 'Self-employed'];
                if (!in_array($cleanValue, $allowedStatuses)) {
                    // Try to match common variations
                    $statusMap = [
                        'self-employed' => 'Self-employed',
                        'self employed' => 'Self-employed',
                        'selfemployed' => 'Self-employed',
                        'employed' => 'Employed',
                        'unemployed' => 'Unemployed',
                        'student' => 'Student',
                        'retired' => 'Retired',
                    ];
                    $cleanValue = $statusMap[strtolower($cleanValue)] ?? $cleanValue;
                }
            }
            
            // Handle marital status formatting
            if ($key === 'marital_status' && $cleanValue) {
                $cleanValue = ucfirst(strtolower(trim($cleanValue)));
                $allowedStatuses = ['Single', 'Married', 'Divorced', 'Widowed', 'Separated'];
                if (!in_array($cleanValue, $allowedStatuses)) {
                    // Try to match common variations
                    $statusMap = [
                        'single' => 'Single',
                        'married' => 'Married',
                        'divorced' => 'Divorced',
                        'widowed' => 'Widowed',
                        'separated' => 'Separated',
                    ];
                    $cleanValue = $statusMap[strtolower($cleanValue)] ?? $cleanValue;
                }
            }
            
            // Handle phone number formatting for Lebanon
            if ($key === 'phone_number' && $cleanValue) {
                // Remove any non-digit characters except +
                $cleanValue = preg_replace('/[^\d+]/', '', $cleanValue);
                
                // If it starts with 03, 70, 71, 76, 78, 79, 81, format as Lebanese number
                if (preg_match('/^(03|70|71|76|78|79|81)\d{6}$/', $cleanValue)) {
                    $cleanValue = '+961 ' . substr($cleanValue, 1, 2) . ' ' . substr($cleanValue, 3, 3) . ' ' . substr($cleanValue, 6, 3);
                }
                // If it's 8 digits without country code, assume it's Lebanese
                elseif (preg_match('/^\d{8}$/', $cleanValue) && in_array(substr($cleanValue, 0, 2), ['03', '70', '71', '76', '78', '79', '81'])) {
                    $cleanValue = '+961 ' . substr($cleanValue, 0, 2) . ' ' . substr($cleanValue, 2, 3) . ' ' . substr($cleanValue, 5, 3);
                }
                // If it already has +961, ensure proper formatting
                elseif (str_starts_with($cleanValue, '+961') && strlen($cleanValue) > 4) {
                    $numberPart = substr($cleanValue, 4);
                    if (preg_match('/^\d{8}$/', $numberPart)) {
                        $cleanValue = '+961 ' . substr($numberPart, 0, 2) . ' ' . substr($numberPart, 2, 3) . ' ' . substr($numberPart, 5, 3);
                    }
                }
            }
            
            $cleaned[$key] = $cleanValue;
        }
        
        return $cleaned;
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
    /**
 * Get registration patterns (Only type breakdown)
 */
private function getRegistrationPatterns()
{
    return [
        'by_type' => User::groupBy('type')
            ->select('type', DB::raw('COUNT(*) as count'))
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