<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WhatsApp Broadcast System')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        .navbar-nav .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .navbar-nav .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .navbar-nav .dropdown-item.text-danger:hover {
            background-color: #f8d7da;
            color: #721c24 !important;
        }

        .user-welcome-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .btn-logout-confirm {
            transition: all 0.3s ease;
        }

        .btn-logout-confirm:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fab fa-whatsapp me-2"></i>
                WA Broadcast
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-home me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}" href="{{ route('contacts.index') }}">
                            <i class="fas fa-address-book me-1"></i> Kontak
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('broadcast.*') ? 'active' : '' }}" href="{{ route('broadcast.index') }}">
                            <i class="fas fa-bullhorn me-1"></i> Broadcast
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('broadcast.history') ? 'active' : '' }}" href="{{ route('broadcast.history') }}">
                            <i class="fas fa-history me-1"></i> Riwayat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.waboxapp') }}">
                            <i class="fas fa-cog me-1"></i> Settings
                        </a>
                    </li>
                </ul>

                <!-- User Menu -->
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-1"></i>
                            {{ Auth::user()->name ?? 'User' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('settings.waboxapp') }}">
                                    <i class="fas fa-cog me-2"></i>
                                    Pengaturan
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        <div class="container">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="logoutModalLabel">
                        <i class="fas fa-sign-out-alt me-2 text-danger"></i>
                        Konfirmasi Logout
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-question-circle fa-3x text-warning mb-3"></i>
                    <h5>Apakah Anda yakin ingin logout?</h5>
                    <p class="text-muted mb-0">Anda akan diarahkan kembali ke halaman login.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Batal
                    </button>
                    <button type="button" id="confirmLogout" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt me-1"></i>
                        Ya, Logout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center py-3 mt-5">
        <div class="container">
            <small class="text-muted">
                © {{ date('Y') }} WhatsApp Broadcast System - Powered by WaboxApp
            </small>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Logout Confirmation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentLogoutForm = null;

            // Add click handlers to all logout buttons/forms
            const logoutForms = document.querySelectorAll('form[action*="logout"]');
            logoutForms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    currentLogoutForm = this;

                    // Show modal instead of confirm dialog
                    const modal = new bootstrap.Modal(document.getElementById('logoutModal'));
                    modal.show();
                });
            });

            // Handle confirm logout button in modal
            document.getElementById('confirmLogout').addEventListener('click', function() {
                if (currentLogoutForm) {
                    // Add loading state
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Logging out...';
                    this.disabled = true;

                    // Submit the form
                    currentLogoutForm.submit();
                }
            });

            // Reset modal state when closed
            document.getElementById('logoutModal').addEventListener('hidden.bs.modal', function() {
                currentLogoutForm = null;
                const confirmBtn = document.getElementById('confirmLogout');
                confirmBtn.innerHTML = '<i class="fas fa-sign-out-alt me-1"></i> Ya, Logout';
                confirmBtn.disabled = false;
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
