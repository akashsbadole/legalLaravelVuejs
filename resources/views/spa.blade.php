<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="LegalFirm - Comprehensive legal practice management software for law firms. Manage cases, clients, billing, documents, and more.">
    <meta name="keywords" content="legal case management, law firm software, legal CRM, attorney management, case tracking, legal billing">
    <title>{{ config('app.name', 'LegalFirm') }} - Legal Practice Management</title>
    <meta property="og:title" content="{{ config('app.name', 'LegalFirm') }} - Legal Practice Management">
    <meta property="og:description" content="Comprehensive legal practice management software for law firms.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ config('app.name', 'LegalFirm') }} - Legal Practice Management">
    <meta name="twitter:description" content="Comprehensive legal practice management software for law firms.">
    <link rel="canonical" href="{{ url('/') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased">
    <div id="app"></div>
</body>
</html>
