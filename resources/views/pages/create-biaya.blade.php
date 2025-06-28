@extends('layouts.app')
@section('title', 'Tambah Biaya')
@section('content')
    @include('partials.header')

    <div class="container">
        <h2>Tambah Biaya Operasional</h2>
        <form id="formTambahBiaya">


            <div class="form-group">
                <label>Resi</label>
                <input type="text" name="resi" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <input type="text" name="description" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Jumlah (Rp)</label>
                <input type="number" name="amount" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="date" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Dibuat oleh (User ID)</label>
                <input type="number" name="created_by" class="form-control" required>
            </div>
            <button class="btn btn-primary">Simpan</button>
        </form>
    </div>

    <script>
        document.getElementById('formTambahBiaya').addEventListener('submit', async function(e) {
            e.preventDefault();

            const token = localStorage.getItem('token');
            const formData = {
                description: this.description.value,
                amount: this.amount.value,
                date: this.date.value,
                created_by: this.created_by.value,
                resi: this.resi.value,
            };

            const response = await fetch('/api/biaya', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (response.ok) {
                alert('Biaya berhasil ditambahkan');
                window.location.href = '/biaya';
            } else {
                alert('Gagal: ' + JSON.stringify(result.errors));
            }
        });
    </script>
@endsection
