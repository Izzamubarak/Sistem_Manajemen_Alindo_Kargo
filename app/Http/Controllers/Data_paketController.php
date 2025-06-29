<?php

namespace App\Http\Controllers;

use App\Models\Data_paket;
use App\Models\Biaya_operasional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class Data_paketController extends Controller
{
    public function index()
    {
        $data = Data_paket::with(['vendors', 'creator'])->get();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_ids'       => 'nullable|array',
            'vendor_ids.*'     => 'exists:vendors,id',
            'vendor_biayas'    => 'nullable|array',
            'vendor_biayas.*'  => 'nullable|numeric|min:0',
            'resi'             => 'required|string|unique:data_pakets,resi',
            'description'      => 'sometimes|string',
            'kota_asal'        => 'sometimes|string',
            'kota_tujuan'      => 'sometimes|string',
            'no_hp_pengirim'   => 'nullable|string',
            'penerima'         => 'nullable|string',
            'no_hp_penerima'   => 'nullable|string',
            'alamat_penerima'  => 'nullable|string',
            'weight'           => 'sometimes|numeric',
            'jumlah_koli'      => 'sometimes|numeric',
            'volume'           => 'sometimes|numeric',
            'cost'             => 'sometimes|numeric',
            'created_by'       => 'sometimes|exists:users,id',
            'status'           => 'sometimes|string|in:Terkirim,Dalam Proses,Gagal',
        ]);

        $validated['status'] = $validated['status'] ?? 'Dalam Proses';

        $paket = Data_paket::create($validated);

        // Attach vendor dan biaya_vendor
        if ($request->has('vendor_ids') && is_array($request->vendor_ids)) {
            $vendorData = [];
            foreach ($request->vendor_ids as $id) {
                $id = (string) $id;
                $biaya = $request->vendor_biayas[$id] ?? 0;
                $vendorData[$id] = ['biaya_vendor' => $biaya];
            }
            $paket->vendors()->attach($vendorData);
        }


        // Hitung total biaya vendor & paket
        $totalVendor = collect($vendorData)->pluck('biaya_vendor')->sum();
        $totalPaket = $validated['cost'] ?? 0;

        // Simpan ke biaya operasional
        Biaya_operasional::create([
            'resi'               => $paket->resi,
            'total_vendor'       => $totalVendor,
            'total_paket'        => $totalPaket,
            'biaya_lainnya'      => 0,
            'total_keseluruhan'  => $totalVendor + $totalPaket,
            'created_by'         => $validated['created_by'] ?? Auth::id(),
        ]);


        return response()->json($paket->load(['vendors', 'creator']), 201);
    }

    public function show($id)
    {
        $paket = Data_paket::with(['invoice', 'vendors', 'creator'])->findOrFail($id);
        return response()->json($paket);
    }

    public function update(Request $request, $id)
    {
        $Data_paket = Data_paket::findOrFail($id);

        $validated = $request->validate([
            'vendor_ids'       => 'sometimes|array',
            'vendor_ids.*'     => 'exists:vendors,id',
            'vendor_biayas.*'  => 'nullable|numeric',
            'description'      => 'sometimes|string',
            'weight'           => 'sometimes|numeric',
            'cost'             => 'sometimes|numeric',
            'created_by'       => 'sometimes|exists:users,id',
            'status'           => 'sometimes|string|in:Terkirim,Dalam Proses,Gagal',
        ]);

        $Data_paket->update($validated);

        if ($request->has('vendor_ids')) {
            $vendorData = collect($request->vendor_ids)->mapWithKeys(function ($id) use ($request) {
                return [$id => ['biaya_vendor' => $request->vendor_biayas[$id] ?? 0]];
            });
            $Data_paket->vendors()->sync($vendorData);
        }

        return response()->json($Data_paket->load(['vendors', 'creator']));
    }

    public function destroy($id)
    {
        Data_paket::destroy($id);
        return response()->json(null, 204);
    }
}
