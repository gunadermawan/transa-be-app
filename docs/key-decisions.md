# 🎯 Key Technical Decisions - JagoFlutter Academy POS SaaS

> Quick reference untuk technology stack & important decisions

---

## 📦 Core Stack

### Backend
- **Framework**: Laravel 12
- **PHP Version**: 8.3.22
- **Admin Panel**: **Filament 4** ✅
- **Authentication**: Laravel Sanctum 4
- **Database**: MySQL/MariaDB
- **Testing**: PHPUnit 11

### Frontend (Mobile)
- **Framework**: Flutter (latest stable)
- **State Management**: Riverpod (recommended)
- **Local Database**: sqflite
- **HTTP Client**: dio
- **Image Caching**: cached_network_image

---

## 🏗️ Architecture Decisions

### 1. Product Types & Stock Management ✅
**Decision**: Use `is_stock_managed` boolean field

```php
// products table
is_stock_managed: boolean (default: true)
```

**Product Types**:
- **Stock-managed** (true): Physical goods → Track stock
  - Examples: Snack, bottled drinks, retail items
- **Non-stock** (false): Made-to-order & services → No stock tracking
  - Examples: Es teh, bakso, kopi, jasa laundry

**Benefits**:
- ✅ Simple to implement
- ✅ Flexible (retail + F&B)
- ✅ No complexity of recipe management
- ✅ Easy to teach in course

**Future Enhancement**:
- Recipe & Ingredient Management System (Phase 5+)

---

### 2. Multi-Tenancy Strategy
**Decision**: Single Database with `business_id` (tenant_id)

**Implementation**:
- Global scope untuk auto-filter by tenant
- Middleware untuk tenant identification
- Subdomain routing (optional)

**Why**:
- ✅ Easier to manage
- ✅ Lower infrastructure cost
- ✅ Good for course/demo
- ✅ Can migrate to multi-DB later if needed

---

### 3. Stock Reduction Logic
**When**: After successful order creation (not on cart)

```php
// OrderService
public function createOrder($items) {
    foreach ($items as $item) {
        $product = Product::find($item['product_id']);

        // Conditional stock reduction
        if ($product->is_stock_managed) {
            $this->stockService->reduceStock(
                $product->id,
                $item['quantity']
            );
        }
    }
}
```

---

## 📚 Library Choices

### Export & Reports
- **Excel**: `maatwebsite/laravel-excel` ✅
  - Why: Full-featured, popular, well-documented
- **PDF**: `barryvdh/laravel-dompdf` ✅
  - Why: Simple for basic PDFs
  - Alternative: mpdf (for complex layouts)

### Performance & Monitoring
- **N+1 Detection**: Laravel Debugbar (dev) / Laravel Telescope (dev)
- **Error Tracking**: Sentry (recommended)
- **Cache/Queue**: Redis (production)
- **Performance Monitoring**: Laravel Pulse

### Flutter Packages
- **State Management**: Riverpod ✅
- **HTTP**: dio
- **Local Storage**: shared_preferences
- **Offline DB**: sqflite
- **Image Caching**: cached_network_image
- **Bluetooth Printer**: blue_thermal_printer or flutter_bluetooth_serial
- **Crash Reporting**: Firebase Crashlytics

---

## 🔐 Security Standards

### Backend
- ✅ All inputs validated via Form Requests
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ XSS prevention (Blade escaping)
- ✅ CSRF protection (Laravel default)
- ✅ Rate limiting (API throttle)
- ✅ Password hashing (bcrypt)
- ✅ Token expiration (Sanctum)

### Flutter App
- ✅ Secure token storage (flutter_secure_storage)
- ✅ SSL certificate validation
- ✅ Code obfuscation (production)
- ✅ No hardcoded secrets

---

## 🎨 UI/UX Standards

### Filament Admin
- Color scheme: Professional (blue/indigo)
- Navigation: Grouped by module
- Tables: Searchable, filterable, sortable
- Forms: Validated, clear error messages

### Flutter POS
- **Phone**: 2-column product grid
- **Tablet**: Split view (products 60% | cart 40%)
- Font: Clear, readable (Material Design)
- Colors: High contrast for kasir environment
- Buttons: Large touch targets (min 48x48dp)

---

## 📊 Data Flow

