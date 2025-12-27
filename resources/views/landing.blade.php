@extends('layouts.landing')

@section('content')
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <a href="/" class="text-xl font-bold text-gray-900">
                        Transa
                    </a>
                    <span class="ml-3 text-sm text-gray-500 hidden sm:inline">
                        Early Access
                    </span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/admin/login" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                        Masuk
                    </a>
                    <a href="{{ route('register.step1') }}"
                        class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                        Coba Gratis
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-20 pb-16 bg-gradient-to-b from-gray-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight mb-6">
                    Sistem POS Modern untuk<br>
                    Bisnis Retail & F&B Anda
                </h1>
                <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                    Kelola penjualan, stok, dan multi-outlet dalam satu platform.
                    Dibangun dengan teknologi terkini hasil JagoFlutter Academy.
                </p>
                <div class="flex flex-col gap-4">
                    <!-- Primary CTAs -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        @auth
                            <a href="/admin"
                                class="bg-primary-600 text-white px-8 py-4 rounded-lg text-lg font-medium hover:bg-primary-700 transition shadow-lg">
                                Buka Dashboard
                            </a>
                        @else
                            <a href="{{ route('register.step1') }}"
                                class="bg-primary-600 text-white px-8 py-4 rounded-lg text-lg font-medium hover:bg-primary-700 transition shadow-lg">
                                Mulai Trial Gratis 14 Hari
                            </a>
                        @endauth
                        <a href="https://jagoflutter.com/academy/pos-saas/register" target="_blank"
                            class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-8 py-4 rounded-lg text-lg font-medium hover:from-purple-700 hover:to-blue-700 transition shadow-lg">
                            Join Academy - Belajar Membangunnya
                        </a>
                    </div>

                    <!-- Secondary CTA -->
                    <div class="flex justify-center">
                        <a href="#features"
                            class="text-gray-700 px-6 py-2 rounded-lg text-base font-medium hover:text-primary-600 transition inline-flex items-center gap-2">
                            Lihat Fitur Lengkap
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="mt-6 space-y-2">
                    @auth
                        <p class="text-sm text-gray-500">
                            <span class="font-semibold text-primary-600">Selamat datang kembali!</span> Akses dashboard Anda
                            untuk mengelola bisnis
                        </p>
                    @else
                        <p class="text-sm text-gray-500">
                            <span class="font-semibold text-primary-600">Gunakan Sistem:</span> Tidak perlu kartu kredit •
                            Setup 5 menit • Data Anda aman
                        </p>
                    @endauth
                    <p class="text-sm text-gray-500">
                        <span class="font-semibold text-purple-600">Belajar Membuat:</span> Kuasai skill development POS
                        SaaS • Project portfolio • Sertifikat
                    </p>
                </div>
            </div>

            <!-- Main Screenshot -->
            <div class="mt-16 max-w-5xl mx-auto">
                <div class="bg-gray-100 rounded-xl shadow-2xl overflow-hidden border border-gray-200">
                    <div class="bg-gray-200 px-4 py-3 flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    </div>
                    <img src="{{ asset('images/img5.jpeg') }}" alt="Dashboard POS SaaS - Preview"
                        class="w-full object-cover cursor-pointer hover:opacity-90 transition-opacity lightbox-trigger"
                        data-lightbox-src="{{ asset('images/img5.jpeg') }}"
                        data-lightbox-alt="Dashboard POS SaaS - Preview"
                        loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-12 bg-white border-y border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-3xl font-bold text-gray-900">100%</div>
                    <div class="text-sm text-gray-600 mt-1">Cloud-based</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-gray-900">Real-time</div>
                    <div class="text-sm text-gray-600 mt-1">Update Data</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-gray-900">Multi</div>
                    <div class="text-sm text-gray-600 mt-1">Outlet Ready</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-gray-900">14 Hari</div>
                    <div class="text-sm text-gray-600 mt-1">Trial Gratis</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Screenshots Gallery Section -->
    <section class="py-20 bg-gradient-to-b from-white to-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    Lihat Tampilan Aplikasi Kami
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Interface yang intuitif dan modern, dirancang untuk memudahkan pengelolaan bisnis Anda
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Screenshot 2 -->
                <div
                    class="group relative overflow-hidden rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer lightbox-trigger"
                    data-lightbox-src="{{ asset('images/img2.jpeg') }}"
                    data-lightbox-alt="Screenshot POS SaaS 2">
                    <img src="{{ asset('images/img2.jpeg') }}" alt="Screenshot POS SaaS 2"
                        class="w-full h-auto object-cover" loading="lazy">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path>
                        </svg>
                    </div>
                </div>

                <!-- Screenshot 3 -->
                <div
                    class="group relative overflow-hidden rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer lightbox-trigger"
                    data-lightbox-src="{{ asset('images/img3.jpeg') }}"
                    data-lightbox-alt="Screenshot POS SaaS 3">
                    <img src="{{ asset('images/img3.jpeg') }}" alt="Screenshot POS SaaS 3"
                        class="w-full h-auto object-cover" loading="lazy">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path>
                        </svg>
                    </div>
                </div>

                <!-- Screenshot 4 -->
                <div
                    class="group relative overflow-hidden rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer lightbox-trigger"
                    data-lightbox-src="{{ asset('images/img4.jpeg') }}"
                    data-lightbox-alt="Screenshot POS SaaS 4">
                    <img src="{{ asset('images/img4.jpeg') }}" alt="Screenshot POS SaaS 4"
                        class="w-full h-auto object-cover" loading="lazy">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path>
                        </svg>
                    </div>
                </div>

                <!-- Screenshot 5 -->
                <div
                    class="group relative overflow-hidden rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer lightbox-trigger"
                    data-lightbox-src="{{ asset('images/img5.jpeg') }}"
                    data-lightbox-alt="Screenshot POS SaaS 5">
                    <img src="{{ asset('images/img5.jpeg') }}" alt="Screenshot POS SaaS 5"
                        class="w-full h-auto object-cover" loading="lazy">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path>
                        </svg>
                    </div>
                </div>

                <!-- Screenshot 6 -->
                <div
                    class="group relative overflow-hidden rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer lightbox-trigger"
                    data-lightbox-src="{{ asset('images/img6.jpeg') }}"
                    data-lightbox-alt="Screenshot POS SaaS 6">
                    <img src="{{ asset('images/img6.jpeg') }}" alt="Screenshot POS SaaS 6"
                        class="w-full h-auto object-cover" loading="lazy">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path>
                        </svg>
                    </div>
                </div>

                <!-- Screenshot 7 -->
                <div
                    class="group relative overflow-hidden rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer lightbox-trigger"
                    data-lightbox-src="{{ asset('images/img7.jpeg') }}"
                    data-lightbox-alt="Screenshot POS SaaS 7">
                    <img src="{{ asset('images/img7.jpeg') }}" alt="Screenshot POS SaaS 7"
                        class="w-full h-auto object-cover" loading="lazy">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mt-12">
                <a href="https://jagoflutter.com/academy/pos-saas/register" target="_blank"
                    class="inline-block bg-white text-primary-600 px-8 py-4 rounded-lg text-lg font-medium border-2 border-primary-600 hover:bg-primary-50 transition shadow-lg">
                    Pelajari Cara Membuatnya
                </a>
                @auth
                    <a href="/admin"
                        class="inline-block bg-primary-600 text-white px-8 py-4 rounded-lg text-lg font-medium hover:bg-primary-700 transition shadow-lg">
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('register.step1') }}"
                        class="inline-block bg-primary-600 text-white px-8 py-4 rounded-lg text-lg font-medium hover:bg-primary-700 transition shadow-lg">
                        Coba Gratis Sekarang
                    </a>
                @endauth
            </div>
            <p class="text-center text-sm text-gray-600 mt-4">
                Ingin membangun sistem serupa? Bergabunglah dengan <span class="font-semibold text-primary-600">JagoFlutter
                    Academy</span> dan kuasai skill development POS SaaS dari nol hingga production-ready
            </p>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    Semua yang Anda Butuhkan dalam Satu Platform
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Fitur lengkap untuk mengelola bisnis retail dan F&B, dari transaksi hingga laporan keuangan.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-lg border border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">
                        Dashboard Real-time
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Pantau penjualan, stok, dan performa bisnis Anda dalam satu dashboard yang mudah dipahami.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-lg border border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">
                        Multi-Outlet Management
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Kelola banyak cabang sekaligus. Bandingkan performa dan transfer stok antar outlet dengan mudah.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-lg border border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">
                        Manajemen Stok Pintar
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Tracking stok real-time, notifikasi stok menipis, dan histori pergerakan stok lengkap.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white p-8 rounded-lg border border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">
                        Laporan & Analitik
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Laporan penjualan harian, bulanan, dan tahunan. Export ke Excel untuk analisa lebih lanjut.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white p-8 rounded-lg border border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">
                        Manajemen User & Role
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Atur akses tim Anda dengan sistem role. Owner, manajer, dan kasir punya akses berbeda.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white p-8 rounded-lg border border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">
                        Promo & Voucher
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Buat promosi dan voucher untuk menarik pelanggan. Atur periode dan syarat promo dengan fleksibel.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    Pilih Paket yang Sesuai untuk Bisnis Anda
                </h2>
                <p class="text-lg text-gray-600">
                    Mulai dengan trial gratis, upgrade kapan saja tanpa komitmen jangka panjang.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                @foreach ($plans as $plan)
                    @php
                        $isPopular = $plan->is_popular;
                        $features = json_decode($plan->features, true) ?? [];
                        $priceDisplay = $plan->price == 0 ? 'Gratis' : number_format($plan->price / 1000, 0) . 'rb';
                        $periodDisplay = $plan->billing_cycle === 'trial' ? $plan->trial_days . ' hari' : 'per bulan';
                        $buttonText = $plan->billing_cycle === 'trial' ? 'Mulai Trial' : 'Pilih ' . $plan->name;
                    @endphp

                    <div
                        class="{{ $isPopular ? 'bg-primary-600 border-primary-600' : 'bg-white border-gray-200' }} border-2 rounded-xl p-8 relative">
                        @if ($isPopular)
                            <div
                                class="absolute -top-4 left-1/2 -translate-x-1/2 bg-yellow-400 text-gray-900 px-4 py-1 rounded-full text-sm font-medium">
                                Paling Populer
                            </div>
                        @endif

                        <div class="text-center">
                            <h3 class="text-xl font-bold {{ $isPopular ? 'text-white' : 'text-gray-900' }} mb-2">
                                {{ $plan->name }}
                            </h3>
                            <div class="text-4xl font-bold {{ $isPopular ? 'text-white' : 'text-gray-900' }} mb-1">
                                {{ $priceDisplay }}
                            </div>
                            <div class="{{ $isPopular ? 'text-primary-100' : 'text-gray-600' }} mb-6">
                                {{ $periodDisplay }}
                            </div>
                        </div>

                        <ul class="space-y-3 mb-8">
                            @foreach ($features as $feature)
                                <li class="flex items-start">
                                    <span class="{{ $isPopular ? 'text-primary-200' : 'text-primary-600' }} mr-2">✓</span>
                                    <span
                                        class="{{ $isPopular ? 'text-white' : 'text-gray-700' }}">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ route('register.step1') }}"
                            class="block w-full text-center {{ $isPopular ? 'bg-white text-primary-600 hover:bg-gray-50' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} py-3 rounded-lg font-medium transition">
                            {{ $buttonText }}
                        </a>
                    </div>
                @endforeach
            </div>

            <p class="text-center text-gray-600 mt-12">
                Butuh lebih banyak outlet?
                <a href="mailto:saifulbkn@gmail.com" class="text-primary-600 font-medium hover:underline">Hubungi kami</a>
                untuk paket Enterprise.
            </p>
        </div>
    </section>

    <!-- Trial CTA Section -->
    <section id="trial" class="py-20 bg-primary-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                Siap untuk Langkah Selanjutnya?
            </h2>
            <p class="text-xl text-primary-100 mb-8">
                Gunakan sistem kami atau belajar membangun sendiri. Pilihan ada di tangan Anda.
            </p>

            <!-- Dual CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                <div class="flex-1 max-w-sm">
                    @auth
                        <a href="/admin"
                            class="block bg-white text-primary-600 px-8 py-4 rounded-lg text-lg font-medium hover:bg-gray-50 transition shadow-lg">
                            <div class="font-bold mb-1">Buka Dashboard</div>
                            <div class="text-sm">Kelola bisnis Anda sekarang</div>
                        </a>
                    @else
                        <a href="{{ route('register.step1') }}"
                            class="block bg-white text-primary-600 px-8 py-4 rounded-lg text-lg font-medium hover:bg-gray-50 transition shadow-lg">
                            <div class="font-bold mb-1">Mulai Trial Gratis</div>
                            <div class="text-sm">14 hari • Tanpa kartu kredit</div>
                        </a>
                    @endauth
                </div>
                <div class="flex-1 max-w-sm">
                    <a href="https://jagoflutter.com/academy/pos-saas/register" target="_blank"
                        class="block bg-gradient-to-r from-purple-700 to-blue-700 text-white px-8 py-4 rounded-lg text-lg font-medium hover:from-purple-800 hover:to-blue-800 transition shadow-lg border-2 border-white/20">
                        <div class="font-bold mb-1">Join Academy</div>
                        <div class="text-sm">Belajar • Build • Sertifikat</div>
                    </a>
                </div>
            </div>

            @guest
                <p class="text-primary-100 text-sm">
                    Sudah punya akun?
                    <a href="/admin/login" class="text-white font-medium hover:underline">Masuk di sini</a>
                </p>
            @endguest
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <div>
                    <h3 class="text-white font-bold mb-4">POS SaaS</h3>
                    <p class="text-sm leading-relaxed">
                        Sistem Point of Sale modern untuk bisnis retail dan F&B.
                        Hasil karya JagoFlutter Academy.
                    </p>
                </div>
                <div>
                    <h3 class="text-white font-bold mb-4">Produk</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#features" class="hover:text-white">Fitur</a></li>
                        <li><a href="#pricing" class="hover:text-white">Harga</a></li>
                        <li><a href="/admin/login" class="hover:text-white">Login</a></li>
                        <li><a href="/register" class="hover:text-white">Daftar</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-bold mb-4">JagoFlutter Academy</h3>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="https://jagoflutter.com/academy/pos-saas" target="_blank" class="hover:text-white">
                                Course POS SaaS
                            </a>
                        </li>
                        <li>
                            <a href="https://jagoflutter.com" target="_blank" class="hover:text-white">
                                Website Utama
                            </a>
                        </li>
                        <li>
                            <a href="mailto:saifulbkn@gmail.com" class="hover:text-white">
                                Kontak
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-sm">
                <p>&copy; {{ date('Y') }} POS SaaS by JagoFlutter Academy. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Lightbox Modal -->
    <div id="lightbox-modal" class="fixed inset-0 z-[9999] hidden bg-black bg-opacity-95 flex items-center justify-center p-4"
        style="backdrop-filter: blur(10px);">
        <!-- Close Button -->
        <button id="lightbox-close"
            class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors z-10 p-2 rounded-full hover:bg-white/10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Navigation Buttons -->
        <button id="lightbox-prev"
            class="absolute left-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 transition-colors p-3 rounded-full hover:bg-white/10 disabled:opacity-30 disabled:cursor-not-allowed">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>

        <button id="lightbox-next"
            class="absolute right-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 transition-colors p-3 rounded-full hover:bg-white/10 disabled:opacity-30 disabled:cursor-not-allowed">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>

        <!-- Image Container -->
        <div class="relative max-w-7xl max-h-full flex items-center justify-center">
            <img id="lightbox-image" src="" alt="" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">

            <!-- Image Counter -->
            <div class="absolute -bottom-12 left-1/2 -translate-x-1/2 text-white text-sm bg-black/50 px-4 py-2 rounded-full">
                <span id="lightbox-counter"></span>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div id="lightbox-loading" class="absolute inset-0 flex items-center justify-center">
            <div class="animate-spin rounded-full h-16 w-16 border-4 border-white border-t-transparent"></div>
        </div>
    </div>

    <script>
        (function() {
            const modal = document.getElementById('lightbox-modal');
            const image = document.getElementById('lightbox-image');
            const counter = document.getElementById('lightbox-counter');
            const closeBtn = document.getElementById('lightbox-close');
            const prevBtn = document.getElementById('lightbox-prev');
            const nextBtn = document.getElementById('lightbox-next');
            const loading = document.getElementById('lightbox-loading');

            let images = [];
            let currentIndex = 0;

            // Collect all lightbox images
            function collectImages() {
                const triggers = document.querySelectorAll('.lightbox-trigger');
                images = Array.from(triggers).map(trigger => ({
                    src: trigger.dataset.lightboxSrc || trigger.src,
                    alt: trigger.dataset.lightboxAlt || trigger.alt
                }));
            }

            // Show loading
            function showLoading() {
                loading.classList.remove('hidden');
                image.classList.add('opacity-0');
            }

            // Hide loading
            function hideLoading() {
                loading.classList.add('hidden');
                image.classList.remove('opacity-0');
            }

            // Open lightbox
            function openLightbox(index) {
                currentIndex = index;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                updateImage();
                updateNavButtons();
            }

            // Close lightbox
            function closeLightbox() {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }

            // Update image
            function updateImage() {
                if (images.length === 0) return;

                showLoading();

                const currentImage = images[currentIndex];

                // Preload image
                const img = new Image();
                img.onload = function() {
                    image.src = currentImage.src;
                    image.alt = currentImage.alt;
                    counter.textContent = `${currentIndex + 1} / ${images.length}`;
                    hideLoading();
                };
                img.src = currentImage.src;
            }

            // Update navigation buttons
            function updateNavButtons() {
                prevBtn.disabled = currentIndex === 0;
                nextBtn.disabled = currentIndex === images.length - 1;
            }

            // Navigate to previous image
            function prevImage() {
                if (currentIndex > 0) {
                    currentIndex--;
                    updateImage();
                    updateNavButtons();
                }
            }

            // Navigate to next image
            function nextImage() {
                if (currentIndex < images.length - 1) {
                    currentIndex++;
                    updateImage();
                    updateNavButtons();
                }
            }

            // Event listeners
            document.addEventListener('DOMContentLoaded', function() {
                collectImages();

                // Click on image triggers
                document.querySelectorAll('.lightbox-trigger').forEach((trigger, index) => {
                    trigger.addEventListener('click', function(e) {
                        e.preventDefault();
                        openLightbox(index);
                    });
                });

                // Close button
                closeBtn.addEventListener('click', closeLightbox);

                // Navigation buttons
                prevBtn.addEventListener('click', prevImage);
                nextBtn.addEventListener('click', nextImage);

                // Click outside image to close
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeLightbox();
                    }
                });

                // Keyboard navigation
                document.addEventListener('keydown', function(e) {
                    if (!modal.classList.contains('hidden')) {
                        switch(e.key) {
                            case 'Escape':
                                closeLightbox();
                                break;
                            case 'ArrowLeft':
                                prevImage();
                                break;
                            case 'ArrowRight':
                                nextImage();
                                break;
                        }
                    }
                });
            });
        })();
    </script>
@endsection

