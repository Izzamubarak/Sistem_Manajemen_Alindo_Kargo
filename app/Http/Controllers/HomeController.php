<?php

namespace App\Http\Controllers;

use App\Models\Data_paket;
use App\Models\Biaya_operasional;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $pakets = Paket::where('status', 'Terkirim')->get();
        $biayaList = BiayaOperasional::all();

        $totalPendapatan = 0;
        $totalPengeluaran = 0;

        foreach ($pakets as $paket) {
            $biaya = $biayaList->where('resi', $paket->resi)->first();
            $totalVendor = $biaya->total_vendor ?? 0;
            $biayaLainnya = is_array($biaya->biaya_lainnya) ? collect($biaya->biaya_lainnya)->sum('nominal') : 0;
            $pengeluaran = $totalVendor + $biayaLainnya;

            $totalPendapatan += $paket->cost;
            $totalPengeluaran += $pengeluaran;
        }

        // Tambahkan ini:
        $pesananBulanan = Paket::selectRaw('MONTH(tanggal_kirim) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_kirim', Carbon::now()->year)
            ->groupByRaw('MONTH(tanggal_kirim)')
            ->pluck('total', 'bulan')
            ->toArray();

        return view('dashboard.home', [
            'totalPendapatan' => $totalPendapatan,
            'totalPengeluaran' => $totalPengeluaran,
            'jumlahPaket' => $pakets->count(),
            'pesananBulanan' => $pesananBulanan, // ✅ kirim ke view
        ]);
    }
}
