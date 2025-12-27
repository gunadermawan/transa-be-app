# JagoFlutter Academy - POS SaaS Todolist
## 🎯 Project Overview
**Full-stack POS SaaS Application** - Laravel Backend + Filament Admin + Flutter Mobile App
Course: Dari development hingga production release di Google Play

---

## 📊 Current Project Status

### ✅ Yang Sudah Ada (Backend Foundation)
- ✅ Laravel 12 + Sanctum authentication
- ✅ Core Models: Business, Outlet, User, Role, Category, Product, Stock, StockHistory, Order, OrderItem, OrderTax, OrderDiscount, Printer, BusinessSetting, SalesSummary, SalesTransaction
- ✅ Database migrations lengkap
- ✅ API endpoints dasar untuk CRUD operations
- ✅ Relasi database terdefinisi
- ✅ Basic API: Auth, Products, Categories, Orders, Stocks, Outlets, Staff, Printers, Business Settings, Sales Report

### ❌ Yang Belum Ada
**Backend:**
- ❌ Filament Admin Panel (belum terinstall)
- ❌ Policies & Permissions system
- ❌ Complete Factories & Seeders
- ❌ Feature Tests & Unit Tests
- ❌ Advanced Reporting & Export (Excel/PDF)
- ❌ Dashboard & Analytics dengan Charts
- ❌ Multi-tenant global scopes
- ❌ Performance optimization (N+1, indexing)
- ❌ Service layer architecture
- ❌ Production-ready logging

**Flutter App:**
- ❌ Flutter project structure
- ❌ POS UI (phone & tablet responsive)
- ❌ Cart & checkout flow
- ❌ Payment & receipt
- ❌ Printer integration (Bluetooth ESC/POS)
- ❌ Offline mode & sync (sqflite)
- ❌ Performance optimization
- ❌ Subscription & license validation
- ❌ Release build & obfuscation

**DevOps:**
- ❌ VPS deployment setup
- ❌ CI/CD pipeline
- ❌ Production environment configuration
- ❌ Google Play release preparation

---

# 🚀 PHASE 2: Backend & Filament Admin (Sesi 9-17)

> **💡 Design Decision: Product Types & Stock Management**
>
> Sistem akan support 2 tipe produk:
> - **Stock-managed products** (is_stock_managed = true): Physical goods yang di-track stocknya (snacks, bottled drinks, retail items)
> - **Non-stock products** (is_stock_managed = false): Made-to-order items & services (es teh, bakso, kopi, jasa)
>
> Benefit: Flexibility untuk retail + F&B tanpa kompleksitas recipe/ingredient management (yang jadi future feature).

## **Sesi 9 – Filament Setup & Resource Dasar**

### 9.1 Install & Setup Filament 4
- [ ] Install Filament 4: `composer require filament/filament:"^4.0"`
- [ ] Run installation: `php artisan filament:install --panels`
- [ ] Create admin panel configuration
- [ ] Setup color scheme & branding
- [ ] Create super admin user via seeder
- [ ] Configure Filament navigation groups
- [ ] Test admin panel access di browser
- [ ] Configure sidebar navigation

### 9.2 ProductResource
- [ ] Generate: `php artisan make:filament-resource Product`
- [ ] Form builder:
  - [ ] name, description, price, cost_price
  - [ ] category, image, barcode, SKU
  - [ ] **is_stock_managed** toggle (or product_type select)
  - [ ] stock_minimum (only show if is_stock_managed = true)
- [ ] Table columns: image preview, name, category, price, stock, status
- [ ] Add badge/indicator for product type (stock-managed vs non-stock)
- [ ] Implement search: name, SKU, barcode
- [ ] Add filters: category, stock status (in stock, low stock, out of stock), product type
- [ ] Bulk actions: delete, change category, activate/deactivate
- [ ] Add custom actions: duplicate product
- [ ] Validation rules di form

### 9.3 CategoryResource
- [ ] Generate CategoryResource
- [ ] Form: name, description, icon/image
- [ ] Table: name, products count, created_at
- [ ] Search & filter active/inactive
- [ ] Show products relation manager

---

## **Sesi 10 – Relasi, Upload & Basic Permission**

### 10.1 Relations Manager
- [ ] ProductResource: CategoryRelationManager
- [ ] UserResource: OutletRelationManager
- [ ] BusinessResource: OutletsRelationManager
- [ ] OrderResource: OrderItemsRelationManager
- [ ] Configure editable/read-only relations
- [ ] Test create/edit via relation manager

### 10.2 Upload Gambar Produk
- [ ] Configure storage disk: `config/filesystems.php` (public disk)
- [ ] Run: `php artisan storage:link`
- [ ] Add FileUpload field ke ProductResource form
- [ ] Configure image validation (max 2MB, jpg/png)
- [ ] Image optimization: install intervention/image
- [ ] Resize uploaded images (800x800, 200x200 thumbnail)
- [ ] Add image preview di table column dengan ImageColumn
- [ ] Handle update image (replace old file)
- [ ] Handle delete image when product deleted
- [ ] Test upload/edit/delete images

### 10.3 Basic Policy & Permission
- [ ] Generate policies: `php artisan make:policy ProductPolicy --model=Product`
- [ ] ProductPolicy: viewAny, view, create, update, delete, restore, forceDelete
- [ ] CategoryPolicy: same as ProductPolicy
- [ ] OutletPolicy: restrict access by business
- [ ] StockPolicy: only admin & manager can edit
- [ ] OrderPolicy: cashier can create, admin can delete
- [ ] Register policies di AppServiceProvider atau bootstrap/app.php
- [ ] Implement di Filament: `->authorize('viewAny', Product::class)`
- [ ] Test: admin full access, cashier limited access
- [ ] Create RoleSeeder: Owner, Admin, Manager, Cashier

---

## **Sesi 11 – Inventory Model Enhancement**

### 11.1 Product Types & Stock Management Strategy
- [ ] Understand product types:
  - [ ] **Physical Products**: Stock-tracked (snack, bottled drinks, goods)
  - [ ] **Made-to-Order**: Non-stock (es teh, bakso, kopi, services)
  - [ ] Note: Recipe/ingredient tracking = future advanced module
- [ ] Add migration: `is_stock_managed` boolean field to products table (default: true)
- [ ] Or use enum: `product_type` (physical, service, made_to_order)
- [ ] Update ProductResource form: add toggle/select for stock management
- [ ] Update Product model: add `is_stock_managed` to fillable

### 11.2 Stock Management Logic
- [ ] Review Stock model & StockHistory model
- [ ] Add stock movement types enum: IN, OUT, ADJUSTMENT, TRANSFER
- [ ] Create StockMovementService class in `app/Services/`
- [ ] Implement addStock() method (IN)
- [ ] Implement reduceStock() method (OUT):
  - [ ] Check if product `is_stock_managed = true`
  - [ ] Only reduce stock for stock-managed products
  - [ ] Skip stock reduction for services/made-to-order
- [ ] Implement adjustStock() method (ADJUSTMENT)
- [ ] Add stock_minimum field to products table (migration)
- [ ] Create low stock alert logic (only for stock-managed products)
- [ ] Add observer/event untuk auto create StockHistory
- [ ] Test stock movements with different product types

### 11.3 Stock Resource & Reporting
- [ ] Generate StockResource
- [ ] Table: product, outlet, quantity, stock_minimum, status, is_stock_managed
- [ ] Filters: outlet, low stock alert, stock-managed only
- [ ] Generate StockHistoryResource
- [ ] Table: product, type (IN/OUT/ADJ), quantity, before/after, user, created_at
- [ ] Filters: type, date range, product, outlet
- [ ] Export stock movements to Excel
- [ ] Widget: Low Stock Alert count (only stock-managed products)
- [ ] Create artisan command: `php artisan stock:check-low` (daily cron)
- [ ] Display badge/indicator di ProductResource untuk non-stock products

