# Jago POS Backend

Backend API untuk aplikasi Point of Sale (POS) yang dibangun dengan Laravel 12 dan Filament 4. Sistem ini mendukung multi-outlet, multi-tenant, dan terintegrasi dengan Midtrans payment gateway.

## Fitur Utama

- **Multi-Tenant Architecture** - Mendukung banyak bisnis dalam satu aplikasi
- **Multi-Outlet** - Satu bisnis dapat memiliki banyak outlet
- **Role-Based Access Control** - Owner, Manager, dan Cashier
- **Manajemen Produk & Kategori** - CRUD produk dengan dukungan gambar
- **Manajemen Stok** - Tracking stok per outlet dengan history
- **Order Management** - Membuat dan mengelola pesanan
- **Quick Service** - Mode dine-in dan take-away
- **Subscription System** - Sistem langganan dengan berbagai paket
- **Payment Gateway** - Integrasi Midtrans untuk pembayaran
- **Dashboard & Laporan** - Statistik penjualan dan laporan harian
- **Printer Support** - Dukungan printer kasir dan dapur
- **Admin Panel** - Panel admin menggunakan Filament

## Tech Stack

- PHP 8.2+
- Laravel 12
- Filament 4 (Admin Panel)
- Laravel Sanctum (API Authentication)
- Midtrans PHP SDK (Payment Gateway)
- MySQL / SQLite
- Tailwind CSS
- Vite

---

## Instalasi

### Requirements

| Software | Versi Minimum |
|----------|---------------|
| PHP | 8.2 |
| Composer | 2.x |
| Node.js | 18.x |
| npm | 9.x |
| MySQL | 8.0 (opsional, default SQLite) |

### PHP Extensions yang Dibutuhkan

- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- PDO SQLite / PDO MySQL
- Tokenizer
- XML

---

## Instalasi di Windows

### 1. Install Prerequisites

**Install PHP:**
- Download PHP 8.2+ dari https://windows.php.net/download/
- Extract ke `C:\php`
- Copy `php.ini-development` menjadi `php.ini`
- Edit `php.ini` dan aktifkan extensions yang dibutuhkan:
  ```ini
  extension=bcmath
  extension=curl
  extension=fileinfo
  extension=mbstring
  extension=openssl
  extension=pdo_sqlite
  extension=pdo_mysql
  ```
- Tambahkan `C:\php` ke System PATH

**Install Composer:**
- Download dan jalankan installer dari https://getcomposer.org/download/

**Install Node.js:**
- Download dan install dari https://nodejs.org/ (LTS version)

**Install MySQL (Opsional):**
- Download dan install dari https://dev.mysql.com/downloads/installer/

### 2. Clone dan Setup Project

```powershell
# Clone repository
git clone https://github.com/your-username/laravel_jago_pos_backend.git
cd laravel_jago_pos_backend

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file
copy .env.example .env

# Generate application key
php artisan key:generate

# Buat database SQLite (default)
New-Item -ItemType File -Path database\database.sqlite

# Jalankan migrasi
php artisan migrate

# (Opsional) Jalankan seeder
php artisan db:seed

# Build frontend assets
npm run build
```

### 3. Menjalankan Aplikasi

```powershell
# Development mode (semua service sekaligus)
composer run dev

# Atau jalankan terpisah:
php artisan serve
# Di terminal lain:
npm run dev
```

Akses aplikasi di: http://localhost:8000

---

## Instalasi di Linux (Ubuntu)

### 1. Install Prerequisites

```bash
# Update package list
sudo apt update

# Install PHP dan extensions
sudo apt install -y php8.2 php8.2-cli php8.2-common php8.2-mysql \
    php8.2-sqlite3 php8.2-xml php8.2-curl php8.2-mbstring \
    php8.2-bcmath php8.2-zip php8.2-gd php8.2-intl

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js (via NodeSource)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install MySQL (Opsional)
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

### 2. Clone dan Setup Project

```bash
# Clone repository
git clone https://github.com/your-username/laravel_jago_pos_backend.git
cd laravel_jago_pos_backend

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Buat database SQLite (default)
touch database/database.sqlite

