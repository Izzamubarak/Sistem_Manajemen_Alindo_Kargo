@extends('layouts.app')
@section('title', 'Edit Vendor')
@section('content')
    @include('partials.header', ['title' => 'Edit', 'breadcrumb' => 'Edit vendor'])

    <div class="container">
        <form id="formEditVendor">
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
            <button class="btn btn-primary">Update</button>
            <a href="/vendor" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const token = localStorage.getItem('token');
            const user = JSON.parse(localStorage.getItem('user'));
            const id = {{ $id }};

            const res = await fetch(`/api/vendor/${id}`, {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            const vendor = await res.json();
            const form = document.getElementById('formEditVendor');
            form.name.value = vendor.name;
            form.phone.value = vendor.phone;
            form.address.value = vendor.address;

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const updatedData = {
                    name: form.name.value,
                    phone: form.phone.value,
                    address: form.address.value,
                    created_by: user.id
                };

                const update = await fetch(`/api/vendor/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(updatedData)
                });

                if (resUpdate.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Vendor berhasil diperbarui.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "/vendor";
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal memperbarui vendor.'
                    });
                }
            });
        });
    </script>
@endsection
