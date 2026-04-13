<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // VALIDASI
        $request->validate([
            'nim' => 'required|unique:users,nim',
            'full_name' => 'required|string|max:100',
            'password' => 'required|min:6',
            'subdivision' => 'required|in:software,hardware'
        ]);

        // SIMPAN USER
        $user = User::create([
            'nim' => $request->nim,
            'full_name' => $request->full_name,
            'password' => Hash::make($request->password),
            'subdivision' => $request->subdivision,
            'role' => $request->role
        ]);

        // BUAT TOKEN (Sanctum)
        $token = $user->createToken('api-token')->plainTextToken;

        // RESPONSE
        return response()->json([
            'message' => 'Register berhasil 🔥',
            'user' => $user,
            'token' => $token
        ], 201);
    }
}
