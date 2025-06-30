@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

    <div class="container">
        <h2>Dashboard</h2>
        <div class="container mt-4">
            <div class="row">

                <!-- Card Pendapatan -->
                <div class="col-md-4 mb-3">
                    <div class="card text-white bg-success h-100 shadow">
                        <div class="card-body">
                            <h6 class="card-title">Total Pendapatan</h6>
                            <h4 class="card-text">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h4>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="{{ route('biaya.index') }}" class="btn btn-sm btn-light">Lihat Detail</a>
                        </div>
                    </div>
                </div>

                <!-- Card Pengeluaran -->
                <div class="col-md-4 mb-3">
                    <div class="card text-white bg-danger h-100 shadow">
                        <div class="card-body">
                            <h6 class="card-title">Total Pengeluaran</h6>
                            <h4 class="card-text">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h4>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="{{ route('biaya.index') }}" class="btn btn-sm btn-light">Lihat Detail</a>
                        </div>
                    </div>
                </div>

                <!-- Card Jumlah Paket -->
                <div class="col-md-4 mb-3">
                    <div class="card text-white bg-primary h-100 shadow">
                        <div class="card-body">
                            <h6 class="card-title">Jumlah Paket Terkirim</h6>
                            <h4 class="card-text">{{ $jumlahPaket }} Paket</h4>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="{{ route('paket.index') }}" class="btn btn-sm btn-light">Lihat Detail</a>
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
