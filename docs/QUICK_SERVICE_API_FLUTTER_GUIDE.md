# Quick Service API - Flutter Integration Guide

## 📱 Overview
Panduan lengkap untuk mengintegrasikan Quick Service API (Dine In & Take Away) ke aplikasi Flutter.

**Base URL:** `https://your-api-domain.com/api`

**Status Implementasi:** ✅ **100% Complete - Ready for Integration**

---

## 🔐 Authentication

Semua endpoint memerlukan Bearer Token authentication:

```dart
final dio = Dio(BaseOptions(
  baseUrl: 'https://your-api-domain.com/api',
  headers: {
    'Authorization': 'Bearer ${your_access_token}',
    'Accept': 'application/json',
  },
));
```

---

## 📋 Table of Contents

1. [Data Models](#-data-models)
2. [API Endpoints](#-api-endpoints)
3. [Usage Examples](#-usage-examples)
4. [Error Handling](#-error-handling)
5. [Best Practices](#-best-practices)

---

## 📦 Data Models

### QuickServiceOrder Model

```dart
class QuickServiceOrder {
  final int id;
  final String orderNumber;
  final String type; // "DINE IN" or "TAKE AWAY"
  final String status; // "draft", "completed", "cancelled"
  final int pax;
  final String? tableNumber;
  final String? customerName;
  final String? notes;
  final int itemsCount;
  final double subtotal;
  final double tax;
  final double taxPercentage;
  final double serviceCharge;
  final double serviceChargePercentage;
  final double discount;
  final double discountPercentage;
  final double total;
  final String? paymentMethod;
  final double? paidAmount;
  final double? change;
  final String? paymentStatus;
  final String? invoiceNumber;
  final DateTime createdAt;
  final DateTime updatedAt;
  final DateTime? savedAt;
  final DateTime? cancelledAt;
  final String? cancelReason;
  final Cashier cashier;
  final List<QuickServiceOrderItem> items;

  QuickServiceOrder({
    required this.id,
    required this.orderNumber,
    required this.type,
    required this.status,
    required this.pax,
    this.tableNumber,
    this.customerName,
    this.notes,
    required this.itemsCount,
    required this.subtotal,
    required this.tax,
    required this.taxPercentage,
    required this.serviceCharge,
    required this.serviceChargePercentage,
    required this.discount,
    required this.discountPercentage,
    required this.total,
    this.paymentMethod,
    this.paidAmount,
    this.change,
    this.paymentStatus,
    this.invoiceNumber,
    required this.createdAt,
    required this.updatedAt,
    this.savedAt,
    this.cancelledAt,
    this.cancelReason,
    required this.cashier,
    required this.items,
  });

  factory QuickServiceOrder.fromJson(Map<String, dynamic> json) {
    return QuickServiceOrder(
      id: json['id'],
      orderNumber: json['order_number'],
      type: json['type'],
      status: json['status'],
      pax: json['pax'],
      tableNumber: json['table_number'],
      customerName: json['customer_name'],
      notes: json['notes'],
      itemsCount: json['items_count'],
      subtotal: double.parse(json['subtotal'].toString()),
      tax: double.parse(json['tax'].toString()),
      taxPercentage: double.parse(json['tax_percentage'].toString()),
      serviceCharge: double.parse(json['service_charge'].toString()),
      serviceChargePercentage: double.parse(json['service_charge_percentage'].toString()),
      discount: double.parse(json['discount'].toString()),
      discountPercentage: double.parse(json['discount_percentage'].toString()),
      total: double.parse(json['total'].toString()),
      paymentMethod: json['payment_method'],
      paidAmount: json['paid_amount'] != null ? double.parse(json['paid_amount'].toString()) : null,
      change: json['change'] != null ? double.parse(json['change'].toString()) : null,
      paymentStatus: json['payment_status'],
      invoiceNumber: json['invoice_number'],
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
      savedAt: json['saved_at'] != null ? DateTime.parse(json['saved_at']) : null,
      cancelledAt: json['cancelled_at'] != null ? DateTime.parse(json['cancelled_at']) : null,
      cancelReason: json['cancel_reason'],
      cashier: Cashier.fromJson(json['cashier']),
      items: (json['items'] as List).map((item) => QuickServiceOrderItem.fromJson(item)).toList(),
    );
  }
}
```

### QuickServiceOrderItem Model

```dart
class QuickServiceOrderItem {
  final int id;
  final int productId;
  final String productName;
  final String? productImage;
  final int quantity;
  final double price;
  final double subtotal;
  final String? notes;

  QuickServiceOrderItem({
    required this.id,
    required this.productId,
    required this.productName,
    this.productImage,
    required this.quantity,
    required this.price,
    required this.subtotal,
    this.notes,
  });

  factory QuickServiceOrderItem.fromJson(Map<String, dynamic> json) {
    return QuickServiceOrderItem(
      id: json['id'],
      productId: json['product_id'],
      productName: json['product_name'],
      productImage: json['product_image'],
      quantity: json['quantity'],
      price: double.parse(json['price'].toString()),
      subtotal: double.parse(json['subtotal'].toString()),
      notes: json['notes'],
    );
  }
}
```

### Cashier Model

```dart
class Cashier {
  final int id;
  final String name;
  final String? email;

  Cashier({
    required this.id,
    required this.name,
    this.email,
  });

  factory Cashier.fromJson(Map<String, dynamic> json) {
    return Cashier(
      id: json['id'],
      name: json['name'],
      email: json['email'],
    );
  }
}
```

---

## 🔌 API Endpoints

### 1. Get Orders List

**GET** `/api/quick-service/orders`

Mengambil daftar order dengan pagination dan filter.

**Query Parameters:**
```dart
{
  'status': 'draft', // optional: 'draft', 'completed', 'cancelled', 'all'
  'mode': 'dine_in', // optional: 'dine_in', 'take_away', 'all'
  'date': '2025-12-08', // optional: 'YYYY-MM-DD'
  'page': 1, // optional
  'per_page': 20, // optional
}
```

**Response:**
```dart
{
  "success": true,
  "message": "Orders retrieved successfully",
  "data": {
    "orders": [...],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 50,
      "total_pages": 3
    },
    "summary": {
      "total_draft": 10,
      "total_completed": 40,
      "total_dine_in": 30,
      "total_take_away": 20
    }
  }
}
```

---

### 2. Create New Order

**POST** `/api/quick-service/orders`

Membuat order baru dengan status draft.

**Request Body:**
```dart
{
  "type": "DINE IN", // required: "DINE IN" or "TAKE AWAY"
  "pax": 4, // required: jumlah orang
  "table_number": "A12", // optional
  "customer_name": "John Doe", // optional
  "notes": "" // optional
}
```

**Response (201):**
```dart
{
  "success": true,
  "message": "Order created successfully",
  "data": {
    "order": {
      "id": 1,
      "order_number": "QS-001-2025",
      "type": "DINE IN",
      "status": "draft",
      "pax": 4,
      ...
    }
  }
}
```

---

### 3. Get Order Detail

**GET** `/api/quick-service/orders/{order_id}`

Mengambil detail order beserta items.

**Response:**
```dart
{
  "success": true,
  "message": "Order detail retrieved successfully",
  "data": {
    "order": {
      "id": 1,
      "order_number": "QS-001-2025",
      "items": [...],
      "subtotal": 75000,
      "tax": 7500,
      "service_charge": 3750,
      "total": 86250,
      ...
    }
  }
}
```

---

### 4. Update Order Info

**PUT** `/api/quick-service/orders/{order_id}`

Update informasi order (hanya untuk status draft).

**Request Body:**
```dart
{
  "pax": 5, // optional
  "table_number": "B15", // optional
  "customer_name": "Jane Doe", // optional
  "notes": "Extra pedas" // optional
}
```

**Response:**
```dart
{
  "success": true,
  "message": "Order updated successfully",
  "data": {
    "order": {
      "id": 1,
      "pax": 5,
      "table_number": "B15",
      ...
    }
  }
}
```

---

### 5. Add Item to Order

**POST** `/api/quick-service/orders/{order_id}/items`

Menambah item ke order (hanya untuk status draft).

**Request Body:**
```dart
{
  "product_id": 10, // required
  "quantity": 2, // required
  "notes": "Tanpa timun" // optional
}
```

**Response (201):**
```dart
{
  "success": true,
  "message": "Item added to order successfully",
  "data": {
    "order_item": {
      "id": 1,
      "product_id": 10,
      "product_name": "Nasi Goreng Kampung",
      "quantity": 2,
      "price": 25000,
      "subtotal": 50000,
      "notes": "Tanpa timun"
    },
    "order": {
      "id": 1,
      "items_count": 3,
      "subtotal": 75000,
      "tax": 7500,
      "service_charge": 3750,
      "total": 86250
    }
  }
}
```

---

### 6. Update Order Item

**PUT** `/api/quick-service/orders/{order_id}/items/{item_id}`

Update quantity atau notes item (hanya untuk status draft).

**Request Body:**
```dart
{
  "quantity": 3, // required
  "notes": "Extra pedas" // optional
}
```

**Response:**
```dart
{
  "success": true,
  "message": "Order item updated successfully",
  "data": {
    "order_item": {
      "id": 1,
      "quantity": 3,
      "subtotal": 75000,
      "notes": "Extra pedas"
    },
    "order": {
      "subtotal": 100000,
      "tax": 10000,
      "service_charge": 5000,
      "total": 115000
    }
  }
}
```

---

### 7. Delete Order Item

**DELETE** `/api/quick-service/orders/{order_id}/items/{item_id}`

Hapus item dari order (hanya untuk status draft).

**Response:**
```dart
{
  "success": true,
  "message": "Order item removed successfully",
  "data": {
    "order": {
      "id": 1,
      "items_count": 2,
      "subtotal": 50000,
      "tax": 5000,
      "service_charge": 2500,
      "total": 57500
    }
  }
}
```

---

### 8. Save Order as Draft

**POST** `/api/quick-service/orders/{order_id}/save`

Simpan order sebagai draft (belum bayar).

**Request Body:**
```dart
{
  "notes": "Order untuk meja VIP" // optional
}
```

**Response:**
```dart
{
  "success": true,
  "message": "Order saved successfully",
  "data": {
    "order": {
      "id": 1,
      "order_number": "QS-001-2025",
      "status": "draft",
      "saved_at": "2025-12-08T10:15:00Z"
    }
  }
}
```

---

### 9. Process Payment

**POST** `/api/quick-service/orders/{order_id}/payments`

Proses pembayaran order (mengubah status menjadi completed).

**Request Body:**
```dart
{
  "payment_method": "CASH", // required: "CASH", "CREDIT_CARD", "DEBIT_CARD", "EWALLET", "MEMBER_DEPOSIT"
  "amount_paid": 100000, // required
  "notes": "" // optional
}
```

**Response (201):**
```dart
{
  "success": true,
  "message": "Payment processed successfully",
  "data": {
    "payment": {
      "id": 1,
      "order_id": 1,
      "payment_method": "CASH",
      "amount_paid": 100000,
      "amount_due": 86250,
      "change": 13750,
      "payment_date": "2025-12-08T10:20:00Z"
    },
    "order": {
      "id": 1,
      "order_number": "QS-001-2025",
      "invoice_number": "NSGR0220251208001",
      "status": "completed",
      "total": 86250,
      "paid_amount": 100000,
      "change": 13750,
      "payment_status": "paid"
    }
  }
}
```

**Important:**
- Stock akan otomatis berkurang untuk produk yang `is_stock_managed = true`
- Invoice number akan otomatis di-generate
- Order status berubah dari `draft` ke `completed`

---

### 10. Get Receipt Data

**GET** `/api/quick-service/orders/{order_id}/receipt`

Mengambil data receipt untuk dicetak/ditampilkan (hanya untuk status completed).

**Response:**
```dart
{
  "success": true,
  "message": "Receipt data retrieved successfully",
  "data": {
    "receipt": {
      "order_number": "QS-001-2025",
      "invoice_number": "NSGR0220251208001",
      "type": "DINE IN",
      "pax": 4,
      "table_number": "A12",
      "customer_name": "John Doe",
      "date": "2025-12-08",
      "time": "10:20:00",
      "cashier": "DEWA ESB",
      "items": [
        {
          "quantity": 2,
          "name": "Nasi Goreng Kampung",
          "price": 25000,
          "subtotal": 50000
        }
      ],
      "subtotal": 75000,
      "tax": {
        "name": "PB1",
        "percentage": 10,
        "amount": 7500
      },
      "service_charge": {
        "name": "Service Charge",
        "percentage": 5,
        "amount": 3750
      },
      "discount": 0,
      "total": 86250,
      "payment_method": "CASH",
      "amount_paid": 100000,
      "change": 13750,
      "outlet": {
        "name": "[STG-EXT] NASGOR HO",
        "address": "Jl. Raya No. 123",
        "phone": "021-12345678"
      },
      "footer_text": "Terima kasih atas kunjungan Anda"
    }
  }
}
```

---

### 11. Print Kitchen Order

**POST** `/api/quick-service/orders/{order_id}/print-kitchen`

Trigger print struk dapur.

**Request Body:**
```dart
{
  "printer_id": 2, // optional
  "copies": 1 // optional, default 1
}
```

**Response:**
```dart
{
  "success": true,
  "message": "Kitchen order printed successfully",
  "data": {
    "print_job_id": "PRINT-675568f4e1234",
    "printed_at": "2025-12-08T10:15:00Z",
    "printer": {
      "id": 2,
      "name": "Kitchen Printer 1",
      "location": "Dapur Utama"
    }
  }
}
```

---

### 12. Print Receipt

**POST** `/api/quick-service/orders/{order_id}/print-receipt`

Trigger print nota customer (hanya untuk status completed).

**Request Body:**
```dart
{
  "printer_id": 1, // optional
  "copies": 1, // optional, default 1
  "send_email": false, // optional
  "email": "customer@example.com" // required if send_email = true
}
```

**Response:**
```dart
{
  "success": true,
  "message": "Receipt printed successfully",
  "data": {
    "print_job_id": "PRINT-675568f4e5678",
    "printed_at": "2025-12-08T10:20:00Z",
    "email_sent": false
  }
}
```

---

### 13. Cancel Order

**POST** `/api/quick-service/orders/{order_id}/cancel`

Cancel order (untuk order completed atau draft).

**Request Body:**
```dart
{
  "reason": "Customer request" // required
}
```

**Response:**
```dart
{
  "success": true,
  "message": "Order cancelled successfully",
  "data": {
    "order": {
      "id": 1,
      "status": "cancelled",
      "cancelled_at": "2025-12-08T10:25:00Z",
      "cancel_reason": "Customer request"
    }
  }
}
```

---

### 14. Delete Order

**DELETE** `/api/quick-service/orders/{order_id}`

Hapus order (hanya untuk status draft).

**Response:**
```dart
{
  "success": true,
  "message": "Order deleted successfully"
}
```

**Error Response (400):**
```dart
{
  "success": false,
  "message": "Cannot delete completed order",
  "errors": {
    "order": ["Only draft orders can be deleted"]
  }
}
```

---

### 15. Get Statistics

**GET** `/api/quick-service/statistics`

Mengambil statistik untuk dashboard.

**Query Parameters:**
```dart
{
  'date_from': '2025-12-01', // optional: 'YYYY-MM-DD'
  'date_to': '2025-12-08', // optional: 'YYYY-MM-DD'
  'mode': 'all', // optional: 'dine_in', 'take_away', 'all'
}
```

**Response:**
```dart
{
  "success": true,
  "message": "Statistics retrieved successfully",
  "data": {
    "today": {
      "total_orders": 25,
      "total_revenue": 2500000,
      "dine_in": {
        "orders": 15,
        "revenue": 1500000
      },
      "take_away": {
        "orders": 10,
        "revenue": 1000000
      },
      "avg_order_value": 100000,
      "avg_pax": 3.5
    },
    "top_products": [
      {
        "product_id": 10,
        "product_name": "Nasi Goreng Kampung",
        "quantity_sold": 50,
        "revenue": 1250000
      }
    ]
  }
}
```

---

## 💡 Usage Examples

### Example 1: Complete Order Flow

```dart
class QuickServiceProvider extends ChangeNotifier {
  final Dio _dio;

  QuickServiceProvider(this._dio);

  // Step 1: Create new order
  Future<QuickServiceOrder> createOrder({
    required String type,
    required int pax,
    String? tableNumber,
    String? customerName,
  }) async {
    try {
      final response = await _dio.post(
        '/quick-service/orders',
        data: {
          'type': type,
          'pax': pax,
          'table_number': tableNumber,
          'customer_name': customerName,
        },
      );

      return QuickServiceOrder.fromJson(response.data['data']['order']);
    } catch (e) {
      throw _handleError(e);
    }
  }

  // Step 2: Add items to order
  Future<void> addItem({
    required int orderId,
    required int productId,
    required int quantity,
    String? notes,
  }) async {
    try {
      await _dio.post(
        '/quick-service/orders/$orderId/items',
        data: {
          'product_id': productId,
          'quantity': quantity,
          'notes': notes,
        },
      );
      notifyListeners();
    } catch (e) {
      throw _handleError(e);
    }
  }

  // Step 3: Update item quantity
  Future<void> updateItem({
    required int orderId,
    required int itemId,
    required int quantity,
    String? notes,
  }) async {
    try {
      await _dio.put(
        '/quick-service/orders/$orderId/items/$itemId',
        data: {
          'quantity': quantity,
          'notes': notes,
        },
      );
      notifyListeners();
    } catch (e) {
      throw _handleError(e);
    }
  }

  // Step 4: Save as draft
  Future<void> saveDraft(int orderId, {String? notes}) async {
    try {
      await _dio.post(
        '/quick-service/orders/$orderId/save',
        data: {'notes': notes},
      );
      notifyListeners();
    } catch (e) {
      throw _handleError(e);
    }
  }

  // Step 5: Process payment
  Future<Map<String, dynamic>> processPayment({
    required int orderId,
    required String paymentMethod,
    required double amountPaid,
    String? notes,
  }) async {
    try {
      final response = await _dio.post(
        '/quick-service/orders/$orderId/payments',
        data: {
          'payment_method': paymentMethod,
          'amount_paid': amountPaid,
          'notes': notes,
        },
      );

      notifyListeners();
      return response.data['data'];
    } catch (e) {
      throw _handleError(e);
    }
  }

  // Step 6: Get receipt data
  Future<Map<String, dynamic>> getReceipt(int orderId) async {
    try {
      final response = await _dio.get(
        '/quick-service/orders/$orderId/receipt',
      );

      return response.data['data']['receipt'];
    } catch (e) {
      throw _handleError(e);
    }
  }

  // Step 7: Print receipt
  Future<void> printReceipt(int orderId) async {
    try {
      await _dio.post(
        '/quick-service/orders/$orderId/print-receipt',
        data: {'copies': 1},
      );
    } catch (e) {
      throw _handleError(e);
    }
  }

  String _handleError(dynamic error) {
    if (error is DioException) {
      if (error.response?.data != null) {
        return error.response!.data['message'] ?? 'Unknown error';
      }
      return error.message ?? 'Network error';
    }
    return error.toString();
  }
}
```

### Example 2: Order List with Filters

```dart
class OrderListScreen extends StatefulWidget {
  @override
  _OrderListScreenState createState() => _OrderListScreenState();
}

class _OrderListScreenState extends State<OrderListScreen> {
  final Dio _dio = Dio();
  List<QuickServiceOrder> _orders = [];
  String _selectedStatus = 'all';
  String _selectedMode = 'all';
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _loadOrders();
  }

  Future<void> _loadOrders() async {
    setState(() => _isLoading = true);

    try {
      final response = await _dio.get(
        '/quick-service/orders',
        queryParameters: {
          'status': _selectedStatus,
          'mode': _selectedMode,
          'per_page': 20,
        },
      );

      final List ordersJson = response.data['data']['orders'];
      setState(() {
        _orders = ordersJson
            .map((json) => QuickServiceOrder.fromJson(json))
            .toList();
      });
    } catch (e) {
      // Handle error
      print('Error loading orders: $e');
    } finally {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Quick Service Orders')),
      body: Column(
        children: [
          // Filter chips
          Row(
            children: [
              ChoiceChip(
                label: Text('All'),
                selected: _selectedStatus == 'all',
                onSelected: (selected) {
                  setState(() => _selectedStatus = 'all');
                  _loadOrders();
                },
              ),
              ChoiceChip(
                label: Text('Draft'),
                selected: _selectedStatus == 'draft',
                onSelected: (selected) {
                  setState(() => _selectedStatus = 'draft');
                  _loadOrders();
                },
              ),
              ChoiceChip(
                label: Text('Completed'),
                selected: _selectedStatus == 'completed',
                onSelected: (selected) {
                  setState(() => _selectedStatus = 'completed');
                  _loadOrders();
                },
              ),
            ],
          ),

          // Orders list
          Expanded(
            child: _isLoading
                ? Center(child: CircularProgressIndicator())
                : ListView.builder(
                    itemCount: _orders.length,
                    itemBuilder: (context, index) {
                      final order = _orders[index];
                      return OrderCard(order: order);
                    },
                  ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          // Navigate to create order screen
        },
        child: Icon(Icons.add),
      ),
    );
  }
}
```

### Example 3: Statistics Dashboard

```dart
class QuickServiceDashboard extends StatelessWidget {
  final Dio _dio = Dio();

  Future<Map<String, dynamic>> _loadStatistics() async {
    final response = await _dio.get('/quick-service/statistics');
    return response.data['data'];
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<Map<String, dynamic>>(
      future: _loadStatistics(),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return Center(child: CircularProgressIndicator());
        }

        if (snapshot.hasError) {
          return Center(child: Text('Error: ${snapshot.error}'));
        }

        final stats = snapshot.data!['today'];
        final topProducts = snapshot.data!['top_products'] as List;

        return SingleChildScrollView(
          padding: EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Today\'s Summary', style: Theme.of(context).textTheme.headline6),
              SizedBox(height: 16),

              // Summary cards
              Row(
                children: [
                  Expanded(
                    child: _SummaryCard(
                      title: 'Total Orders',
                      value: stats['total_orders'].toString(),
                      icon: Icons.receipt,
                    ),
                  ),
                  SizedBox(width: 16),
                  Expanded(
                    child: _SummaryCard(
                      title: 'Revenue',
                      value: 'Rp ${stats['total_revenue']}',
                      icon: Icons.attach_money,
                    ),
                  ),
                ],
              ),

              SizedBox(height: 16),

              Row(
                children: [
                  Expanded(
                    child: _SummaryCard(
                      title: 'Dine In',
                      value: '${stats['dine_in']['orders']} orders',
                      subtitle: 'Rp ${stats['dine_in']['revenue']}',
                      icon: Icons.restaurant,
                    ),
                  ),
                  SizedBox(width: 16),
                  Expanded(
                    child: _SummaryCard(
                      title: 'Take Away',
                      value: '${stats['take_away']['orders']} orders',
                      subtitle: 'Rp ${stats['take_away']['revenue']}',
                      icon: Icons.takeout_dining,
                    ),
                  ),
                ],
              ),

              SizedBox(height: 24),

              // Top products
              Text('Top Products', style: Theme.of(context).textTheme.headline6),
              SizedBox(height: 16),

              ...topProducts.map((product) {
                return ListTile(
                  title: Text(product['product_name']),
                  subtitle: Text('Sold: ${product['quantity_sold']}'),
                  trailing: Text('Rp ${product['revenue']}'),
                );
              }).toList(),
            ],
          ),
        );
      },
    );
  }
}

class _SummaryCard extends StatelessWidget {
  final String title;
  final String value;
  final String? subtitle;
  final IconData icon;

  const _SummaryCard({
    required this.title,
    required this.value,
    this.subtitle,
    required this.icon,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      child: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(icon, size: 24),
                SizedBox(width: 8),
                Text(title, style: TextStyle(fontSize: 14)),
              ],
            ),
            SizedBox(height: 8),
            Text(value, style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
            if (subtitle != null) ...[
              SizedBox(height: 4),
              Text(subtitle!, style: TextStyle(fontSize: 12, color: Colors.grey)),
            ],
          ],
        ),
      ),
    );
  }
}
```

---

## ⚠️ Error Handling

### Error Response Format

```dart
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field_name": [
      "Validation error message"
    ]
  }
}
```

### Common HTTP Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | Success | Request berhasil |
| 201 | Created | Resource berhasil dibuat |
| 400 | Bad Request | Validation error atau business logic error |
| 401 | Unauthorized | Token invalid/expired |
| 403 | Forbidden | Tidak memiliki akses |
| 404 | Not Found | Resource tidak ditemukan |
| 422 | Unprocessable Entity | Validation error |
| 500 | Internal Server Error | Server error |

### Error Handling Example

```dart
Future<void> _handleApiCall(Future<void> Function() apiCall) async {
  try {
    await apiCall();
  } on DioException catch (e) {
    if (e.response != null) {
      final statusCode = e.response!.statusCode;
      final data = e.response!.data;

      switch (statusCode) {
        case 401:
          // Redirect to login
          _handleUnauthorized();
          break;
        case 400:
        case 422:
          // Show validation errors
          final errors = data['errors'] as Map<String, dynamic>;
          _showValidationErrors(errors);
          break;
        case 404:
          _showError('Resource not found');
          break;
        default:
          _showError(data['message'] ?? 'Unknown error');
      }
    } else {
      _showError('Network error: ${e.message}');
    }
  } catch (e) {
    _showError('Unexpected error: $e');
  }
}

void _showError(String message) {
  // Show snackbar or dialog
  ScaffoldMessenger.of(context).showSnackBar(
    SnackBar(content: Text(message)),
  );
}

void _showValidationErrors(Map<String, dynamic> errors) {
  final messages = errors.values
      .expand((errorList) => errorList as List)
      .join('\n');
  _showError(messages);
}

void _handleUnauthorized() {
  // Clear token and redirect to login
  Navigator.of(context).pushReplacementNamed('/login');
}
```

---

## 📝 Best Practices

### 1. State Management

Gunakan state management yang sesuai (Provider, Riverpod, BLoC, dll):

```dart
// Using Riverpod
final quickServiceProvider = StateNotifierProvider<QuickServiceNotifier, QuickServiceState>((ref) {
  return QuickServiceNotifier(ref.read(dioProvider));
});

class QuickServiceNotifier extends StateNotifier<QuickServiceState> {
  final Dio _dio;

  QuickServiceNotifier(this._dio) : super(QuickServiceState.initial());

  Future<void> createOrder({...}) async {
    state = state.copyWith(isLoading: true);
    try {
      // API call
      state = state.copyWith(isLoading: false, order: newOrder);
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }
}
```

### 2. Caching

Implementasikan caching untuk mengurangi network calls:

```dart
class QuickServiceRepository {
  final Dio _dio;
  final Map<int, QuickServiceOrder> _orderCache = {};

  Future<QuickServiceOrder> getOrder(int id, {bool forceRefresh = false}) async {
    if (!forceRefresh && _orderCache.containsKey(id)) {
      return _orderCache[id]!;
    }

    final response = await _dio.get('/quick-service/orders/$id');
    final order = QuickServiceOrder.fromJson(response.data['data']['order']);
    _orderCache[id] = order;
    return order;
  }

  void clearCache() {
    _orderCache.clear();
  }
}
```

### 3. Optimistic Updates

Update UI segera, rollback jika API call gagal:

```dart
Future<void> updateItemQuantity(int orderId, int itemId, int newQuantity) async {
  final oldOrder = _currentOrder;

  // Optimistic update
  _updateOrderItemLocally(itemId, newQuantity);
  notifyListeners();

  try {
    await _dio.put(
      '/quick-service/orders/$orderId/items/$itemId',
      data: {'quantity': newQuantity},
    );
  } catch (e) {
    // Rollback on error
    _currentOrder = oldOrder;
    notifyListeners();
    rethrow;
  }
}
```

### 4. Pagination

Implementasikan infinite scroll untuk list orders:

```dart
class OrderListNotifier extends StateNotifier<OrderListState> {
  int _currentPage = 1;
  bool _hasMore = true;

  Future<void> loadMore() async {
    if (!_hasMore || state.isLoading) return;

    state = state.copyWith(isLoading: true);

    try {
      final response = await _dio.get(
        '/quick-service/orders',
        queryParameters: {'page': _currentPage, 'per_page': 20},
      );

      final newOrders = (response.data['data']['orders'] as List)
          .map((json) => QuickServiceOrder.fromJson(json))
          .toList();

      _currentPage++;
      _hasMore = newOrders.length == 20;

      state = state.copyWith(
        orders: [...state.orders, ...newOrders],
        isLoading: false,
      );
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }
}
```

### 5. Real-time Updates (Optional)

Jika menggunakan WebSocket untuk real-time updates:

```dart
class QuickServiceWebSocket {
  late IO.Socket _socket;

  void connect(String token) {
    _socket = IO.io('https://your-api-domain.com', <String, dynamic>{
      'transports': ['websocket'],
      'extraHeaders': {'Authorization': 'Bearer $token'},
    });

    _socket.on('quick-service.order.created', (data) {
      // Handle new order
      _handleNewOrder(data);
    });

    _socket.on('quick-service.order.updated', (data) {
      // Handle order update
      _handleOrderUpdate(data);
    });

    _socket.on('quick-service.order.completed', (data) {
      // Handle order completion
      _handleOrderCompleted(data);
    });
  }

  void disconnect() {
    _socket.disconnect();
  }
}
```

### 6. Loading States

Berikan feedback visual untuk setiap action:

```dart
class OrderScreen extends StatefulWidget {
  @override
  _OrderScreenState createState() => _OrderScreenState();
}

class _OrderScreenState extends State<OrderScreen> {
  bool _isProcessing = false;

  Future<void> _processPayment() async {
    setState(() => _isProcessing = true);

    try {
      await _quickServiceProvider.processPayment(...);
      _showSuccess('Payment successful');
      Navigator.pop(context);
    } catch (e) {
      _showError(e.toString());
    } finally {
      setState(() => _isProcessing = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Stack(
        children: [
          // Your content

          if (_isProcessing)
            Container(
              color: Colors.black54,
              child: Center(
                child: CircularProgressIndicator(),
              ),
            ),
        ],
      ),
    );
  }
}
```

---

## 🔄 Order Status Flow

```
draft -> completed -> (optional) cancelled
  ↓
deleted (only for draft)
```

**Rules:**
- Order bisa di-update hanya saat status `draft`
- Items bisa ditambah/edit/hapus hanya saat status `draft`
- Order bisa dihapus hanya saat status `draft`
- Order bisa di-cancel kapan saja (draft atau completed)
- Payment hanya bisa diproses untuk status `draft`
- Receipt hanya tersedia untuk status `completed`

---

## 💰 Payment Methods

| Value | Description |
|-------|-------------|
| `CASH` | Tunai |
| `CREDIT_CARD` | Kartu Kredit |
| `DEBIT_CARD` | Kartu Debit |
| `EWALLET` | Dompet Digital (GoPay, OVO, dll) |
| `MEMBER_DEPOSIT` | Deposit Member |

---

## 🍽️ Order Types

| Value | Description |
|-------|-------------|
| `DINE IN` | Makan di tempat |
| `TAKE AWAY` | Bungkus |

---

## 🚀 Implementation Checklist

### Phase 1 (Must Have - High Priority) ✅
- [x] Create order
- [x] Add/Remove/Update items
- [x] Save draft
- [x] Process payment
- [x] Get orders list
- [x] Get order detail

### Phase 2 (Should Have - Medium Priority) ✅
- [x] Update order info
- [x] Print kitchen order
- [x] Print receipt
- [x] Get receipt data

### Phase 3 (Nice to Have - Low Priority) ✅
- [x] Cancel order
- [x] Delete order
- [x] Statistics
- [ ] WebSocket events (backend belum implement)
- [ ] Email receipt (backend belum implement)

---

## 📞 Support

Jika ada pertanyaan atau issue:
1. Check dokumentasi API requirements: `docs/QUICK_SERVICE_API_REQUIREMENTS.md`
2. Test endpoint menggunakan Postman/Thunder Client
3. Contact backend team untuk troubleshooting

---

## 📄 Changelog

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | 2025-12-08 | Initial documentation - All endpoints implemented and ready |

---

**Status:** ✅ **Production Ready**

Semua 15 endpoint telah diimplementasikan dengan lengkap dan siap untuk diintegrasikan ke aplikasi Flutter.
