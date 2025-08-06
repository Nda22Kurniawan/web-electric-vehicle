@extends('layouts.layout')

@section('title', 'Detail Kategori Layanan')

@section('page-title', 'Detail Kategori Layanan')
@section('page-subtitle', 'Informasi lengkap kategori layanan')

@section('content')
<div class="container-fluid px-4">
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Category Profile Card -->
    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-tag me-1"></i>
                    Informasi Kategori
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="fas fa-tags fa-3x text-muted"></i>
                        </div>
                        <h4 class="mb-1">{{ $serviceCategory->name }}</h4>
                        <div class="mb-2">
                            @php
                                $servicesCount = $serviceCategory->services()->count();
                            @endphp
                            @if($servicesCount > 0)
                                <span class="badge bg-success fs-6">{{ $servicesCount }} Layanan</span>
                            @else
                                <span class="badge bg-warning fs-6">Belum Ada Layanan</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row text-start">
                        <div class="col-12 mb-3">
                            <p class="mb-2"><strong>Deskripsi:</strong></p>
                            <p class="text-muted">{{ $serviceCategory->description ?: 'Tidak ada deskripsi untuk kategori ini.' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-2"><strong>Dibuat:</strong></p>
                            <p class="text-muted mb-3">{{ $serviceCategory->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-2"><strong>Terakhir Update:</strong></p>
                            <p class="text-muted mb-3">{{ $serviceCategory->updated_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <a href="{{ route('service-categories.edit', $serviceCategory) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('service-categories.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-8 col-lg-7">
            @php
                $services = $serviceCategory->services()->get();
                $totalServices = $services->count();
                $avgPrice = $services->avg('price');
                $minPrice = $services->min('price');
                $maxPrice = $services->max('price');
                $avgDuration = $services->avg('duration_estimate');
            @endphp

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="small text-white-50">Total Layanan</div>
                                    <div class="h4">{{ $totalServices }}</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-tools fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="small text-white-50">Rata-rata Harga</div>
                                    <div class="h5">{{ $avgPrice ? 'Rp ' . number_format($avgPrice, 0, ',', '.') : '-' }}</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-money-bill-wave fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="small text-white-50">Harga Terendah</div>
                                    <div class="h5">{{ $minPrice ? 'Rp ' . number_format($minPrice, 0, ',', '.') : '-' }}</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-arrow-down fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="small text-white-50">Harga Tertinggi</div>
                                    <div class="h5">{{ $maxPrice ? 'Rp ' . number_format($maxPrice, 0, ',', '.') : '-' }}</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-arrow-up fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services List -->
            @if($totalServices > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-list me-1"></i>
                    Daftar Layanan dalam Kategori
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Layanan</th>
                                    <th>Harga</th>
                                    <th>Durasi</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($services as $index => $service)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $service->name }}</strong></td>
                                    <td class="text-success fw-bold">Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                                    <td>
                                        @if($service->duration_estimate)
                                            <span class="badge bg-info">{{ $service->duration_estimate }} menit</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($service->description, 50) ?: '-' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('services.show', $service->id) }}" class="btn btn-info btn-sm" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('services.edit', $service->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Price Range Analysis -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Analisis Harga Layanan
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Harga Terendah:</strong></td>
                                    <td class="text-end">Rp {{ number_format($minPrice, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Harga Tertinggi:</strong></td>
                                    <td class="text-end">Rp {{ number_format($maxPrice, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td><strong>Selisih Harga:</strong></td>
                                    <td class="text-end text-primary fw-bold">Rp {{ number_format($maxPrice - $minPrice, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Rata-rata Harga:</strong></td>
                                    <td class="text-end">Rp {{ number_format($avgPrice, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Rata-rata Durasi:</strong></td>
                                    <td class="text-end">{{ $avgDuration ? number_format($avgDuration, 0) . ' menit' : 'Tidak ada data' }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td><strong>Total Layanan:</strong></td>
                                    <td class="text-end text-success fw-bold">{{ $totalServices }} layanan</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @else
            <!-- Empty State -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-list me-1"></i>
                    Daftar Layanan dalam Kategori
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum Ada Layanan</h5>
                    <p class="text-muted">Kategori ini belum memiliki layanan. Tambahkan layanan pertama untuk kategori ini.</p>
                    <a href="{{ route('services.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Tambah Layanan
                    </a>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-bolt me-1"></i>
                    Aksi Cepat
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-grid">
                                <a href="{{ route('services.create') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-plus me-2"></i>
                                    Tambah Layanan Baru
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-grid">
                                <a href="{{ route('service-categories.edit', $serviceCategory) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-edit me-2"></i>
                                    Edit Kategori
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-grid">
                                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i>
                                    Cetak Detail
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto hide success alert after 5 seconds
        setTimeout(function() {
            $('.alert-success').fadeOut('slow');
        }, 5000);
    });
</script>
@endsection