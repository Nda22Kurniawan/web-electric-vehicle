@extends('layouts.layout')

@section('title', 'Detail Layanan')

@section('page-title', 'Detail Layanan')
@section('page-subtitle', 'Informasi lengkap layanan servis')

@section('content')
<div class="container-fluid px-4">
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-info-circle me-1"></i>
            Detail Layanan
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4>{{ $service->name }}</h4>
                    <p class="text-muted">{{ $service->description ?? 'Tidak ada deskripsi' }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <h5>Rp {{ number_format($service->price, 0, ',', '.') }}</h5>
                    <p class="text-muted">Durasi: {{ $service->duration_estimate ? $service->duration_estimate.' menit' : '-' }}</p>
                </div>
            </div>
            
            <div class="mb-3">
                <h5>Kategori:</h5>
                <div>
                    @forelse($service->categories as $category)
                        <span class="badge bg-secondary">{{ $category->name }}</span>
                    @empty
                        <span class="text-muted">Tidak ada kategori</span>
                    @endforelse
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('services.edit', $service->id) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <form action="{{ route('services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection