<?php

namespace App\Http\Controllers;

use App\Models\Biaya_operasional;
use App\Models\Data_paket;
use Illuminate\Http\Request;

class Biaya_operasionalController extends Controller
{
    public function index()
    {
        $biayas = Biaya_operasional::all();
        $resis = $biayas->pluck('resi')->filter();
        $pakets = Data_paket::with('vendors')->whereIn('resi', $resis)->get()->keyBy('resi');

        $data = $biayas->map(function ($item) use ($pakets) {
            $item->vendor_names = [];
            $item->total_vendor = 0;
            $item->total_paket = 0;
            $item->total_pengeluaran = 0;
            $item->pendapatan = 0;

            $paket = $pakets[$item->resi] ?? null;

            if ($paket) {
                $item->vendor_names = $paket->vendors->pluck('name')->toArray();

                // Hitung total biaya vendor dari pivot
                $item->total_vendor = $paket->vendors->sum(function ($vendor) {
                    return $vendor->pivot->biaya_vendor ?? 0;
                });

                // Biaya kirim asli dari kolom cost
                $item->total_paket = $paket->cost ?? 0;
            }

            // Hitung biaya lainnya
            $totalBiayaLain = 0;
            if (is_array($item->biaya_lainnya)) {
                foreach ($item->biaya_lainnya as $biaya) {
                    $totalBiayaLain += floatval($biaya);
                }
            }

            // Pengeluaran = vendor + biaya lainnya
            $item->total_pengeluaran = $item->total_vendor + $totalBiayaLain;

            // Pendapatan = biaya kirim paket - pengeluaran
            $item->pendapatan = $item->total_paket - $item->total_pengeluaran;

            return $item;
        });

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'resi'            => 'nullable|string|max:255',
            'total_vendor'    => 'nullable|numeric',
            'total_paket'     => 'nullable|numeric',
            'biaya_lainnya'   => 'nullable|array',
            'created_by'      => 'required|exists:users,id',
        ]);

        $data = Biaya_operasional::create($validated);
        return response()->json($data, 201);
    }

    public function show($id)
    {
        $item = Biaya_operasional::findOrFail($id);
        $item->vendor_names = [];
        $item->total_vendor = 0;
        $item->total_paket = 0;
        $item->total_pengeluaran = 0;
        $item->pendapatan = 0;

        $paket = Data_paket::with('vendors')->where('resi', $item->resi)->first();

        if ($paket && $paket->vendors) {
            $item->vendor_names = $paket->vendors->pluck('name')->toArray();

            $item->total_vendor = $paket->vendors->sum(function ($vendor) {
                return $vendor->pivot->biaya_vendor ?? 0;
            });

            $item->total_paket = $paket->cost ?? 0;
        }

        $totalBiayaLain = 0;
        if (is_array($item->biaya_lainnya)) {
            foreach ($item->biaya_lainnya as $biaya) {
                $totalBiayaLain += floatval($biaya);
            }
        }

        $item->total_pengeluaran = $item->total_vendor + $totalBiayaLain;
        $item->pendapatan = $item->total_paket - $item->total_pengeluaran;

        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $biaya = Biaya_operasional::findOrFail($id);

        $validated = $request->validate([
            'resi'            => 'nullable|string|max:255',
            'total_vendor'    => 'nullable|numeric',
            'total_paket'     => 'nullable|numeric',
            'biaya_lainnya'   => 'nullable|array',
            'created_by'      => 'required|exists:users,id',
        ]);

        $biaya->update($validated);

        // Update status paket menjadi "Terkirim" jika biaya lainnya terisi
        if (!empty($validated['biaya_lainnya'])) {
            $paket = Data_paket::where('resi', $biaya->resi)->first();
            if ($paket) {
                $paket->status = 'Terkirim';
                $paket->save();
            }
        }

        return response()->json($biaya);
    }

    public function destroy($id)
    {
        Biaya_operasional::destroy($id);
        return response()->json(null, 204);
    }
}
