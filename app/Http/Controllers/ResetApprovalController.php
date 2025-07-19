<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\PasswordResetRequest;
use App\Models\User;

class ResetApprovalController extends Controller
{
    public function approve($id)
    {
        $user = User::findOrFail($id);

        // Cek apakah ada request sebelumnya yang belum digunakan
        $existing = PasswordResetRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$existing) {
            return redirect()->back()->withErrors(['msg' => 'Tidak ada permintaan reset yang aktif.']);
        }

        // Update status menjadi approved & simpan token jika belum ada
        $existing->update([
            'status' => 'approved',
            'token' => $existing->token ?? Str::random(60),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('status', 'Permintaan disetujui dan link telah digenerate.');
    }
}
