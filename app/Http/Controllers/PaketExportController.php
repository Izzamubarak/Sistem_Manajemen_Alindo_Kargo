<?php

namespace App\Http\Controllers;

use App\Models\Data_paket;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class PaketExportController extends Controller
{
    public function export()
    {
        $data = Data_paket::select('resi', 'description', 'weight', 'kota_asal', 'kota_tujuan', 'cost', 'created_at', 'status')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set Header Kolom
        $sheet->setCellValue('A1', 'Resi');
        $sheet->setCellValue('B1', 'Deskripsi');
        $sheet->setCellValue('C1', 'Berat (kg)');
        $sheet->setCellValue('D1', 'Kota Asal');
        $sheet->setCellValue('E1', 'Kota Tujuan');
        $sheet->setCellValue('F1', 'Biaya (Rp)');
        $sheet->setCellValue('G1', 'Tanggal');
        $sheet->setCellValue('H1', 'Status');

        // Isi data dimulai dari baris ke-2
        $row = 2;
        foreach ($data as $paket) {
            $sheet->setCellValue('A' . $row, $paket->resi);
            $sheet->setCellValue('B' . $row, $paket->description);
            $sheet->setCellValue('C' . $row, $paket->weight);
            $sheet->setCellValue('D' . $row, $paket->kota_asal);
            $sheet->setCellValue('E' . $row, $paket->kota_tujuan);
            $sheet->setCellValue('F' . $row, $paket->cost);
            $sheet->setCellValue('G' . $row, $paket->created_at->format('d-m-Y'));
            $sheet->setCellValue('H' . $row, $paket->status);
            $row++;
        }

        // Simpan file ke storage sementara
        $writer = new Xlsx($spreadsheet);
        $filename = 'data_paket.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        // Kirim file ke browser
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
