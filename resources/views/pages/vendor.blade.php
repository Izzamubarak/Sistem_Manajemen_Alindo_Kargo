@extends('layouts.app')
@section('title', 'Data Vendor')
@section('content')
    @include('partials.header', ['title' => 'Vendor', 'breadcrumb' => 'Data vendor'])

    <div class="container">
        <a href="/vendor/create" class="btn btn-primary mb-3">Tambah Vendor</a>
        <div class="card-table">
            <div class="table-responsive">
                <table class="table table-bordered" id="vendorTable">
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
                            <a href="/vendor/edit/${v.id}" class="btn-action btn-edit">
                                <i class="fas fa-pen"></i> Edit
                            </a>
                            <button class="btn-action btn-delete" onclick="hapusVendor(${v.id})">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                `;
                    });
                }
                setTimeout(() => {
                    $('#vendorTable').DataTable({
                        pageLength: 10,
                        lengthChange: false,
                        destroy: true,
                        language: {
                            search: "Cari:",
                            paginate: {
                                previous: "Sebelumnya",
                                next: "Berikutnya"
                            },
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"
                        }
                    });
                }, 0);
            } catch (err) {
                tbody.innerHTML =
                    `<tr><td colspan="5" class="text-center text-danger">Gagal memuat data</td></tr>`;
            }
        });

        async function hapusVendor(id) {
            const confirmResult = await Swal.fire({
                title: 'Yakin ingin menghapus vendor?',
                text: "Data vendor akan hilang permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            });

            if (!confirmResult.isConfirmed) return;

            const token = localStorage.getItem('token');

            const res = await fetch(`/api/vendor/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Vendor berhasil dihapus.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Gagal menghapus vendor.',
                    icon: 'error'
                });
            }
        }
    </script>
@endsection
