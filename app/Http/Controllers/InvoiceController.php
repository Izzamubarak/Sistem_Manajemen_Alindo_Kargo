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
            $paket = Data_paket::with('vendors', 'creator')->findOrFail($request->package_id);

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
                'nama_pengirim' => $paket->creator->name ?? '-',
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
    }
    .main-table {
        width: 100%;
        border-collapse: collapse;
    }
    .main-table th, .main-table td {
        border: 1px solid #000;
        padding: 5px;
    }
    .section-title {
        font-weight: bold;
        background-color: #eee;
        padding: 4px;
        margin-top: 10px;
    }
</style>

<table class="main-table">
    <tr>
        <td width="30%">
            <img src="' . public_path('images/logo2.png') . '" width="100" alt="Logo">
        </td>
        <td style="text-align:center;">
            <h2 style="margin:5px 0;">PT. ALIF LOGISTIK INDONESIA</h2>
            <p style="margin:0;">Jl. Nogosaren Baru No.60A Nogotirto, Gamping Sleman, Jogjakarta</p>
            <p style="margin:0;">Email: aliflogistikindonesia@gmail.com</p>
        </td>
        <td width="20%" style="text-align:center;">
            <strong>SURAT TANDA TERIMA</strong><br>
            <span>' . ($data["resi"] ?? '-') . '</span>
        </td>
    </tr>
</table>

<br>

<table class="main-table">
    <tr>
        <td colspan="2">
            [ ] Darat  [ ] Laut  [ ] Udara<br>
            [ ] Port to Port  [ ] Door to Door
        </td>
        <td><strong>Pengirim:</strong></td>
        <td>' . ($data['nama_pengirim'] ?? '-') . '</td>
    </tr>
    <tr>
        <td><strong>Kota Asal:</strong></td>
        <td>' . ($data["kota_asal"] ?? '-') . '</td>
        <td><strong>No HP:</strong></td>
        <td>' . ($data["no_hp_pengirim"] ?? '-') . '</td>
    </tr>
    <tr>
        <td><strong>Kota Tujuan:</strong></td>
        <td>' . ($data["kota_tujuan"] ?? '-') . '</td>
        <td><strong>Penerima:</strong></td>
        <td>' . ($data["penerima"] ?? '-') . '</td>
    </tr>
    <tr>
        <td><strong>Tanggal Kirim:</strong></td>
        <td>' . now()->format('d-m-Y') . '</td>
        <td><strong>Alamat:</strong></td>
        <td>' . ($data["alamat_penerima"] ?? '-') . '</td>
    </tr>
    <tr>
        <td><strong>Jumlah Koli:</strong></td>
        <td>' . ($data["jumlah_koli"] ?? '-') . '</td>
        <td><strong>Berat:</strong></td>
        <td>' . ($data["weight"] ?? '-') . ' kg</td>
    </tr>
    <tr>
        <td><strong>Berat Volume:</strong></td>
        <td colspan="3">' . ($data["volume"] ?? '-') . '</td>
    </tr>
</table>

<br><strong>ISI BARANG:</strong><br>
<p>' . ($data["description"] ?? '-') . '</p>

<br><strong>BIAYA:</strong>
<table class="main-table">
    <tr><th>Vendor</th><th>Biaya</th></tr>' . $vendorRows . '
    <tr><th>Total Vendor</th><td>Rp ' . number_format($totalBiayaVendor, 0, ',', '.') . '</td></tr>
    <tr><th>Biaya Paket</th><td>Rp ' . number_format($data["cost"] ?? 0, 0, ',', '.') . '</td></tr>
    <tr><th>Total Keseluruhan</th><td>Rp ' . number_format($totalBiaya, 0, ',', '.') . '</td></tr>
</table>

<br><strong>PETUGAS & PERNYATAAN:</strong>
<table class="main-table">
    <tr>
        <th>Petugas / Driver</th>
        <th>Pernyataan Pengirim</th>
        <th>Pernyataan Penerima</th>
    </tr>
    <tr>
        <td style="height: 60px;"></td>
        <td style="text-align: center;">
            Saya menyatakan bahwa<br>informasi & barang yang dikirim<br>sudah sesuai & kami menyetujuinya.
            <br><br>( __________________ )
        </td>
        <td style="text-align: center;">
            Saya menerima barang<br>dengan baik & benar.
            <br><br>( __________________ )
        </td>
    </tr>
</table>

<br><strong>KETENTUAN PENGIRIMAN:</strong>
<ol style="font-size: 11px;">
    <li>Titipan rusak / hilang akibat force majeur (bencana alam, perampokan, kecelakaan, kebakaran) di luar tanggung jawab kami.</li>
    <li>Titipan rusak / hilang akibat kemasan packing yang kurang baik menjadi tanggung jawab pengirim.</li>
    <li>Isi titipan rusak pada waktu penerimaan harus disaksikan oleh petugas kami dan dinyatakan secara tertulis atau dibuatkan berita acara.</li>
    <li>Alamat tidak jelas atau no HP tidak bisa dihubungi jika ada keterlambatan pengantaran maka akan menjadi tanggung jawab pengirim.</li>
    <li>Pengirim tidak bisa minta ganti rugi akibat keterlambatan proses pengiriman dengan waktu yang sudah dijanjikan.</li>
    <li>Bila dalam waktu 1 (satu) bulan tidak ada pengaduan, kiriman dianggap sudah diterima dengan baik.</li>
    <li>Ganti rugi yang diakibatkan karena rusak/hilang maksimal 10 kali dari biaya kirim atau maksimal Rp1.000.000.</li>
    <li>Biaya kirim belum termasuk biaya asuransi barang.</li>
</ol>

<div style="text-align:center; font-style:italic;">
    Terima kasih telah menggunakan layanan kami.
</div>';


        $pdf = Pdf::loadHtml($html);

        return $pdf->stream('invoice-' . ($data['resi'] ?? 'no-resi') . '.pdf');
    }
}
