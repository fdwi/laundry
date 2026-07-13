<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>L-DRY — Perawatan Pakaian Premium & Dry Cleaning</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 scroll-smooth relative" x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">

    @php
        $regulerService = $services->firstWhere('slug', 'laundry-reguler');
        $dryService = $services->firstWhere('slug', 'dry-cleaning');
        $sepatuService = $services->firstWhere('slug', 'sepatu-tas');
        $expressService = $services->firstWhere('slug', 'express-service');
    @endphp

    <!-- Dekorasi Latar Belakang Glow -->
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-200/10 rounded-full blur-3xl pointer-events-none z-0"></div>
    <div class="absolute top-[1200px] left-0 w-[400px] h-[400px] bg-sky-200/10 rounded-full blur-3xl pointer-events-none z-0"></div>

    <!-- Navigasi Utama (Navbar) -->
    <nav :class="scrolled ? 'bg-white/90 backdrop-blur-md shadow-sm border-b border-slate-100 py-3' : 'bg-transparent py-6'" 
         class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <a href="#" class="flex items-center">
                    <img src="{{ asset('logo.png') }}" class="h-10 w-auto object-contain" alt="L-DRY Logo">
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#keunggulan" class="text-slate-600 hover:text-blue-600 font-semibold transition duration-200 text-sm">Keunggulan</a>
                    <a href="#layanan" class="text-slate-600 hover:text-blue-600 font-semibold transition duration-200 text-sm">Layanan</a>
                    <a href="#cara-kerja" class="text-slate-600 hover:text-blue-600 font-semibold transition duration-200 text-sm">Cara Kerja</a>
                    <a href="#harga" class="text-slate-600 hover:text-blue-600 font-semibold transition duration-200 text-sm">Paket Harga</a>
                    <a href="#testimoni" class="text-slate-600 hover:text-blue-600 font-semibold transition duration-200 text-sm">Testimoni</a>
                    <a href="#faq" class="text-slate-600 hover:text-blue-600 font-semibold transition duration-200 text-sm">Tanya Jawab</a>
                </div>

                <!-- Tombol Aksi Kanan -->
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-full px-6 py-2.5 shadow-md shadow-blue-500/10 hover:shadow-lg transition duration-200 text-sm">Dashboard</a>
                    @else
                        <div class="flex items-center gap-6">
                             <a href="{{ route('login') }}" class="font-bold text-slate-600 hover:text-blue-600 transition duration-200 text-sm">Masuk</a>
                             <a href="{{ route('register') }}" class="font-bold px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-full transition duration-200 text-sm shadow-md shadow-blue-600/10">Pesan Sekarang</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Bagian Hero -->
    <section id="keunggulan" class="relative pt-32 pb-16 lg:pt-44 lg:pb-24 overflow-hidden z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                <div class="lg:col-span-7 space-y-6">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-[#0a2540] leading-[1.1] tracking-tight">
                        Perawatan Pakaian Premium & <span class="text-blue-600">Laundry</span> Kilat
                    </h1>
                    <p class="text-lg text-slate-600 max-w-xl leading-relaxed">
                        Layanan laundry dan dry cleaning profesional untuk pakaian kesayangan Anda. Hemat waktu berharga Anda dengan layanan antar-jemput langsung ke pintu rumah.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center gap-4 pt-2">
                        <a href="{{ auth()->check() ? (auth()->user()->role === 'customer' ? route('customer.orders.create') : route('dashboard')) : route('register') }}" 
                           class="font-bold px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-lg hover:shadow-xl hover:shadow-blue-500/20 transition duration-200 text-center text-sm w-full sm:w-auto">
                            Pesan Penjemputan Pertama
                        </a>
                        <a href="#layanan" 
                           class="font-bold px-8 py-4 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl transition duration-200 text-center text-sm w-full sm:w-auto shadow-sm">
                            Lihat Layanan Pilihan
                        </a>
                    </div>
                </div>

                <!-- Hero Kanan: Mockup Smartphone & Kartu Elemen -->
                <div class="lg:col-span-5 relative flex justify-center">
                    <!-- Glow Efek Latar Belakang -->
                    <div class="absolute -top-10 -right-10 w-72 h-72 bg-blue-400/20 rounded-full blur-2xl pointer-events-none"></div>
                    
                    <!-- SVG/CSS Mockup Smartphone -->
                    <div class="relative border-[10px] border-slate-900 rounded-[3rem] h-[500px] w-[250px] bg-[#0a2540] shadow-2xl overflow-hidden flex flex-col justify-between p-4 text-white">
                        <!-- Top Notch Speaker -->
                        <div class="absolute top-2 left-1/2 -translate-x-1/2 w-28 h-5 bg-slate-900 rounded-full flex items-center justify-center">
                            <span class="w-2.5 h-2.5 bg-slate-800 rounded-full"></span>
                        </div>
                        
                        <!-- Header Internal Aplikasi -->
                        <div class="pt-6 flex justify-between items-center text-[10px] opacity-80 px-2">
                            <span>Aplikasi L-Dry</span>
                            <div class="flex items-center space-x-1">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></span>
                                <span>Lacak Kurir</span>
                            </div>
                        </div>

                        <!-- Status Pesanan Aktif Mockup -->
                        <div class="my-auto space-y-6 px-1">
                            <div class="text-center">
                                <span class="text-[10px] uppercase tracking-widest text-blue-400 font-bold">Pesanan Aktif</span>
                                <h4 class="text-lg font-black mt-1">Sedang Diantar</h4>
                                <p class="text-[11px] text-slate-300">Estimasi sampai: 14:45</p>
                            </div>

                            <!-- Langkah Progress -->
                            <div class="space-y-4">
                                <div class="flex items-center space-x-3 text-xs">
                                    <div class="w-5 h-5 rounded-full bg-blue-500 flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                    </div>
                                    <span class="text-slate-300">Sudah Dijemput</span>
                                </div>
                                <div class="flex items-center space-x-3 text-xs">
                                    <div class="w-5 h-5 rounded-full bg-blue-500 flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                    </div>
                                    <span class="text-slate-300">Sedang Dicuci</span>
                                </div>
                                <div class="flex items-center space-x-3 text-xs font-bold text-white">
                                    <div class="w-5 h-5 rounded-full bg-blue-400 flex items-center justify-center animate-pulse">
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125a1.125 1.125 0 001.125-1.125V9.75M8.25 18.75h6m-6 0V6.75A2.25 2.25 0 0110.5 4.5h3a2.25 2.25 0 012.25 2.25v12m-6 0h6" />
                                        </svg>
                                    </div>
                                    <span>Sedang Diantar</span>
                                </div>
                            </div>

                            <!-- Informasi Driver/Kurir -->
                            <div class="bg-white/10 rounded-2xl p-3 flex items-center justify-between border border-white/5">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center font-bold text-xs">KB</div>
                                    <div>
                                        <h5 class="text-[11px] font-bold">Kurir Budi</h5>
                                        <p class="text-[9px] text-slate-300">Armada Premium L-Dry</p>
                                    </div>
                                </div>
                                <span class="text-[10px] bg-blue-500/20 text-blue-300 py-1 px-2.5 rounded-full font-bold">Hubungi</span>
                            </div>
                        </div>

                        <!-- Menu Navigasi Smartphone Mockup -->
                        <div class="border-t border-white/10 pt-2 flex justify-around text-[10px] opacity-75">
                            <span class="text-blue-400">Pesan</span>
                            <span>Lacak</span>
                            <span>Profil</span>
                        </div>
                    </div>

                    <!-- Kartu Melayang di Sekitar Smartphone -->
                    <div class="absolute -left-10 bottom-12 bg-white rounded-2xl p-4 shadow-xl border border-slate-100 max-w-[180px] hidden sm:block transform -rotate-3 transition duration-300 hover:rotate-0">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="sparkleGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#f59e0b" />
                                        <stop offset="100%" stop-color="#ef4444" />
                                    </linearGradient>
                                </defs>
                                <path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275Z" fill="url(#sparkleGrad)" stroke="url(#sparkleGrad)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="m5 3 1 2.5L8.5 6 6 7 5 9.5 4 7 1.5 6 4 5.5Z" fill="url(#sparkleGrad)" />
                                <path d="m19 17 1 2.5 2.5.5-2.5 1-1 2.5-1-2.5-2.5-1 2.5-1Z" fill="url(#sparkleGrad)" />
                            </svg>
                            <span class="text-xs font-black text-[#0a2540]">Layanan Rapi & Wangi</span>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-1">Pakaian Anda diperiksa dengan teliti sebelum dikemas.</p>
                    </div>

                    <div class="absolute -right-8 top-16 bg-white rounded-2xl p-4 shadow-xl border border-slate-100 max-w-[160px] hidden sm:block transform rotate-6 transition duration-300 hover:rotate-0">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="zapGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#3b82f6" />
                                        <stop offset="100%" stop-color="#1d4ed8" />
                                    </linearGradient>
                                </defs>
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" fill="url(#zapGrad)" stroke="url(#zapGrad)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="text-xs font-black text-[#0a2540]">Selesai Tepat Waktu</span>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-1">Pakaian bersih siap diantar kembali dalam 24 jam.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bagian Kartu Keunggulan Utama -->
    <section class="py-16 bg-white border-t border-slate-50 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Keunggulan 1: Penjemputan & Pengantaran Ekspres -->
                <div class="p-8 rounded-3xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:shadow-xl hover:border-blue-100 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#0a2540] mb-3">Layanan Antar Jemput</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            Hemat waktu berharga Anda. Cukup pesan lewat website dan tentukan jadwal penjemputan, kurir resmi kami siap mengambil pakaian kotor langsung dari rumah Anda dan mengantarkannya kembali setelah bersih.
                        </p>
                    </div>
                    <a href="#cara-kerja" class="inline-flex items-center text-xs font-bold text-blue-600 mt-6 hover:text-blue-700 transition">
                        Lihat cara kerja layanan kami
                        <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <!-- Keunggulan 2: Pelacakan Pintar -->
                <div class="p-8 rounded-3xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:shadow-xl hover:border-blue-100 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A2 2 0 013 15.485V8.515a2 2 0 011.026-1.743L9 4m0 16l5.447-2.724A2 2 0 0015 15.485V8.515a2 2 0 00-1.026-1.743L9 4m0 16V4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#0a2540] mb-3">Lacak Status Cucian</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            Pantau setiap tahap proses pencucian pakaian Anda secara langsung melalui website. Anda akan mendapatkan pembaruan status mulai dari penjemputan, pencucian, penyetrikaan, hingga siap diantar kembali ke rumah.
                        </p>
                    </div>
                    <a href="#faq" class="inline-flex items-center text-xs font-bold text-blue-600 mt-6 hover:text-blue-700 transition">
                        Lihat pertanyaan yang sering diajukan
                        <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <!-- Keunggulan 3: Utamakan Keberlanjutan (Navy) -->
                <div class="p-8 rounded-3xl bg-[#0a2540] text-white hover:shadow-2xl transition-all duration-300 flex flex-col justify-between relative overflow-hidden group">
                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-blue-500/10 rounded-full blur-xl pointer-events-none"></div>
                    
                    <div>
                        <span class="text-xs font-bold tracking-widest text-blue-400 uppercase">RAMAH LINGKUNGAN</span>
                        <h3 class="text-xl font-bold text-white mt-2 mb-3">Aman & Ramah Lingkungan</h3>
                        <p class="text-slate-300 text-sm leading-relaxed">
                            Kami menggunakan detergen ramah lingkungan yang aman untuk kulit dan tidak mencemari lingkungan. Proses pencucian juga dirancang hemat air tanpa mengurangi kebersihan pakaian kesayangan Anda.
                        </p>
                    </div>

                    <!-- Indikator Metrik -->
                    <div class="grid grid-cols-2 gap-4 mt-8 pt-6 border-t border-white/10">
                        <div class="flex items-center space-x-2">
                            <div class="w-10 h-10 rounded-full border-2 border-blue-400 border-t-transparent flex items-center justify-center font-bold text-xs text-blue-300">
                                40%
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-300">Air Dihemat</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-10 h-10 rounded-full border-2 border-blue-400 flex items-center justify-center font-bold text-xs text-blue-300">
                                ZERO
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-300">Bebas Sampah Plastik</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Highlight Keunggulan Teknis -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mt-20 pt-16 border-t border-slate-100">
                <!-- Highlight 1 -->
                <div class="space-y-4">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest">PERAWATAN KHUSUS</span>
                    <h3 class="text-2xl font-extrabold text-[#0a2540]">Pencucian Berbagai Jenis Bahan</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Tim ahli kami berpengalaman dalam menangani jenis kain sensitif seperti sutra, wol, jas resmi, kebaya, hingga bahan kulit dan suede. Kami memastikan setiap jenis bahan dicuci dengan metode yang tepat agar serat kain tidak rusak.
                    </p>
                    <div class="flex flex-wrap gap-2 pt-2">
                        <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-slate-100 text-[#0a2540]">Spesialisasi Sutra</span>
                        <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-slate-100 text-[#0a2540]">Kulit & Suede</span>
                        <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-slate-100 text-[#0a2540]">Setelan Jas Resmi</span>
                    </div>
                </div>

                <!-- Highlight 2 -->
                <div class="space-y-4">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest">MESIN MODERN</span>
                    <h3 class="text-2xl font-extrabold text-[#0a2540]">Pembersihan Hingga Bersih Maksimal</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Mesin cuci modern kami dapat mengangkat noda membandel secara menyeluruh tanpa merusak serat kain atau melunturkan warna. Proses pencucian ini menjaga pakaian Anda tetap lembut, harum, dan tampak seperti baru.
                    </p>
                    <div class="flex items-center space-x-2 pt-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                        <span class="text-xs font-bold text-[#0a2540]">Tanpa Bahan Kimia Berbahaya</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 ml-4"></span>
                        <span class="text-xs font-bold text-[#0a2540]">Warna Pakaian Tetap Cerah</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bagian Layanan Pilihan -->
    <section id="layanan" class="py-20 bg-slate-50 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12 items-end">
                <div class="lg:col-span-8 space-y-4">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest">PILIHAN LAYANAN</span>
                    <h2 class="text-3xl sm:text-4xl font-black text-[#0a2540]">Layanan Terbaik Kami</h2>
                </div>
                <div class="lg:col-span-4">
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Kami menyediakan berbagai pilihan jenis laundry dengan proses pengerjaan yang rapi, bersih, dan higienis.
                    </p>
                </div>
            </div>

            <!-- Grid 4 Kolom Seimbang & Rapi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch">
                
                <!-- Layanan 1: Laundry Reguler -->
                <div class="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm flex flex-col justify-between hover:shadow-xl hover:border-blue-100 transition duration-300">
                    <div class="flex flex-col">
                        <div class="h-44 overflow-hidden relative">
                            <img src="{{ asset('laundry-reguler.png') }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 hover:scale-105" alt="Laundry Reguler">
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center text-[10px] font-bold text-slate-400">
                                <span class="text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full uppercase tracking-wider">REGULER</span>
                                <span>Selesai 2 Hari</span>
                            </div>
                            <h3 class="text-lg font-black text-[#0a2540]">Cuci Kiloan Reguler</h3>
                            <ul class="space-y-2 text-xs text-slate-500">
                                <li class="flex items-center">
                                    <svg class="w-3.5 h-3.5 text-blue-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Cuci Bersih & Wangi
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3.5 h-3.5 text-blue-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Pemisahan Warna (Bebas Luntur)
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3.5 h-3.5 text-blue-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Pengeringan Aman
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3.5 h-3.5 text-blue-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Setrika & Lipat Rapi
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="p-6 pt-4 border-t border-slate-50 flex items-center justify-between">
                        <div>
                            <span class="text-[9px] text-slate-400 block uppercase tracking-wider font-bold">Harga Mulai</span>
                            <span class="text-base font-extrabold text-[#0a2540]">
                                Rp {{ number_format($regulerService?->price_per_kg ?? 15900, 0, ',', '.') }}/kg
                            </span>
                        </div>
                        <a href="{{ auth()->check() ? (auth()->user()->role === 'customer' ? route('customer.orders.create', ['service' => $regulerService?->id]) : route('dashboard')) : route('register', ['service' => $regulerService?->id]) }}" 
                           class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition flex items-center justify-center">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Layanan 2: Dry Cleaning -->
                <div class="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm flex flex-col justify-between hover:shadow-xl hover:border-blue-100 transition duration-300">
                    <div class="flex flex-col">
                        <div class="h-44 overflow-hidden relative">
                            <img src="{{ asset('laundry-dryclean.png') }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 hover:scale-105" alt="Dry Cleaning">
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center text-[10px] font-bold text-slate-400">
                                <span class="text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full uppercase tracking-wider">CUCI SATUAN</span>
                                <span>Selesai 3 Hari</span>
                            </div>
                            <h3 class="text-lg font-black text-[#0a2540]">Dry Cleaning</h3>
                            <ul class="space-y-2 text-xs text-slate-500">
                                <li class="flex items-center">
                                    <svg class="w-3.5 h-3.5 text-blue-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Pembersihan Noda Detail
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3.5 h-3.5 text-blue-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Cairan Cuci Ramah Serat
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3.5 h-3.5 text-blue-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Setrika Uap Profesional
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3.5 h-3.5 text-blue-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Kemasan Gantungan Premium
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="p-6 pt-4 border-t border-slate-50 flex items-center justify-between">
                        <div>
                            <span class="text-[9px] text-slate-400 block uppercase tracking-wider font-bold">Harga Mulai</span>
                            <span class="text-base font-extrabold text-[#0a2540]">
                                Rp {{ number_format($dryService?->price_per_pcs ?? 45000, 0, ',', '.') }}/pcs
                            </span>
                        </div>
                        <a href="{{ auth()->check() ? (auth()->user()->role === 'customer' ? route('customer.orders.create', ['service' => $dryService?->id]) : route('dashboard')) : route('register', ['service' => $dryService?->id]) }}" 
                           class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition flex items-center justify-center">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Layanan 3: Sepatu & Tas -->
                <div class="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm flex flex-col justify-between hover:shadow-xl hover:border-blue-100 transition duration-300">
                    <div class="flex flex-col">
                        <div class="h-44 overflow-hidden relative">
                            <img src="{{ asset('laundry-sepatu-tas.png') }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 hover:scale-105" alt="Sepatu & Tas">
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center text-[10px] font-bold text-slate-400">
                                <span class="text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full uppercase tracking-wider">SPESIAL</span>
                                <span>Selesai 4 Hari</span>
                            </div>
                            <h3 class="text-lg font-black text-[#0a2540]">Sepatu & Tas</h3>
                            <ul class="space-y-2 text-xs text-slate-500">
                                <li class="flex items-center">
                                    <svg class="w-3.5 h-3.5 text-blue-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Pembersihan Menyeluruh
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3.5 h-3.5 text-blue-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Perawatan Serat Bahan
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3.5 h-3.5 text-blue-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Pewarnaan Ulang (Recolor)
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3.5 h-3.5 text-blue-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Sanitasi UV Anti Jamur
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="p-6 pt-4 border-t border-slate-50 flex items-center justify-between">
                        <div>
                            <span class="text-[9px] text-slate-400 block uppercase tracking-wider font-bold">Harga Mulai</span>
                            <span class="text-base font-extrabold text-[#0a2540]">
                                Rp {{ number_format($sepatuService?->price_per_pcs ?? 80000, 0, ',', '.') }}/pcs
                            </span>
                        </div>
                        <a href="{{ auth()->check() ? (auth()->user()->role === 'customer' ? route('customer.orders.create', ['service' => $sepatuService?->id]) : route('dashboard')) : route('register', ['service' => $sepatuService?->id]) }}" 
                           class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition flex items-center justify-center">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Layanan 4: Bed Cover & Sprei -->
                <div class="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm flex flex-col justify-between hover:shadow-xl hover:border-blue-100 transition duration-300">
                    <div class="flex flex-col">
                        <div class="h-44 overflow-hidden relative">
                            <img src="{{ asset('laundry-bedcover.png') }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 hover:scale-105" alt="Bed Cover & Sprei">
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center text-[10px] font-bold text-slate-400">
                                <span class="text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full uppercase tracking-wider">BED COVER & SPREI</span>
                                <span>Selesai 3 Hari</span>
                            </div>
                            <h3 class="text-lg font-black text-[#0a2540]">Bed Cover & Sprei</h3>
                            <ul class="space-y-2 text-xs text-slate-500">
                                <li class="flex items-center">
                                    <svg class="w-3.5 h-3.5 text-blue-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Cuci Steril Bersih
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3.5 h-3.5 text-blue-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Pengeringan Suhu Tinggi
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3.5 h-3.5 text-blue-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Pelembut Aman untuk Kulit
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3.5 h-3.5 text-blue-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Kemasan Rapi Bebas Debu
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="p-6 pt-4 border-t border-slate-50 flex items-center justify-between">
                        <div>
                            <span class="text-[9px] text-slate-400 block uppercase tracking-wider font-bold">Harga Mulai</span>
                            <span class="text-base font-extrabold text-[#0a2540]">Rp 55.000/pcs</span>
                        </div>
                        <a href="{{ auth()->check() ? (auth()->user()->role === 'customer' ? route('customer.orders.create') : route('dashboard')) : route('register') }}" 
                           class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition flex items-center justify-center">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Blok Perawatan Khusus (Bawah Grid) -->
            <div class="bg-white border border-slate-100 rounded-3xl p-8 md:p-12 mt-12 shadow-sm flex flex-col md:flex-row items-center justify-between gap-8 hover:shadow-lg transition duration-300">
                <div class="space-y-4 max-w-xl text-center md:text-left">
                    <span class="inline-flex px-3.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider">PUNYA PAKAIAN MAHAL?</span>
                    <h3 class="text-2xl font-black text-[#0a2540]">Layanan Perawatan Khusus</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Kami menyediakan penanganan khusus secara manual untuk pakaian bernilai tinggi seperti gaun pesta, gaun pengantin, pakaian berbahan kulit, dan sepatu branded.
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs text-slate-600 pt-2 font-medium">
                        <div class="flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            Pakaian Pesta / Sutra
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            Gaun Pengantin
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            Bahan Kulit & Suede
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            Sepatu Branded
                        </div>
                    </div>
                </div>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp']) }}?text={{ urlencode('Halo L-Dry, saya ingin bertanya tentang Layanan Perawatan Khusus untuk pakaian saya.') }}" target="_blank"
                   class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md transition duration-200 text-sm text-center w-full md:w-auto whitespace-nowrap">
                    Hubungi Layanan Khusus
                </a>
            </div>

            <!-- Jaminan L-Dry (Navy) -->
            <div class="bg-[#0a2540] rounded-3xl p-8 md:p-12 lg:p-16 mt-20 text-white relative overflow-hidden">
                <div class="absolute -top-20 -right-20 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="max-w-3xl space-y-4 mb-12">
                    <span class="text-xs font-bold uppercase tracking-widest text-blue-400">JAMINAN GARANSI</span>
                    <h2 class="text-3xl font-black">Garansi Kepuasan L-Dry</h2>
                    <p class="text-slate-300 text-sm leading-relaxed">
                        Kami berkomitmen menjaga kualitas terbaik untuk pakaian Anda. Jika Anda tidak puas dengan hasilnya, kami siap mencuci ulang secara gratis atau memberikan ganti rugi jika terjadi kerusakan.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 pt-8 border-t border-white/10">
                    <div class="space-y-2">
                        <div class="w-10 h-10 rounded-full bg-blue-500/10 text-blue-400 flex items-center justify-center mb-4">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-sm">Antar Jemput Aman</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">Pakaian Anda dijemput dan diantar kembali dengan aman oleh kurir resmi kami.</p>
                    </div>
                    <div class="space-y-2">
                        <div class="w-10 h-10 rounded-full bg-blue-500/10 text-blue-400 flex items-center justify-center mb-4">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-sm">Pemeriksaan Kerapian</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">Setiap pakaian diperiksa kerapian dan kebersihannya sebelum dikemas.</p>
                    </div>
                    <div class="space-y-2">
                        <div class="w-10 h-10 rounded-full bg-blue-500/10 text-blue-400 flex items-center justify-center mb-4">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-sm">Jaminan Ganti Rugi</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">Kami bertanggung jawab memberikan ganti rugi jika pakaian mengalami kerusakan saat dicuci.</p>
                    </div>
                    <div class="space-y-2">
                        <div class="w-10 h-10 rounded-full bg-blue-500/10 text-blue-400 flex items-center justify-center mb-4">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-sm">Layanan Pelanggan</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">Layanan customer service kami siap membantu Anda dengan cepat setiap hari.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bagian Cara Kerja -->
    <section id="cara-kerja" class="py-20 bg-white relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16 space-y-4">
                <span class="text-blue-600 text-xs font-bold uppercase tracking-widest">CARA PESAN</span>
                <h2 class="text-3xl sm:text-4xl font-black text-[#0a2540]">Cara Mudah Pesan Laundry</h2>
                <p class="text-slate-500 text-sm">
                    Pesan laundry di L-Dry sangat mudah dan praktis melalui langkah-langkah berikut:
                </p>
            </div>

            <!-- Timeline Cara Kerja -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative">
                <!-- Langkah 1 -->
                <div class="text-center space-y-4 relative group">
                    <div class="relative inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-600 text-white font-black text-xl shadow-lg shadow-blue-500/20 transform group-hover:scale-110 transition duration-300">
                        1
                    </div>
                    <h3 class="text-lg font-bold text-[#0a2540]">Pesan Online</h3>
                    <p class="text-slate-500 text-xs max-w-xs mx-auto leading-relaxed">
                        Daftar dan buat pesanan melalui dashboard. Pilih jenis layanan laundry yang Anda butuhkan.
                    </p>
                </div>

                <!-- Langkah 2 -->
                <div class="text-center space-y-4 relative group">
                    <div class="relative inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-600 text-white font-black text-xl shadow-lg shadow-blue-500/20 transform group-hover:scale-110 transition duration-300">
                        2
                    </div>
                    <h3 class="text-lg font-bold text-[#0a2540]">Pakaian Dijemput</h3>
                    <p class="text-slate-500 text-xs max-w-xs mx-auto leading-relaxed">
                        Kurir kami akan menjemput pakaian kotor langsung ke rumah Anda untuk diproses di workshop.
                    </p>
                </div>

                <!-- Langkah 3 -->
                <div class="text-center space-y-4 relative group">
                    <div class="relative inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-600 text-white font-black text-xl shadow-lg shadow-blue-500/20 transform group-hover:scale-110 transition duration-300">
                        3
                    </div>
                    <h3 class="text-lg font-bold text-[#0a2540]">Pakaian Diantar</h3>
                    <p class="text-slate-500 text-xs max-w-xs mx-auto leading-relaxed">
                        Pakaian Anda diantar kembali dalam kondisi bersih, harum, dan rapi. Lakukan pembayaran dengan mudah setelah selesai.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Bagian Paket Harga -->
    <section id="harga" class="py-20 bg-slate-50 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16 space-y-4">
                <span class="text-blue-600 text-xs font-bold uppercase tracking-widest">PILIHAN PAKET</span>
                <h2 class="text-3xl sm:text-4xl font-black text-[#0a2540]">Pilihan Paket Laundry Hemat</h2>
                <p class="text-slate-500 text-sm">Pilih paket laundry yang sesuai dengan kebutuhan harian atau bulanan Anda.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch max-w-5xl mx-auto" 
                 x-data="{ hoverCard: 2 }">
                
                <!-- Paket 1 -->
                <div :class="hoverCard === 1 ? 'bg-blue-600 text-white shadow-xl scale-105 border-2 border-blue-500 z-10' : 'bg-white border border-slate-100 text-slate-800 shadow-sm scale-100 z-0'"
                     @mouseenter="hoverCard = 1" 
                     class="rounded-3xl p-8 flex flex-col justify-between relative transform transition-all duration-300 cursor-pointer">
                    <div class="space-y-6">
                        <div>
                            <h3 :class="hoverCard === 1 ? 'text-white' : 'text-slate-800'" class="text-lg font-bold">Paket Kiloan</h3>
                            <p :class="hoverCard === 1 ? 'text-blue-100' : 'text-slate-400'" class="text-xs">Sangat pas untuk pakaian santai sehari-hari</p>
                        </div>
                        <div :class="hoverCard === 1 ? 'text-white' : 'text-slate-900'" class="text-3xl font-black">
                            Rp 7.000 <span :class="hoverCard === 1 ? 'text-blue-200' : 'text-slate-400'" class="text-sm font-medium">/ kg</span>
                        </div>
                        <ul class="space-y-3.5 text-sm">
                            <li class="flex items-center space-x-2">
                                <svg :class="hoverCard === 1 ? 'text-blue-300' : 'text-blue-500'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span :class="hoverCard === 1 ? 'text-blue-100' : 'text-slate-600'">Selesai dalam 2 Hari</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg :class="hoverCard === 1 ? 'text-blue-300' : 'text-blue-500'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span :class="hoverCard === 1 ? 'text-blue-100' : 'text-slate-600'">Cuci, Setrika, dan Lipat Rapi</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg :class="hoverCard === 1 ? 'text-blue-300' : 'text-blue-500'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span :class="hoverCard === 1 ? 'text-blue-100' : 'text-slate-600'">Pilihan Pewangi Premium</span>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ auth()->check() ? (auth()->user()->role === 'customer' ? route('customer.orders.create', ['service' => $regulerService?->id]) : route('dashboard')) : route('register', ['service' => $regulerService?->id]) }}" 
                       :class="hoverCard === 1 ? 'bg-white hover:bg-slate-50 text-blue-600' : 'border border-blue-500 text-blue-600 hover:bg-blue-50'"
                       class="mt-8 text-center font-bold py-3.5 rounded-xl transition duration-300 block text-xs uppercase tracking-wider">Pilih Paket</a>
                </div>

                <!-- Paket 2 -->
                <div :class="hoverCard === 2 ? 'bg-blue-600 text-white shadow-xl scale-105 border-2 border-blue-500 z-10' : 'bg-white border border-slate-100 text-slate-800 shadow-sm scale-100 z-0'"
                     @mouseenter="hoverCard = 2"
                     class="rounded-3xl p-8 flex flex-col justify-between relative transform transition-all duration-300 cursor-pointer">
                    <span class="absolute top-0 right-1/2 transform translate-x-1/2 -translate-y-1/2 bg-blue-500 text-white font-extrabold text-[10px] uppercase tracking-widest px-4 py-1.5 rounded-full shadow-md">Terpopuler</span>
                    <div class="space-y-6">
                        <div>
                            <h3 :class="hoverCard === 2 ? 'text-white' : 'text-slate-800'" class="text-lg font-bold">Paket Ekspres</h3>
                            <p :class="hoverCard === 2 ? 'text-blue-100' : 'text-slate-400'" class="text-xs">Untuk kebutuhan mendesak, selesai lebih cepat</p>
                        </div>
                        <div :class="hoverCard === 2 ? 'text-white' : 'text-slate-900'" class="text-3xl font-black">
                            Rp 12.000 <span :class="hoverCard === 2 ? 'text-blue-200' : 'text-slate-400'" class="text-sm font-medium">/ kg</span>
                        </div>
                        <ul class="space-y-3.5 text-sm">
                            <li class="flex items-center space-x-2">
                                <svg :class="hoverCard === 2 ? 'text-blue-300' : 'text-blue-500'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span :class="hoverCard === 2 ? 'text-blue-100' : 'text-slate-600'">Selesai dalam 24 Jam</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg :class="hoverCard === 2 ? 'text-blue-300' : 'text-blue-500'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span :class="hoverCard === 2 ? 'text-blue-100' : 'text-slate-600'">Prioritas Antrean Workshop</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg :class="hoverCard === 2 ? 'text-blue-300' : 'text-blue-500'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span :class="hoverCard === 2 ? 'text-blue-100' : 'text-slate-600'">Proses Cuci & Setrika Kilat</span>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ auth()->check() ? (auth()->user()->role === 'customer' ? route('customer.orders.create', ['service' => $expressService?->id]) : route('dashboard')) : route('register', ['service' => $expressService?->id]) }}" 
                       :class="hoverCard === 2 ? 'bg-white hover:bg-slate-50 text-blue-600' : 'border border-blue-500 text-blue-600 hover:bg-blue-50'"
                       class="mt-8 text-center font-bold py-3.5 rounded-xl transition duration-300 block text-xs uppercase tracking-wider">Pilih Paket</a>
                </div>

                <!-- Paket 3 -->
                <div :class="hoverCard === 3 ? 'bg-blue-600 text-white shadow-xl scale-105 border-2 border-blue-500 z-10' : 'bg-white border border-slate-100 text-slate-800 shadow-sm scale-100 z-0'"
                     @mouseenter="hoverCard = 3" 
                     class="rounded-3xl p-8 flex flex-col justify-between relative transform transition-all duration-300 cursor-pointer">
                    <div class="space-y-6">
                        <div>
                            <h3 :class="hoverCard === 3 ? 'text-white' : 'text-slate-800'" class="text-lg font-bold">Paket Cuci Satuan</h3>
                            <p :class="hoverCard === 3 ? 'text-blue-100' : 'text-slate-400'" class="text-xs">Untuk jas, kebaya, gaun, dan pakaian berbahan sensitif</p>
                        </div>
                        <div :class="hoverCard === 3 ? 'text-white' : 'text-slate-900'" class="text-3xl font-black">
                            Rp 25.000 <span :class="hoverCard === 3 ? 'text-blue-200' : 'text-slate-400'" class="text-sm font-medium">/ pcs</span>
                        </div>
                        <ul class="space-y-3.5 text-sm">
                            <li class="flex items-center space-x-2">
                                <svg :class="hoverCard === 3 ? 'text-blue-300' : 'text-blue-500'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span :class="hoverCard === 3 ? 'text-blue-100' : 'text-slate-600'">Penanganan Khusus per Satuan</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg :class="hoverCard === 3 ? 'text-blue-300' : 'text-blue-500'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span :class="hoverCard === 3 ? 'text-blue-100' : 'text-slate-600'">Pembersihan Lembut Ramah Serat</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg :class="hoverCard === 3 ? 'text-blue-300' : 'text-blue-500'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span :class="hoverCard === 3 ? 'text-blue-100' : 'text-slate-600'">Menggunakan Gantungan & Cover Pelindung</span>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ auth()->check() ? (auth()->user()->role === 'customer' ? route('customer.orders.create', ['service' => $dryService?->id]) : route('dashboard')) : route('register', ['service' => $dryService?->id]) }}" 
                       :class="hoverCard === 3 ? 'bg-white hover:bg-slate-50 text-blue-600' : 'border border-blue-500 text-blue-600 hover:bg-blue-50'"
                       class="mt-8 text-center font-bold py-3.5 rounded-xl transition duration-300 block text-xs uppercase tracking-wider">Pilih Paket</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Bagian Cerita & Testimoni (Sesuai Layout Hifi Mockup 2) -->
    <section id="testimoni" class="py-20 bg-white relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Grid Header -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-16 items-end">
                <div class="lg:col-span-8 space-y-4">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest">KEUNGGULAN KAMI</span>
                    <h2 class="text-3xl sm:text-4xl font-black text-[#0a2540]">Telah Dipercaya oleh Banyak Pelanggan</h2>
                </div>
                <!-- Statistik Kanan -->
                <div class="lg:col-span-4 flex justify-between gap-6 border-l border-slate-100 pl-6">
                    <div>
                        <span class="text-2xl font-black text-[#0a2540] block">50rb+</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Pelanggan Puas</span>
                    </div>
                    <div>
                        <span class="text-2xl font-black text-[#0a2540] block">4.9/5</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Rata-rata Rating</span>
                    </div>
                    <div>
                        <span class="text-2xl font-black text-[#0a2540] block">99.8%</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Tepat Waktu</span>
                    </div>
                </div>
            </div>

            <!-- Layout Testimoni Utama -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                <!-- Testimoni Utama (Elena Rossi) -->
                <div class="lg:col-span-8 bg-slate-50 border border-slate-100 rounded-3xl overflow-hidden flex flex-col md:flex-row hover:shadow-lg transition duration-300">
                    <div class="md:w-2/5 min-h-[300px] relative">
                        <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=500&q=80" 
                             class="w-full h-full object-cover" alt="Elena Rossi Creative Director">
                    </div>
                    <div class="md:w-3/5 p-8 md:p-12 flex flex-col justify-between space-y-6">
                        <div class="space-y-4">
                            <!-- Bintang Rating -->
                            <div class="flex items-center space-x-0.5 text-amber-400">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            </div>
                            <blockquote class="text-[#0a2540] text-lg font-medium leading-relaxed italic">
                                "L-Dry adalah satu-satunya jasa laundry yang saya percayai untuk merawat pakaian-pakaian mahal saya. Teknik pencucian mereka sangat aman untuk berbagai jenis bahan sensitif."
                            </blockquote>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-[#0a2540] text-sm">Elena Rossi</h4>
                            <p class="text-xs text-slate-400 font-medium">Creative Director, Atelier Rossi</p>
                        </div>
                    </div>
                </div>

                <!-- Kartu Janji Perawatan (Navy) -->
                <div class="lg:col-span-4 bg-[#0a2540] rounded-3xl p-8 text-white flex flex-col justify-between hover:shadow-xl transition duration-300 relative overflow-hidden">
                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-blue-500/10 rounded-full blur-xl pointer-events-none"></div>
                    <div class="space-y-6">
                        <h3 class="text-xl font-bold">Standar Kualitas Kami</h3>
                        <div class="space-y-6 text-xs leading-relaxed">
                            <div class="flex items-start space-x-3">
                                <div class="w-1.5 h-1.5 rounded-full bg-blue-400 mt-1.5 shrink-0"></div>
                                <div>
                                    <h4 class="font-bold text-slate-200">PENCUCIAN KHUSUS</h4>
                                    <p class="text-slate-400 mt-1">Setiap bahan kain dicuci dengan teknik dan sabun yang sesuai agar awet.</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-1.5 h-1.5 rounded-full bg-blue-400 mt-1.5 shrink-0"></div>
                                <div>
                                    <h4 class="font-bold text-slate-200">GARANSI BARANG</h4>
                                    <p class="text-slate-400 mt-1">Kami memberikan jaminan ganti rugi jika terjadi kerusakan pada pakaian Anda.</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-1.5 h-1.5 rounded-full bg-blue-400 mt-1.5 shrink-0"></div>
                                <div>
                                    <h4 class="font-bold text-slate-200">PEMERIKSAAN TELITI</h4>
                                    <p class="text-slate-400 mt-1">Setiap pakaian diperiksa kebersihannya dengan seksama sebelum dikemas.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ulasan Tambahan Klien -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                <!-- Ulasan 1 -->
                <div class="p-8 rounded-2xl bg-slate-50/50 border border-slate-100 hover:bg-white hover:shadow-lg transition duration-300 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-0.5 text-amber-400">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        </div>
                        <p class="text-slate-600 text-xs leading-relaxed italic">
                            "Layanan luar biasa! Jas saya tidak pernah terasa sebersih atau sewangi ini. Penjemputannya sangat lancar dan kurirnya sangat sopan."
                        </p>
                    </div>
                    <div class="flex items-center space-x-3 mt-6 border-t border-slate-100 pt-4">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-extrabold flex items-center justify-center text-xs">AK</div>
                        <div>
                            <h4 class="text-xs font-bold text-[#0a2540]">Alexander K.</h4>
                            <p class="text-[9px] text-slate-400">Klien Terverifikasi</p>
                        </div>
                    </div>
                </div>

                <!-- Ulasan 2 -->
                <div class="p-8 rounded-2xl bg-slate-50/50 border border-slate-100 hover:bg-white hover:shadow-lg transition duration-300 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-0.5 text-amber-400">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        </div>
                        <p class="text-slate-600 text-xs leading-relaxed italic">
                            "Perhatian terhadap detailnya luar biasa. Mereka berhasil menghilangkan noda anggur merah pada kemeja saya yang menurut laundry lain tidak bisa hilang."
                        </p>
                    </div>
                    <div class="flex items-center space-x-3 mt-6 border-t border-slate-100 pt-4">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-extrabold flex items-center justify-center text-xs">SM</div>
                        <div>
                            <h4 class="text-xs font-bold text-[#0a2540]">Sarah M.</h4>
                            <p class="text-[9px] text-slate-400">Klien Terverifikasi</p>
                        </div>
                    </div>
                </div>

                <!-- Ulasan 3 -->
                <div class="p-8 rounded-2xl bg-slate-50/50 border border-slate-100 hover:bg-white hover:shadow-lg transition duration-300 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-0.5 text-amber-400">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        </div>
                        <p class="text-slate-600 text-xs leading-relaxed italic">
                            "Aplikasi web membuat urusan laundry menjadi sangat praktis. Ini adalah layanan operasional premium yang sangat menghemat waktu berharga saya."
                        </p>
                    </div>
                    <div class="flex items-center space-x-3 mt-6 border-t border-slate-100 pt-4">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-extrabold flex items-center justify-center text-xs">DL</div>
                        <div>
                            <h4 class="text-xs font-bold text-[#0a2540]">David L.</h4>
                            <p class="text-[9px] text-slate-400">Klien Terverifikasi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sub CTA bawah -->
            <div class="text-center mt-16 max-w-xl mx-auto space-y-6">
                <h3 class="text-2xl font-black text-[#0a2540]">Nikmati Layanan Laundry Terbaik Kami</h3>
                <p class="text-slate-500 text-xs leading-relaxed">
                    Bergabunglah bersama ribuan pelanggan yang telah mempercayakan pakaian mereka kepada kami.
                </p>
                <div class="flex flex-col sm:flex-row justify-center items-center gap-4 pt-2">
                     <a href="{{ auth()->check() ? (auth()->user()->role === 'customer' ? route('customer.orders.create') : route('dashboard')) : route('register') }}" 
                        class="font-bold px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md hover:shadow-lg transition duration-200 text-xs uppercase tracking-wider w-full sm:w-auto">
                         Pesan Penjemputan Pertama
                     </a>
                     <a href="#harga" 
                        class="font-bold px-8 py-3.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl transition duration-200 text-xs uppercase tracking-wider w-full sm:w-auto shadow-sm">
                         Lihat Paket Harga
                     </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ & Concierge Support (Sesuai Layout Hifi Mockup 3) -->
    <section id="faq" class="py-20 bg-slate-50 relative z-10" x-data="{ active: 1 }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-16">
                <span class="text-blue-600 text-xs font-bold uppercase tracking-widest">TANYA JAWAB</span>
                <h2 class="text-3xl sm:text-4xl font-black text-[#0a2540] mt-4">Pertanyaan yang Sering Diajukan</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Kolom Kiri: FAQ Accordion -->
                <div class="lg:col-span-8 space-y-4">
                    
                    <!-- FAQ 1 -->
                    <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-sm">
                        <button @click="active = active === 1 ? null : 1" class="w-full flex items-center justify-between p-6 text-left font-bold text-[#0a2540] hover:text-blue-600 focus:outline-none">
                            <span class="text-sm md:text-base">Bagaimana cara mencuci bahan sensitif seperti sutra?</span>
                            <svg class="w-5 h-5 transition-transform duration-300 text-slate-400" :class="active === 1 ? 'rotate-180 text-blue-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="px-6 pb-6 text-slate-500 text-xs md:text-sm leading-relaxed" x-show="active === 1" x-transition x-cloak>
                            Kami mencuci bahan sutra secara manual menggunakan sabun lembut khusus dan air dengan suhu yang diatur agar serat kain tetap terjaga.
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-sm">
                        <button @click="active = active === 2 ? null : 2" class="w-full flex items-center justify-between p-6 text-left font-bold text-[#0a2540] hover:text-blue-600 focus:outline-none">
                            <span class="text-sm md:text-base">Bagaimana cara membersihkan pakaian antik atau vintage?</span>
                            <svg class="w-5 h-5 transition-transform duration-300 text-slate-400" :class="active === 2 ? 'rotate-180 text-blue-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="px-6 pb-6 text-slate-500 text-xs md:text-sm leading-relaxed" x-show="active === 2" x-transition x-cloak>
                            Kami memeriksa kondisi serat pakaian terlebih dahulu sebelum dicuci. Kami menggunakan formula pembersih yang lembut untuk mengangkat noda tanpa merusak motif asli.
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-sm">
                        <button @click="active = active === 3 ? null : 3" class="w-full flex items-center justify-between p-6 text-left font-bold text-[#0a2540] hover:text-blue-600 focus:outline-none">
                            <span class="text-sm md:text-base">Apakah bisa menjadwalkan penjemputan rutin?</span>
                            <svg class="w-5 h-5 transition-transform duration-300 text-slate-400" :class="active === 3 ? 'rotate-180 text-blue-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="px-6 pb-6 text-slate-500 text-xs md:text-sm leading-relaxed" x-show="active === 3" x-transition x-cloak>
                            Ya, Anda bisa menjadwalkan penjemputan laundry secara rutin (misalnya seminggu sekali) langsung dari halaman dashboard Anda.
                        </div>
                    </div>

                </div>

                <!-- Kolom Kanan: Kartu Layanan Concierge -->
                <div class="lg:col-span-4 bg-[#0a2540] rounded-3xl p-8 text-white flex flex-col justify-between min-h-[300px] hover:shadow-xl transition duration-300 relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-500/10 rounded-full blur-xl pointer-events-none"></div>
                    <div class="space-y-4">
                        <span class="text-[10px] font-bold text-blue-400 uppercase tracking-widest">HUBUNGI KAMI</span>
                        <h3 class="text-xl font-bold">Customer Service</h3>
                        <p class="text-slate-400 text-xs leading-relaxed">
                            Tim CS kami siap membantu Anda dengan cepat jika memiliki pertanyaan khusus mengenai cara perawatan pakaian kesayangan Anda.
                        </p>
                    </div>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp']) }}?text={{ urlencode('Halo Customer Service L-Dry, saya butuh bantuan mengenai layanan laundry.') }}" target="_blank"
                       class="mt-8 px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs uppercase tracking-wider text-center transition shadow-md">
                        Hubungi Customer Service
                    </a>
                </div>
            </div>

            <!-- Blok CTA Download Aplikasi & WhatsApp Concierge -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12">
                <!-- Kartu Aplikasi -->
                <div class="bg-white border border-slate-100 rounded-3xl p-8 md:p-10 flex flex-col justify-between hover:shadow-md transition duration-300">
                    <div class="space-y-4">
                        <span class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">PESAN MUDAH</span>
                        <h3 class="text-2xl font-black text-[#0a2540]">Aplikasi L-Dry (Web & Android)</h3>
                        <ul class="space-y-2 text-xs text-slate-500">
                            <li class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-2"></span> Lacak lokasi kurir secara real-time</li>
                            <li class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-2"></span> Atur jadwal penjemputan laundry dengan mudah</li>
                            <li class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-2"></span> Pantau status cucian Anda kapan saja</li>
                        </ul>
                    </div>
                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <a href="{{ auth()->check() ? (auth()->user()->role === 'customer' ? route('customer.orders.create') : route('dashboard')) : route('register') }}" 
                           class="flex-1 px-4 py-3.5 bg-[#0a2540] hover:bg-slate-900 text-white font-bold rounded-xl text-[11px] uppercase tracking-wider text-center transition shadow-sm">
                            Pesan Sekarang
                        </a>
                        <a href="{{ asset('downloads/l-dry.apk') }}" download
                           class="flex-1 px-4 py-3.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded-xl text-[11px] uppercase tracking-wider text-center transition border border-blue-100/50">
                            Download App (APK)
                        </a>
                    </div>
                </div>

                <!-- Kartu WhatsApp -->
                <div class="bg-white border border-slate-100 rounded-3xl p-8 md:p-10 flex flex-col justify-between hover:shadow-md transition duration-300">
                    <div class="space-y-4">
                        <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">LAYANAN CEPAT</span>
                        <h3 class="text-2xl font-black text-[#0a2540]">Layanan Chat WhatsApp</h3>
                        <p class="text-slate-500 text-xs leading-relaxed">
                            Punya pertanyaan cepat atau ingin pesan laundry lebih mudah? Silakan hubungi admin kami via WhatsApp.
                        </p>
                    </div>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp']) }}?text={{ urlencode('Halo L-Dry, saya ingin memesan layanan laundry antar-jemput.') }}" target="_blank"
                       class="mt-8 px-6 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs uppercase tracking-widest text-center transition shadow-sm">
                        Chat WhatsApp Sekarang
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- Bagian Kaki (Footer) -->
    <footer id="kontak" class="bg-[#0a2540] text-slate-400 pt-20 pb-12 border-t border-slate-900 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-12 gap-12 mb-16">
            <!-- Kolom Brand -->
            <div class="md:col-span-4 space-y-4">
                <a href="#" class="flex items-center text-white">
                    <img src="{{ asset('logo.png') }}" class="h-10 w-auto object-contain brightness-0 invert" alt="L-DRY Logo">
                </a>
                <p class="text-slate-400 text-xs leading-relaxed max-w-xs">
                    Layanan perawatan pakaian premium dan laundry tepercaya untuk kenyamanan hidup Anda. Pakaian bersih, bumi terjaga.
                </p>
                <p class="text-xs text-blue-400 font-bold">LAYANAN AKTIF & SIAP MEMBANTU</p>
            </div>

            <!-- Kolom Link 1: Layanan -->
            <div class="md:col-span-2 space-y-4">
                <h4 class="text-white font-bold text-xs uppercase tracking-wider">Layanan</h4>
                <ul class="space-y-2.5 text-xs">
                    <li><a href="#layanan" class="hover:text-blue-400 transition">Cuci Premium</a></li>
                    <li><a href="#layanan" class="hover:text-blue-400 transition">Dry Cleaning</a></li>
                    <li><a href="#layanan" class="hover:text-blue-400 transition">Sepatu & Tas</a></li>
                    <li><a href="#layanan" class="hover:text-blue-400 transition">Perawatan Khusus</a></li>
                </ul>
            </div>

            <!-- Kolom Link 2: Perusahaan -->
            <div class="md:col-span-2 space-y-4">
                <h4 class="text-white font-bold text-xs uppercase tracking-wider">Perusahaan</h4>
                <ul class="space-y-2.5 text-xs">
                    <li><a href="#keunggulan" class="hover:text-blue-400 transition">Tentang Kami</a></li>
                    <li><a href="#keunggulan" class="hover:text-blue-400 transition">Keberlanjutan</a></li>
                    <li><a href="#testimoni" class="hover:text-blue-400 transition">Kisah Pelanggan</a></li>
                    <li><a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp']) }}?text={{ urlencode('Halo L-Dry, saya tertarik untuk bermitra dengan L-Dry.') }}" class="hover:text-blue-400 transition">Kemitraan</a></li>
                </ul>
            </div>

            <!-- Kolom Link 3: Bantuan -->
            <div class="md:col-span-2 space-y-4">
                <h4 class="text-white font-bold text-xs uppercase tracking-wider">Bantuan</h4>
                <ul class="space-y-2.5 text-xs">
                    <li><a href="#faq" class="hover:text-blue-400 transition">Tanya Jawab</a></li>
                    <li><a href="#cara-kerja" class="hover:text-blue-400 transition">Lacak Pesanan</a></li>
                    <li><a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp']) }}?text={{ urlencode('Halo L-Dry, saya ingin bertanya tentang status layanan laundry saya.') }}" class="hover:text-blue-400 transition">WhatsApp Langsung</a></li>
                    <li><a href="mailto:support@ldry.com" class="hover:text-blue-400 transition">Bantuan Email</a></li>
                </ul>
            </div>

            <!-- Kolom Link 4: Hukum -->
            <div class="md:col-span-2 space-y-4">
                <h4 class="text-white font-bold text-xs uppercase tracking-wider">Hukum</h4>
                <ul class="space-y-2.5 text-xs">
                    <li><a href="#" class="hover:text-blue-400 transition">Ketentuan Layanan</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition">Kebijakan Privasi</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition">Aksesibilitas</a></li>
                </ul>
            </div>
        </div>

        <!-- Hak Cipta -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 border-t border-white/5 pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
            <p>&copy; {{ date('Y') }} L-DRY Garment Care. Hak Cipta Dilindungi Undang-Undang. Dirancang untuk layanan operasional premium.</p>
            <p>Surabaya, Indonesia | {{ $settings['address'] }}</p>
        </div>
    </footer>

</body>
</html>
