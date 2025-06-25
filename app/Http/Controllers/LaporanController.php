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
            $paket->total_keseluruhan = $biayaMap[$paket->resi]->total_keseluruhan ?? 0;
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
