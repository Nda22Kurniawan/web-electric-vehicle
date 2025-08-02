@extends('layouts.layout')

@section('title', 'Tambah Kendaraan')

@section('page-title', 'Manajemen Kendaraan')
@section('page-subtitle', 'Tambah kendaraan baru')

@section('content')
<div class="container-fluid px-4">
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-motorcycle me-1"></i>
            Form Tambah Kendaraan
        </div>
        <div class="card-body">
            <form action="{{ route('vehicles.store') }}" method="POST">
                @csrf
                
                <!-- Customer Selection Section -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Informasi Pelanggan</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="customer_id" class="form-label">Pelanggan <span class="text-danger">*</span></label>
                                <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                                    <option value="">-- Pilih Pelanggan --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" 
                                                data-name="{{ $customer->name }}" 
                                                data-phone="{{ $customer->phone }}"
                                                data-email="{{ $customer->email }}"
                                                {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} - {{ $customer->phone }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Jika pelanggan belum terdaftar, silakan daftarkan terlebih dahulu di menu Manajemen User.
                                </div>
                            </div>
                        </div>

                        <!-- Selected Customer Info Display -->
                        <div id="customer_info" class="alert alert-info" style="display: none;">
                            <strong>Informasi Pelanggan:</strong><br>
                            <div id="customer_details"></div>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Information Section -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Informasi Kendaraan</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="type" class="form-label">Jenis Kendaraan <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="motorcycle" {{ old('type') == 'motorcycle' ? 'selected' : '' }}>Sepeda Motor</option>
                                    <option value="electric_bike" {{ old('type') == 'electric_bike' ? 'selected' : '' }}>Sepeda Listrik</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="brand" class="form-label">Merek <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('brand') is-invalid @enderror" id="brand" name="brand" value="{{ old('brand') }}" required>
                                @error('brand')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="model" class="form-label">Model <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('model') is-invalid @enderror" id="model" name="model" value="{{ old('model') }}" required>
                                @error('model')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="year" class="form-label">Tahun <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('year') is-invalid @enderror" id="year" name="year" value="{{ old('year') }}" min="1900" max="{{ date('Y') + 1 }}" required>
                                @error('year')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            
                            <div class="col-md-3">
                                <label for="license_plate" class="form-label">Plat Nomor <span class="text-danger">*</span></label>
                                <input type="text" class="form-control text-uppercase @error('license_plate') is-invalid @enderror" id="license_plate" name="license_plate" value="{{ old('license_plate') }}" required>
                                @error('license_plate')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">Contoh: B 1234 XYZ</div>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="color" class="form-label">Warna</label>
                                <input type="text" class="form-control @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color') }}">
                                @error('color')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            
                            <div class="col-md-3">
                                <label for="color_preview_label" class="form-label">Pratinjau Warna</label>
                                <div class="color-preview rounded" style="height: 38px; border: 1px solid #ced4da; background-color: {{ old('color') ? old('color') : 'transparent' }}"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Catatan tambahan tentang kendaraan...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Kendaraan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Handle customer selection
        $('#customer_id').change(function() {
            const selectedOption = $(this).find('option:selected');
            if (selectedOption.val()) {
                // Show customer info
                const customerName = selectedOption.data('name');
                const customerPhone = selectedOption.data('phone');
                const customerEmail = selectedOption.data('email');
                
                let customerDetails = `
                    <i class="fas fa-user me-1"></i> <strong>Nama:</strong> ${customerName}<br>
                    <i class="fas fa-phone me-1"></i> <strong>Telepon:</strong> ${customerPhone}
                `;
                
                if (customerEmail) {
                    customerDetails += `<br><i class="fas fa-envelope me-1"></i> <strong>Email:</strong> ${customerEmail}`;
                }
                
                $('#customer_details').html(customerDetails);
                $('#customer_info').show();
            } else {
                $('#customer_info').hide();
            }
        });
        
        // Trigger change event if there's a selected customer (for old input)
        if ($('#customer_id').val()) {
            $('#customer_id').trigger('change');
        }
        
        // Color preview functionality
        $('#color').on('input', function() {
            const color = $(this).val();
            $('.color-preview').css('background-color', color || 'transparent');
        });
        
        // License plate formatting
        $('#license_plate').on('input', function() {
            $(this).val($(this).val().toUpperCase());
        });
        
        // Form validation
        $('form').on('submit', function(e) {
            if (!$('#customer_id').val()) {
                e.preventDefault();
                alert('Silakan pilih pelanggan terlebih dahulu');
                $('#customer_id').focus();
                return false;
            }
        });
    });
</script>
@endsection