---

## **Sesi 12 – Transaction Logic Enhancement**

### 12.1 Order Calculation & Stock Integration
- [ ] Review Order & OrderItem models
- [ ] Create OrderService class in `app/Services/`
- [ ] Implement createOrder() method:
  - [ ] Create order record
  - [ ] Create order items
  - [ ] **Conditional stock reduction**:
    - [ ] Loop through order items
    - [ ] Check if product `is_stock_managed = true`
    - [ ] Only call `StockService->reduceStock()` for stock-managed products
    - [ ] Skip stock reduction for services/made-to-order items
  - [ ] Handle insufficient stock error (for stock-managed products only)
- [ ] Implement discount calculation:
  - [ ] Discount per item (percentage/amount)
  - [ ] Discount total order (percentage/amount)
  - [ ] Store in order_discounts table
- [ ] Implement tax calculation:
  - [ ] Tax inclusive vs exclusive
  - [ ] Tax percentage from settings
  - [ ] Store in order_taxes table
- [ ] Calculate grand total: subtotal - discount + tax
- [ ] Payment methods: Cash, Card, QRIS, Transfer
- [ ] Change/return money calculation
- [ ] Create transaction unit tests (test with both product types)

### 12.2 OrderResource & Actions
- [ ] Generate OrderResource
- [ ] Form wizard: Customer Info → Items → Payment → Review
- [ ] Table columns: order_number, outlet, total, payment_method, cashier, status, date
- [ ] Filters: status, payment method, outlet, date range, cashier
- [ ] View page: show order details, items table, payment info
- [ ] Custom actions:
  - [ ] Void/Cancel transaction (admin only)
  - [ ] Print receipt
  - [ ] Refund (future)
- [ ] Order statuses: pending, completed, void, refunded
- [ ] Test create order via Filament
- [ ] Validate stock availability before order

---

## **Sesi 13 – Reporting & Export**

### 13.1 Install Export Libraries
- [ ] Install: `composer require maatwebsite/laravel-excel`
- [ ] Install: `composer require barryvdh/laravel-dompdf`
- [ ] Configure export settings
- [ ] Create Exports directory: `app/Exports/`

### 13.2 Sales Report
- [ ] Create SalesReportResource (custom page)
- [ ] Daily sales report:
  - [ ] Filter: date, outlet, cashier
  - [ ] Show: total sales, transaction count, payment method breakdown
  - [ ] Export to Excel & PDF
- [ ] Monthly sales report:
  - [ ] Group by date
  - [ ] Chart visualization
  - [ ] Export options
- [ ] Sales by product report:
  - [ ] Best selling products
  - [ ] Quantity sold, revenue
  - [ ] Filter by date range, category
- [ ] Sales by category report
- [ ] Cashier performance report

### 13.3 Inventory Report
- [ ] Stock IN/OUT report:
  - [ ] Filter: date range, product, outlet, type
  - [ ] Export to Excel
- [ ] Current stock report (all products):
  - [ ] Show: product, outlet, current stock, stock_minimum
  - [ ] Highlight low stock
- [ ] Low stock report (dedicated)
- [ ] Stock valuation report:
  - [ ] Calculate: quantity × cost price
  - [ ] Total inventory value

### 13.4 Profit Report (Basic)
- [ ] Add cost_price field to products table (migration)
- [ ] Add COGS (Cost of Goods Sold) calculation
- [ ] Profit formula: (selling_price - cost_price) × quantity
- [ ] Profit by product report
- [ ] Profit by period report (daily, monthly)
- [ ] Profit margin calculation
- [ ] Export profit reports

### 13.5 Export Implementation
- [ ] Create Excel exports for all reports
- [ ] Create PDF exports for:
  - [ ] Sales summary
  - [ ] Receipts
  - [ ] Stock reports
- [ ] Download & email options
- [ ] Schedule automatic reports (artisan command + cron)

---

## **Sesi 14 – Filament Dashboard & Analytics**

### 14.1 Dashboard Widgets
- [ ] Create widgets directory
- [ ] Today's Sales Widget (StatsOverviewWidget):
  - [ ] Total amount today
  - [ ] Transaction count today
  - [ ] Trend vs yesterday
- [ ] This Month Sales Widget:
  - [ ] Total amount this month
  - [ ] Average per day
  - [ ] Trend vs last month
- [ ] Total Transactions Widget
- [ ] Active Outlets Widget
- [ ] Low Stock Products Widget (table widget)
- [ ] Recent Orders Widget (table widget, last 10)

### 14.2 Chart Widgets
- [ ] Install Filament Charts or use ChartWidget
- [ ] Sales Trend Chart (LineChartWidget):
  - [ ] Last 7 days
  - [ ] Last 30 days
  - [ ] Last 12 months (switch)
- [ ] Sales by Outlet Chart (BarChart or PieChart)
- [ ] Top 10 Products Chart (BarChart)
- [ ] Sales by Category Chart (DoughnutChart)
- [ ] Payment Method Distribution (PieChart)
- [ ] Sales vs Cost Chart (comparison)

### 14.3 Dashboard Filters
- [ ] Add date range filter to dashboard
- [ ] Outlet filter (dropdown)
- [ ] Refresh widgets on filter change
- [ ] Export dashboard data to PDF/Excel

### 14.4 Real-time Updates (Optional)
- [ ] Setup Laravel Echo + Pusher
- [ ] Real-time widget updates
- [ ] Live order notifications

---

## **Sesi 15 – Multi-Outlet & Role Management**

### 15.1 User-Outlet Relations
- [ ] Review users-outlets relationship (many-to-many via pivot)
- [ ] UserResource: add Outlet relation manager
- [ ] Allow assign multiple outlets to user
- [ ] Default outlet selection for users
- [ ] Create middleware: EnsureUserHasOutlet
- [ ] Outlet switcher di Filament navigation (jika user punya multiple outlets)

### 15.2 Role & Permission System
- [ ] Create RoleResource di Filament
- [ ] Define roles:
  - [ ] **Owner**: Full access to all businesses & outlets
  - [ ] **Admin**: Full access within business
  - [ ] **Manager**: Can view reports, manage products, view orders
  - [ ] **Cashier**: Can only create orders, view own transactions
- [ ] Create permission matrix table:
  - [ ] products: view, create, edit, delete
  - [ ] categories: view, create, edit, delete
  - [ ] orders: view, create, void (admin only)
  - [ ] reports: view, export
  - [ ] users: manage (admin only)
  - [ ] outlets: manage (owner/admin)
  - [ ] settings: manage (owner/admin)
- [ ] Implement Spatie Permission (or manual role check)
- [ ] Seed roles & permissions
- [ ] Test role-based access

### 15.3 Data Isolation per Outlet
- [ ] Create global scope: OutletScope
- [ ] Apply OutletScope to:
  - [ ] Products
  - [ ] Orders
  - [ ] Stock
  - [ ] StockHistory
- [ ] Cashier hanya lihat data outlet mereka
- [ ] Manager & Admin lihat semua outlets dalam business
- [ ] Owner lihat semua (multi-business if super admin)
- [ ] Test isolation:
  - [ ] User A (Outlet 1) tidak bisa lihat data User B (Outlet 2)
  - [ ] Admin bisa lihat semua outlets

---

## **Sesi 16 – SaaS / Multi-Tenant Implementation**

### 16.1 Tenant Model & Setup
- [ ] Review Business model sebagai Tenant
- [ ] Ensure business_id exists di semua tabel:
  - [ ] users, outlets, categories, products, orders, stocks, stock_histories
- [ ] Create TenantScope global scope
- [ ] Apply TenantScope ke semua models kecuali Business

