<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\registerRequest;
use App\Http\Requests\UpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


class UserController extends Controller
{
    /**
     * Cek ketersediaan nama & email secara real-time saat registrasi
     */
    public function checkAvailability(Request $request)
    {
        $name = $request->input('name') ? preg_replace('/\s+/', ' ', trim($request->input('name'))) : null;
        $email = $request->input('email') ? strtolower(trim($request->input('email'))) : null;

        $nameExists = $name ? User::where('name', $name)->exists() : false;
        $emailExists = $email ? User::where('email', $email)->exists() : false;

        return response()->json([
            'name_taken'  => $nameExists,
            'email_taken' => $emailExists,
        ]);
    }
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
        $data = $request->only(['name', 'email', 'password']);

        // Paksa ke role terendah ('kasir') saat registrasi mandiri
        $data['role'] = 'kasir';

        if ($request->hasFile('avatar')) {
            if (!Schema::hasColumn('users', 'avatar')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('avatar')->nullable()->after('role');
                });
            }

            $file = $request->file('avatar');
            $filename = 'avatar_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('storage/avatars');

            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $filename);
            $data['avatar'] = 'avatars/' . $filename;
        }

        $user = User::create($data);
        $token = $user->createToken('auth_token', ['*'], now()->addWeeks(1))->plainTextToken;

        return response()->json([
            'message' => 'Pendaftaran akun berhasil.',
            'access_token' => $token,
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
