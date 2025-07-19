<?php

namespace App\Http\Controllers;

use App\Models\Biaya_operasional;
use App\Models\Data_paket;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use Carbon\Carbon;

class BiayaExportController extends Controller
{
    public function export()
    {
        $biayas = Biaya_operasional::all();
        $resis = $biayas->pluck('resi')->filter();
        $pakets = Data_paket::with('vendors')->whereIn('resi', $resis)->get()->keyBy('resi');

        $data = $biayas->map(function ($item) use ($pakets) {
            $item->vendor_names = [];
            $item->total_vendor = 0;
            $item->total_paket = 0;

            $paket = $pakets[$item->resi] ?? null;
            if ($paket) {
                $item->vendor_names = $paket->vendors->pluck('name')->toArray();
                $item->total_vendor = $paket->vendors->sum(fn($vendor) => $vendor->pivot->biaya_vendor ?? 0);
                $item->total_paket = $paket->cost ?? 0;
            }

            $biayaLainnya = is_array($item->biaya_lainnya)
                ? $item->biaya_lainnya
                : json_decode($item->biaya_lainnya, true);

            $item->total_biaya_lainnya = is_array($biayaLainnya)
                ? collect($biayaLainnya)->sum(fn($b) => $b['jumlah'] ?? 0)
                : 0;

            $item->pengeluaran = $item->total_vendor + $item->total_biaya_lainnya;
            $item->pendapatan = $item->total_paket - $item->pengeluaran;

            return $item;
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Resi');
        $sheet->setCellValue('B1', 'Vendor');
        $sheet->setCellValue('C1', 'Total Vendor');
        $sheet->setCellValue('D1', 'Total Paket');
        $sheet->setCellValue('E1', 'Biaya Lainnya');
        $sheet->setCellValue('F1', 'Pengeluaran');
        $sheet->setCellValue('G1', 'Pendapatan');
        $sheet->setCellValue('H1', 'Tanggal');

        // Data
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->resi);
            $sheet->setCellValue('B' . $row, implode(', ', $item->vendor_names));
            $sheet->setCellValue('C' . $row, $item->total_vendor);
            $sheet->setCellValue('D' . $row, $item->total_paket);
            $sheet->setCellValue('E' . $row, $item->total_biaya_lainnya);
            $sheet->setCellValue('F' . $row, $item->pengeluaran);
            $sheet->setCellValue('G' . $row, $item->pendapatan);
            $sheet->setCellValue('H' . $row, Carbon::parse($item->created_at)->format('d-m-Y'));
            $row++;
        }

        // Tambahkan border ke seluruh sel
        $lastRow = $row - 1;
        $sheet->getStyle("A1:H$lastRow")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        $writer = new Xlsx($spreadsheet);
        $filename = 'biaya_operasional_terkini.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
