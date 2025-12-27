# 📱 API Reference for Flutter Integration

**Base URL:** `http://127.0.0.1:8000/api` (Development)
**Production URL:** `https://yourdomain.com/api`

**Authentication:** Bearer Token (Laravel Sanctum)

---

## 🔐 Authentication

### 1. Register
```http
POST /api/register

Body:
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password",
  "business_id": 1,
  "outlet_id": 1,
  "role_id": 4
}

Response:
{
  "message": "User registered successfully",
  "user": {...},
  "token": "1|abc123..."
}
```

### 2. Login
```http
POST /api/login

Body:
{
  "email": "cashier1@tokomajujaya.com",
  "password": "password"
}

Response:
{
  "message": "Login successful",
  "user": {
    "id": 3,
    "name": "Kasir 1",
    "email": "cashier1@tokomajujaya.com",
    "business_id": 1,
    "outlet_id": 1
  },
  "token": "2|xyz789..."
}
```

### 3. Get Current User
```http
GET /api/me
Headers: Authorization: Bearer {token}

Response:
{
  "id": 3,
  "name": "Kasir 1",
  "email": "cashier1@tokomajujaya.com",
  "business_id": 1,
  "outlet_id": 1,
  "role": {...}
}
```

### 4. Logout
```http
POST /api/logout
Headers: Authorization: Bearer {token}

Response:
{
  "message": "Logout successful"
}
```

---

## 🏬 Categories

### Get Categories
```http
GET /api/get-categories
Headers: Authorization: Bearer {token}

Response:
{
  "data": [
    {
      "id": 1,
      "name": "Makanan",
      "description": "Produk makanan",
      "business_id": 1
    }
  ]
}
```

---

## 📦 Products

### 1. Get Products
```http
GET /api/get-products
Headers: Authorization: Bearer {token}

Response:
{
  "data": [
    {
      "id": 1,
      "name": "Indomie Goreng",
      "sku": "1701234567",
      "barcode": "8991234567890",
      "category_id": 1,
      "category": {
        "id": 1,
        "name": "Mie Instant"
      },
      "business_id": 1,
      "description": "Mie instant rasa goreng",
      "color": null,
      "price": 3500,
      "cost": 2800,
      "image": "/storage/products/abc.jpg",
      "is_stock_managed": true,
      "stock_minimum": 20,
      "stocks": [
        {
          "id": 1,
          "product_id": 1,
          "outlet_id": 1,
          "quantity": 150,
          "outlet": {
            "id": 1,
            "name": "Cabang Gatot Subroto"
          }
        }
      ]
    }
  ]
}
```

**Flutter Helper - Calculate Stock Status:**
```dart
String getStockStatus(Product product, int outletId) {
  if (!product.isStockManaged) return 'unlimited';

  final stock = product.stocks?.firstWhere(
    (s) => s.outletId == outletId,
    orElse: () => null,
  );

  if (stock == null || stock.quantity == 0) return 'out_of_stock';
  if (stock.quantity <= (product.stockMinimum ?? 10)) return 'low_stock';
  return 'in_stock';
}
```

### 2. Get Single Product
```http
GET /api/get-product/{id}
Headers: Authorization: Bearer {token}

Response:
{
  "data": {
    "id": 1,
    "name": "Indomie Goreng",
    ...
  }
}
```

### 3. Add Product (Admin Only)
```http
POST /api/add-product
Headers:
  Authorization: Bearer {token}
  Content-Type: multipart/form-data

Body (form-data):
  name: "Product Name"
  category_id: 1
  business_id: 1
  description: "Description"
  price: 10000
  cost: 8000
  barcode: "123456789"
  image: (file)

Response:
{
  "message": "Product added successfully",
  "data": {...}
}
```

---

## 🛒 Orders

