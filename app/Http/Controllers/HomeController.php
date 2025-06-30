<?php

namespace App\Http\Controllers;

use App\Models\Data_paket;
use App\Models\Biaya_operasional;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $pakets = Data_paket::where('status', 'Terkirim')->get();
        $biayaList = Biaya_operasional::all();

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
        $pesananBulanan = Data_paket::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'bulan')->toArray();

        $kotaTujuan = Data_paket::selectRaw('kota_tujuan, COUNT(*) as total')
            ->groupBy('kota_tujuan')
            ->pluck('total', 'kota_tujuan')
            ->toArray();

        return view('dashboard.home', [
            'totalPendapatan' => $totalPendapatan,
            'totalPengeluaran' => $totalPengeluaran,
            'jumlahPaket' => $pakets->count(),
            'pesananBulanan' => $pesananBulanan,
            'kotaTujuan' => $kotaTujuan,
        ]);
    }
}
