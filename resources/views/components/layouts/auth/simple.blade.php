<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')

        <style>
            .auth-gradient {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                position: relative;
                overflow: hidden;
            }

            .auth-gradient::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            }

            .auth-card {
                backdrop-filter: blur(10px);
                background: rgba(255, 255, 255, 0.98);
                border: 1px solid rgba(255, 255, 255, 0.3);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                color: #1f2937; /* Ensure dark text color */
            }

            .auth-input {
                transition: all 0.3s ease;
                border: 2px solid #d1d5db;
                background: #ffffff;
                color: #1f2937;
            }

            .auth-input:focus {
                border-color: #667eea;
                background: #ffffff;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
                color: #1f2937;
            }

            .auth-input::placeholder {
                color: #6b7280;
                opacity: 1;
            }

            /* Input dengan padding kanan untuk toggle button */
            .auth-input.pr-12 {
                padding-right: 48px !important;
            }

            /* Password Toggle Button Positioning */
            .relative {
                position: relative;
            }

            .absolute.inset-y-0.right-0 {
                position: absolute;
                top: 0;
                bottom: 0;
                right: 0;
                display: flex;
                align-items: center;
                padding-right: 12px;
                pointer-events: auto;
                z-index: 10;
            }

            .absolute.inset-y-0.right-0 button,
            button.absolute.inset-y-0.right-0 {
                background: none;
                border: none;
                cursor: pointer;
                padding: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 6px;
                transition: all 0.2s ease;
                min-width: 32px;
            }

            .absolute.inset-y-0.right-0 button:hover,
            button.absolute.inset-y-0.right-0:hover {
                background-color: rgba(99, 102, 241, 0.1);
            }

            .absolute.inset-y-0.right-0 button:focus,
            button.absolute.inset-y-0.right-0:focus {
                outline: 2px solid rgba(99, 102, 241, 0.3);
                outline-offset: 2px;
            }

            .absolute.inset-y-0.right-0 i {
                font-size: 16px;
                color: #9ca3af;
                transition: color 0.2s ease;
            }

            .absolute.inset-y-0.right-0 button:hover i,
            button.absolute.inset-y-0.right-0:hover i {
                color: #6366f1;
            }            .auth-button {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            }

            .auth-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            }

            .floating-shapes {
                position: absolute;
                width: 100%;
                height: 100%;
                overflow: hidden;
                pointer-events: none;
            }

            .floating-shapes::before {
                content: '';
                position: absolute;
                width: 300px;
                height: 300px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                top: -150px;
                right: -150px;
                animation: float 6s ease-in-out infinite;
            }

            .floating-shapes::after {
                content: '';
                position: absolute;
                width: 200px;
                height: 200px;
                background: rgba(255, 255, 255, 0.08);
                border-radius: 50%;
                bottom: -100px;
                left: -100px;
                animation: float 8s ease-in-out infinite reverse;
            }

            @keyframes float {
                0%, 100% { transform: translateY(0px) rotate(0deg); }
                50% { transform: translateY(-20px) rotate(180deg); }
            }

            .logo-pulse {
                animation: pulse 2s ease-in-out infinite;
            }

            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.8; }
            }

            /* Mobile Responsive */
            @media (max-width: 640px) {
                .floating-shapes::before {
                    width: 200px;
                    height: 200px;
                    top: -100px;
                    right: -100px;
                }

                .floating-shapes::after {
                    width: 150px;
                    height: 150px;
                    bottom: -75px;
                    left: -75px;
                }

                .auth-card {
                    margin: 1rem;
                    padding: 1.5rem;
                }

                h1 {
                    font-size: 1.875rem;
                }

                .auth-button {
                    padding: 0.875rem 1rem;
                }
            }

            @media (max-width: 480px) {
                .auth-card {
                    padding: 1.25rem;
                    background: rgba(255, 255, 255, 1);
                }

                h1 {
                    font-size: 1.5rem;
                }

                h2 {
                    font-size: 1.375rem;
                }

                .auth-input {
                    font-size: 16px; /* Prevent zoom on iOS */
                    padding: 0.75rem 1rem;
                }
            }

            /* Text Enhancement */
            .text-contrast {
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            }

            /* Force dark text in auth card */
            .auth-card label,
            .auth-card span {
                color: #ffffff !important;
            }

            .auth-card .text-gray-800 {
                color: #1f2937 !important;
            }

            .auth-card .text-gray-700 {
                color: #374151 !important;
            }

            /* Specific header styling */
            .auth-header {
                color: #1f2937 !important;
                font-weight: 700;
            }

            /* Improved accessibility and focus states */
            .auth-input:focus,
            .auth-button:focus {
                outline: 2px solid rgba(102, 126, 234, 0.5);
                outline-offset: 2px;
            }
        </style>
    </head>
    <body class="min-h-screen antialiased auth-gradient">
        <!-- Loading Overlay -->
        <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg p-6 text-center shadow-2xl">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto mb-4"></div>
                <p class="text-gray-700 font-medium">Sedang memproses...</p>
            </div>
        </div>

        <div class="floating-shapes"></div>
        <div class="flex min-h-screen items-center justify-center p-4 relative z-10">
            <div class="w-full max-w-md">
                <!-- Logo Section -->
                <div class="text-center !pb-12">
                    <a href="{{ route('home') }}" class="inline-block" wire:navigate>
                        <h1 class="!text-3xl font-bold text-white mb-2">WhatsApp Broadcast</h1>
                        <p class="text-purple-100 pb-12">Sistem Broadcast Professional</p>
                    </a>
                </div>

                <!-- Login Card -->
                <div class="auth-card rounded-2xl p-8">
                    {{ $slot }}
                </div>

                <!-- Footer -->
                <div class="text-center mt-6">
                    <p class="text-purple-100 text-sm">
                        © {{ date('Y') }} WhatsApp Broadcast System. All rights reserved.
                    </p>
                </div>
            </div>
        </div>

        <script>
            // Show loading overlay for form submissions
            document.addEventListener('DOMContentLoaded', function() {
                const forms = document.querySelectorAll('form');
                const loadingOverlay = document.getElementById('loadingOverlay');

                forms.forEach(form => {
                    form.addEventListener('submit', function() {
                        loadingOverlay.style.display = 'flex';
                        loadingOverlay.classList.remove('hidden');
                    });
                });

                // Hide loading on page load
                window.addEventListener('load', function() {
                    loadingOverlay.style.display = 'none';
                    loadingOverlay.classList.add('hidden');
                });

                // Accessibility: Focus management
                const inputs = document.querySelectorAll('input');
                inputs.forEach(input => {
                    input.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' && this.type !== 'submit') {
                            e.preventDefault();
                            const form = this.closest('form');
                            const formData = new FormData(form);
                            const isEmpty = Array.from(formData.values()).some(value => !value.trim());

                            if (!isEmpty) {
                                form.requestSubmit();
                            }
                        }
                    });
                });
            });

            // Smooth animations for better UX
            window.addEventListener('load', function() {
                document.body.style.opacity = '0';
                document.body.style.transition = 'opacity 0.3s ease-in-out';
                setTimeout(() => {
                    document.body.style.opacity = '1';
                }, 100);
            });
        </script>

        @fluxScripts
    </body>
</html>
