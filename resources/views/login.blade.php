<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SantosoEV</title>
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
            background-color: #f5f7ff;
            display: flex;
            min-height: 100vh;
        }

        .login-container {
            display: flex;
            width: 100%;
        }

        .login-left {
            flex: 1;
            background: linear-gradient(135deg, rgba(46, 91, 255, 0.9), rgba(255, 107, 53, 0.8)), url('/api/placeholder/1200/800') center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            color: white;
            flex-direction: column;
            text-align: center;
        }

        .login-left-content {
            max-width: 600px;
        }

        .login-left h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .login-left p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            margin-right: 1.5rem;
            font-size: 1.2rem;
            color: white;
            flex-shrink: 0;
        }

        .login-right {
            width: 450px;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            box-shadow: -5px 0 30px rgba(0, 0, 0, 0.1);
        }

        .login-form-container {
            width: 100%;
        }

        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo a {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--primary);
            text-decoration: none;
        }

        .logo span {
            color: var(--secondary);
        }

        .login-form-container h2 {
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
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

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            border-radius: 50px;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #1a46e0;
            border-color: #1a46e0;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(46, 91, 255, 0.3);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: var(--gray);
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #dee2e6;
        }

        .divider::before {
            margin-right: 1rem;
        }

        .divider::after {
            margin-left: 1rem;
        }

        .social-login {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .social-btn {
            flex: 1;
            border-radius: 50px;
            padding: 0.6rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 1px solid #dee2e6;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark);
        }

        .social-btn:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
        }

        .social-btn i {
            margin-right: 0.5rem;
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--gray);
        }

        .login-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 992px) {
            .login-left {
                display: none;
            }

            .login-right {
                width: 100%;
                max-width: 500px;
                margin: 0 auto;
                box-shadow: none;
            }
        }

        @media (max-width: 576px) {
            .login-right {
                padding: 2rem;
            }

            .social-login {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Left Side with Branding and Features -->
        <div class="login-left" data-aos="fade-right" data-aos-duration="1000">
            <div class="login-left-content">
                <h1>Selamat Datang di SantosoEV</h1>
                <p>Masuk ke akun Anda untuk mengakses semua fitur dan layanan kami</p>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div>
                        <h5>Kelola Booking Servis</h5>
                        <p>Pantau dan atur jadwal servis kendaraan listrik Anda dengan mudah</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <div>
                        <h5>Riwayat Servis</h5>
                        <p>Akses seluruh riwayat perawatan kendaraan Anda kapan saja</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <h5>Notifikasi</h5>
                        <p>Dapatkan pemberitahuan tentang jadwal servis berikutnya dan promo khusus</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side with Login Form -->
        <div class="login-right" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
            <div class="login-form-container">
                <div class="logo">
                    <a href="index.html">Santoso<span>EV</span></a>
                </div>

                <h2>Masuk ke Akun Anda</h2>

                <form method="POST" action="{{ route('login') }}">
                @csrf
                    <div class="mb-3">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email"
                            autofocus placeholder="Alamat Email">

                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password" required autocomplete="current-password"
                            placeholder="Kata Sandi">

                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember"
                                id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>
                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">Lupa kata sandi?</a>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary mb-3">Masuk</button>

                    <div class="divider">atau masuk dengan</div>

                    <div class="social-login">
                        <button type="button" class="social-btn">
                            <i class="fab fa-google"></i> Google
                        </button>
                        <button type="button" class="social-btn">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </button>
                    </div>

                    <div class="login-footer">
                        Belum punya akun? <a href="register.html">Daftar sekarang</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

    </script>
</body>

</html>