<!-- edit-admin.blade.php -->
@extends('layouts.app')
@section('title', 'Edit Profil Admin')
@section('content')
    @include('partials.header')

    <div class="container">
        <h2>Edit Profil Admin</h2>
        <form id="editForm">
            <div class="form-group">
                <label>Nama</label>
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
                <label>Password (kosongkan jika tidak ingin mengubah)</label>
                <input type="password" name="password" class="form-control">
            </div>

            <button class="btn btn-primary">Update</button>
            <a href="/profile-admin" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const token = localStorage.getItem("token");

            if (!token) {
                alert("Token tidak ditemukan. Silakan login ulang.");
                return;
            }

            const res = await fetch('/api/profile/admin', {
                headers: {
                    "Authorization": "Bearer " + token,
                    "Accept": "application/json"
                }
            });

            const result = await res.json();
            console.log("Response dari /api/profile/admin:", result);

            if (!res.ok) {
                alert("Gagal mengambil data profil admin!");
                return;
            }

            const user = result.data ?? result;

            document.querySelector('[name="name"]').value = user.name ?? '';
            document.querySelector('[name="username"]').value = user.username ?? '';
            document.querySelector('[name="email"]').value = user.email ?? '';
        });
    </script>
@endsection
