@extends('layouts.layout')

@section('title', 'Testimonial Pelanggan')

@section('page-title', 'Testimonial Pelanggan')
@section('page-subtitle', 'Ulasan dari pelanggan kami')

@section('content')
<div class="container-fluid px-4">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Apa Kata Pelanggan Kami</h2>
        <p class="lead text-muted">Berikut adalah testimonial dari pelanggan yang telah menggunakan layanan kami</p>
    </div>

    @if($testimonials->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-comments fa-4x text-muted mb-4"></i>
            <h4 class="text-muted">Belum ada testimonial</h4>
            <p class="text-muted">Testimonial dari pelanggan akan muncul di sini</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($testimonials as $testimonial)
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $testimonial->rating)
                                    <i class="fas fa-star text-warning"></i>
                                @else
                                    <i class="far fa-star text-warning"></i>
                                @endif
                            @endfor
                        </div>
                        <p class="card-text mb-4">
                            <i class="fas fa-quote-left text-muted me-2"></i>
                            {{ $testimonial->comment ?? 'Pelanggan memberikan rating ' . $testimonial->rating . ' bintang tanpa komentar' }}
                            <i class="fas fa-quote-right text-muted ms-2"></i>
                        </p>
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="avatar bg-primary text-white rounded-circle me-3" style="width: 50px; height: 50px; line-height: 50px;">
                                {{ substr($testimonial->customer_name ?? $testimonial->customer->name, 0, 1) }}
                            </div>
                            <div class="text-start">
                                <h6 class="mb-0">{{ $testimonial->customer_name ?? $testimonial->customer->name }}</h6>
                                <small class="text-muted">
                                    @if($testimonial->workOrder)
                                        Layanan #{{ $testimonial->workOrder->id }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 text-end">
                        <small class="text-muted">
                            {{ $testimonial->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $testimonials->links() }}
        </div>
    @endif

</div>
@endsection

@section('styles')
<style>
    .avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.25rem;
    }
    .card {
        transition: transform 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
    }
</style>
@endsection