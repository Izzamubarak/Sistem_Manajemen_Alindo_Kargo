@extends('layouts.app')
@section('title', 'Tambah User')
@section('content')
    @include('partials.header', ['title' => 'Tambah Admin', 'breadcrumb' => 'Kelola akun admin'])

    <div class="container mt-4">
        <h2>Tambah User Baru</h2>
        <form id="formTambahUser">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>

    <script>
        document.getElementById('formTambahUser').addEventListener('submit', async function(e) {
            e.preventDefault();

            const token = localStorage.getItem('token');
            if (!token) {
                alert('Anda belum login.');
                return;
            }

            // ✅ Cek apakah user yang login adalah super-admin
            try {
                const check = await fetch('/api/user', {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                if (!check.ok) throw new Error('Gagal memverifikasi pengguna');

                const user = await check.json();
                if (user.role !== 'super-admin') {
                    alert('Hanya super-admin yang dapat menambahkan user admin.');
                    return;
                }

                const formData = {
                    name: this.name.value,
                    username: this.username.value,
                    email: this.email.value,
                    password: this.password.value,
                    password_confirmation: this.password_confirmation.value,
                    role: 'admin'
                };

                const res = await fetch('/api/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify(formData)
                });

                const result = await res.json();

                if (res.ok) {
                    alert('User berhasil ditambahkan');
                    window.location.href = '/profile-admin';
                } else {
                    let errorMsg = result.message || "Terjadi kesalahan";
                    if (result.errors) {
                        errorMsg = Object.values(result.errors).flat().join("\n");
                    }
                    alert("Gagal menambahkan user:\n" + errorMsg);
                }

            } catch (err) {
                alert("Terjadi kesalahan: " + err.message);
            }
        });
    </script>
@endsection
