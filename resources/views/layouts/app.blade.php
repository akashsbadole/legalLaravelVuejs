<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Legal Firm') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body x-data="{ sidebarOpen: window.innerWidth >= 1024, theme: localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light') }" 
          x-init="document.documentElement.classList.toggle('dark', theme === 'dark'); 
                  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                      const newTheme = e.matches ? 'dark' : 'light';
                      theme = newTheme;
                      document.documentElement.classList.toggle('dark', newTheme === 'dark');
                      localStorage.setItem('theme', newTheme);
                  });"
          x-on:resize.window="sidebarOpen = window.innerWidth >= 1024"
          class="font-sans antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100 relative"
          :class="{ 'overflow-hidden': sidebarOpen && window.innerWidth < 1024 }">
        @include('layouts.sidebar')
        <div id="app" class="transition-all duration-300 lg:ml-72 min-h-screen flex flex-col" :class="{ 'lg:ml-0': !sidebarOpen }">
            @include('layouts.header')
            <main class="flex-1 p-4 sm:p-6">
                {{ $slot }}
            </main>
            <footer class="py-4 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} LegalFirm Management System
            </footer>
        </div>
        
        <!-- Mobile Sidebar Backdrop -->
        <div class="fixed inset-0 z-40 lg:hidden transition-opacity duration-300" @click="sidebarOpen = false" :class="{ 'bg-black/50 opacity-100 pointer-events-auto': sidebarOpen, 'opacity-0 pointer-events-none': !sidebarOpen }"></div>
        
        <!-- Mobile Sidebar Toggle Button -->
        <div class="fixed left-4 top-4 z-30 lg:hidden">
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg bg-white dark:bg-gray-800 shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg class="w-6 h-6 text-gray-900 dark:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </div>
    </body>
</html>