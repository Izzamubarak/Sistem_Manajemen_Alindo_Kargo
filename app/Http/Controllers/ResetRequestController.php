<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PasswordResetRequest;

class ResetRequestController extends Controller
{
    public function showForm()
    {
        return view('auth.forgot-password');
    }

    public function submitRequest(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        if ($user->role === 'super-admin') {
            $token = Str::random(60);
            return redirect()->route('reset.form', ['token' => $token, 'email' => $user->email]);
        }

        $token = Str::random(60);
        PasswordResetRequest::create([
            'user_id' => $user->id,
            'token' => $token,
            'status' => 'pending',
        ]);

        return back()->with('status', 'Permintaan reset password terkirim dan menunggu persetujuan superadmin.');
    }

    public function showResetForm($token, Request $request)
    {
        $requestRecord = PasswordResetRequest::where('token', $token)
            ->where('status', 'approved')
            ->first();

        if (!$requestRecord) {
            abort(403, 'Token tidak valid atau belum disetujui.');
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        $resetRequest = PasswordResetRequest::where('token', $request->token)
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->first();

        if (!$resetRequest) {
            return redirect()->back()->withErrors(['token' => 'Token tidak valid atau sudah dipakai.']);
        }

        // Ubah password
        $user->password = bcrypt($request->password);
        $user->save();

        $resetRequest->status = 'used';
        $resetRequest->used_at = now();
        $resetRequest->save();

        return redirect('/login')->with('status', 'Password berhasil direset!');
    }
}
