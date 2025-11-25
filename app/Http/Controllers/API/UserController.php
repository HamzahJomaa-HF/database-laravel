<?php

namespace App\Http\Controllers\API; // Note: This namespace suggests API, but the methods return views.
                                    // I will treat it as a standard Web Controller for this response.

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the users with professional filtering.
     * The logic here supports the filters added in the enhanced index.blade.php.
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
     * Note: UUID is generated in the User Model's boot method.
     */
    public function store(Request $request)
    {
        // Define a comprehensive set of validation rules
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'gender' => ['nullable', Rule::in(['Male', 'Female'])], // Enforce specific values
            'marital_status' => 'nullable|string|max:50',
            'employment_status' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:50',
            
            // Allow identification fields to be nullable, but unique if present
            'identification_id' => 'nullable|string|max:50|unique:users,identification_id',
            'passport_number' => 'nullable|string|max:50|unique:users,passport_number',
            'register_number' => 'nullable|string|max:50',
            'register_place' => 'nullable|string|max:255',
        ];

        // The custom creation logic in the Model will throw a ValidationException 
        // if the required combination of fields (identification_id OR passport_number OR name/dob/phone OR register details) is missing.
        $request->validate($rules);
        
        // Use the Model's create method and let the boot method handle the UUID generation.
        User::create($request->all());

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

        // Define update rules, ignoring uniqueness checks for the current user's data
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'gender' => ['nullable', Rule::in(['Male', 'Female'])],
            'type' => 'nullable|string|max:50',
            
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
        ];

        $request->validate($rules);

        // Use fill/update
        $user->fill($request->all())->save();

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
    public function dashboard()
{
    // Example: show total users
    $totalUsers = \App\Models\User::count();

    return view('users.dashboard', compact('totalUsers'));
}

}