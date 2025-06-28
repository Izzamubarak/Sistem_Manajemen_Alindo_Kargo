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
                <label>Biaya Lainnya (Rp)</label>
                <input type="number" name="biaya_lainnya" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="/biaya" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script>
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
            form.biaya_lainnya.value = data.biaya_lainnya ?? 0;

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const updated = {
                    resi: form.resi.value,
                    total_vendor: parseFloat(form.total_vendor.value),
                    total_paket: parseFloat(form.total_paket.value),
                    biaya_lainnya: parseFloat(form.biaya_lainnya.value),
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
