<?php

namespace App\Http\Controllers;

use App\Models\Data_paket;
use App\Models\Biaya_operasional;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil hanya paket yang statusnya 'Terkirim'
        $paketsTerkirim = Data_paket::with('vendors')
            ->where('status', 'Terkirim')
            ->get()
            ->keyBy('resi');

        // Ambil hanya biaya operasional yang terkait dengan paket terkirim
        $biayas = Biaya_operasional::whereIn('resi', $paketsTerkirim->keys())->get();

        $totalPendapatan = 0;
        $totalPengeluaran = 0;

        foreach ($biayas as $item) {
            $total_vendor = 0;
            $total_paket = 0;

            $paket = $paketsTerkirim[$item->resi] ?? null;
            if ($paket) {
                $total_vendor = $paket->vendors->sum(fn($vendor) => $vendor->pivot->biaya_vendor ?? 0);
                $total_paket = $paket->cost ?? 0;
            }

            $totalBiayaLain = 0;
            if (is_array($item->biaya_lainnya)) {
                foreach ($item->biaya_lainnya as $biaya) {
                    if (is_array($biaya) && isset($biaya['biaya'])) {
                        $totalBiayaLain += floatval($biaya['biaya']);
                    } elseif (is_numeric($biaya)) {
                        $totalBiayaLain += floatval($biaya);
                    }
                }
            }

            $pengeluaran = $total_vendor + $totalBiayaLain;
            $pendapatan = $total_paket - $pengeluaran;

            $totalPengeluaran += $pengeluaran;
            $totalPendapatan += $pendapatan;
        }

        // Untuk grafik
        $pesananBulanan = Data_paket::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', 'Terkirim')
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'bulan')
            ->toArray();

        $kotaTujuan = Data_paket::selectRaw('kota_tujuan, COUNT(*) as total')
            ->where('status', 'Terkirim')
            ->groupBy('kota_tujuan')
            ->pluck('total', 'kota_tujuan')
            ->toArray();

        return view('dashboard.home', [
            'totalPendapatan'   => $totalPendapatan,
            'totalPengeluaran'  => $totalPengeluaran,
            'jumlahPaket'       => $paketsTerkirim->count(),
            'pesananBulanan'    => $pesananBulanan,
            'kotaTujuan'        => $kotaTujuan
        ]);
    }
}
