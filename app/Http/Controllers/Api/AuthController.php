<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\registerRequest;
use App\Http\Resources\AuthMeResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;



class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();

        $expiration = $request->boolean('remember_me') ? null : now()->addWeeks(1);

        $token = $user->createToken('auth_token', ['*'], $expiration)->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'user' => new AuthMeResource($user),
        ]);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function me()
    {
        $user = Auth::user();

        return response()->json([
            'message' => 'Authenticated user retrieved successfully',
            'user' => new AuthMeResource($user),
        ]);
    }
}
