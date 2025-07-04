@extends('layouts.app')
@section('title', 'Manajemen User')
@section('content')
    @include('partials.header')

    <div class="container mt-4">
        <h2>Data Tim Operasional</h2>
        <button id="btn-paket" class="btn btn-primary mb-3"
            onclick="window.location.href='/profile-tim-operasional/create'">Tambah User</button>
        <div class="table-responsive">
            <table class="table table-bordered" id="userTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="userBody">
                    <tr>
                        <td colspan="5" class="text-center">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const token = localStorage.getItem('token');
            const tbody = document.getElementById('userBody');

            try {
                const response = await fetch('/api/profile/tim-operasional', {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Gagal memuat data user.');

                let users = await response.json();
                users = Array.isArray(users) ? users : [users];

                tbody.innerHTML = '';

                if (users.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center">Data tidak ditemukan.</td></tr>`;
                } else {
                    users.forEach((user, index) => {
                        tbody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>${new Date(user.created_at).toLocaleDateString()}</td>
                        <td>
                            <a href="/profile-tim-operasional/edit/${user.id}" class="btn btn-sm btn-warning">Edit</a>
                            <button class="btn btn-sm btn-danger" onclick="hapusUser(${user.id})">Hapus</button>
                        </td>       
                    </tr>
                `;
                    });
                }
            } catch (err) {
                tbody.innerHTML =
                    `<tr><td colspan="5" class="text-danger text-center">${err.message}</td></tr>`;
            }
        });

        async function hapusUser(id) {
            if (confirm("Yakin ingin menghapus user ini?")) {
                const token = localStorage.getItem("token");
                const res = await fetch(`/api/profile/tim-operasional/${id}`, {
                    method: "DELETE",
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                if (res.ok) {
                    alert("User berhasil dihapus.");
                    location.reload();
                } else {
                    alert("Gagal menghapus user.");
                }
            }
        }
    </script>
@endsection
