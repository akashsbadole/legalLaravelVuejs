<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-white/5 shadow-md overflow-hidden sm:rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="text-center mb-6">
                <x-application-logo class="w-20 h-20 mx-auto mb-4" />
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Two-Factor Authentication</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Enter your verification code to continue</p>
            </div>

            <form method="POST" action="{{ route('two-factor.verify.post') }}" class="space-y-4">
                @csrf

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Validation Errors -->
                <x-auth-validation-errors class="mb-4" :errors="$errors" />

                <div>
                    <x-label for="code" :value="__('Verification Code')" />
                    <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code')" placeholder="Enter 6-digit code" required autofocus autocomplete="one-time-code" />
                </div>

                <div class="flex items-center justify-between">
                    <x-primary-button>
                        {{ __('Verify') }}
                    </x-primary-button>

                    <a href="{{ route('logout') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline">
                        {{ __('Logout') }}
                    </a>
                </div>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Don't have access to your authenticator app?
                        <a href="#" onclick="toggleRecovery()" class="text-blue-600 dark:text-blue-400 hover:underline">Use recovery code</a>
                    </p>
                </div>

                <div id="recovery-section" class="hidden mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Enter one of your recovery codes:</p>
                    <input type="text" name="recovery_code" id="recovery_code" class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Enter recovery code">
                </div>
            </form>

            <script>
                function toggleRecovery() {
                    const section = document.getElementById('recovery-section');
                    section.classList.toggle('hidden');
                    const input = document.getElementById('recovery_code');
                    if (!section.classList.contains('hidden')) {
                        input.focus();
                    }
                }
            </script>
        </div>
    </div>
</x-guest-layout>