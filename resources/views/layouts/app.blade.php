<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Klinika Sistemi')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="192x192" href="/images/icon-192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/images/icon-512.png">
    <link rel="shortcut icon" href="/images/icon-192.png">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#2D9B6C">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="EXON">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/images/icon-192.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .navbar-gradient {
            background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%) !important;
            transition: all 0.3s ease;
        }

        .navbar-gradient.scrolled {
            box-shadow: 0 4px 20px rgba(45, 155, 108, 0.3);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0,0,0,0.02);
            transition: background-color 0.2s ease;
        }

        /* Fade In Animation */
        .fade-in {
            animation: fadeInUp 0.6s ease-out;
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

        /* Card Animations */
        .card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
        }

        /* Smooth Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #1e7e4f;
        }

        /* Loading Animation */
        .loading-spinner {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.3s ease;
        }

        .loading-spinner.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #2D9B6C;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Button Ripple Effect */
        .btn {
            position: relative;
            overflow: hidden;
        }

        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transform: scale(0);
            animation: ripple-animation 0.6s ease-out;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* Smooth Page Transition */
        body {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Navbar Styles */
        .navbar {
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
        }

        .navbar-light .nav-link {
            color: #1e7e4f;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 8px 16px;
            margin: 0 4px;
        }

        .navbar-light .nav-link:hover,
        .navbar-light .nav-link.active {
            color: #2D9B6C;
            background: rgba(45, 155, 108, 0.1);
            border-radius: 8px;
        }

        /* Dropdown positioning fix for mobile */
        @media (max-width: 768px) {
            .dropdown {
                position: static !important;
            }

            .dropdown-menu {
                position: fixed !important;
                top: 70px !important;
                right: 10px !important;
                left: 10px !important;
                width: auto !important;
                max-width: none !important;
                transform: none !important;
            }
        }

        @media (min-width: 769px) {
            .dropdown-menu {
                position: absolute !important;
            }

            .dropdown-menu-end {
                right: 0 !important;
                left: auto !important;
            }
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm" style="border-bottom: 3px solid #2D9B6C;">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="@if(Auth::user()->isAdmin()){{ route('admin.dashboard') }}@elseif(Auth::user()->isDoctor()){{ route('doctor.dashboard') }}@elseif(Auth::user()->isRegistrar()){{ route('registrar.dashboard') }}@else#@endif">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('images/exon.webp') }}" alt="EXON Logo" style="height: 40px; width: auto;">
                    <div>
                        <div style="font-size: 1.3rem; font-weight: 800; letter-spacing: 1px; color: #1e7e4f;">EXON</div>
                        <div style="font-size: 0.6rem; margin-top: -5px; opacity: 0.7; font-weight: 500; color: #2D9B6C;">KLİNİKA & LABORATORİYA</div>
                    </div>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @if(Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Ana Səhifə
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.users*', 'admin.analyses*', 'admin.categories*') ? 'active' : '' }}"
                               href="#"
                               id="navbarDropdownTesvirler"
                               role="button"
                               data-bs-toggle="dropdown"
                               aria-expanded="false">
                                <i class="bi bi-journal-text"></i> Təsvirlər
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownTesvirler">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                                        <i class="bi bi-people"></i> İstifadəçilər
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.analyses*') ? 'active' : '' }}" href="{{ route('admin.analyses') }}">
                                        <i class="bi bi-clipboard-pulse"></i> Analizlər
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.categories*') ? 'active' : '' }}" href="{{ route('admin.categories') }}">
                                        <i class="bi bi-tags"></i> Analiz Növləri
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.balances*') || request()->routeIs('admin.program.commission*') ? 'active' : '' }}"
                               href="#"
                               id="navbarDropdownBalances"
                               role="button"
                               data-bs-toggle="dropdown"
                               aria-expanded="false">
                                <i class="bi bi-wallet2"></i> Balanslar
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownBalances">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.balances*') ? 'active' : '' }}" href="{{ route('admin.balances') }}">
                                        <i class="bi bi-person-badge"></i> DR Balansları
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.program.commission*') ? 'active' : '' }}" href="{{ route('admin.program.commission') }}">
                                        <i class="bi bi-coin"></i> Proqram Komissiyası
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.referrals*') ? 'active' : '' }}"
                               href="#"
                               id="navbarDropdownReferrals"
                               role="button"
                               data-bs-toggle="dropdown"
                               aria-expanded="false">
                                <i class="bi bi-send"></i> Göndərişlər
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownReferrals">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.referrals.non-discounted') ? 'active' : '' }}" href="{{ route('admin.referrals.non-discounted') }}">
                                        <i class="bi bi-file-text"></i> Endirimsiz Göndərişlər
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.referrals.discounted') ? 'active' : '' }}" href="{{ route('admin.referrals.discounted') }}">
                                        <i class="bi bi-percent"></i> Endirimli Göndərişlər
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.referrals.cancelled') ? 'active' : '' }}" href="{{ route('admin.referrals.cancelled') }}">
                                        <i class="bi bi-x-circle text-danger"></i> İptal Edilmişlər
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                                <i class="bi bi-bar-chart-line"></i> Raporlar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}" href="{{ route('admin.payments.index') }}">
                                <i class="bi bi-cash-stack"></i> Ödənişlər
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.messages*') ? 'active' : '' }}" href="{{ route('admin.messages.index') }}">
                                <i class="bi bi-chat-square-text"></i> Mesajlar
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.settings*') || request()->routeIs('admin.active.sessions') ? 'active' : '' }}"
                               href="#"
                               id="navbarDropdownSettings"
                               role="button"
                               data-bs-toggle="dropdown"
                               aria-expanded="false">
                                <i class="bi bi-gear"></i> Tənzimləmələr
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownSettings">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}"
                                       href="{{ route('admin.settings') }}">
                                        <i class="bi bi-sliders"></i> Sistem Ayarları
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.active.sessions') ? 'active' : '' }}"
                                       href="{{ route('admin.active.sessions') }}">
                                        <i class="bi bi-people-fill"></i> Aktiv İstifadəçilər
                                    </a>
                                </li>
                            </ul>
                        </li>                    @elseif(Auth::user()->isDoctor())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}" href="{{ route('doctor.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Ana Səhifə
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('doctor.patients*') ? 'active' : '' }}" href="{{ route('doctor.patients') }}">
                                <i class="bi bi-people"></i> Xəstələr
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('doctor.patients.create') ? 'active' : '' }}" href="{{ route('doctor.patients.create') }}">
                                <i class="bi bi-person-plus"></i> Yeni Xəstə
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('doctor.referrals*') ? 'active' : '' }}" href="{{ route('doctor.referrals') }}">
                                <i class="bi bi-file-medical"></i> Göndərişlər
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('doctor.balance') ? 'active' : '' }}" href="{{ route('doctor.balance') }}">
                                <i class="bi bi-wallet2"></i> Balans
                            </a>
                        </li>
                    @elseif(Auth::user()->isRegistrar())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('registrar.dashboard') ? 'active' : '' }}" href="{{ route('registrar.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Ana Səhifə
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('registrar.referrals*') ? 'active' : '' }}" href="{{ route('registrar.referrals') }}">
                                <i class="bi bi-file-medical"></i> Göndərişlər
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('registrar.patients*') ? 'active' : '' }}" href="{{ route('registrar.patients') }}">
                                <i class="bi bi-people"></i> Xəstələr
                            </a>
                        </li>
                    @endif
                </ul>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <!-- Messages Icon (for doctors and registrars) -->
                    @if(Auth::user()->isDoctor() || Auth::user()->isRegistrar())
                    <div class="position-relative">
                        <a href="{{ route('messages.index') }}"
                           class="text-dark text-decoration-none position-relative p-2"
                           style="transition: all 0.3s ease;"
                           aria-label="Mesajlar {{ Auth::user()->unreadMessagesCount() > 0 ? '(' . Auth::user()->unreadMessagesCount() . ' oxunmamış)' : '' }}">
                            <i class="bi bi-chat-dots fs-4" style="color: #2D9B6C;"></i>
                            @if(Auth::user()->unreadMessagesCount() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                      style="font-size: 0.65rem;"
                                      aria-label="{{ Auth::user()->unreadMessagesCount() }} oxunmamış mesaj">
                                    {{ Auth::user()->unreadMessagesCount() }}
                                </span>
                            @endif
                        </a>
                    </div>
                    @endif

                    <!-- Notifications Bell Icon -->
                    <div class="dropdown position-relative">
                        <a class="text-dark text-decoration-none position-relative p-2"
                           href="#"
                           role="button"
                           id="notificationDropdown"
                           data-bs-toggle="dropdown"
                           data-bs-auto-close="outside"
                           aria-expanded="false"
                           aria-label="Bildirişlər"
                           style="transition: all 0.3s ease;">
                            <i class="bi bi-bell fs-4" style="color: #2D9B6C;"></i>
                            <span class="notification-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                  style="display: none; font-size: 0.65rem;"
                                  role="status"
                                  aria-live="polite">
                                0
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg"
                            aria-labelledby="notificationDropdown"
                            style="width: 320px; max-height: 400px; overflow-y: auto;">
                            <li class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="bi bi-bell"></i> Bildirişlər</h6>
                                <a href="#" class="text-success small text-decoration-none" id="markAllRead">Hamısını oxundu et</a>
                            </li>
                            <div id="notificationsList">
                                <li class="text-center py-3 text-muted">
                                    <i class="bi bi-inbox"></i> Bildiriş yoxdur
                                </li>
                            </div>
                            <li class="px-3 py-2 border-top text-center">
                                <a href="{{ route('notifications.index') }}" class="text-success text-decoration-none small">
                                    Hamısına bax <i class="bi bi-arrow-right"></i>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="dropdown">
                        <a class="text-dark text-decoration-none dropdown-toggle d-flex align-items-center gap-2 px-3 py-2 rounded"
                           href="#"
                           role="button"
                           id="profileDropdown"
                           data-bs-toggle="dropdown"
                           aria-expanded="false"
                           style="background: rgba(45, 155, 108, 0.1); transition: all 0.3s ease;">
                            <i class="bi bi-person-circle fs-5" style="color: #2D9B6C;"></i>
                            <div class="d-none d-md-block small">
                                <div class="fw-bold" style="color: #1e7e4f;">{{ Auth::user()->name }} {{ Auth::user()->surname }}</div>
                                <div class="text-muted small">
                                    @if(Auth::user()->isAdmin())
                                        Administrator
                                    @elseif(Auth::user()->isDoctor())
                                        Doktor
                                    @elseif(Auth::user()->isRegistrar())
                                        Qeydiyyatçı
                                    @endif
                                </div>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.show') }}">
                                    <i class="bi bi-person"></i> Profil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0" id="logout-form">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Çıxış
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid p-3 p-md-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" aria-live="polite">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Bildirişi bağla"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="assertive">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Bildirişi bağla"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="mt-5 bg-white shadow-sm" style="border-top: 3px solid #2D9B6C;" role="contentinfo">
        <div class="container-fluid py-4">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2">
                        <img src="{{ asset('images/exon.webp') }}" alt="EXON Logo" style="height: 35px; width: auto;">
                        <h5 class="mb-0 fw-bold" style="color: #1e7e4f;">EXON Klinika</h5>
                    </div>
                    <small class="text-muted d-block mt-2">
                        Müasir tibbi xidmətlər və diaqnostika mərkəzi
                    </small>
                </div>
                <div class="col-12 col-md-6 text-center text-md-end">
                    <p class="mb-1" style="color: #1e7e4f;">
                        <i class="bi bi-c-circle" aria-hidden="true"></i> {{ date('Y') }} Bütün hüquqlar qorunur
                    </p>
                    <small class="text-muted">
                        Copyright &copy; Zaur Eyvazov
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Loading Animation
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'loading-spinner hidden';
            loadingDiv.innerHTML = '<div class="spinner"></div>';
            document.body.appendChild(loadingDiv);

            let isNavigating = false;

            // Show loading on page transitions
            document.querySelectorAll('a:not([target="_blank"])').forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.href && !this.href.includes('#') && !this.classList.contains('dropdown-toggle')) {
                        isNavigating = true;
                        loadingDiv.classList.remove('hidden');
                    }
                });
            });

            // Hide loading when page loads
            window.addEventListener('load', function() {
                setTimeout(() => {
                    loadingDiv.classList.add('hidden');
                    isNavigating = false;
                }, 300);
            });

            // Fix browser back/forward button - CRITICAL FIX
            window.addEventListener('pageshow', function(event) {
                // If page loaded from cache (back/forward button)
                if (event.persisted) {
                    loadingDiv.classList.add('hidden');
                    isNavigating = false;
                }
            });

            // Additional safety: hide loading on any navigation event
            window.addEventListener('pagehide', function() {
                if (!isNavigating) {
                    loadingDiv.classList.add('hidden');
                }
            });

            // Navbar Scroll Effect
            const navbar = document.querySelector('.navbar');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            // Add fade-in animation to cards
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('fade-in', 'card-hover');
            });

            // Ripple Effect for Buttons
            document.querySelectorAll('.btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    ripple.classList.add('ripple');

                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';

                    this.appendChild(ripple);

                    setTimeout(() => ripple.remove(), 600);
                });
            });

            // Smooth Scroll
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });

            // Intersection Observer for Fade-in on Scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -100px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Observe all rows and containers
            document.querySelectorAll('.row, .container, .container-fluid > div').forEach(el => {
                observer.observe(el);
            });

            // Form validation enhancement
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            });

            // Number counter animation for statistics
            const counters = document.querySelectorAll('.fs-2, .fs-3, .fs-4, .display-6');
            counters.forEach(counter => {
                const text = counter.textContent.trim();
                const number = parseInt(text);

                if (!isNaN(number) && number > 0 && number < 10000) {
                    counter.textContent = '0';
                    let current = 0;
                    const increment = number / 50;
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= number) {
                            counter.textContent = number;
                            clearInterval(timer);
                        } else {
                            counter.textContent = Math.floor(current);
                        }
                    }, 20);
                }
            });

            // Table row click animation
            document.querySelectorAll('.table-hover tbody tr').forEach(row => {
                row.addEventListener('click', function() {
                    this.style.transform = 'scale(0.99)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 100);
                });
            });

            // Enhanced dropdown animations
            const dropdowns = document.querySelectorAll('.dropdown-toggle');
            dropdowns.forEach(dropdown => {
                dropdown.addEventListener('show.bs.dropdown', function() {
                    const menu = this.nextElementSibling;
                    if (menu) {
                        menu.style.animation = 'fadeInUp 0.3s ease-out';
                    }
                });
            });

            // Add pulse animation to badges
            document.querySelectorAll('.badge').forEach(badge => {
                badge.style.transition = 'all 0.3s ease';
                badge.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.1)';
                });
                badge.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });

            // Load notifications
            loadNotifications();

            // Refresh notifications every 30 seconds
            setInterval(loadNotifications, 30000);

            // Mark notification as read when clicked
            document.addEventListener('click', function(e) {
                if (e.target.closest('.notification-item')) {
                    const notificationId = e.target.closest('.notification-item').dataset.id;
                    markAsRead(notificationId);
                }
            });

            // Mark all as read
            document.getElementById('markAllRead')?.addEventListener('click', function(e) {
                e.preventDefault();
                markAllAsRead();
            });
        });

        // Load notifications function
        function loadNotifications() {
            fetch('{{ route("notifications.unread") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.querySelector('.notification-badge');
                    const list = document.getElementById('notificationsList');

                    // Update badge
                    if (data.count > 0) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }

                    // Update list
                    if (data.notifications.length > 0) {
                        list.innerHTML = data.notifications.map(notif => `
                            <li class="notification-item border-bottom" data-id="${notif.id}" style="cursor: pointer;">
                                <a href="#" class="dropdown-item py-3">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="flex-shrink-0">
                                            <i class="bi ${getNotificationIcon(notif.type)} fs-5" style="color: #2D9B6C;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">${notif.title}</div>
                                            <div class="small text-muted">${notif.message}</div>
                                            <div class="small text-muted mt-1">
                                                <i class="bi bi-clock"></i> ${formatDate(notif.created_at)}
                                            </div>
                                        </div>
                                        ${!notif.is_read ? '<span class="badge bg-success rounded-circle p-1" style="width: 8px; height: 8px;"></span>' : ''}
                                    </div>
                                </a>
                            </li>
                        `).join('');
                    } else {
                        list.innerHTML = `
                            <li class="text-center py-3 text-muted">
                                <i class="bi bi-inbox"></i> Bildiriş yoxdur
                            </li>
                        `;
                    }
                })
                .catch(error => console.error('Error loading notifications:', error));
        }

        // Mark notification as read
        function markAsRead(id) {
            fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(() => loadNotifications())
            .catch(error => console.error('Error marking as read:', error));
        }

        // Mark all as read
        function markAllAsRead() {
            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(() => loadNotifications())
            .catch(error => console.error('Error marking all as read:', error));
        }

        // Get notification icon based on type
        function getNotificationIcon(type) {
            switch(type) {
                case 'payment': return 'bi-cash-coin';
                case 'referral_approved': return 'bi-check-circle';
                case 'analysis_completed': return 'bi-clipboard-check';
                default: return 'bi-bell';
            }
        }

        // Format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);

            if (diffInSeconds < 60) return 'İndi';
            if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' dəqiqə əvvəl';
            if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' saat əvvəl';
            if (diffInSeconds < 604800) return Math.floor(diffInSeconds / 86400) + ' gün əvvəl';

            return date.toLocaleDateString('az-AZ');
        }

        // Performance optimization: Debounce function
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Lazy load images (if any)
        if ('loading' in HTMLImageElement.prototype) {
            const images = document.querySelectorAll('img[loading="lazy"]');
            images.forEach(img => {
                img.src = img.dataset.src;
            });
        }

        // PWA - Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('✅ Service Worker qeydiyyatdan keçdi:', registration.scope);

                        // Yeni versiya yoxlanışı
                        registration.addEventListener('updatefound', () => {
                            const newWorker = registration.installing;
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    // Yeni versiya var - səhifəni yeniləmə təklifi
                                    if (confirm('Yeni versiya mövcuddur. Yeniləmək istəyirsiniz?')) {
                                        window.location.reload();
                                    }
                                }
                            });
                        });
                    })
                    .catch(error => {
                        console.log('❌ Service Worker xətası:', error);
                    });
            });
        }

        // PWA - Install Prompt (Android)
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            // "Home Screen-ə əlavə et" düyməsi göstər (istəyə bağlı)
            console.log('💡 PWA quraşdırıla bilər');

            // Əgər UI-da quraşdırma düyməsi varsa, göstər
            const installButton = document.querySelector('#pwa-install-btn');
            if (installButton) {
                installButton.style.display = 'block';
                installButton.addEventListener('click', () => {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then((choiceResult) => {
                        if (choiceResult.outcome === 'accepted') {
                            console.log('✅ İstifadəçi PWA quraşdırdı');
                        }
                        deferredPrompt = null;
                        installButton.style.display = 'none';
                    });
                });
            }
        });

        // PWA quraşdırıldıqdan sonra
        window.addEventListener('appinstalled', () => {
            console.log('✅ PWA uğurla quraşdırıldı');
            deferredPrompt = null;
        });

        // Online/Offline status
        window.addEventListener('online', () => {
            console.log('🌐 İnternet bərpa olundu');
        });

        window.addEventListener('offline', () => {
            console.log('📡 Offline rejimdə');
        });
    </script>

    @stack('scripts')
    @yield('scripts')
</body>
</html>
