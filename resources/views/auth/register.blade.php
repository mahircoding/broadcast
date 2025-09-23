@extends('layouts.auth')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="text-center">
        <h2 class="text-2xl font-bold auth-header mb-2">Buat Akun Baru</h2>
        <p class="text-gray-700">Lengkapi data di bawah untuk membuat akun</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm text-center">
            {{ session('status') }}
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

    <!-- Registration Form -->
    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-800 mb-2">
                Nama Lengkap
            </label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Masukkan nama lengkap"
                class="auth-input w-full px-4 py-3 rounded-lg focus:outline-none font-medium text-gray-800 placeholder-gray-500"
            >
            @if (session('errors') && session('errors')->has('name'))
                <p class="text-red-600 text-sm mt-2 font-medium">
                    {{ session('errors')->first('name') }}
                </p>
            @endif
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-800 mb-2">
                Email Address
            </label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                placeholder="Masukkan email Anda"
                class="auth-input w-full px-4 py-3 rounded-lg focus:outline-none font-medium text-gray-800 placeholder-gray-500"
            >
            @if (session('errors') && session('errors')->has('email'))
                <p class="text-red-600 text-sm mt-2 font-medium">
                    {{ session('errors')->first('email') }}
                </p>
            @endif
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-800 mb-2">
                Password
            </label>
            <div class="relative">
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Masukkan password"
                    class="auth-input w-full px-4 py-3 pr-12 rounded-lg focus:outline-none font-medium text-gray-800 placeholder-gray-500"
                >
                <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 focus:outline-none" onclick="togglePassword('password', 'togglePasswordIcon')">
                    <span class="text-gray-400 hover:text-gray-600 transition-colors duration-200" id="togglePasswordIcon">👁️</span>
                </button>
            </div>
            @if (session('errors') && session('errors')->has('password'))
                <p class="text-red-600 text-sm mt-2 font-medium">
                    {{ session('errors')->first('password') }}
                </p>
            @endif
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-800 mb-2">
                Konfirmasi Password
            </label>
            <div class="relative">
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Ulangi password"
                    class="auth-input w-full px-4 py-3 pr-12 rounded-lg focus:outline-none font-medium text-gray-800 placeholder-gray-500"
                >
                <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 focus:outline-none" onclick="togglePassword('password_confirmation', 'togglePasswordIcon2')">
                    <span class="text-gray-400 hover:text-gray-600 transition-colors duration-200" id="togglePasswordIcon2">👁️</span>
                </button>
            </div>
            @if (session('errors') && session('errors')->has('password_confirmation'))
                <p class="text-red-600 text-sm mt-2 font-medium">
                    {{ session('errors')->first('password_confirmation') }}
                </p>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="auth-button w-full text-white font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 transition-all">
            Daftar
        </button>

        <!-- Login Link -->
        <div class="text-center">
            <p class="text-sm text-gray-700">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-700 font-semibold transition-colors">
                    Masuk sekarang
                </a>
            </p>
        </div>
    </form>
</div>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = '🙈';
    } else {
        input.type = 'password';
        icon.textContent = '👁️';
    }
}
</script>
@endsection
