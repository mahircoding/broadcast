<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

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

    <!-- Register Form -->
    <form wire:submit="register" class="space-y-6">
        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-800 mb-2">
                <i class="fas fa-user mr-2 text-purple-600"></i>Nama Lengkap
            </label>
            <input
                wire:model="name"
                id="name"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="Masukkan nama lengkap"
                class="auth-input w-full px-4 py-3 rounded-lg focus:outline-none font-medium text-gray-800 placeholder-gray-500"
            >
            @error('name')
                <p class="text-red-600 text-sm mt-2 font-medium">
                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                </p>
            @enderror
        </div>

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
                    autocomplete="new-password"
                    placeholder="Masukkan password"
                    class="auth-input w-full px-4 py-3 rounded-lg focus:outline-none font-medium pr-12 text-gray-800 placeholder-gray-500"
                >
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i class="fas fa-eye-slash text-gray-500 cursor-pointer hover:text-gray-700 transition-colors"
                       onclick="togglePassword('password', 'togglePasswordIcon1')"
                       id="togglePasswordIcon1"></i>
                </div>
            </div>
            @error('password')
                <p class="text-red-600 text-sm mt-2 font-medium">
                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                </p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-800 mb-2">
                <i class="fas fa-lock mr-2 text-purple-600"></i>Konfirmasi Password
            </label>
            <div class="relative">
                <input
                    wire:model="password_confirmation"
                    id="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Ulangi password"
                    class="auth-input w-full px-4 py-3 rounded-lg focus:outline-none font-medium pr-12 text-gray-800 placeholder-gray-500"
                >
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i class="fas fa-eye-slash text-gray-500 cursor-pointer hover:text-gray-700 transition-colors"
                       onclick="togglePassword('password_confirmation', 'togglePasswordIcon2')"
                       id="togglePasswordIcon2"></i>
                </div>
            </div>
            @error('password_confirmation')
                <p class="text-red-600 text-sm mt-2 font-medium">
                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                </p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="auth-button w-full !text-white font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 transition-all">
            <i class="fas fa-user-plus mr-2"></i>
            <span wire:loading.remove>Buat Akun</span>
            <span wire:loading>
                <i class="fas fa-spinner fa-spin mr-2"></i>Sedang membuat akun...
            </span>
        </button>
    </form>

    <!-- Login Link -->
    <div class="text-center">
        <p class="text-gray-700">
            Sudah punya akun?
            <a href="{{ route('login') }}" wire:navigate class="text-purple-600 hover:text-purple-700 font-semibold transition-colors">
                Masuk sekarang
            </a>
        </p>
    </div>
</div><script>
    function togglePassword(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById(iconId);

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
