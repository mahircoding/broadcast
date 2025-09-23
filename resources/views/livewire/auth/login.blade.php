<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="text-center">
        <h2 class="text-2xl font-bold auth-header mb-2">Masuk ke Akun Anda</h2>
        <p class="text-gray-700">Masukkan email dan password untuk melanjutkan</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm text-center">
            {{ session('status') }}
        </div>
    @endif

    <!-- Login Form -->
    <form wire:submit="login" class="space-y-6">
        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-800 mb-2">
                <i class="fas fa-envelope mr-2 text-purple-600"></i>Email Address
            </label>
            <input
                wire:model="email"
                id="email"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="Masukkan email Anda"
                class="auth-input w-full px-4 py-3 rounded-lg focus:outline-none font-medium text-gray-800 placeholder-gray-500"
            >
            @error('email')
                <p class="text-red-600 text-sm mt-2 font-medium">
                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                </p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-800 mb-2">
                <i class="fas fa-lock mr-2 text-purple-600"></i>Password
            </label>
            <div class="relative">
                <input
                    wire:model="password"
                    id="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Masukkan password Anda"
                    class="auth-input w-full px-4 py-3 rounded-lg focus:outline-none font-medium pr-12 text-gray-800 placeholder-gray-500"
                >
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i class="fas fa-eye-slash text-gray-500 cursor-pointer hover:text-gray-700 transition-colors"
                       onclick="togglePassword()"
                       id="togglePasswordIcon"></i>
                </div>
            </div>
            @error('password')
                <p class="text-red-600 text-sm mt-2 font-medium">
                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                </p>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input wire:model="remember" type="checkbox" class="rounded border-gray-400 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-800 font-medium">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" wire:navigate class="text-sm text-purple-600 hover:text-purple-700 font-semibold transition-colors">
                    Lupa password?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="auth-button w-full text-white font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 transition-all">
            <i class="fas fa-sign-in-alt mr-2"></i>
            <span wire:loading.remove>Masuk</span>
            <span wire:loading>
                <i class="fas fa-spinner fa-spin mr-2"></i>Sedang masuk...
            </span>
        </button>
    </form>

    <!-- Register Link -->
    @if (Route::has('register'))
        <div class="text-center">
            <p class="text-gray-700">
                Belum punya akun?
                <a href="{{ route('register') }}" wire:navigate class="text-purple-600 hover:text-purple-700 font-semibold transition-colors">
                    Daftar sekarang
                </a>
            </p>
        </div>
    @endif
</div><script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        }
    }
</script>
