@extends('layouts.layout')

@section('title', 'Work Orders')

@section('page-title', 'Manajemen Work Orders')
@section('page-subtitle', 'Pengelolaan order pekerjaan')

@section('content')
<div class="container-fluid px-4">
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filter & Pencarian
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('work-orders.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Pencarian</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="No. WO, Nama, Telepon..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Dalam Proses</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="payment_status" class="form-label">Status Bayar</label>
                        <select class="form-select" id="payment_status" name="payment_status">
                            <option value="">Semua</option>
                            <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                            <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Sebagian</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="mechanic_id" class="form-label">Mekanik</label>
                        <select class="form-select" id="mechanic_id" name="mechanic_id">
                            <option value="">Semua Mekanik</option>
                            @foreach($mechanics as $mechanic)
                            <option value="{{ $mechanic->id }}" {{ request('mechanic_id') == $mechanic->id ? 'selected' : '' }}>
                                {{ $mechanic->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="start_date" class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="end_date" class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="sort_by" class="form-label">Urutkan</label>
                        <select class="form-select" id="sort_by" name="sort_by">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tanggal Dibuat</option>
                            <option value="work_order_number" {{ request('sort_by') == 'work_order_number' ? 'selected' : '' }}>No. Work Order</option>
                            <option value="customer_name" {{ request('sort_by') == 'customer_name' ? 'selected' : '' }}>Nama Pelanggan</option>
                            <option value="total_amount" {{ request('sort_by') == 'total_amount' ? 'selected' : '' }}>Total</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="sort_direction" class="form-label">Arah</label>
                        <select class="form-select" id="sort_direction" name="sort_direction">
                            <option value="desc" {{ request('sort_direction') == 'desc' ? 'selected' : '' }}>Terbaru</option>
                            <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>Terlama</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="btn-group w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('work-orders.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-clipboard-list me-1"></i>
                    Daftar Work Orders
                    @if(request()->hasAny(['search', 'status', 'payment_status', 'mechanic_id', 'start_date', 'end_date']))
                        <small class="text-muted">({{ $workOrders->total() }} hasil filter)</small>
                    @endif
                </div>
                <a href="{{ route('work-orders.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Buat Work Order
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="workOrdersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Work Order</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Kendaraan</th>
                            <th>Mekanik</th>
                            <th>Status</th>
                            <th>Status Bayar</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workOrders as $index => $workOrder)
                        <tr>
                            <td>{{ $workOrders->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $workOrder->work_order_number }}</strong>
                                @if($workOrder->appointment_id)
                                    <br><small class="text-info"><i class="fas fa-calendar-check"></i> Dari Appointment</small>
                                @endif
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($workOrder->created_at)->format('d/m/Y') }}
                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($workOrder->created_at)->format('H:i') }}</small>
                            </td>
                            <td>
                                {{-- Use accessor methods from model --}}
                                <strong>{{ $workOrder->customer_name }}</strong>
                                <br><small class="text-muted">{{ $workOrder->customer_phone }}</small>
                            </td>
                            <td>
                                @if($workOrder->vehicle)
                                    {{ $workOrder->vehicle->brand }} {{ $workOrder->vehicle->model }}
                                    <br><small class="text-muted">{{ $workOrder->vehicle->license_plate }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($workOrder->mechanic)
                                    {{ $workOrder->mechanic->name }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @switch($workOrder->status)
                                    @case('pending')
                                        <span class="badge bg-warning">Pending</span>
                                        @break
                                    @case('in_progress')
                                        <span class="badge bg-primary">Dalam Proses</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-success">Selesai</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">Dibatalkan</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $workOrder->status }}</span>
                                @endswitch
                            </td>
                            <td>
                                @switch($workOrder->payment_status)
                                    @case('paid')
                                        <span class="badge bg-success">Lunas</span>
                                        @break
                                    @case('partial')
                                        <span class="badge bg-warning">Sebagian</span>
                                        @break
                                    @case('unpaid')
                                        <span class="badge bg-danger">Belum Bayar</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $workOrder->payment_status }}</span>
                                @endswitch
                            </td>
                            <td class="text-end">
                                <strong>Rp {{ number_format($workOrder->total_amount, 0, ',', '.') }}</strong>
                                @if($workOrder->remaining_balance > 0)
                                    <br><small class="text-danger">Sisa: Rp {{ number_format($workOrder->remaining_balance, 0, ',', '.') }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('work-orders.show', $workOrder->id) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($workOrder->status != 'completed' && $workOrder->status != 'cancelled')
                                    <a href="{{ route('work-orders.edit', $workOrder->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" title="Lainnya">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('work-orders.invoice', $workOrder->id) }}">
                                                <i class="fas fa-file-invoice me-2"></i>Invoice
                                            </a></li>
                                            <li><a class="dropdown-item" href="{{ route('work-orders.receipt', $workOrder->id) }}">
                                                <i class="fas fa-receipt me-2"></i>Kwitansi
                                            </a></li>
                                            @if($workOrder->remaining_balance > 0)
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="{{ route('payments.create', $workOrder->id) }}">
                                                <i class="fas fa-money-bill-wave me-2"></i>Tambah Pembayaran
                                            </a></li>
                                            @endif
                                            <li><a class="dropdown-item" href="{{ route('payments.index', $workOrder->id) }}">
                                                <i class="fas fa-list me-2"></i>Riwayat Pembayaran
                                            </a></li>
                                            @if($workOrder->status != 'completed' && $workOrder->status != 'cancelled')
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('work-orders.update-status', $workOrder->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="in_progress">
                                                    <button type="submit" class="dropdown-item" 
                                                            onclick="return confirm('Ubah status ke Dalam Proses?')"
                                                            @if($workOrder->status == 'in_progress') disabled @endif>
                                                        <i class="fas fa-play me-2"></i>Mulai Proses
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('work-orders.update-status', $workOrder->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="dropdown-item" 
                                                            onclick="return confirm('Tandai sebagai Selesai?')"
                                                            @if($workOrder->status == 'completed') disabled @endif>
                                                        <i class="fas fa-check me-2"></i>Selesai
                                                    </button>
                                                </form>
                                            </li>
                                            @endif
                                            @if($workOrder->status == 'pending')
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('work-orders.destroy', $workOrder->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus work order ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash me-2"></i>Hapus
                                                    </button>
                                                </form>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">
                                <div class="py-4">
                                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">
                                        @if(request()->hasAny(['search', 'status', 'payment_status', 'mechanic_id', 'start_date', 'end_date']))
                                            Tidak ada work order yang sesuai dengan filter
                                        @else
                                            Belum ada work order
                                        @endif
                                    </p>
                                    @if(!request()->hasAny(['search', 'status', 'payment_status', 'mechanic_id', 'start_date', 'end_date']))
                                    <a href="{{ route('work-orders.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> Buat Work Order Pertama
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($workOrders->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Menampilkan {{ $workOrders->firstItem() }} - {{ $workOrders->lastItem() }} dari {{ $workOrders->total() }} work orders
                </div>
                <div>
                    {{ $workOrders->withQueryString()->links() }}
                </div>
            </div>
            @endif

            {{-- Statistics Summary - Fixed to work properly with pagination --}}
            @if($workOrders->count() > 0)
            <div class="mt-3">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="alert alert-info mb-0">
                            <strong>Total Work Orders:</strong> {{ $workOrders->total() }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-warning mb-0">
                            <strong>Pending:</strong> 
                            {{ \App\Models\WorkOrder::where('status', 'pending')
                                ->when(request('search'), function($q, $search) {
                                    return $q->where(function($subQ) use ($search) {
                                        $subQ->where('work_order_number', 'like', "%{$search}%")
                                             ->orWhere('customer_name', 'like', "%{$search}%")
                                             ->orWhere('customer_phone', 'like', "%{$search}%");
                                    });
                                })
                                ->when(request('mechanic_id'), function($q, $mechanicId) {
                                    return $q->where('mechanic_id', $mechanicId);
                                })
                                ->when(request('payment_status'), function($q, $paymentStatus) {
                                    return $q->where('payment_status', $paymentStatus);
                                })
                                ->when(request('start_date'), function($q, $startDate) {
                                    return $q->whereDate('created_at', '>=', $startDate);
                                })
                                ->when(request('end_date'), function($q, $endDate) {
                                    return $q->whereDate('created_at', '<=', $endDate);
                                })
                                ->count() }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-primary mb-0">
                            <strong>Dalam Proses:</strong> 
                            {{ \App\Models\WorkOrder::where('status', 'in_progress')
                                ->when(request('search'), function($q, $search) {
                                    return $q->where(function($subQ) use ($search) {
                                        $subQ->where('work_order_number', 'like', "%{$search}%")
                                             ->orWhere('customer_name', 'like', "%{$search}%")
                                             ->orWhere('customer_phone', 'like', "%{$search}%");
                                    });
                                })
                                ->when(request('mechanic_id'), function($q, $mechanicId) {
                                    return $q->where('mechanic_id', $mechanicId);
                                })
                                ->when(request('payment_status'), function($q, $paymentStatus) {
                                    return $q->where('payment_status', $paymentStatus);
                                })
                                ->when(request('start_date'), function($q, $startDate) {
                                    return $q->whereDate('created_at', '>=', $startDate);
                                })
                                ->when(request('end_date'), function($q, $endDate) {
                                    return $q->whereDate('created_at', '<=', $endDate);
                                })
                                ->count() }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-success mb-0">
                            <strong>Selesai:</strong> 
                            {{ \App\Models\WorkOrder::where('status', 'completed')
                                ->when(request('search'), function($q, $search) {
                                    return $q->where(function($subQ) use ($search) {
                                        $subQ->where('work_order_number', 'like', "%{$search}%")
                                             ->orWhere('customer_name', 'like', "%{$search}%")
                                             ->orWhere('customer_phone', 'like', "%{$search}%");
                                    });
                                })
                                ->when(request('mechanic_id'), function($q, $mechanicId) {
                                    return $q->where('mechanic_id', $mechanicId);
                                })
                                ->when(request('payment_status'), function($q, $paymentStatus) {
                                    return $q->where('payment_status', $paymentStatus);
                                })
                                ->when(request('start_date'), function($q, $startDate) {
                                    return $q->whereDate('created_at', '>=', $startDate);
                                })
                                ->when(request('end_date'), function($q, $endDate) {
                                    return $q->whereDate('created_at', '<=', $endDate);
                                })
                                ->count() }}
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
        $('#workOrdersTable').DataTable({
            paging: false,
            info: false,
            searching: false, // We use our own search
            ordering: false, // We use our own sorting
            responsive: true
        });

        // Auto submit form on filter change
        $('#status, #payment_status, #mechanic_id, #sort_by, #sort_direction').change(function() {
            $('#filterForm').submit();
        });

        // Clear filters
        $('#clearFilters').click(function() {
            window.location.href = "{{ route('work-orders.index') }}";
        });
    });
</script>
@endsection