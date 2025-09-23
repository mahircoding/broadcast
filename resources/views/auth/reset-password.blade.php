@extends('layouts.auth')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-800 auth-header">Reset Password</h2>
        <p class="text-gray-600 mt-2">Masukkan password baru untuk akun Anda</p>
    </div>

    <!-- Alert Messages -->
    @if (session('status'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('status') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Validation Errors -->
    @if (session('errors'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach (session('errors')->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Reset Password Form -->
    <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
        @csrf
        
        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $token }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-800 mb-2">
                <i class="fas fa-envelope mr-2 text-purple-600"></i>Email Address
            </label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', $email) }}"
                required
                autofocus
                autocomplete="username"
                placeholder="Email Anda"
                class="auth-input w-full px-4 py-3 rounded-lg focus:outline-none font-medium text-gray-800 placeholder-gray-500"
            >
            @if (session('errors') && session('errors')->has('email'))
                <p class="text-red-600 text-sm mt-2 font-medium">
                    <i class="fas fa-exclamation-circle mr-1"></i>{{ session('errors')->first('email') }}
                </p>
            @endif
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-800 mb-2">
                <i class="fas fa-lock mr-2 text-purple-600"></i>Password Baru
            </label>
            <div class="relative">
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Masukkan password baru"
                    class="auth-input w-full px-4 py-3 pr-12 rounded-lg focus:outline-none font-medium text-gray-800 placeholder-gray-500"
                >
                <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 focus:outline-none" onclick="togglePassword('password', 'togglePasswordIcon')">
                    <i class="fas fa-eye-slash text-gray-400 hover:text-gray-600 transition-colors duration-200" id="togglePasswordIcon"></i>
                </button>
            </div>
            @if (session('errors') && session('errors')->has('password'))
                <p class="text-red-600 text-sm mt-2 font-medium">
                    <i class="fas fa-exclamation-circle mr-1"></i>{{ session('errors')->first('password') }}
                </p>
            @endif
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-800 mb-2">
                <i class="fas fa-lock mr-2 text-purple-600"></i>Konfirmasi Password
            </label>
            <div class="relative">
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Ulangi password baru"
                    class="auth-input w-full px-4 py-3 pr-12 rounded-lg focus:outline-none font-medium text-gray-800 placeholder-gray-500"
                >
                <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 focus:outline-none" onclick="togglePassword('password_confirmation', 'togglePasswordIcon2')">
                    <i class="fas fa-eye-slash text-gray-400 hover:text-gray-600 transition-colors duration-200" id="togglePasswordIcon2"></i>
                </button>
            </div>
            @if (session('errors') && session('errors')->has('password_confirmation'))
                <p class="text-red-600 text-sm mt-2 font-medium">
                    <i class="fas fa-exclamation-circle mr-1"></i>{{ session('errors')->first('password_confirmation') }}
                </p>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="auth-button w-full text-white font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 transition-all">
            <i class="fas fa-key mr-2"></i>Reset Password
        </button>

        <!-- Back to Login -->
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-sm text-purple-600 hover:text-purple-700 font-semibold transition-colors">
                <i class="fas fa-arrow-left mr-1"></i>Kembali ke Login
            </a>
        </div>
    </form>
</div>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}
</script>
@endsection