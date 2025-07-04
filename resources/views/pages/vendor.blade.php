@extends('layouts.app')
@section('title', 'Data Vendor')
@section('content')
    @include('partials.header')

    <div class="container">
        <h2>Data Vendor</h2>
        <a href="/vendor/create" class="btn btn-primary mb-3">Tambah Vendor</a>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="vendorBody">
                    <tr>
                        <td colspan="5" class="text-center">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const token = localStorage.getItem('token');
            const tbody = document.getElementById('vendorBody');

            try {
                const res = await fetch('/api/vendor', {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();
                tbody.innerHTML = '';

                if (data.length === 0) {
                    tbody.innerHTML =
                        `<tr><td colspan="5" class="text-center">Tidak ada data vendor.</td></tr>`;
                } else {
                    data.forEach((v, i) => {
                        tbody.innerHTML += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${v.name}</td>
                        <td>${v.phone}</td>
                        <td>${v.address}</td>
                        <td>
                            <a href="/vendor/edit/${v.id}" class="btn btn-sm btn-warning">Edit</a>
                            <button onclick="hapusVendor(${v.id})" class="btn btn-sm btn-danger">Hapus</button>
                        </td>
                    </tr>
                `;
                    });
                }
            } catch (err) {
                tbody.innerHTML =
                    `<tr><td colspan="5" class="text-center text-danger">Gagal memuat data</td></tr>`;
            }
        });

        async function hapusVendor(id) {
            if (!confirm("Yakin ingin menghapus vendor ini?")) return;
            const token = localStorage.getItem('token');

            const res = await fetch(`/api/vendor/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                alert('Vendor berhasil dihapus');
                location.reload();
            } else {
                alert('Gagal menghapus vendor');
            }
        }
    </script>
@endsection
