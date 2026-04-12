<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Two-Factor Authentication') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if($enabled)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-green-50 dark:bg-green-900/20">
                    <h3 class="text-lg font-semibold text-green-700 dark:text-green-300">2FA is Enabled</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Your account is protected with two-factor authentication.</p>
                    
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Recovery Codes</h4>
                    <div class="grid grid-cols-2 gap-2 mb-6">
                        @foreach($recoveryCodes as $code)
                        <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-sm">{{ $code }}</code>
                        @endforeach
                    </div>
                    
                    <form method="POST" action="{{ route('two-factor.disable') }}">
                        @csrf
                        <x-danger-button>Disable 2FA</x-danger-button>
                    </form>
                </div>
            </div>
            @else
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Enable Two-Factor Authentication</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Scan the QR code with your authenticator app (Google Authenticator, Authy, etc.)</p>
                    
                    <div class="flex justify-center mb-6">
                        <div class="bg-white p-4 rounded-lg">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" alt="QR Code" class="w-48 h-48">
                        </div>
                    </div>
                    
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Or enter this secret manually: <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $secret }}</code></p>
                    
                    <form method="POST" action="{{ route('two-factor.enable') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Verification Code</label>
                            <input type="text" name="code" id="code" class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700" placeholder="Enter 6-digit code" required>
                        </div>
                        <x-primary-button>Enable 2FA</x-primary-button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
