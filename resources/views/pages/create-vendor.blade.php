@extends('layouts.app')
@section('title', 'Tambah Vendor')
@section('content')
    @include('partials.header')

    <div class="container">
        <h2>Tambah Vendor</h2>
        <form id="formTambahVendor">
            <div class="form-group">
                <label>Nama Vendor</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>No. Telepon</label>
                <input type="text" name="phone" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea name="address" class="form-control" required></textarea>
            </div>
            <button class="btn btn-primary">Simpan</button>
        </form>
    </div>

    <script>
        document.getElementById('formTambahVendor').addEventListener('submit', async function(e) {
            e.preventDefault();

            const token = localStorage.getItem('token');
            const user = JSON.parse(localStorage.getItem('user'));

            const data = {
                name: this.name.value,
                phone: this.phone.value,
                address: this.address.value,
                created_by: user.id
            };

            const response = await fetch('http://localhost:8000/api/vendor', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                alert('Vendor berhasil ditambahkan');
                window.location.href = '/vendor';
            } else {
                const error = await response.json();
                alert('Gagal tambah vendor: ' + JSON.stringify(error.errors));
            }
        });
    </script>
@endsection
