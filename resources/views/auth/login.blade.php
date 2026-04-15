<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ url('/') }}" class="flex justify-center items-center mb-4">
            <x-application-logo class="w-16 h-16 fill-current text-primary-600 dark:text-primary-400" />
        </a>
    </div>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6">
        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-3">Demo Credentials</h3>
        <div class="grid grid-cols-2 gap-2">
            <button type="button" onclick="fillCredentials('admin@legalfirm.com', 'password')" class="px-3 py-2 text-xs font-medium text-left bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                <span class="block text-blue-700 dark:text-blue-400 font-semibold">Admin</span>
                <span class="text-blue-500 dark:text-blue-500">admin@legalfirm.com</span>
            </button>
            <button type="button" onclick="fillCredentials('jane@legalfirm.com', 'password')" class="px-3 py-2 text-xs font-medium text-left bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/50 transition-colors">
                <span class="block text-green-700 dark:text-green-400 font-semibold">Attorney</span>
                <span class="text-green-500 dark:text-green-500">jane@legalfirm.com</span>
            </button>
            <button type="button" onclick="fillCredentials('john@legalfirm.com', 'password')" class="px-3 py-2 text-xs font-medium text-left bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/50 transition-colors">
                <span class="block text-yellow-700 dark:text-yellow-400 font-semibold">Paralegal</span>
                <span class="text-yellow-600 dark:text-yellow-500">john@legalfirm.com</span>
            </button>
            <button type="button" onclick="fillCredentials('client@example.com', 'password')" class="px-3 py-2 text-xs font-medium text-left bg-purple-50 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-800 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/50 transition-colors">
                <span class="block text-purple-700 dark:text-purple-400 font-semibold">Client</span>
                <span class="text-purple-500 dark:text-purple-500">client@example.com</span>
            </button>
        </div>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500 dark:bg-gray-700" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        function fillCredentials(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</x-guest-layout>