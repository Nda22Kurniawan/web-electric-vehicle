@extends('layouts.layout')

@section('title', 'Customer Feedback')

@section('page-title', 'Manajemen Customer Feedback')
@section('page-subtitle', 'Pengelolaan feedback pelanggan')

@section('content')
<div class="container-fluid px-4">
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filter Feedback
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('customer-feedback.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="rating" class="form-label">Rating</label>
                    <select class="form-select" id="rating" name="rating">
                        <option value="">Semua Rating</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5)</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ (4)</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>⭐⭐⭐ (3)</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>⭐⭐ (2)</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>⭐ (1)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="visibility" class="form-label">Visibilitas</label>
                    <select class="form-select" id="visibility" name="visibility">
                        <option value="">Semua</option>
                        <option value="public" {{ request('visibility') == 'public' ? 'selected' : '' }}>Publik</option>
                        <option value="private" {{ request('visibility') == 'private' ? 'selected' : '' }}>Privat</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Tanggal Dari</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Tanggal Sampai</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('customer-feedback.index') }}" class="btn btn-secondary">
                        <i class="fas fa-refresh me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-comments me-1"></i>
                    Daftar Customer Feedback
                </div>
                <div>
                    <a href="{{ route('customer-feedback.testimonials') }}" class="btn btn-info btn-sm me-2">
                        <i class="fas fa-star me-1"></i> Testimonial
                    </a>
                    <a href="{{ route('customer-feedback.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Tambah Feedback
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="feedbackTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Customer</th>
                            <th>Work Order</th>
                            <th>Rating</th>
                            <th>Komentar</th>
                            <th>Visibilitas</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feedback as $index => $item)
                        <tr>
                            <td>{{ $feedback->firstItem() + $index }}</td>
                            <td>{{ $item->customer_name }}</td>
                            <td>
                                @if($item->workOrder)
                                    <span class="badge bg-primary">#{{ $item->workOrder->id }}</span>
                                    <br>
                                    <small class="text-muted">{{ $item->workOrder->description ?? 'N/A' }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $item->rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <small class="text-muted">({{ $item->rating }}/5)</small>
                            </td>
                            <td>
                                @if($item->comment)
                                    <span class="d-inline-block text-truncate" style="max-width: 200px;" title="{{ $item->comment }}">
                                        {{ $item->comment }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->is_public)
                                    <span class="badge bg-success">
                                        <i class="fas fa-eye me-1"></i> Publik
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-eye-slash me-1"></i> Privat
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span title="{{ $item->created_at->format('d/m/Y H:i:s') }}">
                                    {{ $item->created_at->format('d/m/Y') }}
                                </span>
                                <br>
                                <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('customer-feedback.show', $item->id) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('customer-feedback.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('customer-feedback.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus feedback ini?')">
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
                            <td colspan="8" class="text-center">
                                <div class="py-4">
                                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Tidak ada data feedback</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $feedback->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    
    <!-- Statistics Card -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fs-4 fw-bold">{{ $feedback->total() }}</div>
                            <div>Total Feedback</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-comments fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fs-4 fw-bold">
                                {{ $feedback->where('is_public', true)->count() }}
                            </div>
                            <div>Feedback Publik</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-eye fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fs-4 fw-bold">
                                @php
                                    $avgRating = $feedback->avg('rating');
                                @endphp
                                {{ $avgRating ? number_format($avgRating, 1) : '0.0' }}
                            </div>
                            <div>Rating Rata-rata</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-star fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fs-4 fw-bold">
                                {{ $feedback->where('rating', '>=', 4)->count() }}
                            </div>
                            <div>Rating Tinggi (4-5)</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-thumbs-up fa-2x"></i>
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
        // Initialize DataTable with minimal configuration
        $('#feedbackTable').DataTable({
            paging: false,
            info: false,
            searching: true,
            ordering: true,
            responsive: true,
            order: [[6, 'desc']] // Sort by date (column index 6) descending
        });
    });
</script>
@endsection