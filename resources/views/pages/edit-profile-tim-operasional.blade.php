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

            try {
                const res = await fetch(
                    '/api/profile/{{ Request::segment(2) === 'admin' ? 'admin' : 'tim-operasional' }}', {
                        headers: {
                            "Authorization": "Bearer " + token,
                            "Accept": "application/json",
                        },
                    });

                const result = await res.json();

                if (!res.ok || !result.data) {
                    alert("Gagal mengambil data profil. Mungkin token tidak valid atau data tidak ditemukan.");
                    console.error(result);
                    return;
                }

                const user = result.data;

                document.querySelector('[name="name"]').value = user.name ?? '';
                document.querySelector('[name="username"]').value = user.username ?? '';
                document.querySelector('[name="email"]').value = user.email ?? '';

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

                    const updateRes = await fetch(
                        '/api/profile/{{ Request::segment(2) === 'admin' ? 'admin' : 'tim-operasional' }}', {
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
                        window.location.href = "/profile-{{ Request::segment(2) }}";
                    } else {
                        alert("Gagal update profil.");
                        const error = await updateRes.json();
                        console.error(error);
                    }
                });

            } catch (err) {
                console.error("Error saat fetch profil:", err);
                alert("Terjadi kesalahan jaringan.");
            }
        });
    </script>
@endsection