### 16.2 Tenant Identification Middleware
- [ ] Create middleware: IdentifyTenant
- [ ] Tenant identification strategies:
  - [ ] By subdomain: tenant1.posapp.com
  - [ ] By auth user's business_id (default)
  - [ ] By header: X-Tenant-ID (API)
- [ ] Set current tenant in session/cache
- [ ] Auto-apply tenant filter to all queries

### 16.3 Tenant Isolation & Testing
- [ ] Test: Tenant A cannot access Tenant B data
- [ ] Test: API dengan berbeda business_id
- [ ] Test: Admin panel dengan berbeda tenant
- [ ] Seeder for multi-tenant:
  - [ ] Create 3 businesses
  - [ ] Each with 2-3 outlets
  - [ ] Each with users, products, orders
- [ ] Prevent cross-tenant data leaks:
  - [ ] In controllers
  - [ ] In Filament resources
  - [ ] In API responses

### 16.4 Tenant Management (Super Admin)
- [ ] Create super admin role
- [ ] BusinessResource (super admin only):
  - [ ] CRUD businesses
  - [ ] View: name, owner, outlets count, users count, status
  - [ ] Actions: activate, suspend, delete
- [ ] Business status: trial, active, suspended, expired
- [ ] Business subscription info (placeholder for future billing)
- [ ] Tenant registration flow via API

### 16.5 Subdomain Routing (Optional)
- [ ] Configure subdomain routing in routes/web.php
- [ ] Tenant identification by subdomain
- [ ] Redirect to correct tenant panel
- [ ] Wildcard SSL setup (*.posapp.com)

---

## **Sesi 17 – Refactor & Backend Performance**

### 17.1 N+1 Query Prevention
- [ ] Install Laravel Debugbar: `composer require barryvdh/laravel-debugbar --dev`
- [ ] Or Laravel Telescope: `composer require laravel/telescope --dev`
- [ ] Audit all controllers & resources
- [ ] Add eager loading:
  - [ ] Orders: `with(['orderItems.product', 'outlet', 'user'])`
  - [ ] Products: `with(['category'])`
  - [ ] StockHistories: `with(['product', 'user'])`
- [ ] Use `withCount()` where needed:
  - [ ] `Category::withCount('products')`
  - [ ] `Outlet::withCount('orders')`
- [ ] Test query counts before/after
- [ ] Document in code comments

### 17.2 Database Indexing
- [ ] Create indexing migration
- [ ] Add indexes to foreign keys:
  - [ ] business_id, outlet_id, user_id, category_id, product_id
- [ ] Add indexes to frequently queried columns:
  - [ ] orders.created_at
  - [ ] products.name
  - [ ] orders.order_number
- [ ] Add composite indexes:
  - [ ] (business_id, outlet_id)
  - [ ] (business_id, created_at)
- [ ] Run: `php artisan migrate`
- [ ] Test query performance (EXPLAIN ANALYZE)

### 17.3 Code Organization & Service Layer
- [ ] Create Services directory: `app/Services/`
- [ ] Extract logic ke services:
  - [ ] OrderService: createOrder(), calculateTotal(), voidOrder()
  - [ ] StockService: addStock(), reduceStock(), adjustStock()
  - [ ] ReportService: generateSalesReport(), generateStockReport()
  - [ ] SubscriptionService: checkStatus(), validateLicense()
- [ ] Clean up controllers (thin controllers, fat services)
- [ ] Implement Repository pattern (optional):
  - [ ] OrderRepository
  - [ ] ProductRepository
- [ ] Organize routes by module:
  - [ ] routes/api/auth.php
  - [ ] routes/api/products.php
  - [ ] routes/api/orders.php
- [ ] Update route files registration

### 17.4 Logging & Monitoring
- [ ] Configure logging channels in `config/logging.php`:
  - [ ] daily: for general logs
  - [ ] slack: for critical errors (production)
- [ ] Log critical operations:
  - [ ] Order creation/void
  - [ ] Stock changes
  - [ ] Payment transactions
  - [ ] User login/logout
  - [ ] Subscription status changes
- [ ] Create custom log facade or helper
- [ ] Implement error tracking:
  - [ ] Sentry (recommended): `composer require sentry/sentry-laravel`
  - [ ] Or Bugsnag, Rollbar
- [ ] Setup monitoring:
  - [ ] Laravel Horizon (for queues)
  - [ ] Laravel Pulse (for performance)
- [ ] Create health check endpoint: `/api/health`

---

# 📱 PHASE 3: Flutter POS App, Offline & Deployment (Sesi 18-25)

## **Sesi 18 – POS UI Responsive (Phone & Tablet)**

### 18.1 Flutter Project Setup
- [ ] Create Flutter project: `flutter create pos_app`
- [ ] Setup project structure:
  - [ ] lib/core/ (constants, theme, utils)
  - [ ] lib/data/ (models, repositories, providers)
  - [ ] lib/presentation/ (screens, widgets)
- [ ] Install dependencies:
  - [ ] http / dio (API calls)
  - [ ] provider / riverpod (state management)
  - [ ] shared_preferences (local storage)
  - [ ] sqflite (offline database)
  - [ ] flutter_bloc (optional)
- [ ] Configure API base URL
- [ ] Setup theme & colors
- [ ] Create constants (API endpoints, colors, sizes)

### 18.2 Authentication Flow
- [ ] Create LoginScreen
- [ ] Implement login API integration
- [ ] Store auth token (Sanctum)
- [ ] Create AuthProvider/AuthRepository
- [ ] Auto-login if token exists
- [ ] Logout functionality
- [ ] Token refresh logic (optional)

### 18.3 Product Grid UI
- [ ] Create ProductListScreen
- [ ] Fetch products from API
- [ ] GridView for products:
  - [ ] Product image
  - [ ] Product name
  - [ ] Price
  - [ ] Stock indicator
- [ ] Responsive layout:
  - [ ] Phone: 2 columns
  - [ ] Tablet: 3-4 columns
- [ ] Pull-to-refresh
- [ ] Loading state
- [ ] Empty state
- [ ] Error handling

### 18.4 Category Filter & Search
- [ ] Category chips/tabs di atas grid
- [ ] Filter products by category (client-side)
- [ ] Search bar:
  - [ ] Search by name
  - [ ] Search by SKU/barcode
  - [ ] Debouncing search input
- [ ] Clear filter button
- [ ] Show active filters

### 18.5 Tablet Split View
- [ ] Detect tablet vs phone
- [ ] Tablet layout:
  - [ ] Left: Product grid (60%)
  - [ ] Right: Cart (40%)
- [ ] Phone layout:
  - [ ] Product screen
  - [ ] Cart as bottom sheet atau separate screen
- [ ] Test on different screen sizes

---

## **Sesi 19 – Cart & Checkout Flow**

### 19.1 Cart State Management
- [ ] Create CartProvider/CartBloc
- [ ] Cart model: CartItem(product, quantity, notes)
- [ ] Add to cart functionality
- [ ] Update quantity (+/-)
- [ ] Remove from cart
- [ ] Clear cart
- [ ] Cart persistence (local storage)

### 19.2 Cart UI
- [ ] Create CartWidget/CartScreen
- [ ] List cart items:
  - [ ] Product name
  - [ ] Price × Quantity
  - [ ] Subtotal
  - [ ] Increment/decrement buttons
  - [ ] Remove button
- [ ] Empty cart state
- [ ] Cart badge (item count)

### 19.3 Calculation Logic
- [ ] Calculate subtotal: sum(item.price × item.quantity)
- [ ] Apply discount:
  - [ ] Input discount percentage atau amount
  - [ ] Calculate discount value
- [ ] Calculate tax:
  - [ ] Get tax rate from settings atau hardcode
  - [ ] Tax inclusive vs exclusive toggle
