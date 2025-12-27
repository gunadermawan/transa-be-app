@extends('layouts.landing')

@section('content')
    <!-- Progress Bar -->
    <div class="bg-gray-50 border-b border-gray-200">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-primary-600">Langkah 2 dari 2</span>
                <span class="text-sm text-gray-500">Data Bisnis</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-primary-600 h-2 rounded-full" style="width: 100%"></div>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Informasi Bisnis Anda
                </h1>
                <p class="text-gray-600">
                    Kami akan membuat outlet pertama Anda secara otomatis.
                </p>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                @if (session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register.step2.process') }}">
                    @csrf

                    <!-- Nama Bisnis -->
                    <div class="mb-6">
                        <label for="business_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Bisnis <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="business_name" name="business_name" value="{{ old('business_name') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('business_name') border-red-500 @enderror"
                            placeholder="Contoh: Toko Kelontong Maju Jaya" required autofocus>
                        @error('business_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Nama ini akan muncul di struk dan invoice</p>
                    </div>

                    <!-- Nama Outlet (Optional) -->
                    <div class="mb-6">
                        <label for="outlet_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Outlet <span class="text-gray-400 text-xs">(Opsional)</span>
                        </label>
                        <input type="text" id="outlet_name" name="outlet_name" value="{{ old('outlet_name') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('outlet_name') border-red-500 @enderror"
                            placeholder="Contoh: Cabang Pusat atau biarkan kosong">
                        @error('outlet_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Kosongkan untuk menggunakan "[Nama Bisnis] - Pusat"</p>
                    </div>

                    <!-- Alamat Bisnis -->
                    <div class="mb-6">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Alamat Bisnis <span class="text-red-500">*</span>
                        </label>
                        <textarea id="address" name="address" rows="3" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('address') border-red-500 @enderror"
                            placeholder="Jl. Contoh No. 123, Kecamatan, Kota">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Telepon Bisnis -->
                    <div class="mb-6">
                        <label for="business_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Telepon Bisnis <span class="text-gray-400 text-xs">(Opsional)</span>
                        </label>
                        <input type="tel" id="business_phone" name="business_phone" value="{{ old('business_phone') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('business_phone') border-red-500 @enderror"
                            placeholder="0271-123456">
                        @error('business_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Kosongkan untuk menggunakan nomor telepon pribadi Anda</p>
                    </div>

                    <!-- Email Bisnis -->
                    <div class="mb-8">
                        <label for="business_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Bisnis <span class="text-gray-400 text-xs">(Opsional)</span>
                        </label>
                        <input type="email" id="business_email" name="business_email" value="{{ old('business_email') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('business_email') border-red-500 @enderror"
                            placeholder="info@bisnis.com">
                        @error('business_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Kosongkan untuk menggunakan email akun Anda</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-4">
                        <a href="{{ route('register.step1') }}"
                            class="flex-1 bg-white hover:bg-gray-50 text-gray-700 font-semibold py-3 px-6 rounded-lg border border-gray-300 text-center transition duration-150">
                            ← Kembali
                        </a>
                        <button type="submit"
                            class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-150">
                            Mulai Trial Gratis
                        </button>
                    </div>
                </form>
            </div>

            <!-- What Happens Next -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Yang Akan Terjadi Selanjutnya:</h3>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start">
                        <span class="text-primary-600 mr-2">✓</span>
                        <span>Akun bisnis dan outlet pertama Anda akan dibuat otomatis</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-primary-600 mr-2">✓</span>
                        <span>Trial 14 hari dimulai (tidak perlu kartu kredit)</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-primary-600 mr-2">✓</span>
                        <span>Anda langsung masuk ke dashboard dan bisa mulai transaksi</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-primary-600 mr-2">✓</span>
                        <span>Semua fitur POS tersedia untuk Anda coba</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
