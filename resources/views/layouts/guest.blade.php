<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>L-DRY — Masuk / Daftar</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Lucide Icons CDN -->
        <script src="https://unpkg.com/lucide@latest"></script>

        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
            h1, h2, h3, h4, h5, h6 {
                font-family: 'Outfit', sans-serif;
            }
        </style>
    </head>
    <body class="text-slate-800 antialiased bg-slate-50">
        <div class="min-h-screen flex flex-col md:flex-row">
            
            <!-- Left Side: Banner / Marketing Panel (Hidden on Mobile) -->
            <div class="hidden md:flex md:w-1/2 lg:w-3/5 bg-gradient-to-br from-blue-700 via-indigo-900 to-slate-900 text-white p-16 flex-col justify-between relative overflow-hidden border-r border-slate-900/20">
                <!-- Background Glowing blobs -->
                <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl"></div>
                <div class="absolute left-1/4 top-1/4 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl"></div>
                <div class="absolute right-10 top-10 w-44 h-44 bg-indigo-500/10 rounded-full blur-2xl"></div>

                <!-- Header Logo -->
                <div class="relative z-10">
                    <a href="/">
                        <img src="{{ asset('logo.png') }}" class="h-12 w-auto brightness-0 invert" alt="L-DRY Logo">
                    </a>
                </div>

                <!-- Text tagline -->
                <div class="my-auto relative z-10 max-w-lg space-y-6">
                    <h1 class="text-4xl lg:text-5xl font-extrabold leading-tight tracking-tight">Pakaian Bersih & Wangi Tanpa Repot.</h1>
                    <p class="text-slate-300 text-sm leading-relaxed">
                        L-DRY memberikan kemudahan layanan laundry berkualitas tinggi secara online. Nikmati sistem antar-jemput kurir profesional langsung ke depan pintu Anda.
                    </p>
                    
                    <!-- Feature Lists -->
                    <div class="space-y-4 pt-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white/10 backdrop-blur-md flex items-center justify-center text-blue-400 border border-white/10">
                                <i data-lucide="zap" class="w-4 h-4 text-blue-300"></i>
                            </div>
                            <span class="text-sm font-semibold text-slate-200">Layanan Ekspres Kurang Dari 24 Jam</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white/10 backdrop-blur-md flex items-center justify-center text-blue-400 border border-white/10">
                                <i data-lucide="truck" class="w-4 h-4 text-blue-300"></i>
                            </div>
                            <span class="text-sm font-semibold text-slate-200">Antar-Jemput Gratis Sesuai Wilayah</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white/10 backdrop-blur-md flex items-center justify-center text-blue-400 border border-white/10">
                                <i data-lucide="award" class="w-4 h-4 text-blue-300"></i>
                            </div>
                            <span class="text-sm font-semibold text-slate-200">Loyalty Points Kompensasi Keterlambatan</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Text -->
                <div class="relative z-10 text-xs text-slate-400">
                    &copy; 2026 L-DRY Laundry Systems. All rights reserved.
                </div>
            </div>

            <!-- Right Side: Form Container -->
            <div class="w-full md:w-1/2 lg:w-2/5 min-h-screen flex flex-col justify-center items-center p-6 sm:p-10" style="background-color: #f8fafc; background-image: linear-gradient(to right, #e2e8f0 1.5px, transparent 1.5px), linear-gradient(to bottom, #e2e8f0 1.5px, transparent 1.5px); background-size: 32px 32px;">
                
                <!-- Mobile Logo -->
                <div class="mb-8 md:hidden">
                    <a href="/">
                        <img src="{{ asset('logo.png') }}" class="h-10 w-auto" alt="L-DRY Logo">
                    </a>
                </div>

                <!-- Form Card -->
                <div class="w-full max-w-md bg-white border border-slate-200/80 shadow-2xl rounded-3xl px-8 py-10 relative z-10">
                    {{ $slot }}
                </div>
            </div>

        </div>

        <script>
            // Initialize icons
            lucide.createIcons();

            function togglePasswordVisibility(inputId, btnEl) {
                const input = document.getElementById(inputId);
                const icon = btnEl.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.setAttribute('data-lucide', 'eye-off');
                } else {
                    input.type = 'password';
                    icon.setAttribute('data-lucide', 'eye');
                }
                lucide.createIcons();
            }
        </script>
    </body>
</html>