- [ ] Calculate grand total: subtotal - discount + tax
- [ ] Display calculation breakdown
- [ ] Unit test calculation logic

### 19.4 Notes & Special Request
- [ ] Add notes to individual cart items
- [ ] Add notes to entire order
- [ ] Notes input field (text area)
- [ ] Display notes di cart & receipt

### 19.5 Checkout Button
- [ ] Checkout button (always visible)
- [ ] Disabled if cart empty
- [ ] Navigate to PaymentScreen
- [ ] Validate stock availability before checkout

---

## **Sesi 20 – Payment & Receipt**

### 20.1 Payment Screen
- [ ] Create PaymentScreen
- [ ] Summary section:
  - [ ] Subtotal
  - [ ] Discount
  - [ ] Tax
  - [ ] **Grand Total** (prominent)
- [ ] Payment method selection:
  - [ ] Cash
  - [ ] Card
  - [ ] QRIS
  - [ ] Transfer
- [ ] Payment method icons/buttons

### 20.2 Cash Payment & Change Calculation
- [ ] If Cash selected:
  - [ ] Input amount received
  - [ ] Calculate change: received - grand_total
  - [ ] Display change amount (large, prominent)
  - [ ] Shortcut buttons: exact amount, 50k, 100k, 200k
- [ ] Validation: received >= grand_total
- [ ] Show error if insufficient

### 20.3 Save Transaction to Backend
- [ ] Create OrderRepository
- [ ] Build order payload:
  - [ ] outlet_id
  - [ ] items: [{product_id, quantity, price, notes}]
  - [ ] subtotal, discount, tax, grand_total
  - [ ] payment_method, amount_received, change
  - [ ] notes
- [ ] POST to `/api/add-order`
- [ ] Handle success response
- [ ] Handle error response
- [ ] Loading state during submission

### 20.4 Receipt Design
- [ ] Create ReceiptScreen/ReceiptWidget
- [ ] Receipt sections:
  - [ ] **Header**: business name, outlet name, address, phone
  - [ ] **Transaction info**: order number, date/time, cashier name
  - [ ] **Items table**: name, qty, price, subtotal
  - [ ] **Totals**: subtotal, discount, tax, grand total
  - [ ] **Payment**: method, received, change
  - [ ] **Footer**: thank you message, social media
- [ ] Receipt styling (monospace font, black & white)
- [ ] Receipt preview before print

### 20.5 Post-Transaction Flow
- [ ] Show success dialog/screen
- [ ] Option to print receipt
- [ ] Option to share receipt (image/PDF)
- [ ] Clear cart after successful transaction
- [ ] Navigate back to product screen

---

## **Sesi 21 – Printer Integration**

### 21.1 ESC/POS Konsep
- [ ] Research ESC/POS command protocol
- [ ] Understand printer capabilities:
  - [ ] Text formatting (bold, align, size)
  - [ ] Image printing (logo)
  - [ ] Barcode & QR code
  - [ ] Cut paper
- [ ] Common ESC/POS commands cheatsheet

### 21.2 Bluetooth Printer Connection
- [ ] Install package: `flutter_bluetooth_serial` atau `blue_thermal_printer`
- [ ] Permission setup (Android):
  - [ ] BLUETOOTH, BLUETOOTH_ADMIN, BLUETOOTH_SCAN, BLUETOOTH_CONNECT
  - [ ] Location permission (required for BT scan)
- [ ] Create PrinterService class
- [ ] Scan for Bluetooth devices
- [ ] Connect to selected printer
- [ ] Save connected printer to local storage (auto-reconnect)
- [ ] Handle connection errors

### 21.3 Print Receipt
- [ ] Format receipt dengan ESC/POS commands:
  - [ ] Business name (large, bold, center)
  - [ ] Outlet info (center)
  - [ ] Separator line
  - [ ] Items (left align name, right align price)
  - [ ] Subtotal, discount, tax, total
  - [ ] Payment info
  - [ ] Footer (center)
  - [ ] QR code (optional)
  - [ ] Cut paper command
- [ ] Send commands to printer
- [ ] Handle print success/error
- [ ] Loading indicator during print

### 21.4 Logo & QR Code (Optional)
- [ ] Convert logo image to ESC/POS bitmap
- [ ] Print logo at receipt header
- [ ] Generate QR code (order number atau URL)
- [ ] Print QR code di footer

### 21.5 Testing dengan Printer Nyata
- [ ] Test dengan thermal printer 58mm atau 80mm
- [ ] Test connection stability
- [ ] Test berbagai receipt formats
- [ ] Handle edge cases (printer off, paper out, etc.)
- [ ] Create test receipt button

---

## **Sesi 22 – Offline Mode & Sync**

### 22.1 Setup sqflite
- [ ] Install: `sqflite`, `path_provider`
- [ ] Create DatabaseHelper class
- [ ] Database schema:
  - [ ] products table (cache from API)
  - [ ] categories table
  - [ ] pending_orders table (offline transactions)
  - [ ] sync_queue table (for sync tracking)
- [ ] Database version & migration logic
- [ ] Initialize database on app start

### 22.2 Offline Product Caching
- [ ] Fetch products from API
- [ ] Save to local database
- [ ] Display products from local DB if offline
- [ ] Update cache when online
- [ ] Cache expiration logic (re-fetch after X hours)

### 22.3 Save Offline Transactions
- [ ] Check internet connectivity
- [ ] If offline:
  - [ ] Save order to `pending_orders` table
  - [ ] Generate local order_id (UUID)
  - [ ] Show "Saved offline" message
  - [ ] Clear cart
- [ ] If online:
  - [ ] Save directly to backend via API

### 22.4 Sync Queue & Conflict Resolution
- [ ] Create SyncService class
- [ ] Detect when online
- [ ] Auto-sync pending orders:
  - [ ] Fetch pending orders from local DB
  - [ ] POST each to backend API
  - [ ] Handle success: delete local order, add to sync_queue
  - [ ] Handle failure: retry later, log error
- [ ] Avoid duplication:
  - [ ] Use unique local ID
  - [ ] Backend check if order already exists (idempotency)
  - [ ] Add `local_id` field to orders table

### 22.5 Sync UI & Manual Trigger
- [ ] Show sync status indicator:
  - [ ] Pending count badge
  - [ ] Syncing animation
- [ ] Manual sync button
- [ ] Sync history screen
- [ ] Handle partial sync (some success, some fail)
- [ ] Notify user after successful sync

---

## **Sesi 23 – Performance & Monitoring App**

### 23.1 Lazy Loading & Pagination
- [ ] Implement pagination for product list
- [ ] Lazy load products (load more on scroll)
- [ ] Skeleton loading UI
- [ ] Cache images locally

### 23.2 Image Caching
- [ ] Install: `cached_network_image`
- [ ] Cache product images
- [ ] Configure cache duration
- [ ] Fallback for broken images

### 23.3 Build Optimization
- [ ] Shrink APK:
  - [ ] Remove unused resources
  - [ ] Use ProGuard/R8 (enabled by default)
- [ ] Obfuscate code:
  - [ ] Enable obfuscation in build.gradle
  - [ ] Test app after obfuscation
- [ ] Split APKs by ABI (optional)
- [ ] Generate app bundle (.aab)

### 23.4 Crash Reporting
- [ ] Install Firebase Crashlytics:
  - [ ] `firebase_core`, `firebase_crashlytics`
- [ ] Setup Firebase project
- [ ] Configure Crashlytics
- [ ] Test crash reporting
- [ ] Monitor crashes di Firebase console

### 23.5 Analytics (Optional)
- [ ] Install Firebase Analytics
- [ ] Track events:
  - [ ] product_viewed
  - [ ] add_to_cart
  - [ ] purchase
  - [ ] login
- [ ] Custom parameters
- [ ] User properties (role, outlet)

---

## **Sesi 24 – Subscription & License**

