@extends('layouts.app')
@section('title', 'Tambah Paket')
@section('content')
    @include('partials.header')

    <div class="container mt-4">
        <h2>Tambah Paket</h2>
        <form id="formTambahPaket">
            <div id="vendorContainer"></div>
            <button type="button" class="btn btn-info mb-3" onclick="tambahVendor()">Tambah Vendor</button>

            <div class="form-group">
                <label>Nomor Resi</label>
                <input type="text" name="resi" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <input type="text" name="description" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Kota Asal</label>
                <input type="text" name="kota_asal" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Kota Tujuan</label>
                <input type="text" name="kota_tujuan" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="no_hp_pengirim">No HP Pengirim</label>
                <input type="text" name="no_hp_pengirim" class="form-control">
            </div>

            <div class="form-group">
                <label for="penerima">Nama Penerima</label>
                <input type="text" name="penerima" class="form-control">
            </div>

            <div class="form-group">
                <label for="no_hp_penerima">No HP Penerima</label>
                <input type="text" name="no_hp_penerima" class="form-control">
            </div>

            <div class="form-group">
                <label for="alamat_penerima">Alamat Penerima</label>
                <textarea name="alamat_penerima" class="form-control"></textarea>
            </div>

            <div class="form-group">
                <label for="weight">Berat</label>
                <input type="number" step="any" name="weight" class="form-control">
            </div>

            <div class="form-group">
                <label for="jumlah_koli">Jumlah Koli</label>
                <input type="number" name="jumlah_koli" class="form-control">
            </div>

            <div class="form-group">
                <label for="volume">Volume</label>
                <input type="number" step="any" name="volume" class="form-control">
            </div>

            <div class="form-group">
                <label for="cost">Biaya</label>
                <input type="number" step="any" name="cost" class="form-control">
            </div>

            <button type="button" class="btn btn-secondary" id="btnCetak">Cetak Invoice</button>
            <button type="submit" class="btn btn-primary" id="btnSimpan" disabled>Simpan</button>
        </form>
    </div>

    <script>
        let userData = {};
        let invoiceSudahDicetak = false;
        let vendorList = [];

        async function tambahVendor() {
            const container = document.getElementById('vendorContainer');
            const index = container.children.length;

            const row = document.createElement('div');
            row.classList.add('form-row', 'mb-2');
            row.innerHTML = `
                <div class="col-md-6">
                    <label>Vendor</label>
                    <select name="vendor_ids[]" class="form-control" required>
                        ${vendorList.map(v => `<option value="${v.id}">${v.name} - ${v.address}</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-5">
                    <label>Biaya Vendor</label>
                    <input type="number" step="any" name="vendor_biayas[${index}]" class="form-control" required placeholder="Contoh: 50000">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger" onclick="this.parentElement.parentElement.remove()">Hapus</button>
                </div>
            `;
            container.appendChild(row);
        }

        document.addEventListener('DOMContentLoaded', async function() {
            const token = localStorage.getItem('token');

            try {
                const userRes = await fetch('/api/user', {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                userData = await userRes.json();

                const vendorRes = await fetch('/api/vendor', {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                vendorList = await vendorRes.json();
            } catch (error) {
                console.error('Error memuat data:', error);
            }
        });

        document.getElementById('btnCetak').addEventListener('click', async function() {
            const form = document.getElementById('formTambahPaket');
            const formData = new FormData();

            // Salin data non-vendor dari form
            const excludeFields = ['vendor_ids[]', 'vendor_biayas'];
            const formElements = form.elements;
            for (let i = 0; i < formElements.length; i++) {
                const element = formElements[i];
                if (!excludeFields.some(field => element.name.startsWith(field))) {
                    formData.append(element.name, element.value);
                }
            }

            // Tambahkan data vendor dan biaya ke formData
            const vendorIds = Array.from(form.querySelectorAll('select[name="vendor_ids[]"]')).map(s => s
                .value);
            const biayaInputs = form.querySelectorAll('input[name^="vendor_biayas"]');

            vendorIds.forEach((vendorId, idx) => {
                const biaya = biayaInputs[idx].value;
                if (biaya && biaya !== '0') {
                    formData.append(`vendor_ids[]`, vendorId);
                    formData.append(`vendor_biayas[${idx}]`, biaya);
                }
            });

            const query = new URLSearchParams(formData).toString();
            window.open(`/invoice/download?${query}`, '_blank');
            invoiceSudahDicetak = true;
            document.getElementById('btnSimpan').disabled = false;
        });

        document.getElementById('formTambahPaket').addEventListener('submit', async function(e) {
            e.preventDefault();
            if (!invoiceSudahDicetak) {
                alert('Silakan cetak invoice terlebih dahulu sebelum menyimpan.');
                return;
            }

            const token = localStorage.getItem('token');
            const form = e.target;

            const vendorIds = Array.from(form.querySelectorAll('select[name="vendor_ids[]"]')).map(s => s
                .value);
            const biayaInputs = form.querySelectorAll('input[name^="vendor_biayas"]');

            const vendorBiayas = {};
            biayaInputs.forEach((input, idx) => {
                const vendorId = vendorIds[idx];
                const biaya = parseFloat(input.value) || 0;
                vendorBiayas[vendorId] = biaya;
            });

            const formData = {
                resi: form.resi.value,
                description: form.description.value,
                kota_asal: form.kota_asal.value,
                kota_tujuan: form.kota_tujuan.value,
                weight: form.weight.value,
                volume: form.volume.value,
                jumlah_koli: form.jumlah_koli.value,
                cost: form.cost.value,
                created_by: userData.id,
                no_hp_pengirim: form.no_hp_pengirim.value,
                penerima: form.penerima.value,
                no_hp_penerima: form.no_hp_penerima.value,
                alamat_penerima: form.alamat_penerima.value,
                status: "Dalam Proses"
            };
            if (vendorIds.length > 0) {
                formData.vendor_ids = vendorIds;
                formData.vendor_biayas = vendorBiayas;
            }

            try {
                const response = await fetch('/api/paket', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (response.ok) {
                    alert('Paket berhasil ditambahkan');
                    window.location.href = '/paket';
                } else {
                    alert('Gagal tambah paket:\n' + JSON.stringify(result.errors || result.message));
                }
            } catch (err) {
                alert('Terjadi kesalahan:\n' + err.message);
            }
        });
    </script>
@endsection
