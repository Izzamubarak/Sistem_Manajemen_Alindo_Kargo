<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PasswordResetRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // ===================== //
    //     SEMUA PENGGUNA    //
    // ===================== //

    public function index()
    {
        return response()->json(User::all());
    }

    public function show($id)
    {
        return response()->json(User::findOrFail($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:super-admin,admin,operasional',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'sometimes|string',
            'email'    => 'sometimes|email|unique:users,email,' . $user->id,
            'username' => 'sometimes|string|unique:users,username,' . $user->id,
            'password' => 'sometimes|string|min:6',
            'role'     => 'sometimes|in:super-admin,admin,operasional',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(null, 204);
    }

    // ===================== //
    //     TIM OPERASIONAL   //
    // ===================== //

    public function showTim(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'super-admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $tim = User::where('role', 'tim-operasional')->get()->map(function ($timUser) {
            $reset = PasswordResetRequest::where('user_id', $timUser->id)
                ->whereIn('status', ['pending', 'approved']) // hanya tampilkan yang aktif
                ->latest()
                ->first();

            return [
                'id' => $timUser->id,
                'name' => $timUser->name,
                'email' => $timUser->email,
                'username' => $timUser->username,
                'created_at' => $timUser->created_at,
                'reset_status' => $reset?->status,
                'reset_token'  => $reset?->token,
                'has_requested_reset' => $reset !== null,
            ];
        });

        return response()->json($tim);
    }

    public function updateTim(Request $request)
    {
        $current = $request->user();

        if ($current->role !== 'super-admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $target = User::where('role', 'tim-operasional')
            ->where('username', $request->username)
            ->firstOrFail();

        $validated = $request->validate([
            'name'     => 'sometimes|string',
            'email'    => 'sometimes|email|unique:users,email,' . $target->id,
            'username' => 'sometimes|string|unique:users,username,' . $target->id,
            'password' => 'sometimes|string|min:6',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $target->update($validated);

        return response()->json($target);
    }

    public function destroyTim($id)
    {
        $tim = User::where('role', 'tim-operasional')->findOrFail($id);
        $tim->delete();

        return response()->json(null, 204);
    }

    // ===================== //
    //      PROFILE ADMIN    //
    // ===================== //

    public function showAdmin(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'super-admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $admins = User::where('role', 'admin')->get()->map(function ($admin) {
            $reset = PasswordResetRequest::where('user_id', $admin->id)
                ->whereIn('status', ['pending', 'approved'])
                ->whereNull('used_at') // hanya yang belum dipakai
                ->latest()
                ->first();

            return [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'username' => $admin->username,
                'created_at' => $admin->created_at,
                'reset_status' => $reset?->status,
                'reset_token'  => $reset?->token,
                'has_requested_reset' => $reset !== null,
            ];
        });

        return response()->json($admins);
    }

    public function updateAdmin(Request $request)
    {
        $current = $request->user();

        if ($current->role !== 'super-admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Username/email harus dikirim untuk tahu siapa yang ingin diupdate
        $target = User::where('role', 'admin')
            ->where('username', $request->username) // atau pakai email jika kamu lebih suka
            ->firstOrFail();

        $validated = $request->validate([
            'name'     => 'sometimes|string',
            'email'    => 'sometimes|email|unique:users,email,' . $target->id,
            'username' => 'sometimes|string|unique:users,username,' . $target->id,
            'password' => 'sometimes|string|min:6',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $target->update($validated);

        return response()->json($target);
    }



    public function destroyAdmin($id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        $admin->delete();

        return response()->json(null, 204);
    }
}