### 1. Create Order ⭐ MOST IMPORTANT
```http
POST /api/add-order
Headers: Authorization: Bearer {token}

Body:
{
  "outlet_id": 1,
  "customer_id": 5,  // optional
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "price": 3500,
      "total": 7000,
      "notes": "Extra pedas"  // optional
    },
    {
      "product_id": 5,
      "quantity": 1,
      "price": 15000,
      "total": 15000
    }
  ],
  "sub_total": 22000,
  "discount": 2200,
  "tax": 2178,
  "total_price": 21978,
  "total_items": 3,
  "payment_method": "cash",  // cash, card, qris, transfer
  "amount_received": 50000,  // for cash only
  "notes": "Pelanggan langganan"  // optional
}

Response:
{
  "success": true,
  "message": "Order created successfully",
  "data": {
    "id": 1,
    "order_number": "ORD-20251130-000001",
    "grand_total": 21978,
    "payment_method": "cash",
    "amount_received": 50000,
    "change": 28022,
    "created_at": "2025-11-30 14:30:15"
  }
}
```

**Flutter Helper - Calculate Order:**
```dart
class OrderCalculator {
  static OrderTotals calculate({
    required List<CartItem> items,
    double discountPercent = 0,
    double discountAmount = 0,
    double taxPercent = 11,
  }) {
    // Subtotal
    double subtotal = items.fold(0, (sum, item) => sum + (item.price * item.quantity));

    // Discount
    double discount = discountAmount;
    if (discountPercent > 0) {
      discount = subtotal * (discountPercent / 100);
    }

    // Tax (after discount)
    double afterDiscount = subtotal - discount;
    double tax = afterDiscount * (taxPercent / 100);

    // Grand Total
    double grandTotal = subtotal - discount + tax;

    return OrderTotals(
      subtotal: subtotal,
      discount: discount,
      tax: tax,
      grandTotal: grandTotal,
    );
  }

  static double calculateChange(double received, double grandTotal) {
    return received - grandTotal;
  }
}
```

### 2. Get Orders (with filters)
```http
GET /api/get-orders
Headers: Authorization: Bearer {token}

Query Parameters:
  ?outlet_id=1
  &status=success  // success, void, pending
  &payment_method=cash
  &start_date=2025-11-01
  &end_date=2025-11-30
  &search=ORD-20251130
  &per_page=20
  &page=1

Response:
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "order_number": "ORD-20251130-000001",
      "outlet": {...},
      "customer": {...},
      "cashier": {...},
      "items": [
        {
          "product": {...},
          "quantity": 2,
          "price": 3500,
          "total": 7000
        }
      ],
      "sub_total": 22000,
      "discount": 2200,
      "tax": 2178,
      "total_price": 21978,
      "payment_method": "cash",
      "amount_received": 50000,
      "status": "success",
      "created_at": "2025-11-30T14:30:15.000000Z"
    }
  ],
  "total": 150,
  "per_page": 20,
  "last_page": 8
}
```

### 3. Get Single Order
```http
GET /api/get-order/{id}
Headers: Authorization: Bearer {token}

Response:
{
  "data": {
    "id": 1,
    "order_number": "ORD-20251130-000001",
    ...
  }
}
```

### 4. Get Orders by Outlet
```http
GET /api/get-orders-by-outlet/{outlet_id}
Headers: Authorization: Bearer {token}

Response:
{
  "data": [...]
}
```

---

## 🧾 Receipt

### Get Receipt (for printing) ⭐ VIRAL FEATURE
```http
GET /api/receipts/{orderId}
Headers: Authorization: Bearer {token}

Response:
{
  "business": {
    "name": "Toko Maju Jaya",
    "outlet_name": "Cabang Gatot Subroto",
    "address": "Jl. Gatot Subroto No. 123, Jakarta",
    "phone": "+62 21 1234 5678",
    "tax_id": "12.345.678.9-012.345",
    "logo_url": "https://domain.com/storage/logo.jpg"
  },
  "transaction": {
    "order_number": "ORD-20251130-000001",
    "date": "30 November 2025",
    "time": "14:30:15",
    "cashier": "Budi Santoso",
    "customer": "John Doe"  // or null
  },
  "items": [
    {
      "name": "Indomie Goreng",
      "quantity": 2,
      "price": 3500,
      "subtotal": 7000,
      "notes": "Extra pedas"
    }
  ],
  "summary": {
    "subtotal": 22000,
    "discount": 2200,
    "tax": 2178,
    "grand_total": 21978
  },
  "payment": {
    "method": "Cash",
    "subtotal": 22000,
    "discount": 2200,
    "tax": 2178,
    "grand_total": 21978,
    "amount_received": 50000,
    "change": 28022
  },
  "footer": {
    "message": "Terima kasih atas kunjungan Anda!",
    "social_media": "@tokomajujaya"
  }
}
```

