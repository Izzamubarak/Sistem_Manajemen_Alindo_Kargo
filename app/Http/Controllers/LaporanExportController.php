<?php

namespace App\Http\Controllers;

use App\Models\Data_paket;
use App\Models\Biaya_operasional;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanExportController extends Controller
{
    public function export(Request $request)
    {
        $bulan = $request->query('bulan', date('m'));
        $tahun = $request->query('tahun', date('Y'));

        $startDate = Carbon::createFromDate($tahun, $bulan)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan)->endOfMonth();

        $paketList = Data_paket::with(['vendors', 'creator'])
            ->where('status', 'Terkirim')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $biayaMap = Biaya_operasional::whereIn('resi', $paketList->pluck('resi'))
            ->get()
            ->keyBy('resi');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Resi');
        $sheet->setCellValue('B1', 'Deskripsi');
        $sheet->setCellValue('C1', 'Berat');
        $sheet->setCellValue('D1', 'Volume');
        $sheet->setCellValue('E1', 'Jumlah Koli');
        $sheet->setCellValue('F1', 'Kota Tujuan');
        $sheet->setCellValue('G1', 'Nama Penerima');
        $sheet->setCellValue('H1', 'No HP Penerima');
        $sheet->setCellValue('I1', 'Vendor');
        $sheet->setCellValue('J1', 'Pengirim');
        $sheet->setCellValue('K1', 'Total Vendor');
        $sheet->setCellValue('L1', 'Biaya Lainnya');
        $sheet->setCellValue('M1', 'Pengeluaran');
        $sheet->setCellValue('N1', 'Pendapatan');
        $sheet->setCellValue('O1', 'Tanggal');

        $row = 2;

        foreach ($paketList as $paket) {
            $biaya = $biayaMap[$paket->resi] ?? null;

            $total_vendor = $paket->vendors->sum(fn($v) => $v->pivot->biaya_vendor ?? 0);

            $biaya_lain = 0;
            if ($biaya) {
                if (is_string($biaya->biaya_lainnya)) {
                    $decoded = json_decode($biaya->biaya_lainnya, true);
                    $biaya_lain = is_array($decoded)
                        ? collect($decoded)->sum(fn($item) => $item['biaya'] ?? 0)
                        : 0;
                } elseif (is_array($biaya->biaya_lainnya)) {
                    $biaya_lain = collect($biaya->biaya_lainnya)->sum(fn($item) => $item['biaya'] ?? 0);
                }
            }

            $pengeluaran = $total_vendor + $biaya_lain;
            $pendapatan = ($paket->cost ?? 0) - $pengeluaran;

            $sheet->setCellValue('A' . $row, $paket->resi);
            $sheet->setCellValue('B' . $row, $paket->description);
            $sheet->setCellValue('C' . $row, $paket->weight);
            $sheet->setCellValue('D' . $row, $paket->volume);
            $sheet->setCellValue('E' . $row, $paket->jumlah_koli);
            $sheet->setCellValue('F' . $row, $paket->kota_tujuan);
            $sheet->setCellValue('G' . $row, $paket->penerima);
            $sheet->setCellValue('H' . $row, $paket->no_hp_penerima);
            $sheet->setCellValue('I' . $row, $paket->vendors->pluck('name')->implode(', ') ?: '-');
            $sheet->setCellValue('J' . $row, $paket->creator->name ?? '-');
            $sheet->setCellValue('K' . $row, $total_vendor);
            $sheet->setCellValue('L' . $row, $biaya_lain);
            $sheet->setCellValue('M' . $row, $pengeluaran);
            $sheet->setCellValue('N' . $row, $pendapatan);
            $sheet->setCellValue('O' . $row, $paket->created_at->format('d-m-Y'));

            $row++;
        }
        // Tambahkan border ke semua sel
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $lastRow = $row - 1;
        $sheet->getStyle("A1:O$lastRow")->applyFromArray($styleArray);

        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan_paket_terkirim_' . $bulan . '_' . $tahun . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
