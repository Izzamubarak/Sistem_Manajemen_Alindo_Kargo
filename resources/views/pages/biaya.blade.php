@extends('layouts.app')
@section('title', 'Biaya Operasional')
@section('content')
    @include('partials.header')

    <div class="container">
        <h2>Data Biaya Operasional</h2>
        <button id="btn-export" class="btn btn-success mb-3 ml-2"
            onclick="window.location.href='{{ route('biaya.export') }}'"><i class="fa fa-file-excel"></i> Export
            Excel</button>

        <table class="table table-bordered" id="biayaTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Resi</th>
                    <th>Vendor</th>
                    <th>Total Vendor</th>
                    <th>Total Paket</th>
                    <th>Biaya Lainnya</th>
                    <th>Total Keseluruhan</th>
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

                const data = await resData.json();
                tbody.innerHTML = '';

                if (data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>`;
                } else {
                    data.forEach((item, index) => {
                        const vendorList = (item.vendor_names || []).join(', ');
                        const totalVendor = parseFloat(item.total_vendor || 0);
                        const totalPaket = parseFloat(item.total_paket || 0);
                        const biayaLainnya = parseFloat(item.biaya_lainnya || 0);
                        const totalKeseluruhan = totalVendor + totalPaket + biayaLainnya;

                        tbody.innerHTML += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.resi ?? '-'}</td>
                                <td>${vendorList || '-'}</td>
                                <td>Rp${totalVendor.toLocaleString()}</td>
                                <td>Rp${totalPaket.toLocaleString()}</td>
                                <td>Rp${biayaLainnya.toLocaleString()}</td>
                                <td>Rp${totalKeseluruhan.toLocaleString()}</td>
                                <td>
                                    <a href="/biaya/edit/${item.id}" class="btn btn-sm btn-warning">Edit</a>
                                    <button class="btn btn-sm btn-danger" onclick="hapusBiaya(${item.id})">Hapus</button>
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

        async function hapusBiaya(id) {
            if (!confirm("Yakin ingin menghapus data ini?")) return;
            const token = localStorage.getItem('token');

            const res = await fetch(`/api/biaya/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                alert("Data berhasil dihapus");
                location.reload();
            } else {
                alert("Gagal menghapus data");
            }
        }
    </script>
@endsection