**Flutter ESC/POS Printing:**
```dart
import 'package:esc_pos_utils/esc_pos_utils.dart';
import 'package:flutter_bluetooth_serial/flutter_bluetooth_serial.dart';

Future<void> printReceipt(Receipt receipt) async {
  final profile = await CapabilityProfile.load();
  final generator = Generator(PaperSize.mm58, profile);

  List<int> bytes = [];

  // Header
  bytes += generator.text(receipt.business.name,
    styles: PosStyles(
      align: PosAlign.center,
      height: PosTextSize.size2,
      width: PosTextSize.size2,
      bold: true,
    ),
  );

  bytes += generator.text(receipt.business.outletName,
    styles: PosStyles(align: PosAlign.center));
  bytes += generator.text(receipt.business.address,
    styles: PosStyles(align: PosAlign.center));
  bytes += generator.text(receipt.business.phone,
    styles: PosStyles(align: PosAlign.center));

  bytes += generator.hr();

  // Transaction Info
  bytes += generator.text('Order: ${receipt.transaction.orderNumber}');
  bytes += generator.text('${receipt.transaction.date} ${receipt.transaction.time}');
  bytes += generator.text('Kasir: ${receipt.transaction.cashier}');

  bytes += generator.hr();

  // Items
  for (var item in receipt.items) {
    bytes += generator.row([
      PosColumn(text: item.name, width: 7),
      PosColumn(text: item.quantity.toString(), width: 2),
      PosColumn(
        text: formatCurrency(item.subtotal),
        width: 3,
        styles: PosStyles(align: PosAlign.right),
      ),
    ]);

    if (item.notes != null) {
      bytes += generator.text('  * ${item.notes}',
        styles: PosStyles(fontType: PosFontType.fontB));
    }
  }

  bytes += generator.hr();

  // Summary
  bytes += generator.row([
    PosColumn(text: 'Subtotal', width: 6),
    PosColumn(
      text: formatCurrency(receipt.summary.subtotal),
      width: 6,
      styles: PosStyles(align: PosAlign.right),
    ),
  ]);

  if (receipt.summary.discount > 0) {
    bytes += generator.row([
      PosColumn(text: 'Diskon', width: 6),
      PosColumn(
        text: '- ${formatCurrency(receipt.summary.discount)}',
        width: 6,
        styles: PosStyles(align: PosAlign.right),
      ),
    ]);
  }

  bytes += generator.row([
    PosColumn(text: 'Pajak', width: 6),
    PosColumn(
      text: formatCurrency(receipt.summary.tax),
      width: 6,
      styles: PosStyles(align: PosAlign.right),
    ),
  ]);

  bytes += generator.hr(ch: '=');

  bytes += generator.row([
    PosColumn(
      text: 'TOTAL',
      width: 6,
      styles: PosStyles(bold: true, height: PosTextSize.size2),
    ),
    PosColumn(
      text: formatCurrency(receipt.payment.grandTotal),
      width: 6,
      styles: PosStyles(
        align: PosAlign.right,
        bold: true,
        height: PosTextSize.size2,
      ),
    ),
  ]);

  // Payment
  if (receipt.payment.method == 'Cash') {
    bytes += generator.row([
      PosColumn(text: 'Bayar', width: 6),
      PosColumn(
        text: formatCurrency(receipt.payment.amountReceived),
        width: 6,
        styles: PosStyles(align: PosAlign.right),
      ),
    ]);

    bytes += generator.row([
      PosColumn(text: 'Kembali', width: 6),
      PosColumn(
        text: formatCurrency(receipt.payment.change),
        width: 6,
        styles: PosStyles(align: PosAlign.right),
      ),
    ]);
  } else {
    bytes += generator.text('Pembayaran: ${receipt.payment.method}');
  }

  bytes += generator.hr();

  // Footer
  bytes += generator.text(receipt.footer.message,
    styles: PosStyles(align: PosAlign.center));

  if (receipt.footer.socialMedia != null) {
    bytes += generator.text(receipt.footer.socialMedia!,
      styles: PosStyles(align: PosAlign.center));
  }

  bytes += generator.feed(2);
  bytes += generator.cut();

  // Send to printer
  await _sendToPrinter(bytes);
}
```

