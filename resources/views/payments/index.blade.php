@extends('layouts.layout')

@section('title', 'Pembayaran - ' . $workOrder->wo_number)

@section('page-title', 'Pembayaran Work Order')
@section('page-subtitle', 'Pengelolaan pembayaran untuk ' . $workOrder->wo_number)

@section('content')
<div class="container-fluid px-4">
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Work Order Summary Card -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <i class="fas fa-file-alt me-1"></i>
            Informasi Work Order
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>No. Work Order:</strong><br>
                    {{ $workOrder->wo_number }}
                </div>
                <div class="col-md-3">
                    <strong>Pelanggan:</strong><br>
                    {{ $workOrder->vehicle->customer_name ?? '-' }}
                </div>
                <div class="col-md-3">
                    <strong>Total Tagihan:</strong><br>
                    <span class="text-primary fw-bold">Rp {{ number_format($workOrder->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="col-md-3">
                    <strong>Sisa Tagihan:</strong><br>
                    <span class="text-danger fw-bold">Rp {{ number_format($workOrder->remaining_balance, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Status Pembayaran:</strong><br>
                    @if($workOrder->payment_status == 'paid')
                        <span class="badge bg-success">Lunas</span>
                    @elseif($workOrder->payment_status == 'partial')
                        <span class="badge bg-warning">Sebagian</span>
                    @else
                        <span class="badge bg-danger">Belum Bayar</span>
                    @endif
                </div>
                <div class="col-md-6">
                    <strong>Total Dibayar:</strong><br>
                    <span class="text-success fw-bold">Rp {{ number_format($workOrder->total_amount - $workOrder->remaining_balance, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-money-bill-wave me-1"></i>
                    Riwayat Pembayaran
                </div>
                <div>
                    @if($workOrder->remaining_balance > 0)
                    <a href="{{ route('payments.create', $workOrder) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i> Tambah Pembayaran
                    </a>
                    @endif
                    <a href="{{ route('work-orders.index', $workOrder) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Work Order
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="paymentsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Bayar</th>
                            <th>Jumlah</th>
                            <th>Metode Pembayaran</th>
                            <th>No. Referensi</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $index => $payment)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <span class="fw-bold text-success">
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $paymentMethods = \App\Models\Payment::paymentMethods();
                                @endphp
                                <span class="badge bg-primary">
                                    {{ $paymentMethods[$payment->payment_method] ?? $payment->payment_method }}
                                </span>
                            </td>
                            <td>
                                @if($payment->reference_number)
                                    <code>{{ $payment->reference_number }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($payment->notes)
                                    <span title="{{ $payment->notes }}">
                                        {{ Str::limit($payment->notes, 30) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('payments.show', [$workOrder, $payment]) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('payments.receipt', $payment) }}" class="btn btn-success btn-sm" title="Kwitansi">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('payments.edit', [$workOrder, $payment]) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('payments.destroy', [$workOrder, $payment]) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pembayaran ini?')">
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
                            <td colspan="7" class="text-center">
                                <div class="py-4">
                                    <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada pembayaran untuk work order ini</p>
                                    @if($workOrder->remaining_balance > 0)
                                    <a href="{{ route('payments.create', $workOrder) }}" class="btn btn-success">
                                        <i class="fas fa-plus me-1"></i> Tambah Pembayaran Pertama
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($payments->count() > 0)
            <div class="mt-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <strong>Total Pembayaran:</strong> {{ $payments->count() }} transaksi
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-success">
                            <strong>Total Dibayar:</strong> Rp {{ number_format($payments->sum('amount'), 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable with minimal configuration
        $('#paymentsTable').DataTable({
            paging: false,
            info: false,
            searching: true,
            ordering: true,
            responsive: true,
            order: [[1, 'desc']], // Sort by payment date descending
            columnDefs: [
                { targets: [2], className: 'text-end' }, // Right align amount column
                { targets: [6], orderable: false } // Disable sorting for action column
            ]
        });
    });
</script>
@endsection