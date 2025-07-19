@extends('layouts.app')
@section('title', 'Paket')
@section('content')
    @include('partials.header', ['title' => 'Paket', 'breadcrumb' => 'Data paket'])

    <div class="container">
        <button id="btn-paket" class="btn btn-primary mb-3" onclick="window.location.href='/paket/create'">Tambah
            Paket</button>
        <div class="card-table" id="card-table">
            <div class="table-responsive">
                <table id="paketTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Resi</th>
                            <th>Deskripsi</th>
                            <th>Berat (kg)</th>
                            <th>Volume (kg)</th>
                            <th>Jumlah Koli</th>
                            <th>Kota Asal</th>
                            <th>Kota Tujuan</th>
                            <th>Biaya (Rp)</th>
                            <th>Penerima</th>
                            <th>No HP Penerima</th>
                            <th>Vendor</th>
                            <th>Pengirim</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="paketBody">
                        <tr>
                            <td colspan="8" class="text-center">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const token = localStorage.getItem('token');
            const tbody = document.getElementById('paketBody');
            const paketTable = document.getElementById('paketTable');
            const btnPaket = document.getElementById('btn-paket');

            if (!token) return window.location.href = '/login';

            let role = '';
            try {
                const res = await fetch('/api/user', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });

                if (!res.ok) throw new Error('Unauthorized');

                const user = await res.json();
                role = user.role;

                if (role === 'tim-operasional') {
                    document.getElementById('card-table').style.display = 'none';
                }
                if (role === 'super-admin' || role === 'admin') {
                    btnPaket.style.display = 'none';
                }
                if (role === 'tim-operasional') {
                    paketTable.style.display = 'none';
                }
            } catch (error) {
                tbody.innerHTML =
                    `<tr><td colspan="8" class="text-danger text-center">${error.message}</td></tr>`;
                return;
            }

            try {
                const response = await fetch('/api/paket', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`Gagal memuat data: ${errorText}`);
                }

                const data = await response.json();
                tbody.innerHTML = '';

                if (data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="8" class="text-center">Data tidak ditemukan.</td></tr>`;
                    return;
                }

                data.forEach((item, index) => {
                    const badgeClass = item.status === 'Terkirim' ? 'badge-success' :
                        item.status === 'Gagal' ? 'badge-danger' : 'badge-warning';

                    const vendorList = (item.vendors || []).map(v =>
                        `${v.name} (Rp${parseInt(v.pivot?.biaya_vendor || 0).toLocaleString('id-ID')})`
                    ).join('<br>');

                    const pengirim = item.creator?.name || '-';

                    let actionButtons = `
                        <button class="btn-action btn-edit" onclick="editPaket(${item.id})">
                            <i class="fas fa-pen"></i> Edit
                        </button>
                    `;

                    if (role !== 'admin') {
                        actionButtons += `
                        <button class="btn-action btn-delete" onclick="hapusPaket(${item.id})">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                        <button class="btn-action btn-invoice" onclick="cetakInvoice(${item.id})">
                            <i class="fas fa-file-invoice"></i> Invoice
                        </button>
                        `;
                    }

                    tbody.innerHTML += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.resi}</td>
                    <td>${item.description}</td>
                    <td>${item.weight}</td>
                    <td>${item.volume}</td>
                    <td>${item.jumlah_koli}</td>
                    <td>${item.kota_asal}</td>
                    <td>${item.kota_tujuan}</td>
                    <td>Rp${parseFloat(item.cost).toLocaleString('id-ID')}</td>
                    <td>${item.penerima}</td>
                    <td>${item.no_hp_penerima}</td>
                    <td>${vendorList || '-'}</td>
                    <td>${pengirim}</td>
                    <td>${new Date(item.created_at).toLocaleDateString('id-ID')}</td>
                    <td>
                        <span class="badge ${badgeClass}">${item.status || 'Dalam Proses'}</span>
                        ${item.status === 'Gagal' && item.alasan_gagal ? `<br><small class="text-danger"><strong>Alasan:</strong> ${item.alasan_gagal}</small>` : ''}
                    </td>
                    <td>${actionButtons}</td>
                </tr>
            `;
                });
                setTimeout(() => {
                    $('#paketTable').DataTable({
                        pageLength: 20,
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
            } catch (error) {
                tbody.innerHTML =
                    `<tr><td colspan="8" class="text-danger text-center">${error.message}</td></tr>`;
            }
        });

        function editPaket(id) {
            window.location.href = `/paket/edit/${id}`;
        }

        function cetakInvoice(id) {
            const token = localStorage.getItem("token");
            window.open(`/invoice/download?package_id=${id}`, '_blank');
        }

        async function hapusPaket(id) {
            const confirmResult = await Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data paket akan hilang permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            });

            if (!confirmResult.isConfirmed) return;

            const token = localStorage.getItem("token");
            const res = await fetch(`/api/paket/${id}`, {
                method: "DELETE",
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Paket berhasil dihapus.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Gagal menghapus paket.',
                    icon: 'error'
                });
            }
        }
    </script>

@endsection
