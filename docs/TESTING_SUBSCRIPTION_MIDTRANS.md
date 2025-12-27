# Panduan Testing Subscription + Midtrans
**Complete Testing Guide for Web & Mobile Flutter**

---

## 📑 Table of Contents
1. [Setup Midtrans Account](#step-1-setup-midtrans-account-sandbox)
2. [Konfigurasi Laravel Backend](#step-2-konfigurasi-laravel-backend)
3. [Setup Database & Seeder](#step-3-setup-database--seeder)
4. [Setup Ngrok untuk Webhook](#step-4-setup-ngrok-untuk-webhook)
5. [Testing via Web (cURL/Postman)](#step-5-testing-via-web-curlpostman)
6. [Testing via Flutter Mobile](#step-6-testing-via-flutter-mobile)
7. [Cross-Platform Testing](#step-7-cross-platform-testing)
8. [Troubleshooting](#troubleshooting)

---

## STEP 1: Setup Midtrans Account (Sandbox)

### 1.1 Daftar Midtrans
```bash
1. Buka: https://dashboard.midtrans.com/register
2. Daftar akun baru (gratis)
3. Verifikasi email Anda
4. Login ke dashboard
```

### 1.2 Dapatkan API Keys
```bash
1. Toggle environment ke "SANDBOX" (pojok kanan atas)
2. Pergi ke: Settings → Access Keys
3. Copy credentials berikut:

   Server Key: SB-Mid-server-xxxxxxxxxxxxxxxxxxxxxxx
   Client Key: SB-Mid-client-xxxxxxxxxxxxxxxxxxxxxxx

⚠️ JANGAN GUNAKAN PRODUCTION untuk testing!
```

### 1.3 Screenshot Lokasi API Keys
```
Dashboard Midtrans → Settings (sidebar kiri) → Access Keys
┌─────────────────────────────────────────────┐
│ Sandbox Environment                         │
├─────────────────────────────────────────────┤
│ Server Key:                                 │
│ SB-Mid-server-xxxxxx [Copy]                │
│                                             │
│ Client Key:                                 │
│ SB-Mid-client-xxxxxx [Copy]                │
└─────────────────────────────────────────────┘
```

---

## STEP 2: Konfigurasi Laravel Backend

### 2.1 Update File `.env`

Buka file `.env` dan isi Midtrans credentials yang sudah Anda copy:

```env
# Midtrans Configuration (Sandbox Mode)
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxxxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxxxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

### 2.2 Verify Midtrans Package Installed

```bash
# Check if midtrans package exists
composer show midtrans/midtrans-php

# Jika belum ada, install:
composer require midtrans/midtrans-php
```

### 2.3 Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

---

## STEP 3: Setup Database & Seeder

### 3.1 Run Migrations

```bash
# Fresh migration (hati-hati: akan hapus semua data!)
php artisan migrate:fresh

# Atau hanya run migration subscription jika sudah ada data:
php artisan migrate
```

### 3.2 Seed Subscription Plans

```bash
php artisan db:seed --class=SubscriptionPlanSeeder
```

**Output yang diharapkan:**
```
Seeding: Database\Seeders\SubscriptionPlanSeeder
✓ Trial plan created
✓ Starter plan created
✓ Professional plan created
Seeded successfully
```

### 3.3 Verify Plans Created

```bash
# Check subscription plans
php artisan tinker
>>> SubscriptionPlan::all();

# Should show 3 plans:
# 1. Trial - FREE
# 2. Starter - Rp 99,000
# 3. Professional - Rp 249,000
```

---

## STEP 4: Setup Ngrok untuk Webhook

### 4.1 Install Ngrok

**macOS:**
```bash
brew install ngrok/ngrok/ngrok
```

**Linux:**
```bash
wget https://bin.equinox.io/c/bNyj1mQVY4c/ngrok-v3-stable-linux-amd64.tgz
sudo tar xvzf ngrok-v3-stable-linux-amd64.tgz -C /usr/local/bin
```

**Windows:**
Download dari https://ngrok.com/download

### 4.2 Start Laravel Server

```bash
# Start di port 8002 (sesuai APP_URL di .env)
php artisan serve --port=8002
```

### 4.3 Start Ngrok Tunnel

**Terminal baru:**
```bash
ngrok http 8002
```

**Output:**
```
ngrok

Session Status    online
Account           your-email@example.com
Version           3.0.0
Region            Asia Pacific (ap)
Latency           -
Web Interface     http://127.0.0.1:4040
Forwarding        https://a1b2-c3d4-e5f6.ngrok-free.app -> http://localhost:8002

Connections       ttl     opn     rt1     rt5     p50     p90
                  0       0       0.00    0.00    0.00    0.00
```

**Copy URL ngrok:** `https://a1b2-c3d4-e5f6.ngrok-free.app`

### 4.4 Configure Midtrans Webhook

```bash
1. Buka Midtrans Dashboard (Sandbox)
2. Pergi ke: Settings → Configuration
3. Set "Payment Notification URL":

   https://a1b2-c3d4-e5f6.ngrok-free.app/api/payment/webhook

4. Enable "HTTP Notification"
5. Save
```

### 4.5 Test Ngrok Connection

```bash
# Test dari browser atau curl
curl https://a1b2-c3d4-e5f6.ngrok-free.app/api/subscription/plans

# Should return JSON dengan 3 plans
```

---

## STEP 5: Testing via Web (cURL/Postman)

### 5.1 Register User Baru

**Request:**
```bash
curl -X POST http://127.0.0.1:8002/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "testuser@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "business_name": "Test Business POS",
    "address": "Jakarta Selatan"
  }'
```

**Response (200 OK):**
```json
{
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Test User",
      "email": "testuser@example.com",
      "business_id": 1
    },
    "business": {
      "id": 1,
      "name": "Test Business POS",
      "address": "Jakarta Selatan"
    },
    "token": "1|laravel_sanctum_xxxxxxxxxxxxxxxxxxxxxxx"
  }
}
```

**Copy TOKEN untuk request selanjutnya!**

---

### 5.2 Cek Subscription (Otomatis Trial)

```bash
curl -X GET http://127.0.0.1:8002/api/subscription/current \
  -H "Authorization: Bearer 1|laravel_sanctum_xxxxxxxxxxxxxxxxxxxxxxx"
```

**Response:**
```json
{
  "data": {
    "subscription": {
      "id": 1,
      "status": "trial",
      "start_date": "2025-12-05",
      "end_date": "2025-12-19",
      "trial_ends_at": "2025-12-19"
    },
    "plan": {
      "id": 1,
      "name": "Trial",
      "price": "0.00",
      "billing_cycle": "trial",
      "max_outlets": 1,
      "max_users": 2,
      "max_products": 50,
      "max_transactions_per_month": 100
    },
    "usage": {
      "outlets": 1,
      "users": 1,
      "products": 0,
      "transactions_this_month": 0
    },
    "is_active": true,
    "days_remaining": 14
  }
}
```

**✅ Verified: User otomatis dapat Trial subscription 14 hari!**

---

### 5.3 Get All Plans

```bash
curl -X GET http://127.0.0.1:8002/api/subscription/plans
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Trial",
      "price": "0.00",
      "billing_cycle": "trial"
    },
    {
      "id": 2,
      "name": "Starter",
      "price": "99000.00",
      "billing_cycle": "monthly",
      "limits": {
        "max_outlets": 1,
        "max_users": 5,
        "max_products": null,
        "max_transactions_per_month": null
      }
    },
    {
      "id": 3,
      "name": "Professional",
      "price": "249000.00",
      "billing_cycle": "monthly",
      "limits": {
        "max_outlets": 3,
        "max_users": 15,
        "max_products": null,
        "max_transactions_per_month": null
      }
    }
  ]
}
```

---

### 5.4 Initiate Payment (Upgrade ke Starter)

```bash
curl -X POST http://127.0.0.1:8002/api/payment/initiate \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "plan_id": 2
  }'
```

**Response (200 OK):**
```json
{
  "message": "Payment created successfully",
  "data": {
    "snap_token": "66e4fa55-fdac-4ef9-91b5-733b97d1b862",
    "invoice": {
      "id": 1,
      "invoice_number": "INV-20251205-A3F9C1",
      "amount": "99000.00",
      "status": "pending"
    },
    "client_key": "SB-Mid-client-xxxxxxxx"
  }
}
```

**Copy `snap_token` untuk testing payment!**

---

### 5.5 Test Payment dengan Snap Token

**Option A: Via Browser**

1. Buat file HTML sederhana `test-payment.html`:

```html
<!DOCTYPE html>
<html>
<head>
    <title>Test Midtrans Payment</title>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="SB-Mid-client-xxxxxxxx"></script>
</head>
<body>
    <h1>Test Payment</h1>
    <button onclick="pay()">Pay Now</button>

    <script>
        function pay() {
            snap.pay('66e4fa55-fdac-4ef9-91b5-733b97d1b862', {
                onSuccess: function(result) {
                    console.log('Payment Success:', result);
                    alert('Payment berhasil!');
                },
                onPending: function(result) {
                    console.log('Payment Pending:', result);
                    alert('Payment pending!');
                },
                onError: function(result) {
                    console.log('Payment Error:', result);
                    alert('Payment error!');
                },
                onClose: function() {
                    console.log('Payment popup ditutup');
                }
            });
        }
    </script>
</body>
</html>
```

2. Buka di browser
3. Klik "Pay Now"
4. Gunakan test card:

**Test Card Numbers (Sandbox):**
```
✅ SUCCESS:
Card Number: 4811 1111 1111 1114
CVV: 123
Exp: 01/25
OTP: 112233

⏳ PENDING:
Card Number: 4911 1111 1111 1113
CVV: 123
Exp: 01/25

❌ FAILED:
Card Number: 4411 1111 1111 1118
CVV: 123
Exp: 01/25
```

---

### 5.6 Verify Webhook Received

**Monitor ngrok dashboard:**
```
http://127.0.0.1:4040
```

**Atau check Laravel logs:**
```bash
tail -f storage/logs/laravel.log
```

**Setelah payment success, cek subscription lagi:**
```bash
curl -X GET http://127.0.0.1:8002/api/subscription/current \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Response harus berubah ke Starter:**
```json
{
  "data": {
    "subscription": {
      "status": "active",
      "plan": {
        "name": "Starter",
        "price": "99000.00"
      }
    }
  }
}
```

**✅ SUCCESS: Subscription upgraded automatically!**

---

## STEP 6: Testing via Flutter Mobile

### 6.1 Install Midtrans Flutter SDK

**pubspec.yaml:**
```yaml
dependencies:
  flutter:
    sdk: flutter
  dio: ^5.4.0
  midtrans_sdk: ^0.2.0
  flutter_secure_storage: ^9.0.0
```

```bash
flutter pub get
```

---

### 6.2 Buat Subscription Service

**lib/services/subscription_service.dart:**
```dart
import 'package:dio/dio.dart';

class SubscriptionService {
  final Dio _dio;
  final String baseUrl;

  SubscriptionService(this._dio, {required this.baseUrl});

  // Get all plans
  Future<List<dynamic>> getPlans() async {
    final response = await _dio.get('$baseUrl/subscription/plans');
    return response.data['data'];
  }

  // Get current subscription
  Future<Map<String, dynamic>> getCurrentSubscription(String token) async {
    final response = await _dio.get(
      '$baseUrl/subscription/current',
      options: Options(headers: {'Authorization': 'Bearer $token'}),
    );
    return response.data['data'];
  }

  // Initiate payment
  Future<Map<String, dynamic>> initiatePayment(String token, int planId) async {
    final response = await _dio.post(
      '$baseUrl/payment/initiate',
      data: {'plan_id': planId},
      options: Options(headers: {'Authorization': 'Bearer $token'}),
    );
    return response.data['data'];
  }
}
```

---

### 6.3 Buat Payment Service dengan Midtrans SDK

**lib/services/payment_service.dart:**
```dart
import 'package:flutter/material.dart';
import 'package:midtrans_sdk/midtrans_sdk.dart';

class PaymentService {
  MidtransSDK? _midtrans;

  void initMidtrans(String clientKey, BuildContext context) {
    _midtrans = MidtransSDK();
    _midtrans!.init(
      config: MidtransConfig(
        clientKey: clientKey,
        merchantBaseUrl: "https://your-ngrok-url.ngrok-free.app/api/",
        colorTheme: ColorTheme(
          colorPrimary: Theme.of(context).primaryColor,
          colorPrimaryDark: Theme.of(context).primaryColorDark,
          colorSecondary: Theme.of(context).colorScheme.secondary,
        ),
      ),
    );

    // Setup callback
    _midtrans!.setUIFlowObserver(
      onTransactionFinished: (result) {
        print('Transaction finished: ${result.transactionStatus}');
      },
    );
  }

  void startPayment(String snapToken) {
    _midtrans?.startPaymentUiFlow(token: snapToken);
  }
}
```

---

### 6.4 Buat Subscription Screen

**lib/screens/subscription_screen.dart:**
```dart
import 'package:flutter/material.dart';
import '../services/subscription_service.dart';
import '../services/payment_service.dart';

class SubscriptionScreen extends StatefulWidget {
  final String authToken;

  const SubscriptionScreen({required this.authToken});

  @override
  State<SubscriptionScreen> createState() => _SubscriptionScreenState();
}

class _SubscriptionScreenState extends State<SubscriptionScreen> {
  late SubscriptionService _subscriptionService;
  late PaymentService _paymentService;

  List<dynamic> _plans = [];
  Map<String, dynamic>? _currentSubscription;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _subscriptionService = SubscriptionService(
      Dio(),
      baseUrl: 'https://your-ngrok-url.ngrok-free.app/api',
    );
    _paymentService = PaymentService();
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() => _isLoading = true);

    try {
      final plans = await _subscriptionService.getPlans();
      final current = await _subscriptionService.getCurrentSubscription(widget.authToken);

      setState(() {
        _plans = plans;
        _currentSubscription = current;
        _isLoading = false;
      });
    } catch (e) {
      print('Error loading data: $e');
      setState(() => _isLoading = false);
    }
  }

  Future<void> _subscribeToPlan(int planId, String planName) async {
    if (planName == 'Trial') {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Trial otomatis diberikan saat registrasi')),
      );
      return;
    }

    try {
      // Show loading
      showDialog(
        context: context,
        barrierDismissible: false,
        builder: (context) => Center(child: CircularProgressIndicator()),
      );

      // 1. Initiate payment
      final paymentData = await _subscriptionService.initiatePayment(
        widget.authToken,
        planId,
      );

      // Close loading
      Navigator.pop(context);

      // 2. Initialize Midtrans SDK
      _paymentService.initMidtrans(paymentData['client_key'], context);

      // 3. Start payment flow
      _paymentService.startPayment(paymentData['snap_token']);

      // 4. Listen to result
      // (callback sudah di-setup di initMidtrans)

    } catch (e) {
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Gagal memulai payment: $e'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return Scaffold(
        appBar: AppBar(title: Text('Subscription')),
        body: Center(child: CircularProgressIndicator()),
      );
    }

    return Scaffold(
      appBar: AppBar(title: Text('Subscription Plans')),
      body: Column(
        children: [
          // Current subscription card
          if (_currentSubscription != null) ...[
            Card(
              margin: EdgeInsets.all(16),
              color: Colors.blue[50],
              child: Padding(
                padding: EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Current Plan',
                      style: TextStyle(
                        fontSize: 12,
                        color: Colors.grey[600],
                      ),
                    ),
                    SizedBox(height: 4),
                    Text(
                      _currentSubscription!['plan']['name'],
                      style: TextStyle(
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    SizedBox(height: 8),
                    Text(
                      'Status: ${_currentSubscription!['subscription']['status']}',
                      style: TextStyle(color: Colors.green),
                    ),
                    Text(
                      'Sisa hari: ${_currentSubscription!['days_remaining']}',
                    ),
                  ],
                ),
              ),
            ),
            Divider(),
          ],

          // Available plans
          Expanded(
            child: ListView.builder(
              padding: EdgeInsets.all(16),
              itemCount: _plans.length,
              itemBuilder: (context, index) {
                final plan = _plans[index];
                final isCurrentPlan = _currentSubscription?['plan']['id'] == plan['id'];

                return Card(
                  margin: EdgeInsets.only(bottom: 16),
                  elevation: plan['is_popular'] ? 8 : 2,
                  child: Padding(
                    padding: EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        if (plan['is_popular'])
                          Container(
                            padding: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                            decoration: BoxDecoration(
                              color: Colors.orange,
                              borderRadius: BorderRadius.circular(4),
                            ),
                            child: Text(
                              'POPULAR',
                              style: TextStyle(
                                color: Colors.white,
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                        SizedBox(height: 8),
                        Text(
                          plan['name'],
                          style: TextStyle(
                            fontSize: 24,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        SizedBox(height: 4),
                        Text(
                          plan['description'] ?? '',
                          style: TextStyle(color: Colors.grey[600]),
                        ),
                        SizedBox(height: 12),
                        Text(
                          double.parse(plan['price']) > 0
                              ? 'Rp ${double.parse(plan['price']).toStringAsFixed(0)}/bulan'
                              : 'GRATIS',
                          style: TextStyle(
                            fontSize: 20,
                            fontWeight: FontWeight.bold,
                            color: Colors.blue,
                          ),
                        ),
                        SizedBox(height: 16),

                        // Features
                        ...List.generate(
                          (plan['features'] as List).length,
                          (i) => Padding(
                            padding: EdgeInsets.only(bottom: 8),
                            child: Row(
                              children: [
                                Icon(Icons.check_circle, color: Colors.green, size: 20),
                                SizedBox(width: 8),
                                Expanded(
                                  child: Text(plan['features'][i]),
                                ),
                              ],
                            ),
                          ),
                        ),

                        SizedBox(height: 16),

                        // Action button
                        if (!isCurrentPlan && plan['name'] != 'Trial')
                          SizedBox(
                            width: double.infinity,
                            child: ElevatedButton(
                              onPressed: () => _subscribeToPlan(
                                plan['id'],
                                plan['name'],
                              ),
                              style: ElevatedButton.styleFrom(
                                padding: EdgeInsets.symmetric(vertical: 12),
                              ),
                              child: Text('Upgrade ke ${plan['name']}'),
                            ),
                          ),

                        if (isCurrentPlan)
                          Container(
                            width: double.infinity,
                            padding: EdgeInsets.symmetric(vertical: 12),
                            decoration: BoxDecoration(
                              color: Colors.grey[300],
                              borderRadius: BorderRadius.circular(4),
                            ),
                            child: Text(
                              'Current Plan',
                              textAlign: TextAlign.center,
                              style: TextStyle(fontWeight: FontWeight.bold),
                            ),
                          ),
                      ],
                    ),
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}
```

---

### 6.5 Testing di Flutter

**Flow Testing:**

1. **Build & Run App:**
```bash
flutter run
```

2. **Login/Register:**
   - Login dengan user yang sudah dibuat
   - Atau register user baru (otomatis dapat Trial)

3. **Navigate ke Subscription Screen:**
   - Lihat current plan (Trial)
   - Lihat available plans

4. **Tap "Upgrade ke Starter":**
   - Midtrans payment popup akan muncul
   - Pilih payment method
   - Gunakan test card: `4811 1111 1111 1114`
   - Masukkan OTP: `112233`

5. **Setelah Payment Success:**
   - Pull to refresh
   - Current plan berubah ke "Starter"

---

## STEP 7: Cross-Platform Testing

### 7.1 Scenario: Bayar di Web, Login di Mobile

**Di Web (Browser):**
```bash
# 1. Login user
curl -X POST http://127.0.0.1:8002/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "testuser@example.com",
    "password": "password123"
  }'

# 2. Initiate payment Starter
curl -X POST http://127.0.0.1:8002/api/payment/initiate \
  -H "Authorization: Bearer TOKEN_HERE" \
  -d '{"plan_id": 2}'

# 3. Complete payment via Snap HTML
# (Gunakan test card 4811 1111 1111 1114)
```

**Di Mobile Flutter:**
```dart
// 1. Login dengan user yang sama
await login('testuser@example.com', 'password123');

// 2. Load subscription
final subscription = await _subscriptionService.getCurrentSubscription(token);

// 3. Verify plan updated to Starter
expect(subscription['plan']['name'], 'Starter');
```

**✅ Expected: Subscription sync otomatis karena disimpan di database!**

---

### 7.2 Scenario: Bayar di Mobile, Cek di Web

**Di Flutter Mobile:**
```dart
// 1. Login
await login('testuser@example.com', 'password123');

// 2. Upgrade ke Professional via Midtrans
await _subscribeToPlan(3, 'Professional');

// 3. Complete payment di Midtrans popup
```

**Di Web:**
```bash
# 1. Check subscription via API
curl -X GET http://127.0.0.1:8002/api/subscription/current \
  -H "Authorization: Bearer TOKEN_HERE"

# Response:
{
  "data": {
    "plan": {
      "name": "Professional",
      "price": "249000.00"
    }
  }
}
```

**✅ Expected: Status subscription sama di web dan mobile!**

---

## Troubleshooting

### Problem 1: Webhook tidak terkirim

**Symptoms:**
- Payment success di Midtrans
- Tapi subscription tidak update

**Solutions:**
```bash
# 1. Check ngrok masih running
curl https://your-ngrok-url.ngrok-free.app/api/subscription/plans

# 2. Check webhook URL di Midtrans Dashboard
Settings → Configuration → Payment Notification URL

# 3. Monitor webhook di ngrok dashboard
http://127.0.0.1:4040/inspect/http

# 4. Check Laravel logs
tail -f storage/logs/laravel.log
```

---

### Problem 2: Invalid Signature Error

**Symptoms:**
```
Invalid signature from Midtrans webhook
```

**Solutions:**
```bash
# 1. Verify server key di .env benar
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxx

# 2. Clear config cache
php artisan config:clear

# 3. Restart server
php artisan serve --port=8002
```

---

### Problem 3: Snap Token Expired

**Symptoms:**
```
Snap token has expired
```

**Solutions:**
- Snap token berlaku 24 jam
- Generate token baru dengan `/payment/initiate`
- Jangan simpan snap token, selalu generate fresh

---

### Problem 4: Flutter Midtrans SDK Error

**Symptoms:**
```
MidtransSDK is not initialized
```

**Solutions:**
```dart
// Pastikan init dipanggil sebelum startPayment
_paymentService.initMidtrans(clientKey, context);
await Future.delayed(Duration(milliseconds: 100));
_paymentService.startPayment(snapToken);
```

---

### Problem 5: CORS Error di Web

**Symptoms:**
```
Access-Control-Allow-Origin error
```

**Solutions:**

**config/cors.php:**
```php
'paths' => ['api/*'],
'allowed_origins' => ['*'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

```bash
php artisan config:clear
```

---

## Test Checklist

### ✅ Backend Testing
- [ ] Midtrans credentials configured
- [ ] Database migrated
- [ ] Subscription plans seeded (3 plans)
- [ ] Ngrok running & configured
- [ ] Webhook URL set di Midtrans Dashboard

### ✅ Web Testing
- [ ] User dapat register
- [ ] User otomatis dapat Trial subscription
- [ ] Dapat get all plans
- [ ] Dapat initiate payment
- [ ] Payment success via test card
- [ ] Webhook diterima
- [ ] Subscription otomatis upgrade

### ✅ Flutter Testing
- [ ] Dapat login
- [ ] Dapat get current subscription
- [ ] Dapat get all plans
- [ ] Midtrans SDK initialized
- [ ] Payment popup muncul
- [ ] Payment success
- [ ] Subscription updated

### ✅ Cross-Platform Testing
- [ ] Bayar di web → login di mobile (data sync)
- [ ] Bayar di mobile → cek di web (data sync)
- [ ] Multiple devices dengan user sama (consistent data)

---

## Test Cards Summary

```
✅ SUCCESS CARD:
Card: 4811 1111 1111 1114
CVV: 123
Exp: 01/25
OTP: 112233
→ Transaction akan success, subscription otomatis upgrade

⏳ PENDING CARD:
Card: 4911 1111 1111 1113
CVV: 123
Exp: 01/25
→ Transaction pending, subscription tidak update

❌ FAILED CARD:
Card: 4411 1111 1111 1118
CVV: 123
Exp: 01/25
→ Transaction failed, subscription tetap
```

---

## Next Steps

Setelah testing berhasil:

1. **Production Deployment:**
   - Ganti credentials ke Production
   - Set `MIDTRANS_IS_PRODUCTION=true`
   - Update ngrok URL ke production domain
   - Test dengan real card (kecil amount dulu)

2. **Monitoring:**
   - Setup logging untuk payment events
   - Monitor webhook deliveries di Midtrans Dashboard
   - Alert jika payment gagal

3. **Additional Features:**
   - Email notification setelah payment
   - Invoice PDF generation
   - Subscription renewal reminder
   - Grace period untuk expired subscription

---

**Happy Testing! 🚀**

Jika ada error atau pertanyaan, check Troubleshooting section atau contact support.
