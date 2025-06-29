@extends('layouts.app')
@section('title', 'Edit Biaya')
@section('content')
    @include('partials.header')

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

            <div class="form-group">
                <label>Biaya Lainnya</label>
                <div id="biayaLainnyaContainer"></div>
                <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="tambahKegiatan()">Tambah
                    Kegiatan</button>
            </div>

            <div class="form-group mt-3">
                <label>Total Biaya Lainnya (Rp)</label>
                <input type="number" id="totalBiayaLainnya" class="form-control" readonly>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="/biaya" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script>
        function tambahKegiatan(keterangan = '', nominal = '') {
            const container = document.getElementById('biayaLainnyaContainer');
            const row = document.createElement('div');
            row.className = 'form-row d-flex mb-2';
            row.innerHTML = `
        <input type="text" name="kegiatan[]" class="form-control mr-2" placeholder="Keterangan" value="${keterangan}" required>
        <input type="number" name="biaya[]" class="form-control mr-2 biaya-item" placeholder="Biaya (Rp)" value="${nominal}" required>
        <button type="button" class="btn btn-danger" onclick="this.parentNode.remove(); hitungTotal();">Hapus</button>
    `;
            container.appendChild(row);
            hitungTotal();
        }

        function hitungTotal() {
            const biayaInputs = document.querySelectorAll('.biaya-item');
            let total = 0;
            biayaInputs.forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('totalBiayaLainnya').value = total;
        }


        document.addEventListener('DOMContentLoaded', async () => {
            const token = localStorage.getItem('token');
            const id = "{{ $id }}";
            const form = document.getElementById('editBiayaForm');

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
            if (data.biaya_lainnya && typeof data.biaya_lainnya === 'object') {
                for (const [keterangan, nominal] of Object.entries(data.biaya_lainnya)) {
                    tambahKegiatan(keterangan, nominal);
                }
                hitungTotal(); // tampilkan total otomatis
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const kegiatan = document.getElementsByName('kegiatan[]');
                const biaya = document.getElementsByName('biaya[]');
                let biaya_lainnya = {};

                for (let i = 0; i < kegiatan.length; i++) {
                    const ket = kegiatan[i].value.trim();
                    const nilai = parseFloat(biaya[i].value) || 0;
                    if (ket) {
                        biaya_lainnya[ket] = nilai;
                    }
                }

                const updated = {
                    resi: form.resi.value,
                    total_vendor: parseFloat(form.total_vendor.value),
                    total_paket: parseFloat(form.total_paket.value),
                    biaya_lainnya: biaya_lainnya,
                    created_by: JSON.parse(localStorage.getItem('user')).id
                };

                const update = await fetch(`/api/biaya/${id}`, {
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
