<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemUser;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:system_users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,user',
            'phone' => 'required|string|max:20',
        ]);

        // Create system user
        $user = SystemUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        // Auto login after registration (skip OTP for first registration)
        Auth::guard('system')->login($user);

        return redirect()->route('dashboard')->with('success', 'Registration successful!');
    }
}