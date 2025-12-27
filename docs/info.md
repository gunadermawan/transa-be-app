PHASE 2 – POS BACKEND & FILAMENT ADMIN (Sesi 9–17)

Sesi 9 – Filament Setup & Resource Dasar
Install & setup Filament 4
Resource Product (form & table)
Search dan filter dasar di admin panel

Sesi 10 – Relasi, Upload & Basic Permission
Relations manager (Product–Category, User–Outlet)
Upload gambar produk
Basic policy/permission (hanya admin bisa CRUD master data)

Sesi 11 – Inventory Model
Entitas Stock & StockMovement (IN/OUT)
Konsep stock on hand & stock minimum
Migration & model inventory

Sesi 12 – Transaction Logic
Model Transaction & TransactionItem
Diskon, pajak, total, change
Payment methods

Sesi 13 – Reporting & Export
Laporan penjualan harian/bulanan
Laporan inventory (stock in/out)
Profit basic (omzet – COGS sederhana)
Export ke Excel/PDF

Sesi 14 – Filament Dashboard & Analytics
Dashboard manajer: total sales, today sales
Chart penjualan per periode
Top products + filter outlet/tanggal

Sesi 15 – Multi-Outlet & Role
Relasi user–outlet
Role: owner, admin, cashier
Policy/permission untuk batasi data per outlet

Sesi 16 – SaaS / Multi-Tenant Lite (Single DB)
Model Tenant (company)
tenant_id di setiap record
Middleware identifikasi tenant
Konsep isolasi data per tenant

Sesi 17 – Refactor & Backend Performance
N+1 problem & solusi (with/withCount)
Indexing database
Logging basic (daily log)
Rapihin struktur folder & service layer

PHASE 3 – FLUTTER POS APP, OFFLINE & DEPLOYMENT (Sesi 18–25)

Sesi 18 – POS UI Responsive (Phone & Tablet)
Product grid view + filter kategori
Pencarian produk
Layout responsive untuk phone & tablet (split view)

Sesi 19 – Cart & Checkout Flow
Add/update/remove item di cart
Hitung subtotal, diskon, pajak
Notes & special request

Sesi 20 – Payment & Receipt
Pilih metode pembayaran
Hitung kembalian
Simpan transaksi ke backend
Desain struk kasir (header, items, footer)

Sesi 21 – Printer Integration
Konsep ESC/POS
Koneksi ke Bluetooth printer
Print text, logo, QR (opsional)
Testing ke printer nyata

Sesi 22 – Offline Mode & Sync
Setup sqflite
Simpan transaksi ketika offline
Queue sync ketika online kembali
Hindari duplikasi data saat sync

Sesi 23 – Performance & Monitoring App
Lazy loading list & optimasi UI
Image caching
Shrink & obfuscate APK
Crash reporting basic

Sesi 24 – Subscription & License
Desain plan (Basic, Pro, dst.)
Status subscription
API lisensi (cek aktif/tidak)
Validasi lisensi di app (cache + handling expired)

Sesi 25 – Server Deployment
Setup VPS (Nginx + PHP-FPM)
Deploy Laravel + Filament
Domain & SSL

PHASE 4 – AFTER RELEASE EVALUATION (Sesi 26-27)

Sesi 26 – Google Play Release (Internal/Closed Testing)
Build release app
Upload ke Google Play (internal/closed testing)
Setup listing dasar (nama, icon, deskripsi singkat, privacy policy URL)
Strategi roll-out ke tester pertama (peserta & beberapa klien)

Sesi 27 – After-Release Evaluation & Next Step
Evaluasi arsitektur backend & UX POS
Review error/bug awal dari testing
Diskusi strategi monetisasi (lisensi, subscription, jasa custom)
Cara masukkan project ke portfolio / CV / LinkedIn
Next step: maintenance, fitur lanjutan, dan scaling SaaS