### 24.1 Subscription Plan Design
- [ ] Define plans di backend:
  - [ ] **Basic**: 1 outlet, 100 products
  - [ ] **Pro**: 3 outlets, unlimited products
  - [ ] **Enterprise**: unlimited outlets & products
- [ ] Create subscriptions table (or use existing business status)
- [ ] Fields: plan_type, start_date, end_date, status

### 24.2 License Validation API
- [ ] Create API endpoint: `/api/check-license`
- [ ] Input: business_id
- [ ] Output:
  - [ ] is_active: true/false
  - [ ] plan: basic/pro/enterprise
  - [ ] expiry_date
  - [ ] features: {max_outlets, max_products}
- [ ] Middleware: check license before access

### 24.3 App License Validation
- [ ] Create LicenseService
- [ ] Fetch license status from API on app start
- [ ] Cache license info locally (1 hour validity)
- [ ] Check license before sensitive operations (create order, add product)
- [ ] Show expiry warning (7 days before expiry)

### 24.4 Handle Expired License
- [ ] If expired:
  - [ ] Block order creation
  - [ ] Show "License expired" dialog
  - [ ] Redirect to subscription page atau contact admin
  - [ ] Allow view-only mode
- [ ] Grace period (optional): 3 days after expiry
- [ ] Test expired license scenario

### 24.5 In-App Subscription (Future)
- [ ] Research in-app billing:
  - [ ] Google Play Billing (Android)
  - [ ] App Store (iOS)
- [ ] Integrate payment gateway (Midtrans, Stripe)
- [ ] Purchase flow UI
- [ ] Receipt verification

---

## **Sesi 25 – Server Deployment**

### 25.1 VPS Setup
- [ ] Choose VPS provider (DigitalOcean, AWS, Vultr, dll)
- [ ] Server specs: 2GB RAM, 1 vCPU, 50GB SSD (minimum)
- [ ] OS: Ubuntu 22.04 LTS
- [ ] SSH access setup
- [ ] Create non-root user with sudo
- [ ] Configure firewall (UFW):
  - [ ] Allow SSH (22)
  - [ ] Allow HTTP (80)
  - [ ] Allow HTTPS (443)

### 25.2 Install Stack (LEMP)
- [ ] Update system: `apt update && apt upgrade`
- [ ] Install Nginx: `apt install nginx`
- [ ] Install PHP 8.3 & extensions:
  - [ ] php-fpm, php-cli, php-mysql, php-xml, php-mbstring, php-curl, php-zip, php-gd
- [ ] Install Composer
- [ ] Install MySQL/MariaDB:
  - [ ] Secure installation
  - [ ] Create database & user for Laravel
- [ ] Install Node.js & npm (untuk build assets)
- [ ] Install Redis (optional, for cache/queue)

### 25.3 Deploy Laravel Application
- [ ] Clone repository ke server:
  - [ ] Setup Git SSH keys
  - [ ] `git clone` project
- [ ] Install dependencies:
  - [ ] `composer install --optimize-autoloader --no-dev`
  - [ ] `npm install && npm run build`
- [ ] Configure .env:
  - [ ] Database credentials
  - [ ] APP_ENV=production
  - [ ] APP_DEBUG=false
  - [ ] APP_URL
  - [ ] Queue & cache drivers
- [ ] Generate app key: `php artisan key:generate`
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Seed data (optional): `php artisan db:seed`
- [ ] Create storage symlink: `php artisan storage:link`
- [ ] Set permissions:
  - [ ] `chown -R www-data:www-data storage bootstrap/cache`
  - [ ] `chmod -R 775 storage bootstrap/cache`

### 25.4 Configure Nginx
- [ ] Create Nginx server block:
  - [ ] Server name (domain)
  - [ ] Root: `/var/www/posapp/public`
  - [ ] PHP-FPM socket
  - [ ] Laravel rewrite rules
- [ ] Test config: `nginx -t`
- [ ] Restart Nginx: `systemctl restart nginx`
- [ ] Test website access via IP

### 25.5 Domain & SSL
- [ ] Point domain DNS to VPS IP
- [ ] Install Certbot: `apt install certbot python3-certbot-nginx`
- [ ] Obtain SSL certificate:
  - [ ] `certbot --nginx -d yourdomain.com -d www.yourdomain.com`
- [ ] Test SSL: visit https://yourdomain.com
- [ ] Auto-renewal test: `certbot renew --dry-run`
- [ ] Force HTTPS redirect di Nginx

### 25.6 Queue & Scheduler
- [ ] Configure queue worker:
  - [ ] Create Supervisor config
  - [ ] `supervisorctl start laravel-worker`
- [ ] Setup Laravel scheduler:
  - [ ] Add cron job: `* * * * * php /path/artisan schedule:run`
- [ ] Test queue & scheduler

### 25.7 Optimization & Caching
- [ ] Run optimization commands:
  - [ ] `php artisan config:cache`
  - [ ] `php artisan route:cache`
  - [ ] `php artisan view:cache`
- [ ] Enable OPcache (PHP)
- [ ] Setup Redis for cache/session (optional)

### 25.8 Monitoring & Backups
- [ ] Setup database backups:
  - [ ] Daily mysqldump cron
  - [ ] Store backups off-server (S3, Dropbox)
- [ ] Setup log rotation
- [ ] Install monitoring (optional):
  - [ ] Laravel Pulse
  - [ ] New Relic, Datadog
  - [ ] Uptime monitoring (UptimeRobot)
- [ ] Setup error notifications (email, Slack)

---

# 🚀 PHASE 4: After Release Evaluation (Sesi 26-27)

## **Sesi 26 – Google Play Release (Internal/Closed Testing)**

### 26.1 Build Release App
- [ ] Update version in pubspec.yaml
- [ ] Update version code & version name
- [ ] Create keystore:
  - [ ] `keytool -genkey -v -keystore ~/upload-keystore.jks ...`
  - [ ] Store keystore securely
- [ ] Configure key.properties
- [ ] Build release bundle:
  - [ ] `flutter build appbundle --release`
- [ ] Test release build di device
- [ ] Generate signed APK (optional for testing)

### 26.2 Prepare App Assets
- [ ] App icon (adaptive icon untuk Android):
  - [ ] 512x512 PNG (high-res)
  - [ ] Foreground & background layers
- [ ] Feature graphic (1024x500)
- [ ] Screenshots:
  - [ ] Phone: minimum 2, maximum 8
  - [ ] Tablet: optional
  - [ ] Different screens: login, products, cart, receipt
- [ ] Promo video (optional)

### 26.3 Google Play Console Setup
- [ ] Create Google Play Developer account ($25 one-time fee)
- [ ] Create new app:
  - [ ] App name
  - [ ] Default language
  - [ ] App type: App
  - [ ] Free or Paid
- [ ] App access: unrestricted or restricted
- [ ] Ads: declare if app contains ads
- [ ] Content rating questionnaire
- [ ] Target audience & content

### 26.4 Create Internal/Closed Testing Track
- [ ] Navigate to Testing → Internal testing
- [ ] Create release:
  - [ ] Upload app bundle (.aab)
  - [ ] Release name
  - [ ] Release notes
- [ ] Add testers:
  - [ ] Create email list
  - [ ] Add peserta course & selected clients
- [ ] Save & review

### 26.5 Store Listing
- [ ] App details:
  - [ ] Short description (80 chars)
  - [ ] Full description (4000 chars)
  - [ ] App icon
  - [ ] Feature graphic
  - [ ] Screenshots
- [ ] Categorization:
  - [ ] Category: Business / Productivity
  - [ ] Tags
- [ ] Contact details:
  - [ ] Email
  - [ ] Phone (optional)
  - [ ] Website
