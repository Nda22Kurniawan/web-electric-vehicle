@extends('layouts.layout')

@section('title', 'Detail Feedback Pelanggan')

@section('page-title', 'Detail Feedback Pelanggan')
@section('page-subtitle', 'Informasi lengkap feedback dari pelanggan')

@section('content')
<div class="container-fluid px-4">
    <div class="card mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-comment me-1"></i>
                    Detail Feedback
                </div>
                <div class="btn-group">
                    <a href="{{ route('customer-feedback.edit', $customerFeedback->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <form action="{{ route('customer-feedback.destroy', $customerFeedback->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm ms-1" onclick="return confirm('Apakah Anda yakin ingin menghapus feedback ini?')">
                            <i class="fas fa-trash me-1"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <div class="mb-4">
                        <h5 class="text-primary">Informasi Pelanggan</h5>
                        <hr class="mt-1">
                        <div class="row">
                            <div class="col-sm-4 fw-bold">Nama Pelanggan:</div>
                            <div class="col-sm-8">
                                {{ $customerFeedback->customer_name ?? ($customerFeedback->customer->name ?? 'N/A') }}
                            </div>
                        </div>
                        @if($customerFeedback->customer)
                        <div class="row mt-2">
                            <div class="col-sm-4 fw-bold">Email:</div>
                            <div class="col-sm-8">
                                {{ $customerFeedback->customer->email ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-4 fw-bold">Telepon:</div>
                            <div class="col-sm-8">
                                {{ $customerFeedback->customer->phone ?? 'N/A' }}
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <h5 class="text-primary">Informasi Work Order</h5>
                        <hr class="mt-1">
                        @if($customerFeedback->workOrder)
                        <div class="row">
                            <div class="col-sm-4 fw-bold">Nomor WO:</div>
                            <div class="col-sm-8">
                                {{ $customerFeedback->workOrder->work_order_number ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-4 fw-bold">Diagnosis:</div>
                            <div class="col-sm-8">
                                {{ $customerFeedback->workOrder->diagnosis ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-4 fw-bold">Status:</div>
                            <div class="col-sm-8">
                                <span class="badge bg-{{ $customerFeedback->workOrder->status == 'completed' ? 'success' : 'warning' }}">
                                    {{ ucfirst($customerFeedback->workOrder->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-4 fw-bold">Tanggal WO:</div>
                            <div class="col-sm-8">
                                {{ $customerFeedback->workOrder->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        @else
                        <div class="alert alert-warning">
                            Data Work Order tidak ditemukan
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <div class="mb-4">
                        <h5 class="text-primary">Detail Feedback</h5>
                        <hr class="mt-1">
                        <div class="row">
                            <div class="col-sm-4 fw-bold">Rating:</div>
                            <div class="col-sm-8">
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $customerFeedback->rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                    <span class="text-dark ms-2">({{ $customerFeedback->rating }}/5)</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-sm-4 fw-bold">Komentar:</div>
                            <div class="col-sm-8">
                                <div class="border p-3 rounded bg-light">
                                    {{ $customerFeedback->comment ?? 'Tidak ada komentar' }}
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-sm-4 fw-bold">Status:</div>
                            <div class="col-sm-8">
                                @if($customerFeedback->is_public)
                                    <span class="badge bg-success">
                                        <i class="fas fa-eye me-1"></i> Publik
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-eye-slash me-1"></i> Privat
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-sm-4 fw-bold">Tanggal Feedback:</div>
                            <div class="col-sm-8">
                                {{ $customerFeedback->created_at->format('d/m/Y H:i') }}
                                <small class="text-muted">({{ $customerFeedback->created_at->diffForHumans() }})</small>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4">
                        
                        <a href="{{ route('customer-feedback.index') }}" class="btn btn-sm btn-outline-primary ms-2">
                            <i class="fas fa-list me-1"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    .fw-bold {
        color: #495057;
    }
    hr {
        border-top: 1px solid #dee2e6;
    }
    .border {
        border: 1px solid #dee2e6!important;
    }
</style>
@endsection