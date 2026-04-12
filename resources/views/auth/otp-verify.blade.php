<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Enter the 6-digit OTP sent to your phone.') }}
        </div>

        @if (session('error'))
            <div class="mb-4 font-medium text-sm text-red-600">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('otp.verify.post') }}">
            @csrf

            <div class="mt-4">
                <x-label for="otp" value="{{ __('OTP') }}" />
                <x-input id="otp" class="block mt-1 w-full text-center text-2xl tracking-widest" type="text" name="otp" required maxlength="6" placeholder="000000" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100" href="{{ route('otp.login') }}">
                    {{ __('Change phone number') }}
                </a>

                <x-button class="ms-4">
                    {{ __('Verify') }}
                </x-button>
            </div>
        </form>

        <form method="POST" action="{{ route('otp.resend') }}" class="mt-4">
            @csrf
            <button type="submit" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                {{ __('Resend OTP') }}
            </button>
        </form>
    </x-authentication-card>
</x-guest-layout>
