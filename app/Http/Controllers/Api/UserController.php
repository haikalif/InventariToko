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
        $this->authorize('viewAny', User::class);
        $users = User::paginate(10);
        return (new \App\Http\Resources\UserCollection($users))->additional([
            'message' => 'User berhasil diambil',
        ]);
    }

    public function store(registerRequest $request)
    {
        $this->authorize('create', User::class);
        $user = User::create($request->validated());

        return response()->json([
            'message' => 'User registered successfully',
            'user' => new \App\Http\Resources\AuthMeResource($user),
        ], 201);
    }

    public function show(string $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('view', $user);

        return response()->json([
            'message' => 'User retrieved successfully',
            'user' => new \App\Http\Resources\AuthMeResource($user),
        ]);
    }

    public function update(UpdateRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        $user->update($request->validated());

        return response()->json([
            'message' => 'User updated successfully',
            'user' => new \App\Http\Resources\AuthMeResource($user),
        ]);
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }

    public function forceDelete(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $user);
        $user->forceDelete();

        return response()->json([
            'message' => 'User permanently deleted successfully',
        ]);
    }

    public function restore(string $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $user);
        $user->restore();

        return response()->json([
            'message' => 'User restored successfully',
            'user' => new \App\Http\Resources\AuthMeResource($user),
        ]);
    }

    public function trashed()
    {
        $this->authorize('viewAny', User::class);
        $trashedUsers = User::onlyTrashed()->paginate(10);

        return (new \App\Http\Resources\UserCollection($trashedUsers))->additional([
            'message' => 'user yang di hapus berhasil diambil',
        ]);
    }
}
