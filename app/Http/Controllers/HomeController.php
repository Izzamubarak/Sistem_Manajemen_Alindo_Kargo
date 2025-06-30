<?php

namespace App\Http\Controllers;

use App\Models\Data_paket;
use App\Models\Biaya_operasional;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        $pakets = Data_paket::with('vendors')
            ->where('status', 'Terkirim')
            ->whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni)
            ->get();

        $resis = $pakets->pluck('resi');
        $biayas = Biaya_operasional::whereIn('resi', $resis)->get()->keyBy('resi');

        $totalPengeluaran = 0;
        $totalPendapatan = 0;

        foreach ($pakets as $paket) {
            $vendorCost = $paket->vendors->sum('pivot.biaya_vendor') ?? 0;
            $biayaLainnya = 0;

            if (isset($biayas[$paket->resi])) {
                $biayaData = $biayas[$paket->resi]->biaya_lainnya;
                if (is_array($biayaData)) {
                    $biayaLainnya = collect($biayaData)->sum('biaya');
                }
            }

            $pengeluaran = $vendorCost + $biayaLainnya;
            $totalPengeluaran += $pengeluaran;

            $pendapatan = $paket->cost - $pengeluaran;
            $totalPendapatan += $pendapatan;
        }

        $jumlahPaket = $pakets->count();

        return view('pages.dashboard', [
            'totalPendapatan' => $totalPendapatan,
            'totalPengeluaran' => $totalPengeluaran,
            'jumlahPaket' => $jumlahPaket
        ]);
    }
}
