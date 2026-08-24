<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\registerRequest;
use App\Http\Requests\UpdateRequest;
use App\Models\User;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::paginate(10);
        return (new \App\Http\Resources\UserCollection($users))->additional([
            'message' => 'User berhasil diambil',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(registerRequest $request)
    {
        $user = User::create($request->validated());

        return response()->json([
            'message' => 'User registered successfully',
            'user' => new \App\Http\Resources\AuthMeResource($user),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'message' => 'User retrieved successfully',
            'user' => new \App\Http\Resources\AuthMeResource($user),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->validated());

        return response()->json([
            'message' => 'User updated successfully',
            'user' => new \App\Http\Resources\AuthMeResource($user),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }

    public function forceDelete(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();

        return response()->json([
            'message' => 'User permanently deleted successfully',
        ]);
    }

    public function restore(string $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return response()->json([
            'message' => 'User restored successfully',
            'user' => new \App\Http\Resources\AuthMeResource($user),
        ]);
    }

    public function trashed()
    {
        $trashedUsers = User::onlyTrashed()->paginate(10);

        return (new \App\Http\Resources\UserCollection($trashedUsers))->additional([
            'message' => 'user yang di hapus berhasil diambil',
        ]);
    }
}