---

## 📊 Dashboard

### Get Dashboard Stats ⭐ SCREENSHOT GOLD
```http
GET /api/dashboard
Headers: Authorization: Bearer {token}

Query Parameters:
  ?outlet_id=1  // optional, defaults to user's outlet
  &date=2025-11-30  // optional, defaults to today

Response:
{
  "today": {
    "date": "2025-11-30",
    "sales": 2500000,
    "transactions": 45,
    "customers": 38
  },
  "this_month": {
    "sales": 75000000,
    "transactions": 1250,
    "average_per_day": 2500000
  },
  "alerts": {
    "low_stock_count": 8,
    "pending_orders": 0
  },
  "top_products": [
    {
      "product_id": 15,
      "product_name": "Indomie Goreng",
      "quantity_sold": 150,
      "revenue": 525000
    }
  ],
  "payment_methods": {
    "cash": 1800000,
    "card": 500000,
    "qris": 200000
  }
}
```

**Flutter Dashboard Widget:**
```dart
class DashboardScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return FutureBuilder<DashboardStats>(
      future: apiService.getDashboardStats(),
      builder: (context, snapshot) {
        if (!snapshot.hasData) return LoadingWidget();

        final stats = snapshot.data!;

        return Column(
          children: [
            // Today's Sales
            Card(
              child: ListTile(
                title: Text('Sales Hari Ini'),
                subtitle: Text(formatCurrency(stats.today.sales)),
                trailing: Text('${stats.today.transactions} transaksi'),
              ),
            ),

            // Monthly Sales
            Card(
              child: ListTile(
                title: Text('Sales Bulan Ini'),
                subtitle: Text(formatCurrency(stats.thisMonth.sales)),
                trailing: Text('Rata-rata: ${formatCurrency(stats.thisMonth.averagePerDay)}/hari'),
              ),
            ),

            // Alerts
            if (stats.alerts.lowStockCount > 0)
              Card(
                color: Colors.orange[100],
                child: ListTile(
                  leading: Icon(Icons.warning, color: Colors.orange),
                  title: Text('Stok Menipis'),
                  trailing: Text('${stats.alerts.lowStockCount} produk'),
                ),
              ),

            // Top Products
            Card(
              child: Column(
                children: [
                  ListTile(title: Text('Produk Terlaris Hari Ini')),
                  ...stats.topProducts.map((p) => ListTile(
                    title: Text(p.productName),
                    subtitle: Text('${p.quantitySold} terjual'),
                    trailing: Text(formatCurrency(p.revenue)),
                  )),
                ],
              ),
            ),
          ],
        );
      },
    );
  }
}
```

---

## 📈 Sales Report

### Get Daily Sales Report
```http
POST /api/get-daily-sales-report
Headers: Authorization: Bearer {token}

Body:
{
  "business_id": 1,
  "date": "2025-11-30"
}

Response:
{
  "date": "2025-11-30",
  "totalRecipts": 45,
  "totalSales": 2500000,
  "averageSales": 55555.56,
  "totalCost": 1800000,
  "totalPrice": 2500000,
  "totalProfit": 700000,
  "sales": [...]
}
```

---

## 🎯 Offline Mode Support

### Offline Strategy

**1. Cache Products Locally (sqflite)**
```dart
class LocalDatabase {
  // On app start or when online
  Future<void> cacheProducts() async {
    final products = await apiService.getProducts();
    await db.insert('products', products);
  }

  // Use local data when offline
  Future<List<Product>> getProducts() async {
    return await db.query('products');
  }
}
```

