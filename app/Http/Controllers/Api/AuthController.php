<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\registerRequest;
use App\Models\User;



class AuthController extends Controller
{
    public function store(registerRequest $request) {

    $user = User::create($request->validated());

    return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }
}