- [ ] Privacy policy URL (REQUIRED):
  - [ ] Create simple privacy policy page
  - [ ] Host on website atau GitHub pages

### 26.6 Submit for Review
- [ ] Review all sections (completeness check)
- [ ] Submit internal testing release
- [ ] Wait for review (usually few hours)
- [ ] Fix any issues if rejected
- [ ] Once approved, share testing link with testers

### 26.7 Tester Onboarding
- [ ] Send testing link to testers
- [ ] Provide testing guide:
  - [ ] What to test
  - [ ] How to report bugs
  - [ ] Feedback form (Google Forms)
- [ ] Monitor feedback & crashes
- [ ] Iterate and release updates

---

## **Sesi 27 – After-Release Evaluation & Next Steps**

### 27.1 Evaluate Backend Architecture
- [ ] Review backend code quality:
  - [ ] Code duplication
  - [ ] Complex methods (refactor candidates)
  - [ ] Security vulnerabilities
- [ ] Review API design:
  - [ ] RESTful conventions
  - [ ] Response consistency
  - [ ] Error handling
- [ ] Review database design:
  - [ ] Normalization
  - [ ] Missing indexes
  - [ ] Slow queries
- [ ] Performance audit:
  - [ ] API response times
  - [ ] Database query counts
  - [ ] Server resource usage
- [ ] Document findings & improvements

### 27.2 Evaluate UX/UI POS App
- [ ] Collect user feedback from testers
- [ ] Identify pain points:
  - [ ] Confusing flows
  - [ ] Slow screens
  - [ ] Bugs & crashes
- [ ] Usability issues:
  - [ ] Button sizes
  - [ ] Color contrast
  - [ ] Text readability
- [ ] Feature requests from users
- [ ] Prioritize improvements (high/medium/low)

### 27.3 Review Errors & Bugs
- [ ] Check Firebase Crashlytics:
  - [ ] Crash-free users percentage
  - [ ] Top crashes
  - [ ] Fix critical bugs
- [ ] Review backend logs:
  - [ ] API errors (500, 400)
  - [ ] Failed jobs
  - [ ] Slow endpoints
- [ ] Create bug tracker (GitHub Issues, Jira, Trello)
- [ ] Assign priority & fix timeline

### 27.4 Monetization Strategy Discussion
- [ ] **Licensing model**:
  - [ ] Monthly subscription (Rp 100k - 500k/month)
  - [ ] Annual subscription (discount)
  - [ ] Lifetime license (one-time payment)
- [ ] **Freemium**:
  - [ ] Free tier: 1 outlet, limited products
  - [ ] Paid tiers: unlock features
- [ ] **Custom development services**:
  - [ ] Charge for custom features
  - [ ] Integration with existing systems
  - [ ] White-label solutions
- [ ] **Commission-based** (future):
  - [ ] Payment gateway integration
  - [ ] Take small % of transactions
- [ ] **Support & maintenance packages**:
  - [ ] Monthly retainer for support
  - [ ] Training services

### 27.5 Portfolio & Marketing Materials
- [ ] **GitHub repository**:
  - [ ] Make public (or create demo version)
  - [ ] Clean commit history
  - [ ] Professional README
  - [ ] Add badges (tests, coverage, etc.)
- [ ] **Portfolio website**:
  - [ ] Project showcase page
  - [ ] Tech stack used
  - [ ] Key features
  - [ ] Screenshots & demo video
  - [ ] Challenges & solutions
- [ ] **LinkedIn post**:
  - [ ] Project announcement
  - [ ] Tech stack, features, learnings
  - [ ] Link to GitHub & live demo
  - [ ] Tag relevant companies & people
- [ ] **CV update**:
  - [ ] Add project to experience
  - [ ] Highlight: Full-stack SaaS, Laravel, Flutter, multi-tenant, etc.
  - [ ] Quantify impact (e.g., "supports 100+ outlets")
- [ ] **Case study article**:
  - [ ] Write blog post/Medium article
  - [ ] Problem, solution, tech choices, results
  - [ ] Share learnings

### 27.6 Maintenance & Feature Roadmap
- [ ] **Immediate fixes** (Week 1-2):
  - [ ] Critical bugs from testing
  - [ ] UI/UX improvements
- [ ] **Short-term features** (Month 1-3):
  - [ ] Customer management
  - [ ] Loyalty/membership program
  - [ ] Advanced reporting (profit analysis)
  - [ ] Email/WhatsApp receipt
  - [ ] Multi-language support
- [ ] **Mid-term features** (Month 3-6):
  - [ ] Real-time sync (Laravel Echo)
  - [ ] Table management (for restaurants)
  - [ ] Employee shift management
  - [ ] Advanced inventory (batch, expiry)
  - [ ] API integrations (e-commerce, accounting)
- [ ] **Long-term scaling** (6+ months):
  - [ ] Multi-database tenancy
  - [ ] Franchise management features
  - [ ] AI-powered analytics & forecasting
  - [ ] Mobile app for customers (order ahead)

### 27.7 Scaling SaaS Infrastructure
- [ ] **Horizontal scaling**:
  - [ ] Load balancer (Nginx/AWS ALB)
  - [ ] Multiple app servers
  - [ ] Database read replicas
- [ ] **Caching strategy**:
  - [ ] Redis for sessions & cache
  - [ ] CDN for static assets
  - [ ] API response caching
- [ ] **Queue optimization**:
  - [ ] Laravel Horizon for monitoring
  - [ ] Separate queue workers by priority
- [ ] **Database optimization**:
  - [ ] Partitioning large tables
  - [ ] Archive old data
  - [ ] Regular maintenance
- [ ] **Multi-database tenancy** (optional):
  - [ ] Separate database per tenant
  - [ ] Pros: better isolation, scaling
  - [ ] Cons: more complex, higher cost

### 27.8 Community & Support
- [ ] Create user documentation:
  - [ ] How to use POS app (user guide)
  - [ ] Admin panel guide
  - [ ] FAQs
- [ ] Setup support channels:
  - [ ] Email support
  - [ ] WhatsApp group untuk users
  - [ ] Knowledge base (help center)
- [ ] Build community:
  - [ ] Facebook group atau Telegram
  - [ ] User feedback & feature voting
  - [ ] Success stories & testimonials

---

# 🧪 Testing & Quality Assurance (Cross-Phase)

## Backend Testing

### Unit Tests
- [ ] Order calculation logic tests
- [ ] Stock calculation tests
- [ ] Discount & tax calculation
- [ ] License validation logic
- [ ] Service class methods

### Feature Tests
- [ ] Authentication API tests
- [ ] Product CRUD API tests
- [ ] Category CRUD tests
- [ ] Order creation tests
- [ ] Stock movement tests
- [ ] Multi-tenant isolation tests
- [ ] Permission & policy tests
- [ ] Export functionality tests
- [ ] Reporting tests

### Model Factories
- [ ] BusinessFactory
- [ ] OutletFactory
- [ ] RoleFactory
- [ ] UserFactory (enhance existing)
- [ ] CategoryFactory
- [ ] ProductFactory:
  - [ ] Add `is_stock_managed` field (random true/false)
  - [ ] Or states: stockManaged(), nonStock()
- [ ] StockFactory
- [ ] OrderFactory
- [ ] OrderItemFactory

### Database Seeders
- [ ] RoleSeeder (Owner, Admin, Manager, Cashier)
- [ ] BusinessSeeder (3 demo businesses)
- [ ] OutletSeeder (2-3 per business)
- [ ] UserSeeder (different roles per outlet)
- [ ] CategorySeeder (10-15 categories: Food, Beverage, Snacks, Services, dll)
- [ ] ProductSeeder (50+ products with images):
  - [ ] Stock-managed products: Snacks, bottled drinks, retail goods
  - [ ] Non-stock products: Es teh, bakso, kopi, services (is_stock_managed = false)
  - [ ] Mix realistic products untuk demo
