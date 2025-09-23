<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')

        <!-- FontAwesome for Icons -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

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
                color: #1f2937;
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
                height: 32px;
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
            }

            .auth-button {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                transition: all 0.3s ease;
                border: none;
                transform: translateY(0);
            }

            .auth-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.4);
                background: linear-gradient(135deg, #5a6fd8 0%, #6b4190 100%);
            }

            .auth-button:active {
                transform: translateY(0);
                box-shadow: 0 5px 15px -3px rgba(102, 126, 234, 0.4);
            }

            .auth-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                text-shadow: none;
            }

            @media (max-width: 640px) {
                .auth-card {
                    margin: 1rem;
                    padding: 1.5rem;
                }
            }

            /* Loading spinner styles */
            @keyframes spin {
                to { transform: rotate(360deg); }
            }

            .fa-spin {
                animation: spin 1s linear infinite;
            }

            /* Focus styles for accessibility */
            .auth-input:focus,
            .auth-button:focus {
                outline: 2px solid #667eea;
                outline-offset: 2px;
            }

            /* Custom checkbox styles */
            input[type="checkbox"] {
                accent-color: #667eea;
            }

            /* Link hover effects */
            a {
                transition: color 0.2s ease;
            }

            /* Form validation styles */
            .auth-input.border-red-500 {
                border-color: #ef4444;
                box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
            }

            /* Success message styles */
            .bg-green-50 {
                animation: slideIn 0.3s ease-out;
            }

            .bg-red-50 {
                animation: slideIn 0.3s ease-out;
            }

            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Icon animations */
            .fas {
                transition: transform 0.2s ease;
            }

            button:hover .fas {
                transform: scale(1.1);
            }

            /* Gradient text for links */
            .text-purple-600 {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                font-weight: 600;
            }

            .text-purple-700 {
                background: linear-gradient(135deg, #5a6fd8 0%, #6b4190 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                font-weight: 600;
            }

            /* Floating labels effect */
            .floating-label {
                position: relative;
            }

            .floating-label input:focus + label,
            .floating-label input:not(:placeholder-shown) + label {
                transform: translateY(-25px) scale(0.85);
                color: #667eea;
            }

            /* Enhanced card shadow on hover */
            .auth-card:hover {
                box-shadow: 0 32px 64px -12px rgba(0, 0, 0, 0.3);
                transform: translateY(-2px);
                transition: all 0.3s ease;
            }

            /* Responsive improvements */
            @media (max-width: 480px) {
                .auth-gradient {
                    padding: 1rem;
                }
                
                .auth-card {
                    padding: 1.25rem;
                }
                
                .auth-header {
                    font-size: 1.5rem;
                }
            }

            /* Dark mode support (if needed later) */
            @media (prefers-color-scheme: dark) {
                .auth-card {
                    background: rgba(17, 24, 39, 0.95);
                    color: #f9fafb;
                }
                
                .auth-input {
                    background: rgba(31, 41, 55, 0.8);
                    border-color: #374151;
                    color: #f9fafb;
                }
                
                .auth-input::placeholder {
                    color: #9ca3af;
                }
            }
        </style>
    </head>

    <body class="antialiased">
        <div class="min-h-screen auth-gradient flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative">
            <!-- Background decorations -->
            <div class="absolute top-10 left-10 w-20 h-20 bg-white bg-opacity-10 rounded-full blur-xl"></div>
            <div class="absolute bottom-10 right-10 w-32 h-32 bg-white bg-opacity-10 rounded-full blur-xl"></div>
            <div class="absolute top-1/2 left-1/4 w-16 h-16 bg-purple-300 bg-opacity-20 rounded-full blur-lg"></div>

            <!-- Main Content -->
            <div class="max-w-md w-full space-y-8 relative z-10">
                <div class="auth-card rounded-2xl p-8 space-y-6">
                    <!-- Logo/Brand -->
                    <div class="text-center">
                        <div class="mx-auto w-16 h-16 bg-gradient-to-br from-purple-600 to-blue-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg">
                            <i class="fas fa-broadcast-tower text-white text-xl"></i>
                        </div>
                        <h1 class="text-xl font-bold auth-header">WhatsApp Broadcast</h1>
                    </div>

                    @yield('content')
                </div>

                <!-- Footer -->
                <div class="text-center">
                    <p class="text-sm text-white text-opacity-80">
                        © {{ date('Y') }} WhatsApp Broadcast. All rights reserved.
                    </p>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </body>
</html>