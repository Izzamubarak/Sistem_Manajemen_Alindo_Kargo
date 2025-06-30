@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

    <div class="container">
        <h2>Dashboard</h2>

        <div class="row">
    <div class="col-md-4">
        <div class="card mb-4 shadow-sm border-0 text-white" style="background-color: #28a745;">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0">Total Pendapatan</h6>
                    <h4 class="fw-bold">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h4>
                    <a href="{{ route('biaya.index') }}" class="btn btn-light btn-sm mt-2">Lihat Detail</a>
                </div>
                <div>
                    <i class="bi bi-cash-stack fs-1 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4 shadow-sm border-0 text-white" style="background-color: #dc3545;">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0">Total Pengeluaran</h6>
                    <h4 class="fw-bold">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h4>
                    <a href="{{ route('biaya.index') }}" class="btn btn-light btn-sm mt-2">Lihat Detail</a>
                </div>
                <div>
                    <i class="bi bi-credit-card-2-back fs-1 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4 shadow-sm border-0 text-white" style="background-color: #007bff;">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0">Jumlah Paket Terkirim</h6>
                    <h4 class="fw-bold">{{ $jumlahPaket }} Paket</h4>
                    <a href="{{ route('paket.index') }}" class="btn btn-light btn-sm mt-2">Lihat Detail</a>
                </div>
                <div>
                    <i class="bi bi-box-seam fs-1 text-white-50"></i>
                </div>
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
    </script>
@endsection
