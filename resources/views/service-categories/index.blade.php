@extends('layouts.layout')

@section('title', 'Kategori Layanan')

@section('page-title', 'Manajemen Kategori Layanan')
@section('page-subtitle', 'Pengelolaan kategori layanan servis')

@section('content')
<div class="container-fluid px-4">
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-tags me-1"></i>
                    Daftar Kategori Layanan
                    <small class="text-muted">({{ $categories->total() }} kategori)</small>
                </div>
                <a href="{{ route('service-categories.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Tambah Kategori
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="8%">No</th>
                            <th width="25%">Nama Kategori</th>
                            <th width="35%">Deskripsi</th>
                            <th width="15%">Jumlah Layanan</th>
                            <th width="12%">Dibuat</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $index => $category)
                        <tr>
                            <td>{{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}</td>
                            <td>
                                <strong>{{ $category->name }}</strong>
                            </td>
                            <td>{{ $category->description ?: '-' }}</td>
                            <td>
                                @php
                                    $servicesCount = $category->services()->count();
                                @endphp
                                @if($servicesCount > 0)
                                    <span class="badge bg-info">{{ $servicesCount }} layanan</span>
                                @else
                                    <span class="text-muted">Belum ada layanan</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $category->created_at->format('d/m/Y') }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('service-categories.show', $category->id) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('service-categories.edit', $category->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('service-categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Semua layanan dalam kategori ini akan terpengaruh.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="py-4">
                                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum ada kategori layanan</h5>
                                    <p class="text-muted">Mulai dengan menambahkan kategori layanan pertama Anda.</p>
                                    <a href="{{ route('service-categories.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> Tambah Kategori Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Custom Pagination -->
            @if($categories->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Menampilkan {{ $categories->firstItem() }} sampai {{ $categories->lastItem() }} dari {{ $categories->total() }} kategori
                </div>
                
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Button --}}
                        @if($categories->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $categories->previousPageUrl() }}">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            </li>
                        @endif

                        {{-- Page Numbers --}}
                        @php
                            $start = max($categories->currentPage() - 2, 1);
                            $end = min($start + 4, $categories->lastPage());
                            $start = max($end - 4, 1);
                        @endphp

                        @if($start > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ $categories->url(1) }}">1</a>
                            </li>
                            @if($start > 2)
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            @endif
                        @endif

                        @for($i = $start; $i <= $end; $i++)
                            @if($i == $categories->currentPage())
                                <li class="page-item active">
                                    <span class="page-link">{{ $i }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $categories->url($i) }}">{{ $i }}</a>
                                </li>
                            @endif
                        @endfor

                        @if($end < $categories->lastPage())
                            @if($end < $categories->lastPage() - 1)
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            @endif
                            <li class="page-item">
                                <a class="page-link" href="{{ $categories->url($categories->lastPage()) }}">{{ $categories->lastPage() }}</a>
                            </li>
                        @endif

                        {{-- Next Button --}}
                        @if($categories->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $categories->nextPageUrl() }}">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">
                                    Next <i class="fas fa-chevron-right"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Info Cards -->
    <div class="row">
        <div class="col-xl-4 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Kategori</div>
                            <div class="h5">{{ App\Models\ServiceCategory::count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tags fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Kategori Aktif</div>
                            <div class="h5">{{ App\Models\ServiceCategory::has('services')->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Kategori Kosong</div>
                            <div class="h5">{{ App\Models\ServiceCategory::doesntHave('services')->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
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
                        <a href="{{ route('service-categories.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>
                            Tambah Kategori Baru
                        </a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-grid">
                        <a href="{{ route('services.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-tools me-2"></i>
                            Kelola Layanan
                        </a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-grid">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>
                            Cetak Daftar
                        </button>
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