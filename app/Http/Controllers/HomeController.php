<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Data_paket;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil data real dari database
        $rawPesanan = Data_paket::where('status', 'Terkirim')
            ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as jumlah')
            ->whereNotNull('created_at')
            ->groupBy('bulan')
            ->pluck('jumlah', 'bulan')
            ->toArray();

        // Isi semua bulan dari 1–12, default 0 kalau tidak ada
        $pesananBulanan = Data_paket::where('status', 'Terkirim')
            ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as jumlah')
            ->whereNotNull('created_at')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->mapWithKeys(function ($item) {
                return [intval($item->bulan) => $item->jumlah];
            })
            ->toArray();
        // Kota Tujuan real-time
        $kotaTujuan = Data_paket::where('status', 'Terkirim')
            ->selectRaw('kota_tujuan, COUNT(*) as jumlah')
            ->whereNotNull('kota_tujuan')
            ->groupBy('kota_tujuan')
            ->pluck('jumlah', 'kota_tujuan')
            ->toArray();

        $totalPendapatan = \App\Models\Biaya_operasional::join('data_pakets', 'biaya_operasionals.resi', '=', 'data_pakets.resi')
            ->where('data_pakets.status', 'Terkirim')
            ->sum('biaya_operasionals.total_keseluruhan');

        $jumlahPaket = Data_paket::where('status', 'Terkirim')->count();

        return view('dashboard.home', compact(
            'pesananBulanan',
            'kotaTujuan',
            'totalPendapatan',
            'jumlahPaket'
        ));
    }
}
