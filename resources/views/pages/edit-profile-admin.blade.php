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

            const res = await fetch("http://localhost:8000/api/profile/admin", {
                headers: {
                    "Authorization": "Bearer " + token,
                    "Accept": "application/json",
                },
            });

            const data = await res.json();

            document.querySelector('[name="name"]').value = data.name;
            document.querySelector('[name="username"]').value = data.username;
            document.querySelector('[name="email"]').value = data.email;

            document.getElementById("editForm").addEventListener("submit", async function(e) {
                e.preventDefault();

                const formData = {
                    name: this.name.value,
                    username: this.username.value,
                    email: this.email.value,
                };

                if (this.password.value.trim()) {
                    formData.password = this.password.value;
                }

                const updateRes = await fetch("http://localhost:8000/api/profile/admin", {
                    method: "PUT",
                    headers: {
                        "Authorization": "Bearer " + token,
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                    },
                    body: JSON.stringify(formData),
                });

                if (updateRes.ok) {
                    alert("Profil berhasil diperbarui.");
                    window.location.href = "/profile-admin";
                } else {
                    alert("Gagal memperbarui profil.");
                }
            });
        });
    </script>
@endsection
