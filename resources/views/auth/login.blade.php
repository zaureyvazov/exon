<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş - EXON Klinika</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="192x192" href="/images/icon-192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/images/icon-512.png">
    <link rel="shortcut icon" href="/images/icon-192.png">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#2D9B6C">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/images/icon-192.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            position: relative;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="1.5" fill="rgba(255,255,255,0.1)"/></svg>');
            opacity: 0.4;
        }
        .login-card {
            animation: fadeInUp 0.6s ease-out;
            position: relative;
            z-index: 1;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        .gradient-bg::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 15s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(-20px, 20px) rotate(180deg); }
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: all 0.3s ease;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .input-group-text {
            background: linear-gradient(135deg, #f5f7fa 0%, #eef1ff 100%);
            border-color: #667eea;
            color: #667eea;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
    </style>
</head>
<body>
    <div class="min-vh-100 d-flex align-items-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10 col-xl-9">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden login-card">
                        <div class="row g-0">
                            <div class="col-12 col-md-5 gradient-bg text-white p-4 p-lg-5 d-flex flex-column justify-content-center" style="position: relative; z-index: 1;">
                                <div class="mb-3 mb-lg-4 text-center">
                                    <div class="d-inline-block p-4 rounded-4" style="background: rgba(255, 255, 255, 0.98); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);">
                                        <img src="{{ asset('images/exon.webp') }}" alt="EXON Logo" style="height: 100px; max-width: 100%; display: block;">
                                    </div>
                                </div>
                                <h2 class="fw-bold mb-2" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">EXON KLİNİKA</h2>
                                <p class="mb-0 opacity-95" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.1);">LABORATORİYA & GÖNDƏRIŞ SISTEMI</p>
                              

                            </div>

                            <div class="col-12 col-md-7 p-4 p-lg-5">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div>
                                        <h3 class="fw-bold mb-1">Giriş</h3>
                                        <div class="text-muted">Hesabınıza daxil olun</div>
                                    </div>
                                    <span class="badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 8px 16px; font-weight: 500;">
                                        <i class="bi bi-shield-lock-fill"></i> Təhlükəsiz Giriş
                                    </span>
                                </div>

                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                        {{ $errors->first() }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('login.post') }}" class="needs-validation" novalidate>
                                    @csrf

                                    <div class="mb-3">
                                        <label for="username" class="form-label fw-semibold">İstifadəçi Adı</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input
                                                type="text"
                                                class="form-control @error('username') is-invalid @enderror"
                                                id="username"
                                                name="username"
                                                placeholder="İstifadəçi adınızı daxil edin"
                                                value="{{ old('username') }}"
                                                required
                                                autofocus
                                            >
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label fw-semibold">Şifrə</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                            <input
                                                type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                id="password"
                                                name="password"
                                                placeholder="••••••••"
                                                required
                                            >
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                            <label class="form-check-label" for="remember">Məni xatırla</label>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="bi bi-box-arrow-in-right"></i> Daxil Ol
                                    </button>
                                </form>

                                <div class="text-center mt-4">
                                    <small class="text-muted">©  EXON Klinika | Zaur Eyvazov</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
