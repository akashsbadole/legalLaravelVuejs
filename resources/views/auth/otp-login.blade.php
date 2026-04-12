<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Enter your phone number to receive an OTP for login.') }}
        </div>

        @if (session('error'))
            <div class="mb-4 font-medium text-sm text-red-600">
                {{ session('error') }}
            </div>
        @endif

        @if (session('info'))
            <div class="mb-4 font-medium text-sm text-blue-600">
                {{ session('info') }}
            </div>
        @endif

        <form method="POST" action="{{ route('otp.send') }}">
            @csrf

            <div class="mt-4">
                <x-label for="phone" value="{{ __('Phone Number') }}" />
                <x-input id="phone" class="block mt-1 w-full" type="tel" name="phone" required placeholder="Enter phone number" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100" href="{{ route('login') }}">
                    {{ __('Login with password instead') }}
                </a>

                <x-button class="ms-4">
                    {{ __('Send OTP') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
