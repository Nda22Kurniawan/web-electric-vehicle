<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SantosoEV - Bengkel Motor & Sepeda Listrik</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- AOS Animate -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2e5bff;
            --secondary: #ff6b35;
            --dark: #252525;
            --light: #f8f9fa;
            --gray: #6c757d;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark);
            overflow-x: hidden;
        }

        .navbar {
            background-color: transparent;
            transition: all 0.3s ease;
            padding: 1rem 2rem;
        }

        .navbar.scrolled {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 2rem;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--primary);
        }

        .navbar-brand span {
            color: var(--secondary);
        }

        .nav-link {
            font-weight: 600;
            color: var(--dark);
            margin-left: 1rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary);
        }

        .nav-link.active {
            color: var(--primary);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #1a46e0;
            border-color: #1a46e0;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(46, 91, 255, 0.3);
        }

        .btn-secondary {
            background-color: var(--secondary);
            border-color: var(--secondary);
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #e55a2b;
            border-color: #e55a2b;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(255, 107, 53, 0.3);
        }

        .hero {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7)), url('/api/placeholder/1200/800') center/cover no-repeat;
            min-height: 90vh;
            display: flex;
            align-items: center;
            position: relative;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            color: var(--gray);
        }

        .hero-image {
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-weight: 700;
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            width: 50%;
            height: 4px;
            background-color: var(--secondary);
            bottom: -10px;
            left: 0;
        }

        .service-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .service-card .card-body {
            padding: 2rem;
        }

        .service-icon {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(46, 91, 255, 0.1);
            border-radius: 50%;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            color: var(--primary);
        }

        .testimonial-card {
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .testimonial-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1.5rem;
            border: 3px solid var(--primary);
        }

        .stars {
            color: gold;
            margin-bottom: 1rem;
        }

        .appointment-form {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.8rem 1.2rem;
            margin-bottom: 1.5rem;
            border: 1px solid #dee2e6;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(46, 91, 255, 0.2);
            border-color: var(--primary);
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2rem;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(46, 91, 255, 0.1);
            border-radius: 50%;
            margin-right: 1.5rem;
            font-size: 1.5rem;
            color: var(--primary);
            flex-shrink: 0;
        }

        .counter-box {
            text-align: center;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            background-color: white;
            transition: all 0.3s ease;
        }

        .counter-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .counter-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .counter-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .counter-text {
            font-size: 1rem;
            color: var(--gray);
        }

        footer {
            background-color: var(--dark);
            color: white;
            padding: 4rem 0 2rem;
        }

        .footer-title {
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: 1.4rem;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            color: #adb5bd;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            color: white;
            padding-left: 5px;
        }

        .social-links {
            display: flex;
            margin-top: 1rem;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(255, 255, 255, 0.1);
            margin-right: 1rem;
            color: white;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background-color: var(--primary);
            transform: translateY(-3px);
        }

        .copyright {
            text-align: center;
            padding-top: 2rem;
            margin-top: 3rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #adb5bd;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .appointment-form {
                padding: 2rem;
                margin-top: 2rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">Electro<span>Mech</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#layanan">Layanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimoni">Testimoni</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#kontak">Kontak</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-primary" href="#booking">Booking Servis</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                    <h1>Bengkel Spesialis Motor & Sepeda Listrik</h1>
                    <p>Layanan servis berkualitas tinggi dengan teknisi berpengalaman untuk menjaga kendaraan listrik Anda tetap prima dan handal.</p>
                    <div class="d-flex gap-3">
                        <a href="#booking" class="btn btn-primary">Booking Sekarang</a>
                        <a href="#layanan" class="btn btn-outline-dark">Lihat Layanan</a>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <img src="/api/placeholder/600/400" alt="Electric Motorcycle Service" class="hero-image img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light" id="fitur">
        <div class="container">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-6">
                    <h2 class="section-title mb-4">Mengapa Memilih Kami?</h2>
                    <p>Kami menawarkan layanan terbaik dengan teknisi berpengalaman dan peralatan modern.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-4" data-aos="fade-up" data-aos-duration="500">
                    <div class="counter-box">
                        <div class="counter-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div class="counter-number">5+</div>
                        <div class="counter-text">Tahun Pengalaman</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4" data-aos="fade-up" data-aos-duration="500" data-aos-delay="100">
                    <div class="counter-box">
                        <div class="counter-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="counter-number">1000+</div>
                        <div class="counter-text">Pelanggan Puas</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4" data-aos="fade-up" data-aos-duration="500" data-aos-delay="200">
                    <div class="counter-box">
                        <div class="counter-icon">
                            <i class="fas fa-motorcycle"></i>
                        </div>
                        <div class="counter-number">2500+</div>
                        <div class="counter-text">Kendaraan Diperbaiki</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4" data-aos="fade-up" data-aos-duration="500" data-aos-delay="300">
                    <div class="counter-box">
                        <div class="counter-icon">
                            <i class="fas fa-award"></i>
                        </div>
                        <div class="counter-number">4.9</div>
                        <div class="counter-text">Rating Pelanggan</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-5" id="layanan">
        <div class="container">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-6">
                    <h2 class="section-title">Layanan Kami</h2>
                    <p>Kami menyediakan berbagai layanan untuk motor dan sepeda listrik Anda</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up" data-aos-duration="500">
                    <div class="service-card card">
                        <div class="card-body">
                            <div class="service-icon">
                                <i class="fas fa-wrench"></i>
                            </div>
                            <h4>Servis Rutin</h4>
                            <p>Perawatan berkala untuk menjaga kinerja motor dan sepeda listrik tetap optimal.</p>
                            <a href="#booking" class="btn btn-sm btn-outline-primary mt-3">Lihat Detail</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up" data-aos-duration="500" data-aos-delay="100">
                    <div class="service-card card">
                        <div class="card-body">
                            <div class="service-icon">
                                <i class="fas fa-battery-full"></i>
                            </div>
                            <h4>Servis Baterai</h4>
                            <p>Pengecekan dan perbaikan baterai kendaraan listrik untuk daya tahan optimal.</p>
                            <a href="#booking" class="btn btn-sm btn-outline-primary mt-3">Lihat Detail</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up" data-aos-duration="500" data-aos-delay="200">
                    <div class="service-card card">
                        <div class="card-body">
                            <div class="service-icon">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <h4>Perbaikan Motor Listrik</h4>
                            <p>Diagnosa dan perbaikan masalah pada motor penggerak kendaraan listrik.</p>
                            <a href="#booking" class="btn btn-sm btn-outline-primary mt-3">Lihat Detail</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up" data-aos-duration="500" data-aos-delay="300">
                    <div class="service-card card">
                        <div class="card-body">
                            <div class="service-icon">
                                <i class="fas fa-microchip"></i>
                            </div>
                            <h4>Diagnosa Sistem Elektronik</h4>
                            <p>Pemeriksaan dan perbaikan sistem elektronik dan controller kendaraan listrik.</p>
                            <a href="#booking" class="btn btn-sm btn-outline-primary mt-3">Lihat Detail</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up" data-aos-duration="500" data-aos-delay="400">
                    <div class="service-card card">
                        <div class="card-body">
                            <div class="service-icon">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <h4>Penggantian Sparepart</h4>
                            <p>Layanan penggantian suku cadang dengan kualitas terbaik dan garansi.</p>
                            <a href="#booking" class="btn btn-sm btn-outline-primary mt-3">Lihat Detail</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4" data-aos="fade-up" data-aos-duration="500" data-aos-delay="500">
                    <div class="service-card card">
                        <div class="card-body">
                            <div class="service-icon">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                            <h4>Upgrade Performa</h4>
                            <p>Peningkatan kinerja motor dan sepeda listrik untuk performa lebih baik.</p>
                            <a href="#booking" class="btn btn-sm btn-outline-primary mt-3">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-5 bg-light" id="tentang">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right" data-aos-duration="1000">
                    <img src="/api/placeholder/600/400" alt="Bengkel Interior" class="img-fluid rounded-3 shadow">
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <h2 class="section-title">Tentang SantosoEV</h2>
                    <p class="mb-4">SantosoEV adalah bengkel spesialis motor dan sepeda listrik yang berdedikasi untuk memberikan layanan terbaik dengan tenaga ahli yang berpengalaman.</p>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h5>Teknisi Berpengalaman</h5>
                            <p>Tim teknisi kami memiliki sertifikasi dan pengalaman dalam perbaikan kendaraan listrik.</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h5>Peralatan Modern</h5>
                            <p>Menggunakan peralatan diagnostik terkini untuk mendeteksi masalah dengan akurat.</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h5>Sparepart Berkualitas</h5>
                            <p>Hanya menggunakan suku cadang asli dan berkualitas tinggi untuk kendaraan Anda.</p>
                        </div>
                    </div>

                    <a href="#kontak" class="btn btn-primary mt-4">Hubungi Kami</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-5" id="testimoni">
        <div class="container">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-6">
                    <h2 class="section-title">Testimoni Pelanggan</h2>
                    <p>Apa kata pelanggan tentang layanan kami</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-duration="500">
                    <div class="testimonial-card">
                        <img src="/api/placeholder/80/80" alt="Customer" class="testimonial-img">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p>"Pelayanan sangat memuaskan. Motorku yang bermasalah pada bagian controller langsung bisa didiagnosa dengan tepat dan diperbaiki dengan cepat."</p>
                        <h5>Budi Santoso</h5>
                        <p class="text-muted small">Pemilik Motor Listrik</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-duration="500" data-aos-delay="100">
                    <div class="testimonial-card">
                        <img src="/api/placeholder/80/80" alt="Customer" class="testimonial-img">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p>"Bengkel terbaik untuk sepeda listrik! Baterai sepedaku yang sudah tidak awet dibantu untuk diperbaiki dan sekarang dayanya kembali optimal."</p>
                        <h5>Dewi Lestari</h5>
                        <p class="text-muted small">Pemilik Sepeda Listrik</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-duration="500" data-aos-delay="200">
                    <div class="testimonial-card">
                        <img src="/api/placeholder/80/80" alt="Customer" class="testimonial-img">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <p>"Saya sangat puas dengan upgrade performa yang dilakukan pada motor listrik saya. Sekarang kecepatan dan akselerasinya jauh lebih baik."</p>
                        <h5>Rudi Hermawan</h5>
                        <p class="text-muted small">Pemilik Motor Listrik</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Section -->
    <section class="py-5 bg-light" id="booking">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right" data-aos-duration="1000">
                    <h2 class="section-title">Booking Servis Online</h2>
                    <p class="mb-4">Booking jadwal servis kendaraan Anda dengan mudah tanpa perlu antri. Isi form di samping dan pilih waktu yang nyaman untuk Anda.</p>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <h5>Mudah dan Cepat</h5>
                            <p>Proses booking hanya membutuhkan waktu kurang dari 2 menit.</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h5>Hemat Waktu</h5>
                            <p>Tidak perlu menunggu lama, kendaraan akan langsung diproses sesuai jadwal.</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <div>
                            <h5>Lacak Status</h5>
                            <p>Dapatkan kode tracking untuk memantau proses servis kendaraan Anda.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <div class="appointment-form">
                        <form>
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" placeholder="Masukkan nama lengkap Anda">
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Nomor Telepon</label>
                                <input type="tel" class="form-control" id="phone" placeholder="Contoh: 081234567890">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email (Opsional)</label>
                                <input type="email" class="form-control" id="email" placeholder="Contoh: nama@email.com">
                            </div>
                            <div class="mb-3">
                                <label for="vehicle" class="form-label">Jenis Kendaraan</label>
                                <select class="form-select" id="vehicle">
                                    <option selected disabled>Pilih jenis kendaraan</option>
                                    <option>Motor Listrik</option>
                                    <option>Sepeda Listrik</option>
                                    <option>Skuter Listrik</option>
                                    <option>Lainnya</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="service" class="form-label">Jenis Layanan</label>
                                <select class="form-select" id="service">
                                    <option selected disabled>Pilih jenis layanan</option>
                                    <option>Servis Rutin</option>
                                    <option>Servis Baterai</option>
                                    <option>Perbaikan Motor Listrik</option>
                                    <option>Diagnosa Sistem Elektronik</option>
                                    <option>Penggantian Sparepart</option>
                                    <option>Upgrade Performa</option>
                                    <option>Lainnya</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="date" class="form-label">Tanggal Servis</label>
                                <input type="date" class="form-control" id="date">
                            </div>
                            <div class="mb-3">
                                <label for="time" class="form-label">Waktu Servis</label>
                                <select class="form-select" id="time">
                                    <option selected disabled>Pilih waktu</option>
                                    <option>08:00 - 10:00</option>
                                    <option>10:00 - 12:00</option>
                                    <option>13:00 - 15:00</option>
                                    <option>15:00 - 17:00</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Catatan Tambahan</label>
                                <textarea class="form-control" id="notes" rows="3" placeholder="Deskripsi masalah atau permintaan khusus"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Booking Sekarang</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-5" id="kontak">
        <div class="container">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-6">
                    <h2 class="section-title">Hubungi Kami</h2>
                    <p>Kami siap membantu Anda dengan segala kebutuhan servis kendaraan listrik</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0" data-aos="fade-up" data-aos-duration="500">
                    <div class="text-center">
                        <div class="feature-icon mx-auto mb-3">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h4>Lokasi</h4>
                        <p>Jl. Teknologi No. 123<br>Kota Bandung, Jawa Barat<br>40256</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0" data-aos="fade-up" data-aos-duration="500" data-aos-delay="100">
                    <div class="text-center">
                        <div class="feature-icon mx-auto mb-3">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <h4>Telepon</h4>
                        <p>+62 812 3456 7890<br>Senin - Sabtu<br>08:00 - 17:00 WIB</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-duration="500" data-aos-delay="200">
                    <div class="text-center">
                        <div class="feature-icon mx-auto mb-3">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h4>Email</h4>
                        <p>info@electromech.com<br>cs@electromech.com<br>support@electromech.com</p>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right" data-aos-duration="1000">
                    <div class="map-container" style="height: 400px; border-radius: 15px; overflow: hidden;">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.923521676343!2d107.61873231531654!3d-6.897722669411752!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e64a445c2b9d%3A0x1c1e5e3a0b4f4b4f!2sJl.%20Teknologi%20No.123%2C%20Bandung!5e0!3m2!1sen!2sid!4v1620000000000!5m2!1sen!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <div class="appointment-form">
                        <h4 class="mb-4">Kirim Pesan</h4>
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="form-control" placeholder="Nama Anda">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="email" class="form-control" placeholder="Email Anda">
                                </div>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Subjek">
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" rows="4" placeholder="Pesan Anda"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h3 class="footer-title">SantosoEV</h3>
                    <p>Bengkel spesialis motor dan sepeda listrik dengan layanan terbaik dan teknisi berpengalaman.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <h4 class="footer-title">Tautan Cepat</h4>
                    <ul class="footer-links">
                        <li><a href="#home">Beranda</a></li>
                        <li><a href="#layanan">Layanan</a></li>
                        <li><a href="#tentang">Tentang Kami</a></li>
                        <li><a href="#testimoni">Testimoni</a></li>
                        <li><a href="#kontak">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <h4 class="footer-title">Layanan</h4>
                    <ul class="footer-links">
                        <li><a href="#">Servis Rutin</a></li>
                        <li><a href="#">Servis Baterai</a></li>
                        <li><a href="#">Perbaikan Motor</a></li>
                        <li><a href="#">Diagnosa Elektronik</a></li>
                        <li><a href="#">Upgrade Performa</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h4 class="footer-title">Newsletter</h4>
                    <p>Berlangganan newsletter kami untuk mendapatkan promo dan informasi terbaru.</p>
                    <form class="mt-3">
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" placeholder="Email Anda" aria-label="Email Anda">
                            <button class="btn btn-secondary" type="button">Berlangganan</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="copyright">
                <p class="mb-0">&copy; 2025 SantosoEV. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animate -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <!-- Custom JS -->
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();

                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Form submission handling
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                alert('Terima kasih! Pesan/booking Anda telah berhasil dikirim. Kami akan segera menghubungi Anda.');
                this.reset();
            });
        });
    </script>

</body>

</html>