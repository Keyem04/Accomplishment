<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    // REGISTER
    public function register(Request $request)
    {
        $request->validate([
            'FullName' => 'required|string|max:50',
            'email' => 'required|email|unique:fms.systemusers,email|max:100',
            'UserName' => 'required|string|max:30|unique:fms.systemusers,UserName',
            'UserPassword' => 'required|string|min:8|confirmed', // requires UserPassword_confirmation
            'Designation' => 'nullable|string|max:50',
            'department_code' => 'nullable|string|max:3',
            'UserType' => 'nullable|string|max:20',
        ]);

        // Create user in FMS database
        $user = User::create([
            'FullName' => $request->FullName,
            'email' => $request->email,
            'UserName' => $request->UserName,
            'UserPassword' => md5($request->UserPassword), // MD5 for legacy compatibility
            'laravel_password' => Hash::make($request->UserPassword), // Bcrypt for Laravel
            'Designation' => $request->Designation,
            'department_code' => $request->department_code,
            'UserType' => $request->UserType ?? 'user',
            'is_active' => 1, // Active by default
            'passworddate' => now(),
            'password_expiry' => now()->addMonths(6), // 6 months expiry
        ]);

        // Create API token in your database
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => [
                'recid' => $user->recid,
                'UserName' => $user->UserName,
                'FullName' => $user->FullName,
                'email' => $user->email,
                'department_code' => $user->department_code,
                'UserType' => $user->UserType,
            ],
        ], 201);
    }

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'UserName' => 'required|string',
            'UserPassword' => 'required|string',
        ]);

        // Find user by UserName from FMS database
        $user = User::where('UserName', $request->UserName)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'UserName' => ['User not found.'],
            ]);
        }

        // Check if user is active
        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'UserName' => ['Your account is not active.'],
            ]);
        }

        // Compare MD5 password (FMS legacy format)
        // The password in DB is already MD5 hashed
        $providedPasswordMD5 = md5($request->password);
        
        if ($providedPasswordMD5 !== $user->UserPassword) {
            throw ValidationException::withMessages([
                'UserName' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Delete old tokens (optional - keeps only one active session)
        // $user->tokens()->delete();

        // Create new token in accomplishment_db
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'recid' => $user->recid,
                'UserName' => $user->UserName,
                'full_name' => $user->FullName,
                'department_code' => $user->department_code,
                'user_type' => $user->UserType,
            ],
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
