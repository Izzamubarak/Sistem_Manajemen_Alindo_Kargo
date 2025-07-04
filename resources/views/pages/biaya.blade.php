@extends('layouts.app')
@section('title', 'Biaya Operasional')
@section('content')
    @include('partials.header', ['title' => 'Biaya Operasional', 'breadcrumb' => 'Input biaya lainnya untuk memverifikasi status menjadi terkirim'])

    <div class="container">
        <h2>Data Biaya Operasional</h2>
        <button id="btn-export" class="btn btn-success mb-3 ml-2"
            onclick="window.location.href='{{ route('biaya.export') }}'"><i class="fa fa-file-excel"></i> Export
            Excel</button>
        <div class="table-responsive">
            <table class="table table-bordered" id="biayaTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Resi</th>
                        <th>Vendor</th>
                        <th>Total Vendor</th>
                        <th>Biaya Lainnya</th>
                        <th>Pengeluaran</th>
                        <th>Pendapatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody id="biayaBody">
                    <tr>
                        <td colspan="8" class="text-center">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const token = localStorage.getItem('token');
            const tbody = document.getElementById('biayaBody');
            const btnExport = document.getElementById('btn-export');

            if (!token) return window.location.href = '/login';

            try {
                const resUser = await fetch('/api/user', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });

                const user = await resUser.json();
                if (user.role !== 'super-admin') btnExport.style.display = 'none';

                const resData = await fetch('/api/biaya', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                const resJson = await resData.json();
                const data = Array.isArray(resJson) ? resJson : resJson.data;

                tbody.innerHTML = '';

                if (data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>`;
                } else {
                    data.forEach((item, index) => {
                        const vendorList = (item.vendor_names || []).join(', ');
                        const totalVendor = parseFloat(item.total_vendor || 0);
                        const totalPaket = parseFloat(item.total_paket || 0);

                        let biayaLainnya = 0;
                        let biayaLainnyaText = "-";

                        // Jika biaya_lainnya adalah object JSON, loop untuk tampilkan
                        if (Array.isArray(item.biaya_lainnya)) {
                            const entries = item.biaya_lainnya.map(entry => {
                                const kegiatan = entry.kegiatan || '-';
                                const biaya = parseFloat(entry.biaya) || 0;
                                biayaLainnya += biaya;
                                return `${kegiatan}: Rp${biaya.toLocaleString()}`;
                            });
                            biayaLainnyaText = entries.join("<br>");
                        }


                        // Total pengeluaran
                        const pengeluaran = totalVendor + biayaLainnya;
                        const pendapatan = totalPaket - pengeluaran;

                        tbody.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.resi ?? '-'}</td>
                            <td>${vendorList || '-'}</td>
                            <td>Rp${totalVendor.toLocaleString()}</td>
                            <td>${biayaLainnyaText}</td>
                            <td>Rp${pengeluaran.toLocaleString()}</td>
                            <td>Rp${pendapatan.toLocaleString()}</td>
                            <td>
                                <a href="/biaya/edit/${item.id}" class="btn btn-sm btn-warning">Input</a>
                            </td>
                        </tr>
                    `;
                    });
                }

            } catch (error) {
                tbody.innerHTML =
                    `<tr><td colspan="8" class="text-danger text-center">Error: ${error.message}</td></tr>`;
            }
        });
    </script>
@endsection
