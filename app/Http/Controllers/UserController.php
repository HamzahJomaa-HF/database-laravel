<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Diploma;
use App\Models\Nationality;
use App\Models\Cop;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

// ADD THIS NEW FORM REQUEST CLASS
use App\Http\Requests\BulkDeleteUserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the users with professional filtering.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Add filter logic for new fields
        if ($request->filled('name')) {
            $name = $request->name;
            $query->where(function ($q) use ($name) {
                $q->where('first_name', 'ilike', "%$name%")
                  ->orWhere('middle_name', 'ilike', "%$name%")
                  ->orWhere('last_name', 'ilike', "%$name%")
                  ->orWhere('mother_name', 'ilike', "%$name%");
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('scope')) {
            $query->where('scope', $request->scope);
        }

        if ($request->filled('default_cop_id')) {
            $query->where('default_cop_id', $request->default_cop_id);
        }

        if ($request->filled('sector')) {
            $query->where('sector', $request->sector);
        }

        if ($request->filled('is_high_profile')) {
            $query->where('is_high_profile', filter_var($request->is_high_profile, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('organization_1')) {
            $query->where('organization_1', 'ilike', "%{$request->organization_1}%");
        }

        if ($request->filled('organization_type_1')) {
            $query->where('organization_type_1', $request->organization_type_1);
        }

        if ($request->filled('position_1')) {
            $query->where('position_1', 'ilike', "%{$request->position_1}%");
        }

        if ($request->filled('phone_number')) {
            $query->where('phone_number', 'like', "%{$request->phone_number}%");
        }

        if ($request->filled('email')) {
            $query->where('email', 'ilike', "%{$request->email}%");
        }

        // Keep backward compatible filters
        if ($request->filled('marital_status')) {
            $query->where('marital_status', $request->marital_status);
        }

        if ($request->filled('employment_status')) {
            $query->where('employment_status', $request->employment_status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('dob_from')) {
            $query->whereDate('dob', '>=', $request->dob_from);
        }

        if ($request->filled('dob_to')) {
            $query->whereDate('dob', '<=', $request->dob_to);
        }

        // Eager load the default CoP relationship
        $users = $query->with('defaultCop')
            ->orderBy('last_name', 'asc')
            ->paginate(20)
            ->withQueryString();

        // Check if any search/filter was applied
        $hasSearch = $request->anyFilled([
            'name', 'gender', 'scope', 'default_cop_id', 'sector', 'is_high_profile',
            'organization_1', 'organization_type_1', 'position_1', 'phone_number', 'email',
            'marital_status', 'employment_status', 'type', 'dob_from', 'dob_to'
        ]);

        return view('users.index', compact('users', 'hasSearch'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $diplomas = Diploma::orderBy('diploma_name')->get();
        $nationalities = Nationality::orderBy('name')->get();
        $cops = Cop::orderBy('cop_name')->get();
        
        return view('users.create', compact('diplomas', 'nationalities', 'cops'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            // Required fields from new structure
            'prefix' => 'nullable|string|max:50',
            'is_high_profile' => 'required|boolean',
            'scope' => ['required', Rule::in(['International', 'Regional', 'National', 'Local'])],
            'default_cop_id' => 'nullable|exists:cops,cop_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => ['required', Rule::in(['Male', 'Female', 'Other'])],
            'position_1' => 'required|string|max:255',
            'organization_1' => 'required|string|max:255',
            'organization_type_1' => [
                'required',
                Rule::in(['Public Sector', 'Private Sector', 'Academia', 'UN', 'INGOs', 'Civil Society', 'NGOs', 'Activist'])
            ],
            'status_1' => 'required|string|max:255',
            'address' => 'required|string',
            'phone_number' => 'required|string|max:20',
            
            // Optional fields from new structure
            'sector' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'office_phone' => 'nullable|string|max:20',
            'extension_number' => 'nullable|string|max:20',
            'home_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:users,email',
            
            // Optional secondary position fields
            'position_2' => 'nullable|string|max:255',
            'organization_2' => 'nullable|string|max:255',
            'organization_type_2' => [
                'nullable',
                Rule::in(['Public Sector', 'Private Sector', 'Academia', 'UN', 'INGOs', 'Civil Society', 'NGOs', 'Activist'])
            ],
            'status_2' => 'nullable|string|max:255',
            
            // Keep existing fields for backward compatibility
            'mother_name' => 'nullable|string|max:255',
            'marital_status' => 'nullable|string|max:50',
            'employment_status' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:50|in:Stakeholder,Beneficiary',
            'identification_id' => 'nullable|string|max:50|unique:users,identification_id',
            'passport_number' => 'nullable|string|max:50|unique:users,passport_number',
            'register_number' => 'nullable|string|max:50',
            'register_place' => 'nullable|string|max:255',
            
            // Diploma and Nationality fields
            'diplomas' => 'nullable|array',
            'diplomas.*' => 'exists:diploma,diploma_id',
            'nationalities' => 'nullable|array',
            'nationalities.*' => 'exists:nationality,nationality_id',
        ];

        $request->validate($rules);
        
        // Prepare user data
        $userData = $request->only([
            // New required fields
            'prefix', 'is_high_profile', 'scope', 'default_cop_id',
            'first_name', 'last_name', 'gender', 'position_1', 'organization_1',
            'organization_type_1', 'status_1', 'address', 'phone_number',
            
            // New optional fields
            'sector', 'middle_name', 'dob', 'office_phone', 'extension_number',
            'home_phone', 'email', 'position_2', 'organization_2', 'organization_type_2', 'status_2',
            
            // Existing fields
            'mother_name', 'marital_status',
            'employment_status', 'type', 'identification_id', 'passport_number',
            'register_number', 'register_place'
        ]);

        // REMOVED: Default type setting - type will be null if not provided
        
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
        $user = User::with(['diplomas', 'nationalities', 'defaultCop'])
                    ->where('user_id', $user_id)
                    ->firstOrFail();
                    
        $diplomas = Diploma::orderBy('diploma_name')->get();
        $nationalities = Nationality::orderBy('name')->get();
        $cops = Cop::orderBy('cop_name')->get();
        
        return view('users.edit', compact('user', 'diplomas', 'nationalities', 'cops'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $user_id)
    {
        $user = User::where('user_id', $user_id)->firstOrFail();

        $rules = [
            // Required fields from new structure
            'prefix' => 'nullable|string|max:50',
            'is_high_profile' => 'required|boolean',
            'scope' => ['required', Rule::in(['International', 'Regional', 'National', 'Local'])],
            'default_cop_id' => 'nullable|exists:cops,cop_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => ['required', Rule::in(['Male', 'Female', 'Other'])],
            'position_1' => 'required|string|max:255',
            'organization_1' => 'required|string|max:255',
            'organization_type_1' => [
                'required',
                Rule::in(['Public Sector', 'Private Sector', 'Academia', 'UN', 'INGOs', 'Civil Society', 'NGOs', 'Activist'])
            ],
            'status_1' => 'required|string|max:255',
            'address' => 'required|string',
            'phone_number' => 'required|string|max:20',
            
            // Optional fields from new structure
            'sector' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'office_phone' => 'nullable|string|max:20',
            'extension_number' => 'nullable|string|max:20',
            'home_phone' => 'nullable|string|max:20',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->user_id, 'user_id'),
            ],
            
            // Optional secondary position fields
            'position_2' => 'nullable|string|max:255',
            'organization_2' => 'nullable|string|max:255',
            'organization_type_2' => [
                'nullable',
                Rule::in(['Public Sector', 'Private Sector', 'Academia', 'UN', 'INGOs', 'Civil Society', 'NGOs', 'Activist'])
            ],
            'status_2' => 'nullable|string|max:255',
            
            // Keep existing fields for backward compatibility
            'mother_name' => 'nullable|string|max:255',
            'marital_status' => 'nullable|string|max:50',
            'employment_status' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:50|in:Stakeholder,Beneficiary',
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
            
            // Diploma and Nationality fields
            'diplomas' => 'nullable|array',
            'diplomas.*' => 'exists:diploma,diploma_id',
            'nationalities' => 'nullable|array',
            'nationalities.*' => 'exists:nationality,nationality_id',
        ];

        $request->validate($rules);

        // Prepare user data
        $userData = $request->only([
            // New required fields
            'prefix', 'is_high_profile', 'scope', 'default_cop_id',
            'first_name', 'last_name', 'gender', 'position_1', 'organization_1',
            'organization_type_1', 'status_1', 'address', 'phone_number',
            
            // New optional fields
            'sector', 'middle_name', 'dob', 'office_phone', 'extension_number',
            'home_phone', 'email', 'position_2', 'organization_2', 'organization_type_2', 'status_2',
            
            // Existing fields
            'mother_name', 'marital_status',
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
        $user->delete(); // This will now perform a soft delete

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Bulk delete users
     */
    public function bulkDestroy(BulkDeleteUserRequest $request)
    {
        try {
            $userIds = $request->getUserIdsAsArray();
            
            DB::transaction(function () use ($userIds) {
                foreach ($userIds as $userId) {
                    $user = User::where('user_id', $userId)->first();
                    if ($user) {
                        // No need to detach relationships for soft deletes
                        // The relationships will remain intact
                        $user->delete(); // Soft delete
                    }
                }
            });
            
            $count = count($userIds);
            
            return redirect()->route('users.index')
                ->with('success', "Successfully deleted {$count} user(s).");
                
        } catch (\Exception $e) {
            Log::error('Bulk delete failed: ' . $e->getMessage());
            return redirect()->route('users.index')
                ->with('error', 'Failed to delete users: ' . $e->getMessage());
        }
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
            $totalUsers = User::count();
            $newThisWeek = User::where('created_at', '>=', now()->subWeek())->count();
            $newThisMonth = User::where('created_at', '>=', now()->subMonth())->count();
            
            // Calculate weekly growth (for last week vs week before)
            $lastWeekStart = now()->subWeek()->startOfWeek();
            $lastWeekEnd = now()->subWeek()->endOfWeek();
            $twoWeeksAgoStart = now()->subWeeks(2)->startOfWeek();
            
            $lastWeekCount = User::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->count();
            $twoWeeksAgoCount = User::whereBetween('created_at', [$twoWeeksAgoStart, $lastWeekStart])->count();
            
            $weeklyGrowth = $twoWeeksAgoCount > 0 ? 
                round((($lastWeekCount - $twoWeeksAgoCount) / $twoWeeksAgoCount) * 100, 1) : 
                ($lastWeekCount > 0 ? 100 : 0);
            
            // Get average daily registrations (last 30 days)
            $avgDailyRegistrations = User::where('created_at', '>=', now()->subDays(30))->count() / 30;
            
            // Get beneficiary and stakeholder counts
            $beneficiaryCount = User::where('type', 'Beneficiary')->count();
            $stakeholderCount = User::where('type', 'Stakeholder')->count();
            
            // Get gender counts
            $maleCount = User::where('gender', 'Male')->count();
            $femaleCount = User::where('gender', 'Female')->count();
            
            // Get today's registrations
            $todayRegistrations = User::whereDate('created_at', today())->count();
            
            // Calculate average age
            $avgAge = User::whereNotNull('dob')
                ->get()
                ->filter(function($user) {
                    return $user->dob && $user->dob->age > 0;
                })
                ->avg(function($user) {
                    return $user->dob->age;
                });
            $avgAge = $avgAge ? round($avgAge) : 0;
            
            // Get scope distribution counts
            $scopeDistribution = User::groupBy('scope')
                ->select('scope', DB::raw('COUNT(*) as count'))
                ->get();
            
            // Get high profile counts
            $highProfileCount = User::where('is_high_profile', true)->count();
            $regularProfileCount = User::where('is_high_profile', false)->count();
            
            // Get CoP distribution with CoP names
            $copDistribution = User::with('defaultCop')
                ->whereNotNull('default_cop_id')
                ->get()
                ->groupBy('default_cop.cop_name')
                ->map(function($group) {
                    return $group->count();
                });
            
            // Get distribution by CoP ID for raw data
            $copIdDistribution = User::groupBy('default_cop_id')
                ->select('default_cop_id', DB::raw('COUNT(*) as count'))
                ->get();

            $stats = [
                'total_users' => $totalUsers,
                'new_this_week' => $newThisWeek,
                'new_this_month' => $newThisMonth,
                'weekly_growth' => $weeklyGrowth,
                'avg_daily_registrations' => round($avgDailyRegistrations, 1),
                'beneficiary_count' => $beneficiaryCount,
                'stakeholder_count' => $stakeholderCount,
                'male_count' => $maleCount,
                'female_count' => $femaleCount,
                'today_registrations' => $todayRegistrations,
                'avg_age' => $avgAge,
                
                // For charts data
                'scope_distribution' => $scopeDistribution,
                'high_profile_count' => $highProfileCount,
                'regular_profile_count' => $regularProfileCount,
                'cop_distribution' => $copDistribution,
                'cop_id_distribution' => $copIdDistribution,
                
                // Keep existing structure for other parts if needed
                'gender_distribution' => User::groupBy('gender')
                    ->select('gender', DB::raw('COUNT(*) as count'))
                    ->get(),
                'scope_distribution_raw' => $scopeDistribution,
                'organization_type_distribution' => User::groupBy('organization_type_1')
                    ->select('organization_type_1', DB::raw('COUNT(*) as count'))
                    ->get(),
                'sector_distribution' => User::groupBy('sector')
                    ->select('sector', DB::raw('COUNT(*) as count'))
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
                'data_health_score' => 75,
                'user_engagement' => 65,
                'retention_rate' => 80,
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
                'detailed_users' => User::with('defaultCop')->orderBy('created_at', 'desc')->get(),
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

            // Apply filters (same as index method)
            if ($request->filled('name')) {
                $name = $request->name;
                $query->where(function ($q) use ($name) {
                    $q->where('first_name', 'ilike', "%$name%")
                      ->orWhere('middle_name', 'ilike', "%$name%")
                      ->orWhere('last_name', 'ilike', "%$name%")
                      ->orWhere('mother_name', 'ilike', "%$name%");
                });
            }

            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }

            if ($request->filled('scope')) {
                $query->where('scope', $request->scope);
            }

            if ($request->filled('default_cop_id')) {
                $query->where('default_cop_id', $request->default_cop_id);
            }

            if ($request->filled('sector')) {
                $query->where('sector', $request->sector);
            }

            if ($request->filled('is_high_profile')) {
                $query->where('is_high_profile', filter_var($request->is_high_profile, FILTER_VALIDATE_BOOLEAN));
            }

            if ($request->filled('organization_1')) {
                $query->where('organization_1', 'ilike', "%{$request->organization_1}%");
            }

            if ($request->filled('organization_type_1')) {
                $query->where('organization_type_1', $request->organization_type_1);
            }

            if ($request->filled('position_1')) {
                $query->where('position_1', 'ilike', "%{$request->position_1}%");
            }

            if ($request->filled('phone_number')) {
                $query->where('phone_number', 'like', "%{$request->phone_number}%");
            }

            // Apply existing filters for backward compatibility
            if ($request->filled('marital_status')) {
                $query->where('marital_status', $request->marital_status);
            }

            if ($request->filled('employment_status')) {
                $query->where('employment_status', $request->employment_status);
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('dob_from')) {
                $query->whereDate('dob', '>=', $request->dob_from);
            }

            if ($request->filled('dob_to')) {
                $query->whereDate('dob', '<=', $request->dob_to);
            }

            $users = $query->with('defaultCop')->orderBy('last_name', 'asc')->get();
            
            $filename = 'users-export-' . now()->format('Y-m-d-H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($users) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8
                fwrite($file, "\xEF\xBB\xBF");
                
                // Headers - Required fields first, then optional
                $headers = [
                    // Required fields
                    'User ID',
                    'First Name',
                    'Last Name',
                    'Gender',
                    'Position 1',
                    'Organization 1',
                    'Organization Type 1',
                    'Status 1',
                    'Address',
                    'Phone Number',
                    'Is High Profile',
                    'Scope',
                    
                    // Community of Practice (optional but important)
                    'Community of Practice (ID)',
                    'Community of Practice (Name)',
                    
                    // Other important optional fields
                    'Prefix',
                    'Sector',
                    'Middle Name',
                    'Date of Birth',
                    'Office Phone',
                    'Extension Number',
                    'Home Phone',
                    'Email',
                    'Position 2',
                    'Organization 2',
                    'Organization Type 2',
                    'Status 2',
                    
                    // Existing optional fields
                    'Mother Name',
                    'Identification ID',
                    'Passport Number',
                    'Register Number',
                    'Register Place',
                    'Marital Status',
                    'Employment Status',
                    'User Type',
                    'Created Date',
                    'Updated Date'
                ];
                
                fputcsv($file, $headers, ',');
                
                // Data rows
                foreach ($users as $user) {
                    $row = [
                        // Required fields
                        $user->user_id,
                        $user->first_name ?? '',
                        $user->last_name ?? '',
                        $user->gender ?? '',
                        $user->position_1 ?? '',
                        $user->organization_1 ?? '',
                        $user->organization_type_1 ?? '',
                        $user->status_1 ?? '',
                        $user->address ?? '',
                        $user->phone_number ?? '',
                        $user->is_high_profile ? 'Yes' : 'No',
                        $user->scope ?? '',
                        
                        // Community of Practice
                        $user->default_cop_id ?? '',
                        $user->defaultCop ? $user->defaultCop->cop_name : '',
                        
                        // Other important optional fields
                        $user->prefix ?? '',
                        $user->sector ?? '',
                        $user->middle_name ?? '',
                        $user->dob ? $user->dob->format('Y-m-d') : '',
                        $user->office_phone ?? '',
                        $user->extension_number ?? '',
                        $user->home_phone ?? '',
                        $user->email ?? '',
                        $user->position_2 ?? '',
                        $user->organization_2 ?? '',
                        $user->organization_type_2 ?? '',
                        $user->status_2 ?? '',
                        
                        // Existing optional fields
                        $user->mother_name ?? '',
                        $user->identification_id ?? '',
                        $user->passport_number ?? '',
                        $user->register_number ?? '',
                        $user->register_place ?? '',
                        $user->marital_status ?? '',
                        $user->employment_status ?? '',
                        $user->type ?? '',
                        $user->created_at->format('Y-m-d H:i:s'),
                        $user->updated_at->format('Y-m-d H:i:s')
                    ];
                    
                    fputcsv($file, $row, ',');
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
    public function importForm()
    {
        $cops = Cop::orderBy('cop_name')->get();
        return view('users.import', compact('cops'));
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
            
            // Template headers - all 35 columns from your Excel
            fputcsv($file, [
                'prefix',
                'is_high_profile',
                'scope',
                'first_name',
                'last_name',
                'gender',
                'position_1',
                'organization_1',
                'organization_type_1',
                'status_1',
                'address',
                'phone_number',
                'sector',
                'middle_name',
                'mother_name',
                'dob',
                'office_phone',
                'extension_number',
                'home_phone',
                'email',
                'position_2',
                'organization_2',
                'organization_type_2',
                'status_2',
                'identification_id',
                'register_number',
                'marital_status',
                'employment_status',
                'passport_number',
                'register_place',
                'type',
                'default_cop_id',
                // Note: created_at, updated_at, deleted_at will be auto-generated
            ], ',');
            
            // Example data row
            fputcsv($file, [
                'Dr.',                          // prefix
                'true',                          // is_high_profile
                'National',                      // scope
                'John',                          // first_name
                'Doe',                           // last_name
                'Male',                          // gender
                'Senior Doctor',                  // position_1
                'Beirut Medical Center',          // organization_1
                'Public Sector',                  // organization_type_1
                'Active',                         // status_1
                'Beirut, Lebanon',                // address
                '+961 70 123 456',                 // phone_number
                'Healthcare',                     // sector
                'Michael',                        // middle_name
                'Jane Doe',                       // mother_name
                '1980-01-15',                     // dob
                '+961 1 123456',                   // office_phone
                '101',                            // extension_number
                '+961 3 789012',                   // home_phone
                'john.doe@example.com',            // email
                'Consultant',                      // position_2
                'Ministry of Health',              // organization_2
                'Public Sector',                   // organization_type_2
                'Part-time',                       // status_2
                'ID123456',                        // identification_id
                'REG789012',                       // register_number
                'Married',                         // marital_status
                'Employed',                        // employment_status
                'PASS123456',                      // passport_number
                'Beirut',                          // register_place
                'Stakeholder',                     // type (example)
                '123e4567-e89b-12d3-a456-426614174000', // default_cop_id (UUID example)
            ], ',');
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Process imported users - UPDATED for 35-column CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt|max:10240' // 10MB max
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
            
            // Expected number of columns (35)
            $expectedColumns = 35;
            
            $rowNumber = 1; // Start counting after header
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                $results['total']++;
                $rowNumber++;
                
                try {
                    // Skip empty rows
                    if (empty(array_filter($data))) {
                        continue;
                    }

                    // Check if we have enough columns
                    if (count($data) < $expectedColumns) {
                        throw new \Exception("Row has insufficient columns. Expected {$expectedColumns}, got " . count($data));
                    }

                    // Map all 35 CSV columns to database fields
                    $cleanedData = $this->cleanImportData([
                        // Basic Information (1-12)
                        'prefix' => $data[0] ?? null,
                        'is_high_profile' => $data[1] ?? false,
                        'scope' => $data[2] ?? null,
                        'first_name' => $data[3] ?? null,
                        'last_name' => $data[4] ?? null,
                        'gender' => $data[5] ?? null,
                        'position_1' => $data[6] ?? null,
                        'organization_1' => $data[7] ?? null,
                        'organization_type_1' => $data[8] ?? null,
                        'status_1' => $data[9] ?? null,
                        'address' => $data[10] ?? null,
                        'phone_number' => $data[11] ?? null,
                        
                        // Personal Details (13-20)
                        'sector' => $data[12] ?? null,
                        'middle_name' => $data[13] ?? null,
                        'mother_name' => $data[14] ?? null,
                        'dob' => $data[15] ?? null,
                        'office_phone' => $data[16] ?? null,
                        'extension_number' => $data[17] ?? null,
                        'home_phone' => $data[18] ?? null,
                        'email' => $data[19] ?? null,
                        
                        // Secondary Position (20-24)
                        'position_2' => $data[20] ?? null,
                        'organization_2' => $data[21] ?? null,
                        'organization_type_2' => $data[22] ?? null,
                        'status_2' => $data[23] ?? null,
                        
                        // Identification (24-31)
                        'identification_id' => $data[24] ?? null,
                        'register_number' => $data[25] ?? null,
                        'marital_status' => $data[26] ?? null,
                        'employment_status' => $data[27] ?? null,
                        'passport_number' => $data[28] ?? null,
                        'register_place' => $data[29] ?? null,
                        'type' => $data[30] ?? null, // No default, will be null if not provided
                        'default_cop_id' => $data[31] ?? null,
                        
                        // Note: created_at, updated_at, deleted_at (columns 32-34) are auto-generated
                    ]);
                    
                    // Validate required fields
                    $requiredFields = [
                        'first_name', 'last_name', 'gender', 'position_1', 
                        'organization_1', 'organization_type_1', 'status_1',
                        'address', 'phone_number', 'scope', 'is_high_profile'
                    ];
                    
                    // Check required fields
                    $missingFields = [];
                    foreach ($requiredFields as $field) {
                        if ($field === 'is_high_profile') {
                            // Boolean field - check if it's set (can be false)
                            if (!isset($cleanedData[$field])) {
                                $missingFields[] = $field;
                            }
                        } elseif (empty($cleanedData[$field]) && $cleanedData[$field] !== '0') {
                            $missingFields[] = $field;
                        }
                    }
                    
                    if (!empty($missingFields)) {
                        throw new \Exception("Missing required fields: " . implode(', ', $missingFields));
                    }

                    // Validate is_high_profile - ensure it's boolean
                    $cleanedData['is_high_profile'] = filter_var($cleanedData['is_high_profile'], FILTER_VALIDATE_BOOLEAN);
                    
                    // Validate scope
                    $allowedScopes = ['International', 'Regional', 'National', 'Local'];
                    if (!in_array($cleanedData['scope'], $allowedScopes)) {
                        throw new \Exception("Invalid scope '{$cleanedData['scope']}'. Must be one of: " . implode(', ', $allowedScopes));
                    }
                    
                    // Validate gender
                    $allowedGenders = ['Male', 'Female', 'Other'];
                    if (!in_array($cleanedData['gender'], $allowedGenders)) {
                        throw new \Exception("Invalid gender '{$cleanedData['gender']}'. Must be one of: " . implode(', ', $allowedGenders));
                    }
                    
                    // Validate organization_type_1
                    $allowedOrgTypes = ['Public Sector', 'Private Sector', 'Academia', 'UN', 'INGOs', 'Civil Society', 'NGOs', 'Activist'];
                    if (!in_array($cleanedData['organization_type_1'], $allowedOrgTypes)) {
                        throw new \Exception("Invalid organization type '{$cleanedData['organization_type_1']}'. Must be one of: " . implode(', ', $allowedOrgTypes));
                    }
                    
                    // Validate organization_type_2 if provided
                    if (!empty($cleanedData['organization_type_2'])) {
                        if (!in_array($cleanedData['organization_type_2'], $allowedOrgTypes)) {
                            throw new \Exception("Invalid organization type 2 '{$cleanedData['organization_type_2']}'. Must be one of: " . implode(', ', $allowedOrgTypes));
                        }
                    }
                    
                    // Validate type if provided (only Stakeholder or Beneficiary allowed)
                    if (!empty($cleanedData['type'])) {
                        $allowedTypes = ['Stakeholder', 'Beneficiary'];
                        if (!in_array($cleanedData['type'], $allowedTypes)) {
                            throw new \Exception("Invalid type '{$cleanedData['type']}'. Must be one of: " . implode(', ', $allowedTypes));
                        }
                    }

                    // Validate default_cop_id if provided (now UUID)
                    if (!empty($cleanedData['default_cop_id'])) {
                        $copExists = Cop::where('cop_id', $cleanedData['default_cop_id'])->exists();
                        if (!$copExists) {
                            throw new \Exception("Invalid default_cop_id: " . $cleanedData['default_cop_id'] . " does not exist");
                        }
                    }

                    // Validate email if provided (must be unique)
                    if (!empty($cleanedData['email'])) {
                        $existingUser = User::where('email', $cleanedData['email'])->first();
                        if ($existingUser) {
                            throw new \Exception("Email '{$cleanedData['email']}' already exists for user ID: {$existingUser->user_id}");
                        }
                    }

                    // Validate passport_number if provided (must be unique)
                    if (!empty($cleanedData['passport_number'])) {
                        $existingUser = User::where('passport_number', $cleanedData['passport_number'])->first();
                        if ($existingUser) {
                            throw new \Exception("Passport number '{$cleanedData['passport_number']}' already exists for user ID: {$existingUser->user_id}");
                        }
                    }

                    // Validate identification_id if provided (must be unique)
                    if (!empty($cleanedData['identification_id'])) {
                        $existingUser = User::where('identification_id', $cleanedData['identification_id'])->first();
                        if ($existingUser) {
                            throw new \Exception("Identification ID '{$cleanedData['identification_id']}' already exists for user ID: {$existingUser->user_id}");
                        }
                    }

                    // Check for duplicate by name/phone combination (optional but recommended)
                    $existingUser = User::where('first_name', $cleanedData['first_name'])
                        ->where('last_name', $cleanedData['last_name'])
                        ->where('phone_number', $cleanedData['phone_number'])
                        ->first();
                        
                    if ($existingUser) {
                        throw new \Exception("User already exists with same name and phone (ID: {$existingUser->user_id})");
                    }

                    // Create the user
                    $user = User::create($cleanedData);
                    
                    if ($user) {
                        $results['successful']++;
                        Log::info("Successfully imported user: {$user->first_name} {$user->last_name} (ID: {$user->user_id})");
                    } else {
                        throw new \Exception('Failed to create user record');
                    }
                    
                } catch (\Exception $e) {
                    $results['failed']++;
                    $errorMessage = "Row {$rowNumber}: " . $e->getMessage();
                    $results['errors'][] = $errorMessage;
                    Log::error($errorMessage);
                    
                    // Continue with next row instead of stopping
                    continue;
                }
            }
            
            fclose($handle);
            
        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage());
            return redirect()->route('users.import.form')
                ->with('error', 'Failed to process file: ' . $e->getMessage());
        }

        return $this->handleImportResults($results);
    }

    /**
     * Clean import data - UPDATED for cop_id handling
     */
    private function cleanImportData($data)
    {
        $cleaned = [];
        
        foreach ($data as $key => $value) {
            // Handle null/empty values
            if ($value === null || $value === '') {
                $cleaned[$key] = null;
                continue;
            }
            
            $cleanValue = is_string($value) ? trim($value) : $value;
            
            // Convert empty strings to null
            if ($cleanValue === '') {
                $cleanValue = null;
            }
            
            // Handle boolean for is_high_profile
            if ($key === 'is_high_profile') {
                if (is_string($cleanValue)) {
                    $upperValue = strtoupper($cleanValue);
                    if ($upperValue === 'TRUE' || $upperValue === 'YES' || $upperValue === '1' || $upperValue === 'T') {
                        $cleanValue = true;
                    } elseif ($upperValue === 'FALSE' || $upperValue === 'NO' || $upperValue === '0' || $upperValue === 'F') {
                        $cleanValue = false;
                    } else {
                        // Try to convert any other string
                        $cleanValue = filter_var($cleanValue, FILTER_VALIDATE_BOOLEAN);
                        if ($cleanValue === false && $cleanValue !== true) {
                            // filter_var returns false for non-boolean strings, so we need to check
                            $cleanValue = false; // Default to false
                        }
                    }
                } elseif (is_numeric($cleanValue)) {
                    $cleanValue = (bool) $cleanValue;
                }
                
                // Ensure it's always a boolean
                $cleanValue = (bool) $cleanValue;
            }
            
            // Handle default_cop_id - FIXED: UUID should not be cast to integer
            if ($key === 'default_cop_id' && $cleanValue !== null) {
                // Keep as string/uuid, don't cast to int
                $cleanValue = trim($cleanValue);
                // If it's empty after trim, set to null
                if ($cleanValue === '') {
                    $cleanValue = null;
                }
            }
            
            // Handle scope formatting
            if ($key === 'scope' && $cleanValue) {
                $original = $cleanValue;
                $cleanValue = trim($cleanValue);
                $cleanValue = ucfirst(strtolower($cleanValue));
                
                // Fix common typos
                $scopeMap = [
                    'int' => 'International',
                    'internat' => 'International',
                    'international' => 'International',
                    'reg' => 'Regional',
                    'regional' => 'Regional',
                    'nat' => 'National',
                    'national' => 'National',
                    'loc' => 'Local',
                    'local' => 'Local',
                ];
                
                $lowerValue = strtolower($cleanValue);
                if (isset($scopeMap[$lowerValue])) {
                    $cleanValue = $scopeMap[$lowerValue];
                }
                
                if (!in_array($cleanValue, ['International', 'Regional', 'National', 'Local'])) {
                    Log::warning("Invalid scope value '{$original}', defaulting to 'National'");
                    $cleanValue = 'National';
                }
            }
            
            // Handle organization type formatting
            if (in_array($key, ['organization_type_1', 'organization_type_2']) && $cleanValue) {
                $original = $cleanValue;
                $cleanValue = trim($cleanValue);
                $cleanValue = ucwords(strtolower($cleanValue));
                
                // Fix common variations
                $orgMap = [
                    'public sector' => 'Public Sector',
                    'public' => 'Public Sector',
                    'private sector' => 'Private Sector',
                    'private' => 'Private Sector',
                    'academia' => 'Academia',
                    'academic' => 'Academia',
                    'un' => 'UN',
                    'united nations' => 'UN',
                    'ingos' => 'INGOs',
                    'ingo' => 'INGOs',
                    'civil society' => 'Civil Society',
                    'civil' => 'Civil Society',
                    'ngos' => 'NGOs',
                    'ngo' => 'NGOs',
                    'activist' => 'Activist',
                    'advocacy' => 'Activist',
                ];
                
                $lowerValue = strtolower($cleanValue);
                if (isset($orgMap[$lowerValue])) {
                    $cleanValue = $orgMap[$lowerValue];
                }
                
                $allowedTypes = ['Public Sector', 'Private Sector', 'Academia', 'UN', 'INGOs', 'Civil Society', 'NGOs', 'Activist'];
                if (!in_array($cleanValue, $allowedTypes)) {
                    Log::warning("Invalid organization type '{$original}', defaulting to 'Private Sector'");
                    $cleanValue = 'Private Sector';
                }
            }
            
            // Handle gender formatting (keeping as original - Male/Female only)
            if ($key === 'gender' && $cleanValue) {
                $original = $cleanValue;
                $cleanValue = trim($cleanValue);
                $cleanValue = ucfirst(strtolower($cleanValue));
                
                // Fix common variations
                $genderMap = [
                    'm' => 'Male',
                    'male' => 'Male',
                    'f' => 'Female',
                    'female' => 'Female',
                ];
                
                $lowerValue = strtolower($cleanValue);
                if (isset($genderMap[$lowerValue])) {
                    $cleanValue = $genderMap[$lowerValue];
                }
                
                if (!in_array($cleanValue, ['Male', 'Female'])) {
                    Log::warning("Invalid gender value '{$original}', defaulting to 'Male'");
                    $cleanValue = 'Male';
                }
            }
            
            // Handle date format
            if ($key === 'dob' && $cleanValue) {
                try {
                    $cleanValue = trim($cleanValue, '"\' ');
                    $cleanValue = Carbon::parse($cleanValue)->format('Y-m-d');
                } catch (\Exception $e) {
                    Log::warning("Invalid date format for {$key}: {$cleanValue}");
                    $cleanValue = null;
                }
            }
            
            // Handle type formatting (only Stakeholder/Beneficiary allowed, otherwise set to null)
            if ($key === 'type' && $cleanValue) {
                $cleanValue = ucfirst(strtolower(trim($cleanValue)));
                $allowedTypes = ['Stakeholder', 'Beneficiary'];
                if (!in_array($cleanValue, $allowedTypes)) {
                    Log::warning("Invalid type value '{$cleanValue}', setting to null");
                    $cleanValue = null;
                }
            }
            
            // Handle phone number formatting
            if (in_array($key, ['phone_number', 'office_phone', 'home_phone']) && $cleanValue) {
                $original = $cleanValue;
                // Remove all non-numeric characters except +
                $cleanValue = preg_replace('/[^\d+]/', '', $cleanValue);
                
                // Format Lebanese numbers
                if (preg_match('/^(03|70|71|76|78|79|81)(\d{6})$/', $cleanValue, $matches)) {
                    $cleanValue = '+961 ' . $matches[1] . ' ' . substr($matches[2], 0, 3) . ' ' . substr($matches[2], 3, 3);
                } elseif (preg_match('/^\+?961(3|70|71|76|78|79|81)(\d{6})$/', $cleanValue, $matches)) {
                    $cleanValue = '+961 ' . $matches[1] . ' ' . substr($matches[2], 0, 3) . ' ' . substr($matches[2], 3, 3);
                } elseif (preg_match('/^(\d{8})$/', $cleanValue) && in_array(substr($cleanValue, 0, 2), ['03', '70', '71', '76', '78', '79', '81'])) {
                    $cleanValue = '+961 ' . substr($cleanValue, 1, 2) . ' ' . substr($cleanValue, 3, 3) . ' ' . substr($cleanValue, 6, 2);
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
     * Get demographic breakdown - UPDATED for cop_id
     */
    private function getDemographicBreakdown()
    {
        return [
            'by_gender' => User::groupBy('gender')
                ->select('gender', DB::raw('COUNT(*) as count'))
                ->get(),
            'by_scope' => User::groupBy('scope')
                ->select('scope', DB::raw('COUNT(*) as count'))
                ->get(),
            'by_cop' => User::with('defaultCop')
                ->whereNotNull('default_cop_id')
                ->get()
                ->groupBy('default_cop.cop_name')
                ->map(function($group) {
                    return $group->count();
                }),
            'by_organization_type' => User::groupBy('organization_type_1')
                ->select('organization_type_1', DB::raw('COUNT(*) as count'))
                ->get(),
        ];
    }

    /**
     * Get registration patterns - UPDATED for cop_id
     */
    private function getRegistrationPatterns()
    {
        return [
            'by_type' => User::groupBy('type')
                ->select('type', DB::raw('COUNT(*) as count'))
                ->get(),
            'by_sector' => User::groupBy('sector')
                ->select('sector', DB::raw('COUNT(*) as count'))
                ->get(),
            'by_cop' => User::groupBy('default_cop_id')
                ->select('default_cop_id', DB::raw('COUNT(*) as count'))
                ->get(),
        ];
    }

    /**
     * Get export data - UPDATED for cop_id
     */
    private function getExportData()
    {
        return User::with('defaultCop')->orderBy('created_at', 'desc')->limit(100)->get();
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