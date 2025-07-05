@extends('layouts.app')
@section('title', 'Tambah Vendor')
@section('content')
    @include('partials.header', ['title' => 'Tambah Vendor', 'breadcrumb' => 'Data vendor'])

    <div class="container">
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

            const response = await fetch('/api/vendor', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (res.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'User berhasil ditambahkan.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '/profile-admin';
                });
            } else {
                let errorMsg = result.message || "Terjadi kesalahan";
                if (result.errors) {
                    errorMsg = Object.values(result.errors).flat().join("\n");
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal menambahkan user',
                    text: errorMsg
                });
            }

        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: err.message
            });
        });
    </script>
@endsection
