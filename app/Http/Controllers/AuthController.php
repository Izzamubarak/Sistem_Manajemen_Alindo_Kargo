<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email tidak ditemukan.'
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Password salah.'
            ], 401);
        }

        // Jika berhasil
        Auth::login($user); // optional: kalau kamu ingin session
        return response()->json([
            'message' => 'Login berhasil',
            'token' => $user->createToken('api-token')->plainTextToken,
            'user' => $user
        ]);
    }



    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'nullable|string|in:admin,tim-operasional,super-admin', // Validasi role
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $role = $request->input('role', 'tim-operasional');

        if ($role !== 'tim-operasional') {
            $authenticatedUser = $request->user();
            if (!$authenticatedUser || $authenticatedUser->role !== 'super-admin') {
                return response()->json([
                    'message' => 'Hanya super-admin yang dapat menetapkan role tertentu.'
                ], 403);
            }
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
        ]);

        return response()->json([
            'message' => 'Registration successful',
            'token' => $user->createToken('api-token')->plainTextToken,
            'user' => $user
        ], 201);
    }
}
