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
                </div>
                <a href="{{ route('service-categories.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Tambah Kategori
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="categoriesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th>Jumlah Layanan</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $index => $category)
                        <tr>
                            <td>{{ $categories->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $category->name }}</strong>
                            </td>
                            <td>{{ $category->description ?: '-' }}</td>
                            <td>
                                @if($category->services_count ?? 0 > 0)
                                    <span class="badge bg-info">{{ $category->services_count ?? 0 }} layanan</span>
                                @else
                                    <span class="text-muted">Belum ada layanan</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $category->created_at->format('d/m/Y H:i') }}
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
            
            <div class="d-flex justify-content-center mt-3">
                {{ $categories->links() }}
            </div>
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
                            <div class="h5">{{ $categories->total() }}</div>
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
                            <div class="h5">{{ $categories->where('services_count', '>', 0)->count() }}</div>
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
                            <div class="h5">{{ $categories->where('services_count', 0)->count() }}</div>
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
        // Initialize DataTable with minimal configuration
        $('#categoriesTable').DataTable({
            paging: false,
            info: false,
            searching: true,
            ordering: true,
            responsive: true,
            columnDefs: [
                { orderable: false, targets: [5] } // Disable sorting on action column
            ]
        });
        
        // Auto hide success alert after 5 seconds
        setTimeout(function() {
            $('.alert-success').fadeOut('slow');
        }, 5000);
    });
</script>
@endsection