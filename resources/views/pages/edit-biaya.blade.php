@extends('layouts.app')
@section('title', 'Edit Biaya')
@section('content')
    @include('partials.header' , ['title' => 'Biaya Operasional', 'breadcrumb' => 'Input Biaya operasional'])

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
                <div class="form-group biaya-lain-row">
                    <input type="text" class="form-control mb-1" placeholder="Kegiatan" name="kegiatan[]">
                    <input type="number" class="form-control mb-3" placeholder="Biaya (Rp)" name="biaya[]">
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-secondary mb-3" onclick="tambahBaris()">+ Tambah Biaya</button>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="/biaya" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script>
        function tambahBaris() {
            const container = document.getElementById('biayaLainnyaContainer');
            const row = document.createElement('div');
            row.classList.add('form-group', 'biaya-lain-row');

            row.innerHTML = `
                <input type="text" class="form-control mb-1" placeholder="Kegiatan" name="kegiatan[]">
                <input type="number" class="form-control mb-3" placeholder="Biaya (Rp)" name="biaya[]">
            `;

            container.appendChild(row);
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

            // Tampilkan biaya lainnya jika sudah ada
            if (Array.isArray(data.biaya_lainnya)) {
                const container = document.getElementById('biayaLainnyaContainer');
                container.innerHTML = ''; // bersihkan baris default

                data.biaya_lainnya.forEach(item => {
                    const row = document.createElement('div');
                    row.classList.add('form-group', 'biaya-lain-row');

                    row.innerHTML = `
                        <input type="text" class="form-control mb-1" placeholder="Kegiatan" name="kegiatan[]" value="${item.kegiatan}">
                        <input type="number" class="form-control mb-3" placeholder="Biaya (Rp)" name="biaya[]" value="${item.biaya}">
                    `;
                    container.appendChild(row);
                });
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const kegiatan = Array.from(document.getElementsByName('kegiatan[]')).map(input => input.value.trim());
                const biaya = Array.from(document.getElementsByName('biaya[]')).map(input => parseFloat(input.value) || 0);

                const biayaLainnya = kegiatan.map((k, i) => ({
                    kegiatan: k,
                    biaya: biaya[i]
                })).filter(item => item.kegiatan && item.biaya); // filter yang kosong

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
