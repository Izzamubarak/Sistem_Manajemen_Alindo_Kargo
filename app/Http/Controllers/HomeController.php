<?php

namespace App\Http\Controllers;

use App\Models\Data_paket;
use App\Models\Biaya_operasional;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil semua data biaya
        $biayas = Biaya_operasional::all();

        // Ambil semua resi dari biaya dan cari data paket sesuai
        $resis = $biayas->pluck('resi')->filter();
        $pakets = Data_paket::with('vendors')->whereIn('resi', $resis)->get()->keyBy('resi');

        $totalPendapatan = 0;
        $totalPengeluaran = 0;

        foreach ($biayas as $item) {
            $total_vendor = 0;
            $total_paket = 0;

            // Cek apakah resi punya paket terkait
            $paket = $pakets[$item->resi] ?? null;
            if ($paket) {
                // Total biaya vendor dari relasi pivot
                $total_vendor = $paket->vendors->sum(function ($vendor) {
                    return $vendor->pivot->biaya_vendor ?? 0;
                });

                // Biaya pengiriman asli
                $total_paket = $paket->cost ?? 0;
            }

            // Biaya lainnya dijumlahkan dari setiap nilai
            $totalBiayaLain = 0;
            if (is_array($item->biaya_lainnya)) {
                foreach ($item->biaya_lainnya as $namaBiaya => $nilai) {
                    $totalBiayaLain += floatval($nilai);
                }
            }

            // Hitung pengeluaran & pendapatan
            $pengeluaran = $total_vendor + $totalBiayaLain;
            $pendapatan = $total_paket - $pengeluaran;

            // Tambahkan ke total
            $totalPengeluaran += $pengeluaran;
            $totalPendapatan += $pendapatan;
        }

        // Hitung paket terkirim
        $paketsTerkirim = Data_paket::where('status', 'Terkirim')->get();

        // Hitung pesanan bulanan
        $pesananBulanan = Data_paket::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'bulan')
            ->toArray();

        // Hitung distribusi kota tujuan
        $kotaTujuan = Data_paket::selectRaw('kota_tujuan, COUNT(*) as total')
            ->groupBy('kota_tujuan')
            ->pluck('total', 'kota_tujuan')
            ->toArray();

        // Kirim data ke view
        return view('dashboard.home', [
            'totalPendapatan'   => $totalPendapatan,
            'totalPengeluaran'  => $totalPengeluaran,
            'jumlahPaket'       => $paketsTerkirim->count(),
            'pesananBulanan'    => $pesananBulanan,
            'kotaTujuan'        => $kotaTujuan
        ]);
    }
}
