<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Server Error - ') . config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .error-gradient {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            <div class="error-gradient rounded-2xl p-8 shadow-2xl">
                <div class="text-6xl font-bold text-white mb-4">500</div>
                <h1 class="text-2xl font-bold text-white mb-4">{{ __('Server Error') }}</h1>
                <p class="text-red-100 mb-8">
                    {{ __('Oops! Something went wrong on our end. Our team has been notified and is working to fix the issue.') }}
                </p>

                <div class="space-y-4">
                    <button onclick="window.location.reload()" class="inline-block w-full bg-white text-red-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition transform hover:scale-105">
                        {{ __('Try Again') }}
                    </button>

                    <a href="{{ url('/') }}" class="inline-block w-full bg-red-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-600 transition">
                        {{ __('Go to Dashboard') }}
                    </a>
                </div>
            </div>

            <div class="text-center mt-8">
                <p class="text-gray-500">
                    {{ __('Error ID: ') . (isset($exception) ? substr(md5($exception->getMessage() . time()), 0, 8) : 'Unknown') }}
                </p>
                <p class="text-gray-500 mt-2">
                    {{ __('Need help?') }}
                    <a href="mailto:support@legalfirm.com" class="text-red-600 hover:text-red-800 font-medium">
                        {{ __('Contact Support') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>