@extends('layouts.app') {{-- Atau layout lain yang kamu pakai --}}

@section('content')
    <div class="container text-center mt-5">
        <h1 class="display-1 text-danger">404</h1>
        <h2 class="mb-4">Oops! Halaman tidak ditemukan</h2>
        <p class="mb-4">Maaf, halaman yang Anda cari tidak tersedia atau URL-nya salah ketik.</p>
        <a href="{{ route('dashboard.home') }}" class="btn btn-primary">Kembali ke Dashboard</a>
    </div>
@endsection
