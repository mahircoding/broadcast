@extends('layouts.auth')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-800 auth-header">Lupa Password</h2>
        <p class="text-gray-600 mt-2">Masukkan email Anda untuk menerima link reset password</p>
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

    <!-- Forgot Password Form -->
    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf
        
        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-800 mb-2">
                <i class="fas fa-envelope mr-2 text-purple-600"></i>Email Address
            </label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="email"
                placeholder="Masukkan email Anda"
                class="auth-input w-full px-4 py-3 rounded-lg focus:outline-none font-medium text-gray-800 placeholder-gray-500"
            >
            @if (session('errors') && session('errors')->has('email'))
                <p class="text-red-600 text-sm mt-2 font-medium">
                    <i class="fas fa-exclamation-circle mr-1"></i>{{ session('errors')->first('email') }}
                </p>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="auth-button w-full text-white font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 transition-all">
            <i class="fas fa-paper-plane mr-2"></i>Kirim Link Reset Password
        </button>

        <!-- Back to Login -->
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-sm text-purple-600 hover:text-purple-700 font-semibold transition-colors">
                <i class="fas fa-arrow-left mr-1"></i>Kembali ke Login
            </a>
        </div>
    </form>
</div>
@endsection