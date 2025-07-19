@extends('layouts.app')
@section('title', 'Edit Paket')
@section('content')
    @include('partials.header', ['title' => 'Edit Paket', 'breadcrumb' => 'Data paket'])

    <div class="container">
        <form id="editForm">
            <div id="vendorContainer"></div>
            <button type="button" class="btn btn-info mb-3" onclick="tambahVendor()">Tambah Vendor</button>
            <input type="hidden" name="id" id="paketId">

            <div class="form-group">
                <label>Penginput (Tim Operasional)</label>
                <input type="text" name="creator_name" class="form-control" readonly>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <input type="text" name="description" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Berat (kg)</label>
                <input type="number" name="weight" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Jumlah Koli</label>
                <input type="number" name="jumlah_koli" class="form-control">
            </div>

            <div class="form-group">
                <label>Volume</label>
                <input type="number" name="volume" class="form-control">
            </div>

            <div class="form-group">
                <label>Kota Asal</label>
                <input type="text" name="kota_asal" class="form-control">
            </div>

            <div class="form-group">
                <label>Kota Tujuan</label>
                <input type="text" name="kota_tujuan" class="form-control">
            </div>

            <div class="form-group">
                <label>Nama Penerima</label>
                <input type="text" name="penerima" class="form-control">
            </div>

            <div class="form-group">
                <label>No HP Penerima</label>
                <input type="text" name="no_hp_penerima" class="form-control">
            </div>

            <div class="form-group">
                <label>Biaya (Rp)</label>
                <input type="number" name="cost" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" onchange="toggleAlasanGagal()" required>
                    <option value="Dalam Proses">Dalam Proses</option>
                    <option value="Terkirim">Terkirim</option>
                    <option value="Gagal">Gagal</option>
                </select>
            </div>

            <div class="form-group" id="alasanGagalDiv" style="display: none;">
                <label for="alasan_gagal">Alasan Gagal</label>
                <textarea class="form-control" name="alasan_gagal" id="alasan_gagal" rows="3"></textarea>
            </div>

            <button class="btn btn-primary">Update</button>
            <a href="/paket" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script>
        let vendorList = [];

        function tambahVendor(vendorId = '', biaya = '') {
            const container = document.getElementById('vendorContainer');
            const index = container.children.length;

            const row = document.createElement('div');
            row.classList.add('form-row', 'mb-2');
            row.innerHTML = `
                <div class="col-md-5">
                    <select name="vendor_ids[]" class="form-control">
                        ${vendorList.map(v => `<option value="${v.id}" ${v.id == vendorId ? 'selected' : ''}>${v.name}</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="number" step="any" name="vendor_biayas[${index}]" class="form-control" placeholder="Biaya Vendor" value="${biaya}">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-block" onclick="this.closest('.form-row').remove()">Hapus</button>
                </div>
            `;
            container.appendChild(row);
        }

        document.addEventListener("DOMContentLoaded", async () => {
            const id = window.location.pathname.split("/").pop();
            const token = localStorage.getItem("token");

            const [paketRes, vendorRes] = await Promise.all([
                fetch(`/api/paket/${id}`, {
                    headers: {
                        Authorization: "Bearer " + token,
                        Accept: "application/json"
                    }
                }),
                fetch('/api/vendor', {
                    headers: {
                        Authorization: "Bearer " + token,
                        Accept: "application/json"
                    }
                })
            ]);

            const data = await paketRes.json();
            vendorList = await vendorRes.json();

            document.querySelector('[name="description"]').value = data.description;
            document.querySelector('[name="weight"]').value = data.weight;
            document.querySelector('[name="cost"]').value = data.cost;
            document.querySelector('[name="status"]').value = data.status ?? "Dalam Proses";
            document.querySelector('[name="jumlah_koli"]').value = data.jumlah_koli || '';
            document.querySelector('[name="volume"]').value = data.volume || '';
            document.querySelector('[name="kota_asal"]').value = data.kota_asal || '';
            document.querySelector('[name="kota_tujuan"]').value = data.kota_tujuan || '';
            document.querySelector('[name="penerima"]').value = data.penerima || '';
            document.querySelector('[name="no_hp_penerima"]').value = data.no_hp_penerima || '';
            document.querySelector('[name="creator_name"]').value = data.creator?.name || '-';

            document.getElementById("paketId").value = data.id;

            if (data.vendors && data.vendors.length > 0) {
                data.vendors.forEach(v => tambahVendor(v.id, v.pivot?.biaya_vendor ?? 0));
            }

            document.getElementById("editForm").addEventListener("submit", async function(e) {
                e.preventDefault();

                const vendorIds = Array.from(this.querySelectorAll('select[name="vendor_ids[]"]'))
                    .map(s => s.value);
                const biayaInputs = this.querySelectorAll('input[name^="vendor_biayas"]');

                const vendorBiayas = {};
                biayaInputs.forEach((input, idx) => {
                    vendorBiayas[vendorIds[idx]] = parseFloat(input.value) || 0;
                });

                const formData = {
                    description: this.description.value,
                    weight: this.weight.value,
                    cost: this.cost.value,
                    status: this.status.value,
                    alasan_gagal: this.alasan_gagal?.value || null,
                    jumlah_koli: this.jumlah_koli.value,
                    volume: this.volume.value,
                    kota_asal: this.kota_asal.value,
                    kota_tujuan: this.kota_tujuan.value,
                    penerima: this.penerima.value,
                    no_hp_penerima: this.no_hp_penerima.value,
                    vendor_ids: vendorIds,
                    vendor_biayas: vendorBiayas
                };

                const resUpdate = await fetch(`/api/paket/${data.id}`, {
                    method: "PUT",
                    headers: {
                        "Authorization": "Bearer " + token,
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                    },
                    body: JSON.stringify(formData),
                });

                if (resUpdate.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Paket berhasil diperbarui.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "/paket";
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal memperbarui paket.'
                    });
                }
            });
        });

        function toggleAlasanGagal() {
            const statusSelect = document.getElementById("status");
            const alasanDiv = document.getElementById("alasanGagalDiv");

            if (!statusSelect || !alasanDiv) return;

            if (statusSelect.value === "Gagal") {
                alasanDiv.style.display = "block";
            } else {
                alasanDiv.style.display = "none";
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            toggleAlasanGagal();

            const token = localStorage.getItem("token");

            // Ambil role dari /api/user
            fetch('/api/user', {
                    headers: {
                        "Authorization": "Bearer " + token,
                        "Accept": "application/json"
                    }
                })
                .then(res => res.json())
                .then(user => {
                    const role = user.role;
                    const statusSelect = document.getElementById("status");
                    const alasanDiv = document.getElementById("alasanGagalDiv");

                    // Sembunyikan textarea alasan jika bukan admin
                    if (role !== "admin" && alasanDiv) {
                        alasanDiv.style.display = "none";
                    }

                    // Nonaktifkan dropdown status untuk superadmin
                    if (role === "superadmin" && statusSelect) {
                        statusSelect.setAttribute("disabled", true);
                    }

                    // Event ubah status: hanya admin yang bisa munculkan textarea
                    if (statusSelect && role === "admin") {
                        statusSelect.addEventListener("change", toggleAlasanGagal);
                    }
                })
                .catch(err => console.error("Gagal mengambil role user:", err));
        });
    </script>

@endsection
