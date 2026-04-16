<?php

namespace App\Http\Controllers;

use App\Models\Data_paket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadBuktiController extends Controller
{
    public function showForm($token)
    {
        $paket = Data_paket::where('upload_token', $token)->first();

        if (!$paket) {
            return view('upload-bukti.error');
        }

        return view('upload-bukti.form', compact('token', 'paket'));
    }

    public function upload(Request $request, $token)
    {
        $paket = Data_paket::where('upload_token', $token)->first();

        if (!$paket) {
            return view('upload-bukti.error');
        }

        $request->validate([
            'bukti_pengiriman' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('bukti_pengiriman')) {
            $file = $request->file('bukti_pengiriman');
            // Store file in storage/app/public/bukti_pengiriman
            $path = $file->store('bukti_pengiriman', 'public');
            
            $paket->update([
                'bukti_pengiriman' => $path,
                'upload_token' => null, // Invalidate token
            ]);

            return view('upload-bukti.success');
        }

        return back()->withErrors(['bukti_pengiriman' => 'Gagal mengupload file']);
    }
}
