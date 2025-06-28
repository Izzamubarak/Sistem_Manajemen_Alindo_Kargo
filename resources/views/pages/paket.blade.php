@extends('layouts.app')
@section('title', 'Paket')
@section('content')
    @include('partials.header')

    <div class="container mt-4">
        <h2>Data Paket</h2>
        <button id="btn-paket" class="btn btn-primary mb-3" onclick="window.location.href='/paket/create'">Tambah
            Paket</button>
        {{-- <button id="btn-export" class="btn btn-success mb-3 ml-2" style="display: none;"
            onclick="window.location.href='{{ route('paket.export') }}'">Export ke Excel</button> --}}

        <table class="table table-bordered" id="paketTable">
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

    <!-- Modal Ubah Status -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formStatusUpdate">
                    <div class="modal-header">
                        <h5 class="modal-title">Ubah Status Paket</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" id="statusSelect">
                                <option value="Dalam Proses">Dalam Proses</option>
                                <option value="Terkirim">Terkirim</option>
                                <option value="Gagal">Gagal</option>
                            </select>
                        </div>
                        <input type="hidden" id="paketIdForStatus">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Lihat Invoice -->
    <div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="invoiceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Invoice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="invoiceContent">
                    <p>Memuat...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

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

                if (role !== 'tim-operasional') {
                    btnPaket.style.display = 'none';
                }
                if (role === 'super-admin') {
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
                        item.status === 'Gagal' ? 'badge-danger' :
                        'badge-warning';

                    const vendorList = (item.vendors || [])
                        .map(v =>
                            `${v.name} (Rp${parseInt(v.pivot?.biaya_vendor || 0).toLocaleString('id-ID')})`
                        )
                        .join('<br>');

                    const pengirim = item.creator?.name || '-'; // ✅ ambil nama user yang buat

                    let actionButtons = `
        <button class="btn btn-sm btn-info" onclick="editPaket(${item.id})">Edit</button>
        <button class="btn btn-sm btn-danger" onclick="hapusPaket(${item.id})">Hapus</button>
    `;

                    if (role !== 'admin') {
                        actionButtons += `
            <button class="btn btn-sm btn-warning btn-ubah-status" onclick="bukaModalStatus(${item.id}, '${item.status}')">Ubah Status</button>
            <button class="btn btn-sm btn-secondary btn-invoce" onclick="lihatInvoice(${item.id})">Lihat Invoice</button>
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
        <td>${pengirim}</td> <!-- ✅ tampilkan nama pengirim -->
        <td>${new Date(item.created_at).toLocaleDateString('id-ID')}</td>
        <td><span class="badge ${badgeClass}">${item.status || 'Dalam Proses'}</span></td>
        <td>${actionButtons}</td>
    </tr>
    `;
                });



            } catch (error) {
                tbody.innerHTML =
                    `<tr><td colspan="8" class="text-danger text-center">${error.message}</td></tr>`;
            }
        });

        // Function Definitions
        function editPaket(id) {
            window.location.href = `/paket/edit/${id}`;
        }

        async function lihatInvoice(paketId) {
            const token = localStorage.getItem("token");
            try {
                const response = await fetch(`/api/paket/${paketId}`, {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error("Gagal mengambil data paket.");

                const paket = await response.json();

                document.getElementById("invoiceContent").innerHTML = `
            <ul class="list-group">
                <li class="list-group-item"><strong>No Invoice:</strong> ${paket?.id}</li>
                <li class="list-group-item"><strong>Total:</strong> Rp${parseFloat(paket?.cost).toLocaleString('id-ID')}</li>
                <li class="list-group-item"><strong>Status:</strong> ${paket?.status}</li>
                <li class="list-group-item">
  <strong>Vendors:</strong><br>
  ${paket?.vendors?.map(v => `${v.name} (Rp${v.pivot?.biaya_vendor ?? 0})`).join('<br>') || '-'}
</li>
                <li class="list-group-item"><strong>Dibuat Oleh (User ID):</strong> ${paket?.created_by}</li>
            </ul>
        `;

                $('#invoiceModal').modal('show');
            } catch (error) {
                document.getElementById("invoiceContent").innerHTML = `<p class="text-danger">${error.message}</p>`;
                $('#invoiceModal').modal('show');
            }
        }

        async function hapusPaket(id) {
            if (!confirm("Yakin ingin menghapus paket ini?")) return;

            const token = localStorage.getItem("token");
            const res = await fetch(`/api/paket/${id}`, {
                method: "DELETE",
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                alert("Paket berhasil dihapus.");
                location.reload();
            } else {
                alert("Gagal menghapus paket.");
            }
        }

        function bukaModalStatus(id, currentStatus) {
            document.getElementById("paketIdForStatus").value = id;
            document.getElementById("statusSelect").value = currentStatus || "Dalam Proses";
            $('#statusModal').modal('show');
        }

        document.getElementById("formStatusUpdate").addEventListener("submit", async function(e) {
            e.preventDefault();

            const id = document.getElementById("paketIdForStatus").value;
            const status = document.getElementById("statusSelect").value;
            const token = localStorage.getItem("token");

            const res = await fetch(`/api/paket/${id}`, {
                method: "PUT",
                headers: {
                    "Authorization": "Bearer " + token,
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                },
                body: JSON.stringify({
                    status
                })
            });

            if (res.ok) {
                $('#statusModal').modal('hide');
                location.reload();
            } else {
                alert("Gagal memperbarui status");
            }
        });
    </script>

@endsection
