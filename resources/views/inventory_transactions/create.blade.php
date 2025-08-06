@extends('layouts.layout')

@section('title', 'Tambah Transaksi Inventori')

@section('page-title', 'Tambah Transaksi Inventori')
@section('page-subtitle', 'Buat transaksi baru untuk perubahan stok suku cadang')

@section('content')
<div class="container-fluid px-4">
    
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Suku Cadang</div>
                            <div class="h5">{{ number_format($parts->count()) }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-boxes fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Stok Rendah</div>
                            <div class="h5">{{ $parts->filter(function($part) { return $part->stock < $part->min_stock; })->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Stok Habis</div>
                            <div class="h5">{{ $parts->where('stock', 0)->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Stok Aman</div>
                            <div class="h5">{{ $parts->filter(function($part) { return $part->stock >= $part->min_stock; })->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Form Section -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-plus me-1"></i>
                            Form Transaksi Inventori
                        </div>
                        <div>
                            <a href="{{ route('inventory-transactions.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('inventory-transactions.store') }}" id="transactionForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="part_id" class="form-label">
                                        Suku Cadang <span class="text-danger">*</span>
                                    </label>
                                    <select name="part_id" id="part_id" class="form-select @error('part_id') is-invalid @enderror" required>
                                        <option value="">Pilih Suku Cadang</option>
                                        @foreach($parts as $part)
                                            <option value="{{ $part->id }}" 
                                                    data-stock="{{ $part->stock }}" 
                                                    data-min-stock="{{ $part->min_stock }}"
                                                    data-cost="{{ $part->cost }}"
                                                    data-part-number="{{ $part->part_number }}"
                                                    data-vehicle-type="{{ $part->vehicle_type }}"
                                                    {{ old('part_id') == $part->id ? 'selected' : '' }}>
                                                {{ $part->name }}
                                                @if($part->part_number)
                                                    ({{ $part->part_number }})
                                                @endif
                                                - Stok: {{ number_format($part->stock) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('part_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Pilih suku cadang yang akan ditambah/dikurangi stoknya</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transaction_type" class="form-label">
                                        Jenis Transaksi <span class="text-danger">*</span>
                                    </label>
                                    <select name="transaction_type" id="transaction_type" class="form-select @error('transaction_type') is-invalid @enderror" required>
                                        <option value="">Pilih Jenis Transaksi</option>
                                        @foreach($transactionTypes as $key => $type)
                                            <option value="{{ $key }}" {{ old('transaction_type') == $key ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('transaction_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">
                                        Kuantitas <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="quantity" id="quantity" 
                                               class="form-control @error('quantity') is-invalid @enderror" 
                                               value="{{ old('quantity') }}" 
                                               placeholder="Masukkan jumlah" 
                                               min="1" 
                                               required>
                                        <span class="input-group-text">pcs</span>
                                        @error('quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">
                                        <span id="quantity-helper">Masukkan jumlah yang akan ditambahkan atau dikurangi</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Keterangan</label>
                                    <textarea name="notes" id="notes" rows="3" 
                                              class="form-control @error('notes') is-invalid @enderror" 
                                              placeholder="Masukkan keterangan tambahan (opsional)">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Keterangan tambahan mengenai transaksi ini</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Transaction Preview -->
                        <div id="transaction-preview" class="alert alert-info d-none">
                            <h6><i class="fas fa-info-circle me-1"></i> Preview Transaksi</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Suku Cadang:</small><br>
                                    <span id="preview-part" class="fw-bold">-</span>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Jenis Transaksi:</small><br>
                                    <span id="preview-type" class="fw-bold">-</span>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <small class="text-muted">Stok Saat Ini:</small><br>
                                    <span id="preview-current-stock" class="badge bg-secondary">-</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Perubahan:</small><br>
                                    <span id="preview-change" class="badge">-</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Stok Setelah Transaksi:</small><br>
                                    <span id="preview-final-stock" class="badge">-</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('inventory-transactions.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="reset" class="btn btn-outline-warning me-md-2" id="resetBtn">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save me-1"></i> Simpan Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Selected Part Info & Low Stock Alert -->
        <div class="col-lg-4">
            <!-- Selected Part Info -->
            <div class="card mb-4" id="part-info-card" style="display: none;">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-info-circle me-1"></i>
                    Informasi Suku Cadang
                </div>
                <div class="card-body">
                    <div id="part-details">
                        <div class="mb-2">
                            <small class="text-muted">Nama:</small><br>
                            <span id="info-part-name" class="fw-bold">-</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Kode Part:</small><br>
                            <span id="info-part-number" class="text-primary">-</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Tipe Kendaraan:</small><br>
                            <span id="info-vehicle-type" class="badge">-</span>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Stok Saat Ini:</small><br>
                                <span id="info-current-stock" class="h5">-</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Stok Minimum:</small><br>
                                <span id="info-min-stock" class="h5 text-warning">-</span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">Harga Satuan:</small><br>
                            <span id="info-cost" class="text-success fw-bold">-</span>
                        </div>
                        <div class="mt-2">
                            <div id="stock-status-alert"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Low Stock Parts Alert -->
            @if($parts->filter(function($part) { return $part->stock < $part->min_stock; })->count() > 0)
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Peringatan Stok Rendah
                </div>
                <div class="card-body">
                    <p class="card-text mb-3">Suku cadang berikut memiliki stok di bawah minimum:</p>
                    <div class="list-group list-group-flush">
                        @foreach($parts->filter(function($part) { return $part->stock < $part->min_stock; })->take(5) as $lowStockPart)
                            <div class="list-group-item px-0 py-2 border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $lowStockPart->name }}</h6>
                                        @if($lowStockPart->part_number)
                                            <small class="text-muted">{{ $lowStockPart->part_number }}</small>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-danger">{{ $lowStockPart->stock }}</span>
                                        <small class="text-muted d-block">Min: {{ $lowStockPart->min_stock }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @if($parts->filter(function($part) { return $part->stock < $part->min_stock; })->count() > 5)
                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    Dan {{ $parts->filter(function($part) { return $part->stock < $part->min_stock; })->count() - 5 }} lainnya...
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Quick Actions -->
            <div class="card mt-4">
                <div class="card-header">
                    <i class="fas fa-bolt me-1"></i>
                    Aksi Cepat
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('inventory-transactions.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list me-1"></i> Lihat Semua Transaksi
                        </a>
                        <a href="{{ route('inventory_transactions.report') }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-chart-bar me-1"></i> Laporan Stok
                        </a>
                        <a href="{{ route('inventory_transactions.movement') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-exchange-alt me-1"></i> Laporan Pergerakan
                        </a>
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
    // Initialize Select2 for better dropdown experience
    $('#part_id').select2({
        placeholder: 'Pilih Suku Cadang',
        allowClear: true,
        width: '100%'
    });
    
    // Part selection change handler
    $('#part_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const partId = $(this).val();
        
        if (partId) {
            // Show part info card
            $('#part-info-card').show();
            
            // Update part information
            const partName = selectedOption.text().split(' - Stok:')[0];
            const stock = parseInt(selectedOption.data('stock'));
            const minStock = parseInt(selectedOption.data('min-stock'));
            const cost = parseFloat(selectedOption.data('cost'));
            const partNumber = selectedOption.data('part-number');
            const vehicleType = selectedOption.data('vehicle-type');
            
            $('#info-part-name').text(partName);
            $('#info-part-number').text(partNumber || '-');
            $('#info-current-stock').text(stock.toLocaleString());
            $('#info-min-stock').text(minStock.toLocaleString());
            $('#info-cost').text('Rp ' + cost.toLocaleString());
            
            // Vehicle type badge
            let vehicleTypeBadge = '';
            switch(vehicleType) {
                case 'motorcycle':
                    vehicleTypeBadge = '<span class="badge bg-primary">Motor</span>';
                    break;
                case 'electric_bike':
                    vehicleTypeBadge = '<span class="badge bg-success">Sepeda Listrik</span>';
                    break;
                case 'both':
                    vehicleTypeBadge = '<span class="badge bg-info">Keduanya</span>';
                    break;
                default:
                    vehicleTypeBadge = '<span class="badge bg-secondary">' + vehicleType + '</span>';
            }
            $('#info-vehicle-type').html(vehicleTypeBadge);
            
            // Stock status alert
            let statusAlert = '';
            if (stock === 0) {
                statusAlert = '<div class="alert alert-danger py-2 mb-0"><small><i class="fas fa-times-circle me-1"></i>Stok Habis</small></div>';
                $('#info-current-stock').removeClass().addClass('h5 text-danger');
            } else if (stock < minStock) {
                statusAlert = '<div class="alert alert-warning py-2 mb-0"><small><i class="fas fa-exclamation-triangle me-1"></i>Stok Rendah</small></div>';
                $('#info-current-stock').removeClass().addClass('h5 text-warning');
            } else {
                statusAlert = '<div class="alert alert-success py-2 mb-0"><small><i class="fas fa-check-circle me-1"></i>Stok Aman</small></div>';
                $('#info-current-stock').removeClass().addClass('h5 text-success');
            }
            $('#stock-status-alert').html(statusAlert);
            
        } else {
            // Hide part info card
            $('#part-info-card').hide();
        }
        
        updatePreview();
    });
    
    // Transaction type and quantity change handlers
    $('#transaction_type, #quantity').on('change input', function() {
        updatePreview();
    });
    
    // Function to update transaction preview
    function updatePreview() {
        const partId = $('#part_id').val();
        const transactionType = $('#transaction_type').val();
        const quantity = parseInt($('#quantity').val()) || 0;
        
        if (partId && transactionType && quantity > 0) {
            const selectedOption = $('#part_id').find('option:selected');
            const partName = selectedOption.text().split(' - Stok:')[0];
            const currentStock = parseInt(selectedOption.data('stock'));
            
            $('#transaction-preview').removeClass('d-none');
            $('#preview-part').text(partName);
            
            // Transaction type display
            const transactionTypes = {
                'purchase': { text: 'Pembelian', class: 'bg-success' },
                'adjustment': { text: 'Penyesuaian', class: 'bg-warning' }
            };
            
            const typeInfo = transactionTypes[transactionType];
            $('#preview-type').html('<span class="badge ' + typeInfo.class + '">' + typeInfo.text + '</span>');
            
            // Stock calculations
            $('#preview-current-stock').text(currentStock.toLocaleString());
            
            let changeClass = 'bg-success';
            let changeText = '+' + quantity.toLocaleString();
            let finalStock = currentStock + quantity;
            
            // For future negative transactions (sales, returns with negative qty)
            if (transactionType === 'adjustment' && quantity < 0) {
                changeClass = 'bg-danger';
                changeText = quantity.toLocaleString();
                finalStock = currentStock + quantity;
            }
            
            $('#preview-change').removeClass().addClass('badge ' + changeClass).text(changeText);
            
            // Final stock color coding
            let finalStockClass = 'bg-success';
            if (finalStock === 0) {
                finalStockClass = 'bg-danger';
            } else if (finalStock < parseInt(selectedOption.data('min-stock'))) {
                finalStockClass = 'bg-warning';
            }
            
            $('#preview-final-stock').removeClass().addClass('badge ' + finalStockClass).text(finalStock.toLocaleString());
            
        } else {
            $('#transaction-preview').addClass('d-none');
        }
    }
    
    // Form validation
    $('#transactionForm').on('submit', function(e) {
        const quantity = parseInt($('#quantity').val()) || 0;
        
        if (quantity <= 0) {
            e.preventDefault();
            alert('Kuantitas harus lebih dari 0');
            $('#quantity').focus();
            return false;
        }
        
        // Show loading state
        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');
    });
    
    // Reset form handler
    $('#resetBtn').on('click', function() {
        $('#part_id').val('').trigger('change');
        $('#transaction-preview').addClass('d-none');
        $('#part-info-card').hide();
    });
    
    // Auto hide alerts
    setTimeout(function() {
        $('.alert-danger').fadeOut('slow');
    }, 5000);
});
</script>

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Custom CSS for Select2 Bootstrap theme -->
<style>
.select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
    padding-left: 12px;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
    right: 12px;
}

.select2-dropdown {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #0d6efd;
}
</style>
@endsection