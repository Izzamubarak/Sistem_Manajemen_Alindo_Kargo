@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

    <div class="container">
        <h2>Dashboard</h2>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>Total Pendapatan</h5>
                        <h3>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                    </div>
                    <div class="card-footer">
                        <a href="{{ url('/biaya') }}" class="text-white">Lihat Detail</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5>Jumlah Paket Terkirim</h5>
                        <h3>{{ $jumlahPaket }} Paket</h3>
                    </div>
                    <div class="card-footer">
                        <a href="{{ url('/paket') }}" class="text-white">Lihat Detail</a>
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
