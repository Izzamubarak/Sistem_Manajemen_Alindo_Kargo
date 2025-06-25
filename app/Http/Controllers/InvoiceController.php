<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Data_paket;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['vendor', 'package', 'creator'])->get();
        return response()->json($invoices);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'package_id' => 'nullable|exists:data_pakets,id',
            'invoice_number' => 'required|string|unique:invoices',
            'status' => 'in:submitted,reviewed,approved',
            'total_amount' => 'required|numeric',
            'created_by' => 'required|exists:users,id',
        ]);

        $invoice = Invoice::create($validated);
        return response()->json($invoice, 201);
    }

    public function show($id)
    {
        $invoice = Invoice::with(['vendor', 'package', 'creator'])->findOrFail($id);
        return response()->json($invoice);
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'vendor_id' => 'sometimes|exists:vendors,id',
            'package_id' => 'nullable|exists:data_pakets,id',
            'invoice_number' => 'sometimes|string|unique:invoices,invoice_number,' . $invoice->id,
            'status' => 'sometimes|in:submitted,reviewed,approved',
            'total_amount' => 'sometimes|numeric',
            'created_by' => 'sometimes|exists:users,id',
        ]);

        $invoice->update($validated);
        return response()->json($invoice);
    }


    public function showByPaket($packageId)
    {
        $invoice = Invoice::with(['vendor', 'package', 'creator'])
            ->where('package_id', $packageId)
            ->firstOrFail();

        return response()->json($invoice);
    }


    public function download(Request $request)
    {
        if ($request->has('package_id')) {
            $paket = Data_paket::with('vendors')->findOrFail($request->package_id);

            $data = [
                'resi' => $paket->resi,
                'description' => $paket->description,
                'kota_asal' => $paket->kota_asal,
                'kota_tujuan' => $paket->kota_tujuan,
                'no_hp_pengirim' => $paket->no_hp_pengirim,
                'penerima' => $paket->penerima,
                'no_hp_penerima' => $paket->no_hp_penerima,
                'alamat_penerima' => $paket->alamat_penerima,
                'weight' => $paket->weight,
                'jumlah_koli' => $paket->jumlah_koli,
                'volume' => $paket->volume,
                'cost' => $paket->cost,
                'created_by' => $paket->created_by,
                'vendors' => $paket->vendors,
            ];
        } else {
            $data = $request->all();
            
            // Proses data vendor dari form
            $vendorIds = $request->input('vendor_ids', []);
            $vendorBiayas = $request->input('vendor_biayas', []);
            
            // Ambil data vendor dari database
            $vendors = [];
            foreach ($vendorIds as $index => $vendorId) {
                $vendor = \App\Models\Vendor::find($vendorId);
                if ($vendor) {
                    $vendor->pivot = new \stdClass();
                    $vendor->pivot->biaya_vendor = floatval($vendorBiayas[$index] ?? 0);
                    $vendors[] = $vendor;
                }
            }
            $data['vendors'] = $vendors;
        }

        $totalBiayaVendor = 0;
        $vendorRows = '';
        $totalBiaya = floatval($data['cost'] ?? 0); // Inisialisasi total dengan biaya paket

        foreach ($data['vendors'] as $vendor) {
            $biaya = floatval($vendor->pivot->biaya_vendor ?? 0);
            $vendorRows .= "<tr><td>{$vendor->name}</td><td>Rp " . number_format($biaya, 0, ',', '.') . "</td></tr>";
            $totalBiayaVendor += $biaya;
        }
        
        $totalBiaya += $totalBiayaVendor; // Tambahkan biaya vendor ke total

        $html = '
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            margin-bottom: 10px;
        }
        .header h2 {
            margin: 5px 0;
            color: #e74c3c;
        }
        .header p {
            margin: 0;
        }

        .main-table {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .main-table td, .main-table th {
            border: 1px solid #000;
            padding: 6px 10px;
        }

        .section-title {
            background-color: #2c3e50;
            color: white;
            padding: 4px 8px;
            margin-top: 10px;
        }

        .checkbox-group {
            display: flex;
            gap: 20px;
            margin-bottom: 8px;
        }

        .footer {
            margin-top: 20px;
            font-style: italic;
            text-align: center;
        }
    </style>

    <div class="header">
        <h2>PT. ALIF LOGISTIK INDONESIA</h2>
        <p>Jl. Nogosaren Baru No.60A Nogotirto, Gamping Sleman, Jogjakarta</p>
        <p>Email: aliflogistikindonesia@gmail.com</p>
    </div>

    <div class="section-title">SURAT TANDA TERIMA</div>
    <table class="main-table">
        <tr>
            <td><strong>Nomor Resi:</strong> ' . ($data['resi'] ?? '-') . '</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="checkbox-group">
                    <span>[ ] Darat</span>
                    <span>[ ] Laut</span>
                    <span>[ ] Udara</span>
                </div>
                <div class="checkbox-group">
                    <span>[ ] Port to Port</span>
                    <span>[ ] Door to Door</span>
                </div>
            </td>
            <td><strong>Pengirim:</strong></td>
            <td>User ID ' . ($data['created_by'] ?? '-') . '</td>
        </tr>
        <tr>
            <td><strong>Kota Asal:</strong></td>
            <td>' . ($data['kota_asal'] ?? '-') . '</td>
            <td><strong>No HP:</strong></td>
            <td>' . ($data['no_hp_pengirim'] ?? '-') . '</td>
        </tr>
        <tr>
            <td><strong>Kota Tujuan:</strong></td>
            <td>' . ($data['kota_tujuan'] ?? '-') . '</td>
            <td><strong>Penerima:</strong></td>
            <td>' . ($data['no_hp_penerima'] ?? '-') . '</td>
        </tr>
        <tr>
            <td><strong>Tanggal Kirim:</strong></td>
            <td>' . now()->format('d-m-Y') . '</td>
            <td><strong>Alamat:</strong></td>
            <td>' . ($data['alamat_penerima'] ?? '-') . '</td>
        </tr>
        <tr>
            <td><strong>Jumlah Koli:</strong></td>
            <td>' . ($data['jumlah_koli'] ?? '-') . ' </td>
            <td><strong>Berat:</strong></td>
            <td>' . ($data['weight'] ?? '-') . ' kg</td>
        </tr>
        <tr>
            <td><strong>Berat Volume:</strong></td>
            <td colspan="3">' . ($data['volume'] ?? '-') . ' </td>
        </tr>
    </table>

    <div class="section-title">ISI BARANG</div>
    <table class="main-table">
        <tr><th colspan="2">Detail Vendor & Biaya</th></tr>' .
            $vendorRows .
            '<tr><th>Total Biaya Vendor</th><td>Rp ' . number_format($totalBiayaVendor, 0, ',', '.') . '</td></tr>
        <tr><th>Biaya Paket</th><td>Rp ' . number_format($data['cost'] ?? 0, 0, ',', '.') . '</td></tr>
        <tr><th>Total Keseluruhan</th><td>Rp ' . number_format($totalBiaya, 0, ',', '.') . '</td></tr>
        <tr><th>ADM</th><td>-</td></tr>
        <tr><th>Packing</th><td>-</td></tr>
        <tr><th>Pick Up</th><td>-</td></tr>
        <tr><th>Asuransi</th><td>-</td></tr>
        <tr><th>Lain - Lain</th><td>-</td></tr>
    </table>

    <div class="section-title">NILAI BARANG YANG DIASURANSIKAN</div>
    <div class="main-table">
        <p style="padding: 10px;">' . ($data['description'] ?? '-') . '</p>
    </div>

    <div class="footer">
        Terima kasih telah menggunakan layanan kami.
    </div>';

        $pdf = Pdf::loadHtml($html);

        return $pdf->stream('invoice-' . ($data['resi'] ?? 'no-resi') . '.pdf');
    }
}
