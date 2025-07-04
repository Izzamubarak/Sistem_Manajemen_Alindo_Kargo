@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    @include('partials.header')

    <div class="container">
        <div class="row">
            {{-- Total Pendapatan --}}
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-success h-100 shadow">
                    <div class="card-header">Total Pendapatan</div>
                    <div class="card-body">
                        <h4 class="card-text">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h4>
                        <a href="{{ route('biaya.index') }}" id="btn-pendapatan" class="btn btn-outline-light btn-sm mt-3">Lihat Detail</a>
                    </div>
                </div>
            </div>

            {{-- Total Pengeluaran --}}
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-danger h-100 shadow">
                    <div class="card-header">Total Pengeluaran</div>
                    <div class="card-body">
                        <h4 class="card-text">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h4>
                        <a href="{{ route('biaya.index') }}" id="btn-pengeluaran" class="btn btn-outline-light btn-sm mt-3">Lihat Detail</a>
                    </div>
                </div>
            </div>

            {{-- Jumlah Paket --}}
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-primary h-100 shadow">
                    <div class="card-header">Jumlah Paket Terkirim</div>
                    <div class="card-body">
                        <h4 class="card-text">{{ $jumlahPaket }} Paket</h4>
                        <a href="{{ route('paket.index') }}" id="btn-paket" class="btn btn-outline-light btn-sm mt-3">Lihat Detail</a>
                    </div>
                </div>
            </div>
        </div>

        <h4>Grafik Pesanan Per Bulan</h4>
        <canvas id="myAreaChart"></canvas>

        <h4 class="mt-5">Grafik Kota Tujuan</h4>
        <canvas id="myBarChart"></canvas>
    </div>

    @php
        use Carbon\Carbon;
        $bulanKeys = array_keys($pesananBulanan);
        $bulanLabel = [];

        foreach ($bulanKeys as $b) {
            $bulanLabel[] = Carbon::create()->month($b)->translatedFormat('F');
        }
    @endphp

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const pesananData = @json(array_values($pesananBulanan));
        const bulanLabel = @json($bulanLabel);
        const kotaLabel = @json(array_keys($kotaTujuan));
        const kotaData = @json(array_values($kotaTujuan));

        new Chart(document.getElementById('myAreaChart'), {
            type: 'line',
            data: {
                labels: bulanLabel,
                datasets: [{
                    label: 'Pesanan',
                    data: pesananData,
                    borderColor: 'blue',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        new Chart(document.getElementById('myBarChart'), {
            type: 'bar',
            data: {
                labels: kotaLabel,
                datasets: [{
                    label: 'Jumlah Paket',
                    data: kotaData,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // 🔐 Batasi tombol berdasarkan role (dari token lokal)
        document.addEventListener('DOMContentLoaded', async () => {
            const token = localStorage.getItem('token');
            const btnPendapatan = document.getElementById('btn-pendapatan');
            const btnPengeluaran = document.getElementById('btn-pengeluaran');
            const btnPaket = document.getElementById('btn-paket');

            if (!token) return;

            try {
                const res = await fetch('/api/user', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });

                const user = await res.json();
                const role = user.role;

                if (['admin', 'tim-operasional'].includes(role)) {
                    if (btnPendapatan) btnPendapatan.style.display = 'none';
                    if (btnPengeluaran) btnPengeluaran.style.display = 'none';
                    if (btnPaket) btnPaket.style.display = 'none';
                }
            } catch (error) {
                console.warn('Gagal cek role:', error.message);
            }
        });
    </script>
@endsection
