@extends('layouts.app')
@section('title', 'Edit Biaya')
@section('content')
    @include('partials.header', ['title' => 'Edit Biaya Operasional', 'breadcrumb' => 'Biaya'])

    <div class="container mt-4">
        <form id="editBiayaForm">
            <input type="hidden" name="id" value="{{ $id }}">

            <div class="form-group">
                <label>Resi</label>
                <input type="text" name="resi" class="form-control" readonly>
            </div>

            <div class="form-group">
                <label>Total Vendor (Rp)</label>
                <input type="number" name="total_vendor" class="form-control" readonly>
            </div>

            <div class="form-group">
                <label>Total Paket (Rp)</label>
                <input type="number" name="total_paket" class="form-control" readonly>
            </div>

            <hr>
            <h5>Biaya Lainnya</h5>
            <div id="biayaContainer"></div>
            <button type="button" class="btn btn-info mb-3" onclick="tambahBiaya()">+ Tambah Biaya</button>

            <div class="form-group">
                <label><strong>Total Biaya Lainnya (Rp)</strong></label>
                <input type="number" name="total_biaya_lainnya" id="total_biaya_lainnya" class="form-control" readonly>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="/biaya" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script>
        const form = document.getElementById('editBiayaForm');
        const biayaContainer = document.getElementById('biayaContainer');

        function tambahBiaya(kegiatan = '', biaya = 0) {
            const index = biayaContainer.children.length;
            const row = document.createElement('div');
            row.classList.add('form-row', 'mb-2');
            row.innerHTML = `
                <div class="col-md-7">
                    <input type="text" name="kegiatan[]" class="form-control" placeholder="Contoh: Parkir, Bensin" value="${kegiatan}" required>
                </div>
                <div class="col-md-4">
                    <input type="number" name="biaya[]" class="form-control biaya-input" placeholder="Nominal" value="${biaya}" required oninput="hitungTotalBiaya()">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger" onclick="this.parentElement.parentElement.remove(); hitungTotalBiaya();">Hapus</button>
                </div>`;
            biayaContainer.appendChild(row);
            hitungTotalBiaya();
        }

        function hitungTotalBiaya() {
            const inputs = document.querySelectorAll('.biaya-input');
            let total = 0;
            inputs.forEach(input => {
                total += parseFloat(input.value || 0);
            });
            document.getElementById('total_biaya_lainnya').value = total;
        }

        document.addEventListener('DOMContentLoaded', async () => {
            const token = localStorage.getItem('token');
            const user = JSON.parse(localStorage.getItem('user'));
            const id = "{{ $id }}";

            if (!token || !user?.id) return Swal.fire({ icon: 'error', title: 'Token tidak valid', text: 'Silakan login ulang.' });

            try {
                const res = await fetch(`/api/biaya/${id}`, {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();
                form.resi.value = data.resi;
                form.total_vendor.value = data.total_vendor ?? 0;
                form.total_paket.value = data.total_paket ?? 0;

                if (Array.isArray(data.biaya_lainnya)) {
                    data.biaya_lainnya.forEach(item => tambahBiaya(item.kegiatan, item.biaya));
                } else {
                    tambahBiaya();
                }

            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Gagal memuat data', text: error.message });
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const payload = {
                    kegiatan: Array.from(form.querySelectorAll('[name="kegiatan[]"]')).map(i => i.value),
                    biaya: Array.from(form.querySelectorAll('[name="biaya[]"]')).map(i => parseFloat(i.value)),
                    created_by: user.id
                };

                const update = await fetch(`/api/biaya/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (update.ok) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Biaya berhasil diperbarui.' }).then(() => {
                        window.location.href = "/biaya";
                    });
                } else {
                    const result = await update.json();
                    Swal.fire({ icon: 'error', title: 'Gagal', text: result.message || 'Terjadi kesalahan.' });
                }
            });
        });
    </script>
@endsection
