@extends('layouts.app')
@section('title', 'Edit Biaya')
@section('content')
    @include('partials.header', [
        'title' => 'Biaya Operasional',
        'breadcrumb' => 'Input Biaya operasional',
    ])

    <div class="container">
        <h2>Edit Biaya Operasional</h2>
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

            <div id="biayaLainnyaContainer">
                <label>Biaya Lainnya</label>
            </div>
            <button type="button" class="btn btn-sm btn-secondary mb-3" onclick="tambahBaris()">+ Tambah Biaya</button>

            <div class="form-group">
                <label>Total Biaya Lainnya (Rp)</label>
                <input type="number" id="totalBiayaLainnya" class="form-control" readonly>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="/biaya" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script>
        function tambahBaris(kegiatan = '', biaya = '') {
            const container = document.getElementById('biayaLainnyaContainer');

            const row = document.createElement('div');
            row.classList.add('form-row', 'mb-2');

            row.innerHTML = `
            <div class="col-md-5">
                <input type="text" class="form-control" placeholder="Kegiatan" name="kegiatan[]" value="${kegiatan}">
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control biaya-input" placeholder="Biaya (Rp)" name="biaya[]" value="${biaya}" oninput="hitungTotalBiaya()">
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.form-row').remove(); hitungTotalBiaya();">Hapus</button>
            </div>
        `;
            container.appendChild(row);
        }

        function hitungTotalBiaya() {
            const inputs = document.querySelectorAll('.biaya-input');
            let total = 0;
            inputs.forEach(input => {
                total += parseFloat(input.value || 0);
            });
            document.getElementById('totalBiayaLainnya').value = total;
        }

        document.addEventListener('DOMContentLoaded', async () => {
            const token = localStorage.getItem('token');
            const id = "{{ $id }}";
            const form = document.getElementById('editBiayaForm');

            const res = await fetch(`http://localhost:8000/api/biaya/${id}`, {
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
                data.biaya_lainnya.forEach(item => {
                    tambahBaris(item.kegiatan, item.biaya);
                });
                hitungTotalBiaya();
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const kegiatan = Array.from(document.getElementsByName('kegiatan[]')).map(input =>
                    input.value.trim());
                const biaya = Array.from(document.getElementsByName('biaya[]')).map(input =>
                    parseFloat(input.value) || 0);

                const biayaLainnya = kegiatan.map((k, i) => ({
                    kegiatan: k,
                    biaya: biaya[i]
                })).filter(item => item.kegiatan && item.biaya);

                const updated = {
                    resi: form.resi.value,
                    total_vendor: parseFloat(form.total_vendor.value),
                    total_paket: parseFloat(form.total_paket.value),
                    biaya_lainnya: biayaLainnya,
                    created_by: JSON.parse(localStorage.getItem('user')).id
                };

                const update = await fetch(`http://localhost:8000/api/biaya/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(updated)
                });

                if (update.ok) {
                    alert("Data berhasil diperbarui.");
                    window.location.href = "/biaya";
                } else {
                    alert("Gagal memperbarui data.");
                }
            });
        });
    </script>

@endsection
