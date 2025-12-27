@extends('layouts.landing')

@section('content')
    <!-- Progress Bar -->
    <div class="bg-gray-50 border-b border-gray-200">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-primary-600">Langkah 1 dari 2</span>
                <span class="text-sm text-gray-500">Data Akun</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-primary-600 h-2 rounded-full" style="width: 50%"></div>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Mulai Trial Gratis 14 Hari
                </h1>
                <p class="text-gray-600">
                    Buat akun Anda terlebih dahulu. Tidak perlu kartu kredit.
                </p>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                @if (session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register.step1.process') }}">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                            value="{{ old('name', session('registration.step1.name')) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror"
                            placeholder="Masukkan nama lengkap Anda" required autofocus>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email"
                            value="{{ old('email', session('registration.step1.email')) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-500 @enderror"
                            placeholder="nama@perusahaan.com" required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="mb-6">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Telepon <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="phone" name="phone"
                            value="{{ old('phone', session('registration.step1.phone')) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('phone') border-red-500 @enderror"
                            placeholder="08123456789" required>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="password" name="password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('password') border-red-500 @enderror"
                            placeholder="Minimal 8 karakter" required>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="mb-8">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Ketik ulang password Anda" required>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-150">
                        Lanjut ke Data Bisnis →
                    </button>
                </form>

                <!-- Login Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Sudah punya akun?
                        <a href="{{ route('filament.admin.auth.login') }}"
                            class="text-primary-600 hover:text-primary-700 font-medium">
                            Masuk di sini
                        </a>
                    </p>
                </div>
            </div>

            <!-- Trust Badge -->
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-500">
                    Data Anda aman • Tidak perlu kartu kredit • Setup 5 menit
                </p>
            </div>
        </div>
    </div>
@endsection
