<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create new user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    // Login user
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid login details', 'responseCode' => '01'], 401);
        }

        // Generate Sanctum token
        $user = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'responseCode' => '00',
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    // Logout user (Revoke token)
    public function logout(Request $request)
    {
        // Revoke current user's token
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully', 'responseCode' => '00'], 200);
    }
}
