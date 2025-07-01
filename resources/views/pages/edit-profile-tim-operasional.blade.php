<!-- edit-tim-operasional.blade.php -->
@extends('layouts.app')
@section('title', 'Edit Profil tim-operasional')
@section('content')
    @include('partials.header')

    <div class="container">
        <h2>Edit Profil tim-operasional</h2>
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
            <a href="/profile-tim-operasional" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const token = localStorage.getItem("token");
            const path = window.location.pathname;

            // Ambil "admin" atau "tim-operasional" dari URL
            const role = path.includes("admin") ? "admin" : "tim-operasional";

            // Ambil data user
            const res = await fetch(`/api/profile/${role}`, {
                headers: {
                    "Authorization": "Bearer " + token,
                    "Accept": "application/json",
                },
            });

            const result = await res.json();

            if (!res.ok) {
                alert("Gagal mengambil data profil. Mungkin token tidak valid.");
                return;
            }

            const user = result.data ?? result;

            document.querySelector('[name="name"]').value = user.name ?? '';
            document.querySelector('[name="username"]').value = user.username ?? '';
            document.querySelector('[name="email"]').value = user.email ?? '';

            // Saat submit form
            document.getElementById("editForm").addEventListener("submit", async function(e) {
                e.preventDefault();

                const formData = {
                    name: this.name.value,
                    username: this.username.value,
                    email: this.email.value,
                };

                if (this.password.value) {
                    formData.password = this.password.value;
                }

                const updateRes = await fetch(`/api/profile/${role}`, {
                    method: "PUT",
                    headers: {
                        "Authorization": "Bearer " + token,
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(formData)
                });

                if (updateRes.ok) {
                    alert("Profil berhasil diperbarui!");
                    window.location.href = `/profile-${role}`;
                } else {
                    alert("Gagal update profil.");
                }
            });
        });
    </script>

@endsection
