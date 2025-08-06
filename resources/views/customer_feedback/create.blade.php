@extends('layouts.layout')

@section('title', 'Tambah Feedback Pelanggan')

@section('page-title', 'Tambah Feedback Pelanggan')
@section('page-subtitle', 'Form untuk menambahkan feedback pelanggan baru')

@section('content')
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-comment-medical me-1"></i>
                Form Feedback Pelanggan
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('customer-feedback.store') }}">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="work_order_id" class="form-label">Work Order</label>
                            @if(isset($workOrder))
                                <input type="hidden" name="work_order_id" value="{{ $workOrder->id }}">
                                <input type="text" class="form-control"
                                    value="#{{ $workOrder->work_order_number }} - {{ $workOrder->description }}" readonly>
                            @else
                                <select class="form-select @error('work_order_id') is-invalid @enderror" id="work_order_id"
                                    name="work_order_id" required>
                                    <option value="">Pilih Work Order</option>
                                    @isset($workOrders)
                                        @foreach($workOrders as $wo)
                                            <option value="{{ $wo->id }}" {{ old('work_order_id') == $wo->id ? 'selected' : '' }}>
                                                #{{ $wo->id }} - {{ $wo->work_order_number }} - {{ $wo->diagnosis }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                                @error('work_order_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label for="rating" class="form-label">Rating</label>
                            <div class="rating-input">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }} required>
                                    <label for="star{{ $i }}"><i class="fas fa-star"></i></label>
                                @endfor
                            </div>
                            @error('rating')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Rest of the form remains the same -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Customer</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="customer_type" id="existing_customer"
                                    value="existing" checked>
                                <label class="form-check-label" for="existing_customer">
                                    Pelanggan Terdaftar
                                </label>
                            </div>
                            <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id"
                                name="customer_id">
                                <option value="">Pilih Pelanggan</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="form-check mt-3">
                                <input class="form-check-input" type="radio" name="customer_type" id="new_customer"
                                    value="new" {{ old('customer_type') == 'new' ? 'checked' : '' }}>
                                <label class="form-check-label" for="new_customer">
                                    Pelanggan Baru
                                </label>
                            </div>
                            <input type="text" class="form-control mt-2 @error('customer_name') is-invalid @enderror"
                                id="customer_name" name="customer_name" value="{{ old('customer_name') }}"
                                placeholder="Nama Pelanggan" disabled>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="comment" class="form-label">Komentar</label>
                            <textarea class="form-control @error('comment') is-invalid @enderror" id="comment"
                                name="comment" rows="3">{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_public" name="is_public" {{ old('is_public') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_public">Tampilkan sebagai testimonial publik</label>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('customer-feedback.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Feedback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Toggle between existing and new customer
            $('input[name="customer_type"]').change(function () {
                if ($(this).val() === 'existing') {
                    $('#customer_id').prop('disabled', false);
                    $('#customer_name').prop('disabled', true).val('');
                } else {
                    $('#customer_id').prop('disabled', true).val('');
                    $('#customer_name').prop('disabled', false);
                }
            });

            // Initialize based on current selection
            if ($('input[name="customer_type"]:checked').val() === 'existing') {
                $('#customer_id').prop('disabled', false);
                $('#customer_name').prop('disabled', true);
            } else {
                $('#customer_id').prop('disabled', true);
                $('#customer_name').prop('disabled', false);
            }
        });
    </script>
@endsection

@section('styles')
    <style>
        .rating-input {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .rating-input input {
            display: none;
        }

        .rating-input label {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            padding: 0 5px;
        }

        .rating-input input:checked~label,
        .rating-input input:hover~label {
            color: #ffc107;
        }

        .rating-input input:checked+label {
            color: #ffc107;
        }
    </style>
@endsection