- [ ] StockSeeder (stock for each stock-managed product/outlet only)
- [ ] OrderSeeder (100+ sample orders with mixed product types)
- [ ] DatabaseSeeder (orchestrate all seeders)

### Integration Tests
- [ ] API → Database
- [ ] Filament → API
- [ ] Queue jobs execution
- [ ] File upload/storage
- [ ] Email sending (if implemented)

---

## Flutter Testing

### Unit Tests
- [ ] Cart calculation logic
- [ ] Discount/tax calculation
- [ ] Change money calculation
- [ ] Date/time formatting
- [ ] Validation functions

### Widget Tests
- [ ] LoginScreen
- [ ] ProductGrid
- [ ] CartWidget
- [ ] PaymentScreen
- [ ] ReceiptWidget
- [ ] Button interactions
- [ ] Form validations

### Integration Tests
- [ ] Login flow
- [ ] Add to cart → checkout → payment
- [ ] Offline mode → sync
- [ ] Printer connection & print

### Manual Testing Checklist
- [ ] Test on different Android versions (min API 21)
- [ ] Test on different screen sizes (phone, tablet, foldable)
- [ ] Test offline mode extensively
- [ ] Test with slow internet
- [ ] Test Bluetooth printer dengan different models
- [ ] Test license expiry scenarios
- [ ] Stress test (100+ products, large carts)

---

# 📝 Documentation (Cross-Phase)

## Code Documentation
- [ ] PHPDoc untuk semua classes & methods
- [ ] Inline comments untuk complex logic
- [ ] README di setiap module/folder (if needed)

## API Documentation
- [ ] Install Scribe: `composer require --dev knuckleswtf/scribe`
- [ ] Or Laravel API Documentation Generator
- [ ] Document all endpoints:
  - [ ] Method, URL, headers
  - [ ] Request body (with example)
  - [ ] Response (success & error)
  - [ ] Authentication requirements
- [ ] Generate docs: `php artisan scribe:generate`
- [ ] Host docs: `/docs/api`

## Developer Documentation
- [ ] **README.md**:
  - [ ] Project description
  - [ ] Tech stack
  - [ ] Installation instructions
  - [ ] Environment setup
  - [ ] Running tests
  - [ ] Deployment guide
  - [ ] Contributing guidelines
- [ ] **ARCHITECTURE.md**:
  - [ ] Project structure
  - [ ] Design patterns used
  - [ ] Service layer explanation
  - [ ] Multi-tenancy implementation
- [ ] **DATABASE.md**:
  - [ ] ER diagram (use dbdiagram.io atau draw.io)
  - [ ] Table relationships
  - [ ] Indexing strategy
- [ ] **DEPLOYMENT.md**:
  - [ ] Server requirements
  - [ ] Step-by-step deployment
  - [ ] Environment variables
  - [ ] Troubleshooting
- [ ] **CHANGELOG.md**:
  - [ ] Version history
  - [ ] Changes per version

## User Documentation
- [ ] **Admin Panel Guide** (for business owners/managers)
- [ ] **POS App User Guide** (for cashiers)
- [ ] **FAQ** (common questions)
- [ ] **Video tutorials** (optional, for course)

## Course Documentation
- [ ] **COURSE_OUTLINE.md**: Detailed breakdown per sesi
- [ ] **LEARNING_PATH.md**: Student journey
- [ ] **PREREQUISITES.md**: Required knowledge
- [ ] **EXERCISES.md**: Hands-on exercises per session

---

# 🔒 Security Checklist

## Backend Security
- [ ] Use HTTPS everywhere (force SSL)
- [ ] Validate all inputs (Form Requests)
- [ ] Prevent SQL injection (use Eloquent, parameterized queries)
- [ ] Prevent XSS (escape outputs, use Blade `{{ }}`)
- [ ] CSRF protection enabled (Laravel default)
- [ ] Rate limiting on API & login routes
- [ ] Secure file uploads:
  - [ ] Validate file types (MIME check)
  - [ ] Limit file size
  - [ ] Store outside public folder (or use signed URLs)
- [ ] Secure authentication:
  - [ ] Hash passwords (bcrypt)
  - [ ] Token expiration (Sanctum)
  - [ ] Prevent brute force (rate limit login)
- [ ] Environment variables:
  - [ ] Never commit .env
  - [ ] Use strong APP_KEY
  - [ ] Rotate secrets regularly
- [ ] Disable debug mode in production
- [ ] Remove unnecessary routes/endpoints
- [ ] Regular security updates: `composer update`

## Flutter App Security
- [ ] API token storage (secure_storage)
- [ ] Validate SSL certificates
- [ ] Obfuscate code (production build)
- [ ] Don't hardcode API keys/secrets
- [ ] Validate server responses (prevent injection)
- [ ] Secure local database (encrypt sensitive data)
- [ ] Handle permissions properly
- [ ] Test for common vulnerabilities (OWASP Mobile Top 10)

---

# 🚀 Performance Checklist

## Backend Performance
- [ ] N+1 query elimination (verified with Telescope/Debugbar)
- [ ] Database indexing (all foreign keys + common queries)
- [ ] Query optimization (avoid `SELECT *`, use pagination)
- [ ] Eager loading (with, withCount)
- [ ] Config/route/view caching (production)
- [ ] OPcache enabled (PHP)
- [ ] Redis for cache/session (production)
- [ ] Queue long-running tasks
- [ ] CDN for static assets (images, CSS, JS)
- [ ] Compress responses (gzip)
- [ ] Lazy load large datasets

## Flutter App Performance
- [ ] Image caching (cached_network_image)
- [ ] Lazy load lists (ListView.builder)
- [ ] Pagination for large datasets
- [ ] Optimize widget builds (const constructors, keys)
- [ ] Avoid unnecessary rebuilds (use selectors, immutables)
- [ ] Profile app (Flutter DevTools)
- [ ] Reduce app size (remove unused packages/resources)
- [ ] Optimize images (compress, use WebP)

---

# 📅 Suggested Implementation Timeline

## Weeks 1-2: Filament Core (Sesi 9-10)
- Install Filament
- Create Resources: Product, Category, Outlet, User
- Relations, Upload, Basic Permissions
- **Deliverable**: Admin panel dengan CRUD lengkap

## Week 3: Inventory & Transactions (Sesi 11-12)
- Stock management logic
- Order calculation enhancement
- **Deliverable**: Complete transaction flow di backend

## Week 4: Reporting & Dashboard (Sesi 13-14)
- Reports: Sales, Stock, Profit
- Export Excel/PDF
- Dashboard dengan charts
- **Deliverable**: Complete admin analytics

## Week 5: Multi-Tenant & Optimization (Sesi 15-17)
- Multi-outlet & roles
- SaaS tenant isolation
- Performance optimization (N+1, indexing)
- Refactor ke service layer
- **Deliverable**: Production-ready backend

## Weeks 6-7: Flutter POS UI & Core (Sesi 18-20)
- Flutter project setup
- Product grid responsive
- Cart & checkout flow
- Payment & receipt
- **Deliverable**: Working POS app (online mode)

## Week 8: Printer & Offline (Sesi 21-22)
- Bluetooth printer integration
- Offline mode dengan sqflite
- Sync logic
- **Deliverable**: Offline-capable POS dengan print

## Week 9: App Polish & Subscription (Sesi 23-24)
- Performance optimization
- Crash reporting
- Subscription & license validation
- **Deliverable**: Production-ready app

## Week 10: Deployment (Sesi 25)
- VPS setup & Laravel deployment
- Domain & SSL
- Queue & scheduler
- **Deliverable**: Live production server

## Week 11: Testing & Release (Sesi 26)
- Complete testing (backend & app)
- Build release app
- Google Play Console setup
- Internal testing release
- **Deliverable**: App live di Google Play (testing)

