<?php

namespace App\Http\Controllers;

use App\Models\Data_paket;
use App\Models\Biaya_operasional;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->query('bulan', date('m'));
        $tahun = $request->query('tahun', date('Y'));

        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        $paketList = Data_paket::with(['vendors', 'creator'])
            ->where('status', 'Terkirim')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Ambil biaya operasional berdasarkan resi
        $biayaMap = Biaya_operasional::whereIn('resi', $paketList->pluck('resi'))
            ->get()
            ->keyBy('resi');

        // Gabungkan biaya operasional ke paket
        $data = $paketList->map(function ($paket) use ($biayaMap) {
            $biaya = $biayaMap[$paket->resi] ?? null;

            $paket->total_vendor = $biaya->total_vendor ?? 0;
            $paket->biaya_lainnya = is_array($biaya->biaya_lainnya)
                ? collect($biaya->biaya_lainnya)->sum('biaya')
                : ($biaya->biaya_lainnya ?? 0);
            $paket->pengeluaran = $paket->total_vendor + $paket->biaya_lainnya;
            $paket->pendapatan = $paket->cost - $paket->pengeluaran;

            return $paket;
        });

        return view('pages.laporan', [
            'data' => $data,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'start' => $startDate->toFormattedDateString(),
            'end' => $endDate->toFormattedDateString(),
        ]);
    }
}
