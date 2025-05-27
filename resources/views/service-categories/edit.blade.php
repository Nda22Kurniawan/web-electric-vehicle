@extends('layouts.layout')

@section('title', 'Edit Kategori Layanan')

@section('page-title', 'Edit Kategori Layanan')
@section('page-subtitle', 'Form edit kategori layanan')

@section('content')
<div class="container-fluid px-4">
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Form Edit Kategori Layanan
        </div>
        <div class="card-body">
            <form action="{{ route('service-categories.update', $serviceCategory->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Terjadi kesalahan!</strong> Silakan periksa form di bawah.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $serviceCategory->name) }}" 
                               placeholder="Masukkan nama kategori" required>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" 
                                  placeholder="Masukkan deskripsi kategori (opsional)">{{ old('description', $serviceCategory->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('service-categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('service-categories.show', $serviceCategory->id) }}" class="btn btn-info">
                                    <i class="fas fa-eye me-1"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Category Stats Card -->
    <div class="card bg-light mb-4">
        <div class="card-header">
            <i class="fas fa-chart-bar me-1"></i>
            Statistik Kategori
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title">Informasi Dasar</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Dibuat Pada
                            <span class="badge bg-primary rounded-pill">
                                {{ $serviceCategory->created_at->format('d M Y H:i') }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Terakhir Diupdate
                            <span class="badge bg-primary rounded-pill">
                                {{ $serviceCategory->updated_at->format('d M Y H:i') }}
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5 class="card-title">Layanan Terkait</h5>
                    @if($serviceCategory->services_count > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Kategori ini memiliki {{ $serviceCategory->services_count }} layanan. 
                            Perubahan mungkin mempengaruhi layanan terkait.
                        </div>
                        <a href="{{ route('services.index', ['category' => $serviceCategory->id]) }}" 
                           class="btn btn-sm btn-outline-primary">
                            Lihat Daftar Layanan
                        </a>
                    @else
                        <div class="alert alert-secondary">
                            <i class="fas fa-info-circle me-2"></i>
                            Belum ada layanan dalam kategori ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Danger Zone Card -->
    <div class="card border-danger">
        <div class="card-header text-white bg-danger">
            <i class="fas fa-exclamation-triangle me-1"></i>
            Zona Berbahaya
        </div>
        <div class="card-body">
            <h5 class="card-title text-danger">Hapus Kategori Ini</h5>
            <p class="card-text">
                Menghapus kategori akan mempengaruhi semua layanan yang terkait dengannya. 
                Pastikan tidak ada layanan yang menggunakan kategori ini sebelum menghapus.
            </p>
            
            @if($serviceCategory->services_count == 0)
                <form action="{{ route('service-categories.destroy', $serviceCategory->id) }}" method="POST" 
                      class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini secara permanen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Hapus Kategori
                    </button>
                </form>
            @else
                <button class="btn btn-danger" disabled title="Tidak dapat menghapus kategori yang memiliki layanan">
                    <i class="fas fa-trash-alt me-1"></i> Hapus Kategori
                </button>
                <small class="text-danger d-block mt-2">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    Tidak dapat menghapus kategori karena terdapat {{ $serviceCategory->services_count }} layanan terkait.
                </small>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto focus on name field
        $('#name').focus();
        
        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert-success, .alert-danger').fadeOut('slow');
        }, 5000);
    });
</script>
@endsection