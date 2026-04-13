<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Page Not Found - ') . config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .error-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            <div class="error-gradient rounded-2xl p-8 shadow-2xl">
                <div class="text-6xl font-bold text-white mb-4">404</div>
                <h1 class="text-2xl font-bold text-white mb-4">{{ __('Page Not Found') }}</h1>
                <p class="text-blue-100 mb-8">
                    {{ __('Sorry, the page you are looking for could not be found.') }}
                </p>

                <div class="space-y-4">
                    <a href="{{ route('dashboard') }}" class="inline-block w-full bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition transform hover:scale-105">
                        {{ __('Go to Dashboard') }}
                    </a>

                    @auth
                        <a href="{{ route('cases.index') }}" class="inline-block w-full bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 transition">
                            {{ __('View Cases') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-block w-full bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 transition">
                            {{ __('Login') }}
                        </a>
                    @endauth
                </div>
            </div>

            <div class="text-center mt-8">
                <p class="text-gray-500">
                    {{ __('Need help?') }}
                    <a href="{{ route('contact.submit') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        {{ __('Contact Support') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>