## Week 12: Evaluation & Marketing (Sesi 27)
- Collect feedback & fix bugs
- Evaluate & plan improvements
- Create portfolio materials
- Marketing prep
- **Deliverable**: Polished product + marketing materials

---

# 🎯 Success Metrics

## Technical Metrics
- [ ] **Test Coverage**: 80%+ (backend)
- [ ] **API Performance**: <200ms average response time
- [ ] **App Performance**: 60fps, <3s startup
- [ ] **Crash-Free Rate**: 99%+
- [ ] **Security**: Zero critical vulnerabilities
- [ ] **Database**: All queries optimized (no N+1)

## Course Metrics
- [ ] **Content**: 25+ sesi, 30+ jam video
- [ ] **Completion Rate**: Curriculum selesai 100%
- [ ] **Features**: 50+ features implemented
- [ ] **Documentation**: Complete docs (code, API, user, course)

## Marketing Metrics
- [ ] **Demo Environment**: 99% uptime
- [ ] **Landing Page**: Conversion rate 5%+
- [ ] **Email Signups**: 200+ (pre-launch)
- [ ] **Testimonials**: 10+ dari beta testers
- [ ] **Google Play**: 100+ installs (month 1)
- [ ] **Revenue**: [Set target]

---

# 📞 Important Decisions to Make

## Technology Choices
1. **Export Library**: `maatwebsite/excel` vs `spout` (recommendation: maatwebsite)
2. **PDF Library**: `dompdf` vs `mpdf` vs `snappy` (recommendation: dompdf untuk simple, mpdf untuk complex)
3. **State Management (Flutter)**: Provider vs Riverpod vs Bloc (recommendation: Riverpod)
4. **Multi-Tenancy**: Single DB vs Multi-DB (recommendation: Single DB dengan tenant_id)
5. **Image Storage**: Local vs S3 vs CDN (recommendation: Local untuk start, S3 untuk scale)
6. **Queue Driver**: Database vs Redis (recommendation: Redis untuk production)
7. **Cache Driver**: File vs Redis (recommendation: Redis untuk production)

## Business Decisions
1. **Pricing Model**: Subscription vs Lifetime vs Freemium
2. **Target Market**: UMKM vs Enterprise vs Franchise
3. **Payment Gateway**: Midtrans vs Xendit vs Stripe
4. **Support Model**: Email vs Chat vs Phone
5. **Course Platform**: Self-hosted vs Teachable vs Udemy

---

# 🔮 Future Features (Post-MVP)

## High Priority
- [ ] **Recipe & Ingredient Management System** (F&B Advanced):
  - [ ] Create `ingredients` table (raw materials: gula, teh, daging, dll)
  - [ ] Create `recipes` table (1 product = many ingredients)
  - [ ] Create `recipe_items` table (ingredient_id, quantity, unit)
  - [ ] Auto ingredient stock deduction when selling finished goods
  - [ ] Ingredient stock reports & alerts
  - [ ] Recipe costing calculation (accurate COGS)
  - [ ] Batch production tracking
  - [ ] Use case: Restaurant, Cafe, Catering, Food Court
- [ ] Customer management (CRM lite)
- [ ] Loyalty/membership program
- [ ] Email/WhatsApp receipt
- [ ] Advanced reporting (profit analysis, forecasting)
- [ ] Multi-currency support
- [ ] Multi-language (i18n)

## Medium Priority
- [ ] Table management (for restaurants)
- [ ] Employee attendance & shift management
- [ ] Advanced inventory (batch tracking, expiry dates)
- [ ] Supplier management
- [ ] Purchase order system
- [ ] Accounting integration (export to Jurnal, Zahir, etc.)

## Nice to Have
- [ ] Real-time sync (Laravel Echo + Pusher/WebSocket)
- [ ] Mobile app for customers (self-order)
- [ ] Kitchen display system (for restaurants)
- [ ] AI-powered sales forecasting
- [ ] Franchise management features
- [ ] WhatsApp bot for notifications
- [ ] Integration with e-commerce (Tokopedia, Shopee)

---

# 📚 Learning Resources & References

## Laravel & Filament
- Laravel Docs: https://laravel.com/docs
- Filament Docs: https://filamentphp.com/docs
- Laravel Daily: https://laraveldaily.com
- Laracasts: https://laracasts.com

## Flutter
- Flutter Docs: https://docs.flutter.dev
- Dart Docs: https://dart.dev/guides
- Flutter Awesome: https://flutterawesome.com
- Pub.dev: https://pub.dev

## DevOps
- DigitalOcean Tutorials: https://www.digitalocean.com/community/tutorials
- Laravel Deployment: https://laravel.com/docs/deployment
- Docker Laravel: https://serversforhackers.com

---

# ✅ Pre-Release Final Checklist

## Code Quality
- [ ] All feature tests passing
- [ ] All unit tests passing
- [ ] Code formatted (Pint: `vendor/bin/pint`)
- [ ] No debug code (dd, dump, var_dump, console.log)
- [ ] No commented code blocks
- [ ] No TODO comments unresolved
- [ ] Code reviewed

## Functionality
- [ ] All CRUD operations work
- [ ] Multi-tenant isolation verified
- [ ] Permissions work correctly
- [ ] Reports generate correctly
- [ ] Exports work (Excel, PDF)
- [ ] API endpoints tested (Postman/Insomnia)
- [ ] Printer integration tested (real device)
- [ ] Offline mode + sync tested
- [ ] License validation works

## Documentation
- [ ] README complete & accurate
- [ ] API docs generated & accessible
- [ ] Deployment guide tested (fresh server)
- [ ] User guide written
- [ ] Course outline finalized
- [ ] CHANGELOG updated

## Security
- [ ] Security audit passed
- [ ] All inputs validated
- [ ] No sensitive data in logs
- [ ] SSL configured (production)
- [ ] Rate limiting enabled
- [ ] Secrets secured (not in code)

## Performance
- [ ] Backend response times <200ms
- [ ] No N+1 queries
- [ ] App 60fps
- [ ] App size <50MB
- [ ] Load tested (100+ concurrent users)

## Deployment
- [ ] Production environment configured
- [ ] Database backups automated
- [ ] Monitoring setup (Sentry, Firebase)
- [ ] Error notifications working
- [ ] Queue workers running
- [ ] Scheduler cron configured
- [ ] Health check endpoint working

## Marketing
- [ ] Demo environment live & stable
- [ ] Landing page published
- [ ] Course platform ready
- [ ] Marketing materials prepared
- [ ] Launch date set
- [ ] Email campaign ready

---

# 🎉 Launch Readiness

- [ ] **Backend**: Deployed, tested, monitored
- [ ] **Flutter App**: Released to Internal Testing
- [ ] **Documentation**: Complete
- [ ] **Course**: Content ready, platform setup
- [ ] **Marketing**: Materials ready, audience built
- [ ] **Support**: Channels established
- [ ] **Team**: Ready for launch

---

**Project**: JagoFlutter Academy - POS SaaS
**Stack**: Laravel 12 + Filament 4 + Flutter
**Timeline**: 12 weeks (recommended)
**Target**: Production release + Course launch

**Last Updated**: 2025-11-28
**Status**: Planning → Development → Testing → Release

---

## 🚀 Quick Start Commands

```bash
# === BACKEND ===
# Setup
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link

# Development
php artisan serve
php artisan queue:work
php artisan schedule:work

# Testing
php artisan test
php artisan test --coverage
vendor/bin/pint

# Production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# === FLUTTER ===
# Setup
flutter pub get

# Run
flutter run

# Build
flutter build apk --release
flutter build appbundle --release

# Test
flutter test
flutter analyze
```

---

**Siap untuk release? Let's build something amazing! 🚀**
