<?php

namespace App\Http\Controllers;

use App\Models\Data_paket;
use App\Models\Biaya_operasional;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $totalPendapatan = Biaya_operasional::where('jenis_biaya', 'Pendapatan')->sum('nominal');

        $totalPengeluaran = 0;
        $biayaList = Biaya_operasional::where('jenis_biaya', 'Pengeluaran')->get();
        foreach ($biayaList as $biaya) {
            $totalVendor = $biaya->total_vendor ?? 0;
            $biayaLainnya = is_array($biaya->biaya_lainnya) ? collect($biaya->biaya_lainnya)->sum('nominal') : 0;
            $totalPengeluaran += $totalVendor + $biayaLainnya;
        }

        $pakets = Data_paket::where('status', 'Terkirim')->get();

        $pesananBulanan = Data_paket::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'bulan')
            ->toArray();

        $kotaTujuan = Data_paket::selectRaw('kota_tujuan, COUNT(*) as total')
            ->groupBy('kota_tujuan')
            ->pluck('total', 'kota_tujuan')
            ->toArray();

        return view('dashboard.home', [
            'totalPendapatan' => $totalPendapatan,
            'totalPengeluaran' => $totalPengeluaran,
            'jumlahPaket' => $pakets->count(),
            'pesananBulanan' => $pesananBulanan,
            'kotaTujuan' => $kotaTujuan
        ]);
    }
}
