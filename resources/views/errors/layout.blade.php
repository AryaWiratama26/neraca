<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name', 'Neraca') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;450;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://unpkg.com/lucide-static@latest/font/lucide.css" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/landing.css'])
</head>
<body class="antialiased min-h-screen flex flex-col relative overflow-hidden bg-white">
    <!-- Subtle Background Effect -->
    <div class="fixed inset-0 bg-gradient-radial -z-10 bg-white"></div>

    <!-- Navbar Minimal -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300 border-b border-gray-100/50">
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
                <div>
                    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors flex items-center gap-2">
                        <i class="icon-arrow-left" style="font-size: 16px;"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center pt-24 pb-12 px-4 sm:px-6 lg:px-8 relative z-10 w-full min-h-[80vh]">
        <div class="text-center w-full max-w-2xl mx-auto">
            <!-- Glitched or Error Icon Block -->
            <div class="mb-8 flex justify-center">
                <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-3xl flex items-center justify-center relative transition-transform duration-500 hover:scale-105" style="background: var(--n-surface-solid); border: 1px solid var(--n-border); box-shadow: 0 10px 40px rgba(0,0,0,0.04);">
                    <div class="absolute inset-0 bg-red-50 opacity-50 rounded-3xl"></div>
                    @yield('icon')
                </div>
            </div>

            <!-- Error Code & Message -->
            <h1 class="mac-headline mb-4 sm:mb-6 text-gray-900 tracking-tight leading-none" style="font-size: clamp(4rem, 10vw, 7rem);">
                @yield('code')
            </h1>
            <h2 class="text-2xl sm:text-3xl font-semibold mb-3 sm:mb-4 text-gray-800">
                @yield('message')
            </h2>
            <p class="text-base sm:text-lg text-gray-500 max-w-md mx-auto mb-10 leading-relaxed">
                @yield('description', 'Maaf, terjadi kesalahan atau halaman yang Anda tuju tidak ditemukan.')
            </p>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/') }}" class="mac-btn-primary w-full sm:w-auto">
                    <i class="icon-home" style="font-size: 16px; margin-right: 8px;"></i> Ke Halaman Utama
                </a>
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}" class="mac-btn-secondary w-full sm:w-auto">
                    Coba Lagi
                </a>
            </div>
        </div>
    </main>
</body>
</html>
