@extends('layouts.layout')

@section('title', 'Buat Work Order Baru')

@section('page-title', 'Buat Work Order Baru')
@section('page-subtitle', 'Formulir pembuatan work order baru')

@section('content')
<div class="container-fluid px-4">
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-plus-circle me-1"></i>
                    Buat Work Order Baru
                    @if(isset($appointment))
                        <small class="text-info ms-2">
                            <i class="fas fa-calendar-check"></i> Dari Appointment
                        </small>
                    @endif
                </div>
                <div>
                    <a href="{{ route('work-orders.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('work-orders.store') }}" method="POST" id="workOrderForm">
                @csrf
                
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                
                <!-- Customer Information -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="fas fa-user me-2"></i> Informasi Pelanggan</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="customer_id" class="form-label">Pilih Pelanggan</label>
                                <select class="form-select @error('customer_id') is-invalid @enderror" 
                                        id="customer_id" name="customer_id" required>
                                    <option value="">Pilih Pelanggan Terdaftar</option>
                                    @foreach($customers as $customerOption)
                                        <option value="{{ $customerOption->id }}" 
                                                data-name="{{ $customerOption->name }}"
                                                data-phone="{{ $customerOption->phone }}"
                                                {{ old('customer_id', $customer->id ?? '') == $customerOption->id ? 'selected' : '' }}>
                                            {{ $customerOption->name }} - {{ $customerOption->phone }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title mb-2">Info Pelanggan</h6>
                                        <div id="customer-info">
                                            <p class="mb-1"><small class="text-muted">Pilih pelanggan untuk melihat detail</small></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Vehicle and Mechanic Information -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="fas fa-car me-2"></i> Informasi Kendaraan & Mekanik</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="vehicle_id" class="form-label">Kendaraan</label>
                                <select class="form-select @error('vehicle_id') is-invalid @enderror" 
                                        id="vehicle_id" name="vehicle_id" required>
                                    <option value="">Pilih Kendaraan</option>
                                    @foreach($vehicles as $vehicleOption)
                                        <option value="{{ $vehicleOption->id }}" 
                                                {{ old('vehicle_id', $vehicle->id ?? '') == $vehicleOption->id ? 'selected' : '' }}>
                                            {{ $vehicleOption->license_plate }} - {{ $vehicleOption->brand }} {{ $vehicleOption->model }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="mechanic_id" class="form-label">Mekanik</label>
                                <select class="form-select @error('mechanic_id') is-invalid @enderror" 
                                        id="mechanic_id" name="mechanic_id" required>
                                    <option value="">Pilih Mekanik</option>
                                    @foreach($mechanics as $mechanic)
                                        <option value="{{ $mechanic->id }}" {{ old('mechanic_id') == $mechanic->id ? 'selected' : '' }}>
                                            {{ $mechanic->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('mechanic_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Diagnosis -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="fas fa-stethoscope me-2"></i> Diagnosis</h5>
                        <div class="mb-3">
                            <label for="diagnosis" class="form-label">Diagnosis Awal</label>
                            <textarea class="form-control @error('diagnosis') is-invalid @enderror" 
                                      id="diagnosis" name="diagnosis" rows="3">{{ old('diagnosis', $appointment->description ?? '') }}</textarea>
                            @error('diagnosis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Services -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="fas fa-cogs me-2"></i> Layanan</h5>
                            <button type="button" class="btn btn-sm btn-primary" id="addService">
                                <i class="fas fa-plus me-1"></i> Tambah Layanan
                            </button>
                        </div>
                        
                        <div id="servicesContainer">
                            <!-- Service rows will be added here -->
                            @if(old('services'))
                                @foreach(old('services') as $index => $service)
                                    <div class="service-row mb-3 border p-3 rounded">
                                        <div class="row g-3">
                                            <div class="col-md-5">
                                                <label class="form-label">Layanan</label>
                                                <select class="form-select service-select" name="services[{{ $index }}][service_id]" required>
                                                    <option value="">Pilih Layanan</option>
                                                    @foreach($services as $serviceOption)
                                                        <option value="{{ $serviceOption->id }}" 
                                                                data-price="{{ $serviceOption->price }}"
                                                                {{ $service['service_id'] == $serviceOption->id ? 'selected' : '' }}>
                                                            {{ $serviceOption->name }} (Rp {{ number_format($serviceOption->price, 0, ',', '.') }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Quantity</label>
                                                <input type="number" class="form-control service-quantity" 
                                                       name="services[{{ $index }}][quantity]" min="1" 
                                                       value="{{ $service['quantity'] }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Harga</label>
                                                <input type="number" class="form-control service-price" 
                                                       name="services[{{ $index }}][price]" 
                                                       value="{{ $service['price'] }}" step="0.01" required>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="button" class="btn btn-danger btn-sm remove-service">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Catatan</label>
                                                <input type="text" class="form-control" 
                                                       name="services[{{ $index }}][notes]" 
                                                       value="{{ $service['notes'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Parts -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="fas fa-puzzle-piece me-2"></i> Spare Parts</h5>
                            <button type="button" class="btn btn-sm btn-primary" id="addPart">
                                <i class="fas fa-plus me-1"></i> Tambah Part
                            </button>
                        </div>
                        
                        <div id="partsContainer">
                            <!-- Part rows will be added here -->
                            @if(old('parts'))
                                @foreach(old('parts') as $index => $part)
                                    <div class="part-row mb-3 border p-3 rounded">
                                        <div class="row g-3">
                                            <div class="col-md-5">
                                                <label class="form-label">Spare Part</label>
                                                <select class="form-select part-select" name="parts[{{ $index }}][part_id]" required>
                                                    <option value="">Pilih Spare Part</option>
                                                    @foreach($parts as $partOption)
                                                        <option value="{{ $partOption->id }}" 
                                                                data-stock="{{ $partOption->stock }}"
                                                                data-price="{{ $partOption->price }}"
                                                                {{ $part['part_id'] == $partOption->id ? 'selected' : '' }}>
                                                            {{ $partOption->name }} (Stok: {{ $partOption->stock }}) - Rp {{ number_format($partOption->price, 0, ',', '.') }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Quantity</label>
                                                <input type="number" class="form-control part-quantity" 
                                                       name="parts[{{ $index }}][quantity]" min="1" 
                                                       value="{{ $part['quantity'] }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Harga</label>
                                                <input type="number" class="form-control part-price" 
                                                       name="parts[{{ $index }}][price]" 
                                                       value="{{ $part['price'] }}" step="0.01" required>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="button" class="btn btn-danger btn-sm remove-part">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="stock-info mt-2 text-muted small"></div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Hidden appointment_id if exists -->
                @if(isset($appointment))
                    <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
                @endif
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-secondary me-md-2">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Work Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Service counter
        let serviceCounter = {{ old('services') ? count(old('services')) : 0 }};
        // Part counter
        let partCounter = {{ old('parts') ? count(old('parts')) : 0 }};
        
        // Customer selection handler
        const customerSelect = document.getElementById('customer_id');
        const customerInfo = document.getElementById('customer-info');
        
        customerSelect.addEventListener('change', function() {
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                const name = selectedOption.getAttribute('data-name');
                const phone = selectedOption.getAttribute('data-phone');
                
                customerInfo.innerHTML = `
                    <p class="mb-1"><strong>Nama:</strong> ${name}</p>
                    <p class="mb-0"><strong>Telepon:</strong> ${phone}</p>
                `;
            } else {
                customerInfo.innerHTML = '<p class="mb-1"><small class="text-muted">Pilih pelanggan untuk melihat detail</small></p>';
            }
        });
        
        // Initialize customer info if already selected
        if (customerSelect.value) {
            customerSelect.dispatchEvent(new Event('change'));
        }
        
        // Add service row
        document.getElementById('addService').addEventListener('click', function() {
            const container = document.getElementById('servicesContainer');
            const index = serviceCounter++;
            
            const serviceRow = document.createElement('div');
            serviceRow.className = 'service-row mb-3 border p-3 rounded';
            serviceRow.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Layanan</label>
                        <select class="form-select service-select" name="services[${index}][service_id]" required>
                            <option value="">Pilih Layanan</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" data-price="{{ $service->price }}">
                                    {{ $service->name }} (Rp {{ number_format($service->price, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control service-quantity" 
                               name="services[${index}][quantity]" min="1" value="1" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Harga</label>
                        <input type="number" class="form-control service-price" 
                               name="services[${index}][price]" step="0.01" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-service">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Catatan</label>
                        <input type="text" class="form-control" name="services[${index}][notes]">
                    </div>
                </div>
            `;
            
            container.appendChild(serviceRow);
            
            // Set initial price based on selected service
            const select = serviceRow.querySelector('.service-select');
            const priceInput = serviceRow.querySelector('.service-price');
            
            select.addEventListener('change', function() {
                if (this.value) {
                    const selectedOption = this.options[this.selectedIndex];
                    const price = selectedOption.getAttribute('data-price');
                    priceInput.value = price;
                }
            });
        });
        
        // Add part row
        document.getElementById('addPart').addEventListener('click', function() {
            const container = document.getElementById('partsContainer');
            const index = partCounter++;
            
            const partRow = document.createElement('div');
            partRow.className = 'part-row mb-3 border p-3 rounded';
            partRow.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Spare Part</label>
                        <select class="form-select part-select" name="parts[${index}][part_id]" required>
                            <option value="">Pilih Spare Part</option>
                            @foreach($parts as $part)
                                <option value="{{ $part->id }}" 
                                        data-stock="{{ $part->stock }}"
                                        data-price="{{ $part->price }}">
                                    {{ $part->name }} (Stok: {{ $part->stock }}) - Rp {{ number_format($part->price, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control part-quantity" 
                               name="parts[${index}][quantity]" min="1" value="1" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Harga</label>
                        <input type="number" class="form-control part-price" 
                               name="parts[${index}][price]" step="0.01" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-part">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="stock-info mt-2 text-muted small"></div>
            `;
            
            container.appendChild(partRow);
            
            // Set initial price and stock info based on selected part
            const select = partRow.querySelector('.part-select');
            const priceInput = partRow.querySelector('.part-price');
            const quantityInput = partRow.querySelector('.part-quantity');
            const stockInfo = partRow.querySelector('.stock-info');
            
            select.addEventListener('change', function() {
                if (this.value) {
                    const selectedOption = this.options[this.selectedIndex];
                    const price = selectedOption.getAttribute('data-price');
                    const stock = selectedOption.getAttribute('data-stock');
                    
                    priceInput.value = price;
                    stockInfo.textContent = `Stok tersedia: ${stock}`;
                    
                    // Set max quantity to available stock
                    quantityInput.max = stock;
                    
                    // If current quantity exceeds stock, reset to max available
                    if (parseInt(quantityInput.value) > parseInt(stock)) {
                        quantityInput.value = stock;
                    }
                } else {
                    priceInput.value = '';
                    stockInfo.textContent = '';
                    quantityInput.removeAttribute('max');
                }
            });
        });
        
        // Remove service/part row
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-service') || e.target.closest('.remove-service')) {
                const serviceRow = e.target.closest('.service-row');
                if (confirm('Apakah Anda yakin ingin menghapus layanan ini?')) {
                    serviceRow.remove();
                }
            }
            
            if (e.target.classList.contains('remove-part') || e.target.closest('.remove-part')) {
                const partRow = e.target.closest('.part-row');
                if (confirm('Apakah Anda yakin ingin menghapus part ini?')) {
                    partRow.remove();
                }
            }
        });
        
        // Initialize existing service selects
        document.querySelectorAll('.service-select').forEach(select => {
            const priceInput = select.closest('.service-row').querySelector('.service-price');
            
            select.addEventListener('change', function() {
                if (this.value) {
                    const selectedOption = this.options[this.selectedIndex];
                    const price = selectedOption.getAttribute('data-price');
                    priceInput.value = price;
                }
            });
            
            // Trigger change if already selected
            if (select.value) {
                select.dispatchEvent(new Event('change'));
            }
        });
        
        // Initialize existing part selects
        document.querySelectorAll('.part-select').forEach(select => {
            const priceInput = select.closest('.part-row').querySelector('.part-price');
            const stockInfo = select.closest('.part-row').querySelector('.stock-info');
            const quantityInput = select.closest('.part-row').querySelector('.part-quantity');
            
            select.addEventListener('change', function() {
                if (this.value) {
                    const selectedOption = this.options[this.selectedIndex];
                    const price = selectedOption.getAttribute('data-price');
                    const stock = selectedOption.getAttribute('data-stock');
                    
                    priceInput.value = price;
                    stockInfo.textContent = `Stok tersedia: ${stock}`;
                    quantityInput.max = stock;
                    
                    // If current quantity exceeds stock, reset to max available
                    if (parseInt(quantityInput.value) > parseInt(stock)) {
                        quantityInput.value = stock;
                    }
                } else {
                    priceInput.value = '';
                    stockInfo.textContent = '';
                    quantityInput.removeAttribute('max');
                }
            });
            
            // Trigger change if already selected
            if (select.value) {
                select.dispatchEvent(new Event('change'));
            }
        });
        
        // Form validation
        document.getElementById('workOrderForm').addEventListener('submit', function(e) {
            // Check if customer is selected
            if (!customerSelect.value) {
                e.preventDefault();
                alert('Harap pilih pelanggan.');
                customerSelect.focus();
                return false;
            }
            
            // Check if at least one service or part is added
            const serviceRows = document.querySelectorAll('.service-row');
            const partRows = document.querySelectorAll('.part-row');
            
            if (serviceRows.length === 0 && partRows.length === 0) {
                e.preventDefault();
                alert('Harap tambahkan minimal satu layanan atau spare part.');
                return false;
            }
            
            // Check part quantities don't exceed stock
            let valid = true;
            const invalidElements = [];
            
            document.querySelectorAll('.part-select').forEach(select => {
                if (select.value) {
                    const selectedOption = select.options[select.selectedIndex];
                    const stock = parseInt(selectedOption.getAttribute('data-stock'));
                    const quantityInput = select.closest('.part-row').querySelector('.part-quantity');
                    const quantity = parseInt(quantityInput.value);
                    
                    if (quantity > stock) {
                        valid = false;
                        quantityInput.classList.add('is-invalid');
                        
                        // Remove existing feedback
                        const existingFeedback = quantityInput.parentNode.querySelector('.invalid-feedback');
                        if (existingFeedback) {
                            existingFeedback.remove();
                        }
                        
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = `Jumlah melebihi stok yang tersedia (${stock}).`;
                        quantityInput.parentNode.appendChild(feedback);
                        
                        invalidElements.push(quantityInput);
                    } else {
                        quantityInput.classList.remove('is-invalid');
                        const existingFeedback = quantityInput.parentNode.querySelector('.invalid-feedback');
                        if (existingFeedback) {
                            existingFeedback.remove();
                        }
                    }
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Beberapa jumlah part melebihi stok yang tersedia. Harap periksa kembali.');
                if (invalidElements.length > 0) {
                    invalidElements[0].focus();
                }
                return false;
            }
        });
    });
</script>
@endsection