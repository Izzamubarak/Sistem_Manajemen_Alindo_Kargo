<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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

        $admins = User::where('role', 'tim-operasional')->get();
        return response()->json($admins);
    }

    public function updateTim(Request $request)
    {
        $admin = $request->user();

        if ($admin->role !== 'super-admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name'     => 'sometimes|string',
            'username' => 'sometimes|string|unique:users,username,' . $admin->id,
            'email'    => 'sometimes|email|unique:users,email,' . $admin->id,
            'password' => 'sometimes|string|min:6',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $admin->update($validated);
        return response()->json($admin);
    }


    public function destroyTim($id)
    {
        $admin = User::where('role', 'tim-operasional')->findOrFail($id);
        $admin->delete();

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

        $admins = User::where('role', 'admin')->get();
        return response()->json($admins);
    }

    public function updateAdmin(Request $request)
    {
        $admin = $request->user();

        if ($admin->role !== 'super-admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name'     => 'sometimes|string',
            'username' => 'sometimes|string|unique:users,username,' . $admin->id,
            'email'    => 'sometimes|email|unique:users,email,' . $admin->id,
            'password' => 'sometimes|string|min:6',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $admin->update($validated);
        return response()->json($admin);
    }


    public function destroyAdmin($id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        $admin->delete();

        return response()->json(null, 204);
    }
}
