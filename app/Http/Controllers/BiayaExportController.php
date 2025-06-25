<?php

namespace App\Http\Controllers;

use App\Models\Biaya_operasional;
use App\Models\Data_paket;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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
                $item->total_vendor = $paket->vendors->sum(function ($vendor) {
                    return $vendor->pivot->biaya_vendor ?? 0;
                });
                $item->total_paket = $paket->cost ?? 0;
            }

            $item->total_keseluruhan =
                ($item->total_vendor ?? 0) +
                ($item->total_paket ?? 0) +
                ($item->biaya_lainnya ?? 0);

            return $item;
        });

        // Mulai buat Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Resi');
        $sheet->setCellValue('B1', 'Vendor');
        $sheet->setCellValue('C1', 'Total Vendor');
        $sheet->setCellValue('D1', 'Total Paket');
        $sheet->setCellValue('E1', 'Biaya Lainnya');
        $sheet->setCellValue('F1', 'Total Keseluruhan');
        $sheet->setCellValue('G1', 'Tanggal');

        // Data isi
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->resi);
            $sheet->setCellValue('B' . $row, implode(', ', $item->vendor_names));
            $sheet->setCellValue('C' . $row, $item->total_vendor);
            $sheet->setCellValue('D' . $row, $item->total_paket);
            $sheet->setCellValue('E' . $row, $item->biaya_lainnya);
            $sheet->setCellValue('F' . $row, $item->total_keseluruhan);
            $sheet->setCellValue('G' . $row, Carbon::parse($item->created_at)->format('d-m-Y'));
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'biaya_operasional_terkini.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
