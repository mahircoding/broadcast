<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            session()->flash('error', 'Gagal reset password. Token mungkin sudah expired atau tidak valid.');
            return;
        }

        Session::flash('status', 'Password berhasil direset! Silakan login dengan password baru Anda.');
        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-800 auth-header">Reset Password</h2>
        <p class="text-gray-600 mt-2">Masukkan password baru Anda</p>
    </div>

    <!-- Alert Messages -->
    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Reset Password Form -->
    <form wire:submit="resetPassword" class="space-y-6">
        <!-- Email Input (readonly) -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Email Address
            </label>
            <input
                wire:model="email"
                type="email"
                id="email"
                name="email"
                class="auth-input w-full px-4 py-3 rounded-lg bg-gray-50 cursor-not-allowed @error('email') border-red-500 @enderror"
                readonly
            >
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Input -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-lock mr-1"></i>
                Password Baru
            </label>
            <div class="relative">
                <input
                    wire:model="password"
                    type="password"
                    id="password"
                    name="password"
                    class="auth-input w-full px-4 py-3 pr-12 rounded-lg focus:outline-none @error('password') border-red-500 @enderror"
                    placeholder="Masukkan password baru"
                    required
                    autocomplete="new-password"
                >
                <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 focus:outline-none" onclick="togglePassword('password', 'togglePasswordIcon1')">
                    <i class="fas fa-eye-slash text-gray-400 hover:text-gray-600 transition-colors duration-200"
                       id="togglePasswordIcon1"></i>
                </button>
            </div>
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password Input -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-lock mr-1"></i>
                Konfirmasi Password
            </label>
            <div class="relative">
                <input
                    wire:model="password_confirmation"
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="auth-input w-full px-4 py-3 pr-12 rounded-lg focus:outline-none @error('password_confirmation') border-red-500 @enderror"
                    placeholder="Konfirmasi password baru"
                    required
                    autocomplete="new-password"
                >
                <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 focus:outline-none" onclick="togglePassword('password_confirmation', 'togglePasswordIcon2')">
                    <i class="fas fa-eye-slash text-gray-400 hover:text-gray-600 transition-colors duration-200"
                       id="togglePasswordIcon2"></i>
                </button>
            </div>
            @error('password_confirmation')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            class="auth-button w-full py-3 px-4 text-white font-semibold rounded-lg hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition duration-200"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove>
                <i class="fas fa-save mr-2"></i>
                Reset Password
            </span>
            <span wire:loading>
                <i class="fas fa-spinner fa-spin mr-2"></i>
                Mereset...
            </span>
        </button>
    </form>

    <!-- Back to Login -->
    <div class="text-center">
        <p class="text-gray-600">
            <a href="{{ route('login') }}" wire:navigate class="text-purple-600 hover:text-purple-800 font-medium transition duration-200">
                <i class="fas fa-arrow-left mr-1"></i>
                Kembali ke Login
            </a>
        </p>
    </div>
</div>

<script>
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