**2. Save Orders Offline**
```dart
Future<void> createOrder(Order order) async {
  final isOnline = await checkConnectivity();

  if (isOnline) {
    // Send to API
    try {
      final response = await apiService.createOrder(order);
      return response;
    } catch (e) {
      // If fails, save offline
      await savePendingOrder(order);
    }
  } else {
    // Save to local database
    await savePendingOrder(order);
  }
}

Future<void> savePendingOrder(Order order) async {
  // Generate local ID
  final localId = Uuid().v4();

  await db.insert('pending_orders', {
    'local_id': localId,
    'data': jsonEncode(order.toJson()),
    'created_at': DateTime.now().toIso8601String(),
  });

  // Show offline indicator
  showSnackbar('Pesanan disimpan offline. Akan di-sync saat online.');
}
```

**3. Sync When Online**
```dart
Future<void> syncPendingOrders() async {
  final pendingOrders = await db.query('pending_orders');

  for (var orderData in pendingOrders) {
    try {
      final order = Order.fromJson(jsonDecode(orderData['data']));

      // Send to API
      final response = await apiService.createOrder(order);

      // If success, delete from local
      await db.delete('pending_orders',
        where: 'local_id = ?',
        whereArgs: [orderData['local_id']]);

    } catch (e) {
      // Keep in queue, try again later
      print('Sync failed for order ${orderData['local_id']}');
    }
  }
}
```

---

## 🔐 Headers for All Requests

```dart
final headers = {
  'Accept': 'application/json',
  'Content-Type': 'application/json',
  'Authorization': 'Bearer ${authToken}',
};

// For multipart (image upload)
final multipartHeaders = {
  'Accept': 'application/json',
  'Authorization': 'Bearer ${authToken}',
  // Don't set Content-Type for multipart, Dio/http will set it
};
```

---

## 🚨 Error Handling

```dart
class ApiService {
  Future<T> handleRequest<T>(Future<Response> request) async {
    try {
      final response = await request;

      if (response.statusCode == 200 || response.statusCode == 201) {
        return response.data;
      }

      throw ApiException(
        message: response.data['message'] ?? 'Request failed',
        statusCode: response.statusCode,
      );

    } on DioError catch (e) {
      if (e.type == DioErrorType.connectionTimeout) {
        throw ApiException(message: 'Koneksi timeout');
      }

      if (e.type == DioErrorType.connectionError) {
        throw ApiException(message: 'Tidak ada koneksi internet');
      }

      if (e.response?.statusCode == 401) {
        // Logout user
        await authService.logout();
        throw ApiException(message: 'Sesi berakhir, silakan login kembali');
      }

      if (e.response?.statusCode == 422) {
        // Validation errors
        final errors = e.response?.data['errors'];
        throw ValidationException(errors: errors);
      }

      throw ApiException(
        message: e.response?.data['message'] ?? 'Terjadi kesalahan',
        statusCode: e.response?.statusCode,
      );
    }
  }
}
```

---

## ✅ Testing Checklist

Before Flutter integration, test these endpoints:

**Auth:**
- [ ] POST /api/login
- [ ] GET /api/me
- [ ] POST /api/logout

**Products:**
- [ ] GET /api/get-products
- [ ] GET /api/get-categories

**Orders:**
- [ ] POST /api/add-order
- [ ] GET /api/get-orders
- [ ] GET /api/get-order/{id}

**Receipt:**
- [ ] GET /api/receipts/{orderId}

**Dashboard:**
- [ ] GET /api/dashboard

---

## 🎬 Demo Flow Testing

**Complete POS flow (60 seconds):**

1. ✅ Login → GET /api/me
2. ✅ Dashboard → GET /api/dashboard
3. ✅ Product List → GET /api/get-products
4. ✅ Create Order → POST /api/add-order
5. ✅ Get Receipt → GET /api/receipts/{orderId}
6. ✅ Print Receipt → Bluetooth printer

---

## 📞 Support

**Issues?** Check:
1. Server running: `php artisan serve`
2. Token valid: Check Authorization header
3. Database seeded: `php artisan migrate:fresh --seed`
4. Storage linked: `php artisan storage:link`

**Test Credentials:**
- Email: `cashier1@tokomajujaya.com`
- Password: `password`

---

**Ready for Flutter integration! 🚀**

All TIER 1 & TIER 2 APIs are complete and tested!
