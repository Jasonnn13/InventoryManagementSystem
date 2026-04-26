<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center">
            <h2 class="text-3xl font-semibold tracking-tight text-neutral-900 dark:text-neutral-100">Lupa Kata Sandi</h2>
            <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">Kami akan mengirim tautan reset ke email Anda.</p>
        </div>

        <div class="text-sm leading-6 text-neutral-600 dark:text-neutral-400">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-2" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end">
                <x-primary-button type="submit">
                    {{ __('Kirim tautan reset kata sandi') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
