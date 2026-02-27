<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Neraca') }} - Kendalikan Keuangan Anda</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;450;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://unpkg.com/lucide-static@latest/font/lucide.css" rel="stylesheet">

    <!-- Lottie Player -->
    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.9.3/dist/dotlottie-wc.js" type="module"></script>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/landing.css'])
</head>
<body class="antialiased min-h-screen flex flex-col relative overflow-x-hidden bg-white">
    <!-- Subtle Background Effect -->
    <div class="fixed inset-0 bg-gradient-radial -z-10 bg-white"></div>

    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300 border-b border-gray-100/50" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-2">
                    <div style="width: 32px; height: 32px; background: linear-gradient(135deg, var(--n-primary), var(--n-secondary)); border-radius: var(--n-radius-sm); display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(15, 118, 110, 0.3);">
                        <i class="icon-wallet" style="color: white; font-size: 16px;"></i>
                    </div>
                    <a href="/" class="text-xl font-bold tracking-tight text-gray-900">
                        Neraca.
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8"> 
                    <a href="#features" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Fitur</a>
                    <a href="#security" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Keamanan</a>
                    
                    <div class="flex items-center space-x-4 pl-4 border-l border-gray-200">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="mac-btn-primary text-sm" style="padding: 8px 20px; box-shadow: none;">Masuk Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="mac-btn-primary text-sm" style="padding: 8px 20px; box-shadow: none;">Daftar Gratis</a>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="flex md:hidden items-center" id="mobile-menu-btn-container">
                    <button class="focus:outline-none p-2 text-gray-600 hover:text-gray-900" id="mobile-menu-btn">
                        <i class="icon-menu block" id="menu-icon-open" style="font-size: 24px;"></i>
                        <i class="icon-x hidden" id="menu-icon-close" style="font-size: 24px;"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu (Hidden by default) -->
        <div class="md:hidden hidden bg-white/95 backdrop-blur-xl border-b border-gray-100 absolute w-full left-0 top-16 shadow-lg" id="mobile-menu">
            <div class="px-4 pt-2 pb-6 space-y-2">
                <a href="#features" class="block px-3 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg">Fitur</a>
                <a href="#security" class="block px-3 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg">Keamanan</a>
                <div class="border-t border-gray-100 pt-4 mt-2">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="block text-center mac-btn-primary w-full">Masuk Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="block w-full text-center px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg border border-gray-200 mb-3">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block text-center mac-btn-primary w-full">Daftar Gratis</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="flex-grow pt-28 pb-12 sm:pt-40 sm:pb-24 lg:pb-32 overflow-hidden flex items-center bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative w-full">
            <div class="flex flex-col-reverse lg:grid lg:grid-cols-12 lg:gap-16 items-center">
                <!-- Text Content -->
                <div class="md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left text-center z-10 relative mt-10 lg:mt-0">
                    <h1 class="mac-headline mb-4 sm:mb-6 text-gray-900 leading-tight">
                        Kendalikan <br class="hidden sm:block"/>Keuanganmu.<br/>
                        <span class="text-gray-400">Tanpa Kompromi.</span>
                    </h1>
                    <p class="mac-subhead mb-8 sm:mb-10 max-w-lg mx-auto lg:mx-0 text-gray-500 text-base sm:text-lg">
                        Pencatatan cerdas, analitik mendalam, dan keamanan setingkat bank. Semua yang Anda butuhkan untuk mencapai kebebasan finansial dalam satu aplikasi elegan.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="mac-btn-primary w-full sm:w-auto">Ke Dashboard</a>
                        @else
                            <a href="{{ route('register') }}" class="mac-btn-primary w-full sm:w-auto">Mulai Sekarang</a>
                            <a href="#features" class="mac-btn-secondary w-full sm:w-auto">Pelajari Lebih Lanjut</a>
                        @endauth
                    </div>
                </div>

                <!-- Lottie Hero Animation -->
                <div class="lg:col-span-6 flex justify-center lg:justify-end relative z-0 w-full max-w-md mx-auto lg:max-w-none">
                    <div class="relative w-[280px] h-[280px] sm:w-[450px] sm:h-[450px] lg:w-[500px] lg:h-[500px]">
                        <dotlottie-wc src="https://lottie.host/a9571627-09f8-4c25-8ce8-7d7ca5d4dfc7/7DjrcLVVSZ.json" style="width: 100%; height: 100%;" autoplay loop></dotlottie-wc>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Features Section -->
    <section id="features" class="py-16 sm:py-24 relative z-10 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-20">
                <h2 class="text-3xl md:text-5xl font-bold tracking-tight mb-4 sm:mb-6 text-gray-900">Didesain untuk kejelasan.</h2>
                <p class="text-lg sm:text-xl text-gray-500">Lebih dari sekadar pencatatan. Neraca dirancang untuk memberi Anda gambaran utuh tentang kondisi finansial Anda.</p>
            </div>

            <!-- Grid 1 (Top Row 3 Items) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 sm:gap-8 mb-6 sm:mb-8">
                <!-- Feature 1 -->
                <div class="mac-card p-6 sm:p-8 group text-center sm:text-left">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-6 mx-auto sm:mx-0 transition-colors icon-box-primary">
                        <i class="icon-repeat" style="font-size: 22px;"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">Transaksi Otomatis</h3>
                    <p class="text-gray-500 leading-relaxed text-sm">Buat transaksi berulang (recurring) untuk tagihan bulanan atau langganan agar Anda tidak pernah lupa mencatat.</p>
                </div>

                <!-- Feature 2 -->
                <div class="mac-card p-6 sm:p-8 group text-center sm:text-left">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-6 mx-auto sm:mx-0 transition-colors icon-box-income">
                        <i class="icon-piggy-bank" style="font-size: 22px;"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">Anggaran Cerdas</h3>
                    <p class="text-gray-500 leading-relaxed text-sm">Tetapkan batas pengeluaran bulanan per kategori. Dapatkan peringatan dinamis sebelum Anda melewati batas budget.</p>
                </div>

                <!-- Feature 3 -->
                <div class="mac-card p-6 sm:p-8 group text-center sm:text-left">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-6 mx-auto sm:mx-0 transition-colors icon-box-accent">
                        <i class="icon-target" style="font-size: 22px;"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">Tujuan Finansial</h3>
                    <p class="text-gray-500 leading-relaxed text-sm">Simulasikan dan pantau progress tabungan Anda untuk membeli rumah, mobil, atau liburan impian dengan mudah.</p>
                </div>
            </div>

            <!-- Grid 2 (Bottom Row 3 Items) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 sm:gap-8">
                <!-- Feature 4 -->
                <div class="mac-card p-6 sm:p-8 group text-center sm:text-left">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-6 mx-auto sm:mx-0 transition-colors bg-purple-50 text-purple-600 group-hover:bg-purple-600 group-hover:text-white">
                        <i class="icon-bar-chart-3" style="font-size: 22px;"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">Analitik Laporan</h3>
                    <p class="text-gray-500 leading-relaxed text-sm">Visualisasikan arus kas Anda dengan grafik interaktif. Ekspor data ke PDF, Excel, atau CSV untuk analisis lebih dalam.</p>
                </div>

                <!-- Feature 5 -->
                <div class="mac-card p-6 sm:p-8 group text-center sm:text-left">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-6 mx-auto sm:mx-0 transition-colors bg-amber-50 text-amber-600 group-hover:bg-amber-500 group-hover:text-white">
                        <i class="icon-trending-up" style="font-size: 22px;"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">Sistem Prediksi</h3>
                    <p class="text-gray-500 leading-relaxed text-sm">Algoritma cerdas yang mampu memprediksi kondisi keuangan Anda di bulan-bulan berikutnya berdasarkan pola masa lalu.</p>
                </div>

                <!-- Feature 6 -->
                <div class="mac-card p-6 sm:p-8 group text-center sm:text-left">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-6 mx-auto sm:mx-0 transition-colors bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white">
                        <i class="icon-zap" style="font-size: 22px;"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">Template Cepat</h3>
                    <p class="text-gray-500 leading-relaxed text-sm">Simpan jenis transaksi yang paling sering Anda lakukan sebagai template dan gunakan kembali hanya dengan satu klik.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Security Section -->
    <section id="security" class="py-16 sm:py-24 mac-security-section bg-white relative">
        <!-- Glow effects -->
        <div class="glow-blob-1"></div>
        <div class="glow-blob-2"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
            <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
                <!-- Lottie Security Animation -->
                <div class="order-1 lg:order-1 flex justify-center lg:justify-start mb-10 lg:mb-0">
                    <div class="relative w-[280px] h-[280px] sm:w-[400px] sm:h-[400px] p-6 sm:p-8 rounded-3xl bg-white/60 backdrop-blur-3xl border border-gray-100" style="box-shadow: 0 10px 40px rgba(0,0,0,0.03);">
                        <dotlottie-wc src="https://lottie.host/a9db9d9c-7f3b-48f2-8d49-8633795b7306/CsvO5f5wtB.lottie" style="width: 100%; height: 100%;" autoplay loop></dotlottie-wc>
                    </div>
                </div>

                <!-- Text Content -->
                <div class="order-2 lg:order-2 lg:text-left text-center">
                    <div class="inline-flex items-center px-4 py-1.5 rounded-full text-xs sm:text-sm font-medium mb-4 sm:mb-6" style="background: var(--n-primary-light); color: var(--n-primary);">
                        <i class="icon-shield" style="font-size: 14px; margin-right: 8px;"></i>
                        Privasi Penuh
                    </div>
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight mb-4 sm:mb-6 text-gray-900">Data Finansialmu,<br class="hidden sm:block"/>Urusanmu.</h2>
                    <p class="text-lg sm:text-xl mb-6 sm:mb-8 max-w-lg mx-auto lg:mx-0 leading-relaxed text-gray-500">
                        Kami percaya bahwa privasi adalah hak asasi. Semua data transaksi, aset, dan portofolio Anda dienkripsi secara aman di dalam database. Hanya Anda yang memiliki kendali penuh.
                    </p>
                    <ul class="space-y-4 text-left inline-block">
                        <li class="flex items-center text-sm sm:text-base text-gray-600">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center mr-3 flex-shrink-0" style="background: var(--n-primary-light); color: var(--n-primary);">
                                <i class="icon-check" style="font-size: 14px;"></i>
                            </span>
                            Enkripsi Database Standar Industri
                        </li>
                        <li class="flex items-center text-sm sm:text-base text-gray-600">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center mr-3 flex-shrink-0" style="background: var(--n-primary-light); color: var(--n-primary);">
                                <i class="icon-check" style="font-size: 14px;"></i>
                            </span>
                            Otentikasi Dua Langkah (2FA)
                        </li>
                        <li class="flex items-center text-sm sm:text-base text-gray-600">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center mr-3 flex-shrink-0" style="background: var(--n-primary-light); color: var(--n-primary);">
                                <i class="icon-check" style="font-size: 14px;"></i>
                            </span>
                            Backup Reguler & Otomatis
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Proof / Highlight Section -->
    <section class="py-24 bg-gray-50 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h2 class="text-2xl md:text-4xl font-bold tracking-tight text-gray-900 mb-8 max-w-2xl mx-auto">Satu aplikasi untuk seluruh manajemen kekayaan Anda.</h2>
            <div class="flex flex-wrap justify-center items-center gap-8 opacity-60 grayscale">
                <!-- Mockup Partner Icons using Lucide -->
                <div class="flex items-center gap-2"><i class="icon-building" style="font-size: 24px;"></i><span class="font-semibold text-lg">Bank Lokal</span></div>
                <div class="flex items-center gap-2"><i class="icon-credit-card" style="font-size: 24px;"></i><span class="font-semibold text-lg">Kartu Kredit</span></div>
                <div class="flex items-center gap-2"><i class="icon-briefcase" style="font-size: 24px;"></i><span class="font-semibold text-lg">Dompet Digital</span></div>
                <div class="flex items-center gap-2"><i class="icon-landmark" style="font-size: 24px;"></i><span class="font-semibold text-lg">Investasi</span></div>
            </div>
        </div>
    </section>

    <!-- Bottom CTA -->
    <section class="py-24 bg-white text-center">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl md:text-5xl font-bold tracking-tight text-gray-900 mb-6">Mulai kendalikan masa depan finansial Anda.</h2>
            <p class="text-xl text-gray-500 mb-10">Daftar sekarang, 100% gratis. Tanpa kartu kredit. Tanpa kerumitan.</p>
            <a href="{{ route('register') }}" class="mac-btn-primary text-lg" style="padding: 16px 36px;">Buat Akun Gratis</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 bg-white border-t border-gray-100 flex-shrink-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="flex flex-col md:flex-row justify-between items-center text-center md:text-left">
                <div class="mb-6 md:mb-0">
                    <span class="text-xl font-bold text-gray-900">Neraca.</span>
                    <p class="text-sm mt-1 text-gray-500">&copy; {{ date('Y') }} Hak Cipta Dilindungi.</p>
                </div>
                <div class="flex flex-wrap justify-center gap-6">
                    <a href="#" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Ketentuan</a>
                    <a href="#" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Privasi</a>
                    <a href="#" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Kontak</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Navbar Scroll
            const nav = document.getElementById('navbar');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 10) {
                    nav.classList.add('scrolled');
                } else {
                    nav.classList.remove('scrolled');
                }
            });

            // Mobile Menu Toggle
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            const iconOpen = document.getElementById('menu-icon-open');
            const iconClose = document.getElementById('menu-icon-close');
            
            // Only add event listener if elements exist (safety check)
            if(btn && menu && iconOpen && iconClose) {
                // Initial styling for hidden menu
                menu.style.display = 'none';
                
                btn.addEventListener('click', () => {
                    const isHidden = menu.style.display === 'none';
                    if (isHidden) {
                        menu.style.display = 'block';
                        iconOpen.classList.add('hidden');
                        iconOpen.classList.remove('block');
                        iconClose.classList.add('block');
                        iconClose.classList.remove('hidden');
                    } else {
                        menu.style.display = 'none';
                        iconClose.classList.add('hidden');
                        iconClose.classList.remove('block');
                        iconOpen.classList.add('block');
                        iconOpen.classList.remove('hidden');
                    }
                });
                
                // Hide menu when clicking a link inside it
                const menuLinks = menu.querySelectorAll('a');
                menuLinks.forEach(link => {
                    link.addEventListener('click', () => {
                        menu.style.display = 'none';
                        iconClose.classList.add('hidden');
                        iconClose.classList.remove('block');
                        iconOpen.classList.add('block');
                        iconOpen.classList.remove('hidden');
                    });
                });
            }
        });
    </script>
</body>
</html>