### Order Creation Flow
```
1. User add products to cart (local state)
2. Review cart & apply discount/tax
3. Select payment method
4. Submit order → API
5. Backend validates
6. Create order record
7. Create order items
8. Reduce stock (conditional - only if is_stock_managed)
9. Return order + receipt data
10. App shows success + print receipt option
```

### Offline Sync Flow
```
1. Check internet connectivity
2. If offline → Save to local DB (sqflite)
3. When online → Auto sync pending orders
4. POST to API with local_id
5. Backend check idempotency
6. Delete local record if success
7. Retry if failed
```

---

## 🧪 Testing Strategy

### Backend Tests
- **Unit Tests**: Business logic (OrderService, StockService, calculations)
- **Feature Tests**: API endpoints, workflows
- **Coverage Target**: 80%+

### Flutter Tests
- **Unit Tests**: Calculation logic, helpers
- **Widget Tests**: UI components
- **Integration Tests**: Full user flows

---

## 🚀 Deployment Strategy

### Backend (VPS)
- **Server**: Ubuntu 22.04 LTS
- **Stack**: LEMP (Nginx, PHP 8.3, MySQL)
- **Process Manager**: Supervisor (queue workers)
- **SSL**: Let's Encrypt (Certbot)
- **Cron**: Laravel Scheduler

### Flutter App
- **Platform**: Android (Google Play)
- **Release Track**: Internal Testing → Closed Testing → Production
- **Build**: App Bundle (.aab)
- **Min SDK**: API 21 (Android 5.0)

---

## 💰 Monetization Model (TBD)

### Options:
1. **Subscription**: Monthly/Annual recurring
2. **Lifetime License**: One-time payment
3. **Freemium**: Free tier + paid upgrades
4. **Custom Development**: Per-project pricing

### Recommended for Course:
**Freemium + Subscription**:
- Free: 1 outlet, 50 products, 100 transactions/month
- Basic (Rp 150k/month): 3 outlets, unlimited products
- Pro (Rp 300k/month): Unlimited outlets, advanced features

---

## 📅 Timeline (12 Weeks)

- **Week 1-2**: Filament setup & resources (Sesi 9-10)
- **Week 3**: Inventory & transactions (Sesi 11-12)
- **Week 4**: Reporting & dashboard (Sesi 13-14)
- **Week 5**: Multi-tenant & optimization (Sesi 15-17)
- **Week 6-7**: Flutter POS core (Sesi 18-20)
- **Week 8**: Printer & offline (Sesi 21-22)
- **Week 9**: App polish & subscription (Sesi 23-24)
- **Week 10**: Deployment (Sesi 25)
- **Week 11**: Testing & release (Sesi 26)
- **Week 12**: Evaluation & marketing (Sesi 27)

---

## 🔮 Future Roadmap (Post-Course)

### Phase 5: Advanced Features
- Recipe & Ingredient Management (F&B Advanced)
- Customer CRM & Loyalty Program
- Table Management (Restaurant)
- Employee Shift Management
- Multi-currency Support
- WhatsApp Integration

### Phase 6: Scaling
- Multi-database Tenancy
- Horizontal Scaling (Load Balancer)
- CDN Integration
- Advanced Analytics & AI Forecasting

---

## 📝 Important Notes

### DO's ✅
- Follow Laravel conventions
- Use Form Requests for validation
- Service layer for business logic
- Eager loading (prevent N+1)
- Comprehensive tests
- Clear documentation

### DON'Ts ❌
- Don't use `DB::` directly (use Eloquent)
- Don't use `env()` outside config files
- Don't skip validation
- Don't ignore N+1 queries
- Don't commit .env file
- Don't hardcode values

---

**Last Updated**: 2025-11-28
**Status**: Planning Phase
**Ready to Start**: ✅ Sesi 9 - Filament 4 Installation

---

## Quick Commands Reference

```bash
# Filament 4 Installation
composer require filament/filament:"^4.0"
php artisan filament:install --panels

# Create Resources
php artisan make:filament-resource Product
php artisan make:filament-resource Category

# Testing
php artisan test
vendor/bin/pint

# Production Deploy
php artisan config:cache
php artisan route:cache
php artisan optimize
```

---

**Remember**:
- **Filament 4** (not 3!)
- **Product types**: `is_stock_managed` field
- **Multi-tenant**: Single DB with business_id
- **Testing**: 80%+ coverage target
