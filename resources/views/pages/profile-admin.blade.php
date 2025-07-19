@extends('layouts.app')
@section('title', 'Manajemen User')
@section('content')
    @include('partials.header', ['title' => 'Admin', 'breadcrumb' => 'Kelola akun admin'])

    <div class="container mt-4">
        <button id="btn-paket" class="btn btn-primary mb-3" onclick="window.location.href='/profile-admin/create'">Tambah
            User</button>
        <div class="card-table">
            <div class="table-responsive">
                <table class="table table-bordered" id="userTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                            <th>Status Reset</th>
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
    </div>

    <script>
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
        document.addEventListener('DOMContentLoaded', async () => {
            const token = localStorage.getItem('token');
            const tbody = document.getElementById('userBody');

            try {
                const response = await fetch('/api/profile/admin', {
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
                            <a href="/profile-admin/edit/${user.id}" class="btn-action btn-edit">
                                <i class="fas fa-pen"></i> Edit
                            </a>
                            <button class="btn-action btn-delete" onclick="hapusUser(${user.id})">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                        <td>
                            ${
                                !user.has_requested_reset
                                    ? `<span class="badge bg-secondary">Belum ada request</span>`
                                    : user.reset_status === 'approved'
                                        ? `<span class="badge bg-success">Disetujui</span><br><code>/reset-password/${user.reset_token}?email=${encodeURIComponent(user.email)}</code>`
                                        : user.reset_status === 'pending'
                                            ? `
                                                    <span class="badge bg-warning">Menunggu</span><br>
                                                    <form method="POST" action="/reset-approvals/${user.id}/approve" style="margin-top: 5px;">
                                                        <input type="hidden" name="_token" value="${csrfToken}">
                                                        <button type="submit" class="btn btn-sm btn-success">Setujui</button>
                                                    </form>
                                                `
                                            : user.reset_status === 'used'
                                                ? `<span class="badge bg-secondary">Sudah dipakai</span>`
                                                : `<span class="badge bg-danger">Status tidak diketahui</span>`
                            }
                        </td>
                    </tr>
                `;
                    });
                }

                setTimeout(() => {
                    $('#userTable').DataTable({
                        deferRender: true,
                        pageLength: 10,
                        lengthChange: false,
                        destroy: true,
                        language: {
                            search: "Cari:",
                            paginate: {
                                previous: "Sebelumnya",
                                next: "Berikutnya"
                            },
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"
                        }
                    });
                }, 0);

            } catch (err) {
                tbody.innerHTML =
                    `<tr><td colspan="5" class="text-danger text-center">${err.message}</td></tr>`;
            }
        });

        async function hapusUser(id) {
            const konfirmasi = await Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'User ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            });

            if (konfirmasi.isConfirmed) {
                const token = localStorage.getItem("token");
                const res = await fetch(`/api/profile/admin/${id}`, {
                    method: "DELETE",
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                if (res.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'User berhasil dihapus.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal menghapus user.'
                    });
                }
            }
        }

        function ajukanReset(email) {
            if (!confirm(`Ajukan reset password untuk ${email}?`)) return;

            fetch('/forgot-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        email
                    })
                })
                .then(res => {
                    if (!res.ok) throw new Error('Gagal mengajukan reset');
                    return res.json();
                })
                .then(data => {
                    alert('Berhasil diajukan!');
                    location.reload(); // atau panggil ulang loadData()
                })
                .catch(err => alert(err.message));
        }
    </script>
@endsection
