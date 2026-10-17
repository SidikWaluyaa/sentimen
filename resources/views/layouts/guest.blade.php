<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Shopee Sentiment Analysis') }} - Masuk</title>

        <!-- Google Fonts: Outfit -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts & Styles via Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#0b0c10] text-[#f1f2f6] relative min-h-screen flex items-center justify-center p-4">
        <!-- Glowing Aurora Orbs -->
        <div class="aurora-bg" aria-hidden="true">
            <div class="aurora-orb-1"></div>
            <div class="aurora-orb-2"></div>
        </div>

        <div class="relative z-10 w-full max-w-md my-8">
            <!-- Brand Logo & Header -->
            <div class="text-center mb-6">
                <a href="/" class="inline-block hover:scale-105 transition-transform duration-200">
                    <x-application-logo class="w-16 h-16 mx-auto mb-3" />
                </a>
                <h1 class="text-2xl font-black text-white tracking-wide text-gradient-shopee uppercase">ShopeeFood Sentimen</h1>
                <p class="text-xs text-gray-400 mt-1">Platform Analisis Sentimen Keluhan Pengguna (Laravel 12 AI)</p>
            </div>

            <!-- Glass Card for Auth Forms -->
            <div class="glass-panel rounded-3xl p-6 sm:p-8 shadow-2xl">
                {{ $slot }}
            </div>

            <!-- Demo Credentials Helper Note -->
            <div class="mt-4 p-4 rounded-2xl bg-cosmos-800/80 border border-white/5 text-center text-xs text-gray-400 backdrop-blur-md">
                <span class="font-semibold text-gray-300 block mb-1">🔑 Akun Demo Tersedia (Seeder):</span>
                <span class="text-gray-300">Admin: <strong class="text-amber-400">admin@sentiment.id</strong></span> •
                <span class="text-gray-300">Pass: <strong class="text-white">password123</strong></span>
                <br>
                <span class="text-gray-300">Analis: <strong class="text-indigo-400">analyst@sentiment.id</strong></span> •
                <span class="text-gray-300">Pass: <strong class="text-white">password123</strong></span>
            </div>
        </div>
    </body>
</html>