# Jalankan migrasi
php artisan migrate

# (Opsional) Jalankan seeder
php artisan db:seed

# Build frontend assets
npm run build
```

### 3. Menjalankan Aplikasi

```bash
# Development mode (semua service sekaligus)
composer run dev

# Atau jalankan terpisah:
php artisan serve
# Di terminal lain:
npm run dev
```

Akses aplikasi di: http://localhost:8000

---

## Instalasi di macOS

### 1. Install Prerequisites

```bash
# Install Homebrew (jika belum ada)
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install PHP
brew install php@8.2

# Install Composer
brew install composer

# Install Node.js
brew install node

# Install MySQL (Opsional)
brew install mysql
brew services start mysql
```

### 2. Clone dan Setup Project

```bash
# Clone repository
git clone https://github.com/your-username/laravel_jago_pos_backend.git
cd laravel_jago_pos_backend

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Buat database SQLite (default)
touch database/database.sqlite

# Jalankan migrasi
php artisan migrate

# (Opsional) Jalankan seeder
php artisan db:seed

# Build frontend assets
npm run build
```

### 3. Menjalankan Aplikasi

```bash
# Development mode (semua service sekaligus)
composer run dev

# Atau jalankan terpisah:
php artisan serve
# Di terminal lain:
npm run dev
```

Akses aplikasi di: http://localhost:8000

---

## Konfigurasi Database

### Menggunakan SQLite (Default)

SQLite sudah dikonfigurasi sebagai default. Pastikan file `database/database.sqlite` sudah dibuat.

### Menggunakan MySQL

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jago_pos
DB_USERNAME=root
DB_PASSWORD=your_password
```

Buat database:
```sql
CREATE DATABASE jago_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Jalankan migrasi:
```bash
php artisan migrate
```

---

## Konfigurasi Midtrans (Payment Gateway)

Edit file `.env`:

```env
MIDTRANS_SERVER_KEY=your-server-key
MIDTRANS_CLIENT_KEY=your-client-key
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

Untuk mendapatkan API keys, daftar di https://midtrans.com/

---

## Menjalankan Tests

```bash
# Jalankan semua tests
php artisan test

# Jalankan test tertentu
php artisan test --filter=NamaTest
```

---

## API Documentation

API menggunakan Laravel Sanctum untuk autentikasi. Gunakan Bearer Token pada setiap request yang membutuhkan autentikasi.

### Endpoints Utama

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/api/register` | Registrasi user baru |
| POST | `/api/login` | Login user |
| POST | `/api/logout` | Logout user |
| GET | `/api/me` | Data user yang sedang login |
| GET | `/api/dashboard` | Dashboard statistik |
| GET | `/api/get-products` | Daftar produk |
| POST | `/api/add-order` | Buat pesanan baru |
| GET | `/api/get-orders` | Daftar pesanan |

Lihat `routes/api.php` untuk daftar lengkap endpoints.

---

## Admin Panel

Admin panel menggunakan Filament dan dapat diakses di:

```
http://localhost:8000/admin
```

---

## Struktur Folder

```
├── app/
│   ├── Filament/          # Admin Panel Resources
│   ├── Http/
│   │   ├── Controllers/   # API Controllers
│   │   └── Requests/      # Form Requests
│   └── Models/            # Eloquent Models
├── database/
│   ├── factories/         # Model Factories
│   ├── migrations/        # Database Migrations
│   └── seeders/           # Database Seeders
├── routes/
│   ├── api.php            # API Routes
│   └── web.php            # Web Routes
└── tests/                 # Test Files
```

---

## Troubleshooting

### Error: "Unable to locate file in Vite manifest"
```bash
npm run build
# atau untuk development:
npm run dev
```

### Error: Permission denied (Linux/macOS)
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache
```

### Error: Database connection refused
Pastikan MySQL/SQLite sudah berjalan dan konfigurasi `.env` sudah benar.

---

## License

Project ini dilisensikan di bawah [MIT license](https://opensource.org/licenses/MIT).
