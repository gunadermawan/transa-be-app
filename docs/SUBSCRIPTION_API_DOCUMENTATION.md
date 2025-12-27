# Subscription & Payment API Documentation

Complete API documentation for SaaS subscription system with Midtrans payment integration.

## Table of Contents
- [Overview](#overview)
- [Authentication](#authentication)
- [Subscription Plans](#subscription-plans)
- [Current Subscription](#current-subscription)
- [Usage & Limits](#usage--limits)
- [Payment Flow](#payment-flow)
- [Error Codes](#error-codes)
- [Testing Guide](#testing-guide)

---

## Overview

Base URL: `http://your-domain.com/api`

This API provides:
- View available subscription plans
- Check current subscription status
- Monitor usage and limits
- Initiate payments for upgrades
- Handle payment callbacks

### Available Plans

| Plan | Price | Outlets | Users | Products | Transactions/Month |
|------|-------|---------|-------|----------|-------------------|
| **Trial** | FREE | 1 | 2 | 50 | 100 |
| **Starter** | Rp 99,000 | 1 | 5 | Unlimited | Unlimited |
| **Professional** | Rp 249,000 | 3 | 15 | Unlimited | Unlimited |

---

## Authentication

All authenticated endpoints require Bearer token in header:

```
Authorization: Bearer {access_token}
```

Get access token from login/register endpoints.

---

## Subscription Plans

### 1. Get All Plans

Get list of all available subscription plans.

**Endpoint:** `GET /subscription/plans`
**Auth:** Not required

**Request:**
```bash
curl -X GET http://127.0.0.1:8000/api/subscription/plans
```

**Response: 200 OK**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Trial",
      "description": "Mulai dengan trial gratis, upgrade kapan saja tanpa komitmen jangka panjang",
      "price": "0.00",
      "billing_cycle": "trial",
      "trial_days": 14,
      "limits": {
        "max_outlets": 1,
        "max_users": 2,
        "max_products": 50,
        "max_transactions_per_month": 100
      },
      "features": [
        "1 outlet",
        "2 pengguna",
        "Max 50 produk",
        "Max 100 transaksi/bulan",
        "Dashboard dasar"
      ],
      "is_popular": false
    },
    {
      "id": 2,
      "name": "Starter",
      "description": "Cocok untuk bisnis kecil yang baru memulai",
      "price": "99000.00",
      "billing_cycle": "monthly",
      "trial_days": 0,
      "limits": {
        "max_outlets": 1,
        "max_users": 5,
        "max_products": null,
        "max_transactions_per_month": null
      },
      "features": [
        "1 outlet",
        "5 pengguna",
        "Produk unlimited",
        "Transaksi unlimited",
        "Laporan lengkap"
      ],
      "is_popular": false
    },
    {
      "id": 3,
      "name": "Professional",
      "description": "Paket paling populer untuk bisnis yang berkembang",
      "price": "249000.00",
      "billing_cycle": "monthly",
      "trial_days": 0,
      "limits": {
        "max_outlets": 3,
        "max_users": 15,
        "max_products": null,
        "max_transactions_per_month": null
      },
      "features": [
        "3 outlet",
        "15 pengguna",
        "Semua fitur unlimited",
        "Multi-outlet dashboard",
        "Transfer stok antar outlet"
      ],
      "is_popular": true
    }
  ]
}
```

**Flutter Implementation:**
```dart
Future<List<SubscriptionPlan>> getPlans() async {
  final response = await dio.get('/subscription/plans');

  if (response.statusCode == 200) {
    List<dynamic> data = response.data['data'];
    return data.map((json) => SubscriptionPlan.fromJson(json)).toList();
  }
  throw Exception('Failed to load plans');
}
```

---

## Current Subscription

### 2. Get Current Subscription

Get detailed information about current subscription including status, limits, and remaining days.

**Endpoint:** `GET /subscription/current`
**Auth:** Required (Bearer token)

**Request:**
```bash
curl -X GET http://127.0.0.1:8000/api/subscription/current \
  -H "Authorization: Bearer {token}"
```

**Response: 200 OK**
```json
{
  "data": {
    "subscription": {
      "id": 1,
      "business_id": 1,
      "subscription_plan_id": 1,
      "start_date": "2025-12-02",
      "end_date": "2025-12-16",
      "trial_ends_at": "2025-12-16",
      "next_billing_date": null,
      "status": "trial",
      "auto_renew": false
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
      "products": 5,
      "transactions_this_month": 23
    },
    "limits": {
      "outlets": 1,
      "users": 2,
      "products": 50,
      "transactions_per_month": 100
    },
    "is_active": true,
    "days_remaining": 14
  }
}
```

**Flutter Implementation:**
```dart
Future<SubscriptionDetails> getCurrentSubscription() async {
  final response = await dio.get(
    '/subscription/current',
    options: Options(headers: {'Authorization': 'Bearer $token'}),
  );

  if (response.statusCode == 200) {
    return SubscriptionDetails.fromJson(response.data['data']);
  }
  throw Exception('Failed to load subscription');
}
```

---

## Usage & Limits

### 3. Get Usage Statistics

Get current usage and check if you can add more resources.

**Endpoint:** `GET /subscription/usage`
**Auth:** Required

**Request:**
```bash
curl -X GET http://127.0.0.1:8000/api/subscription/usage \
  -H "Authorization: Bearer {token}"
```

**Response: 200 OK**
```json
{
  "data": {
    "usage": {
      "outlets": 1,
      "users": 2,
      "products": 45,
      "transactions_this_month": 87
    },
    "limits": {
      "max_outlets": 1,
      "max_users": 2,
      "max_products": 50,
      "max_transactions_per_month": 100
    },
    "can_add": {
      "outlet": false,
      "user": false,
      "product": true,
      "transaction": true
    }
  }
}
```

**Flutter Implementation:**
```dart
Future<UsageStats> getUsageStats() async {
  final response = await dio.get(
    '/subscription/usage',
    options: Options(headers: {'Authorization': 'Bearer $token'}),
  );

  if (response.statusCode == 200) {
    return UsageStats.fromJson(response.data['data']);
  }
  throw Exception('Failed to load usage stats');
}
```

### 4. Check Specific Limit

Check if you can add a specific resource type.

**Endpoint:** `POST /subscription/check-limit`
**Auth:** Required

**Request:**
```bash
curl -X POST http://127.0.0.1:8000/api/subscription/check-limit \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "product"
  }'
```

**Request Body:**
```json
{
  "type": "outlet" | "user" | "product" | "transaction"
}
```

**Response: 200 OK**
```json
{
  "data": {
    "type": "product",
    "can_add": true
  }
}
```

**Flutter Implementation:**
```dart
Future<bool> canAddResource(String type) async {
  final response = await dio.post(
    '/subscription/check-limit',
    data: {'type': type},
    options: Options(headers: {'Authorization': 'Bearer $token'}),
  );

  if (response.statusCode == 200) {
    return response.data['data']['can_add'];
  }
  return false;
}
```

---

## Payment Flow

### 5. Initiate Payment

Start payment process for subscription upgrade. Returns Midtrans Snap token.

**Endpoint:** `POST /payment/initiate`
**Auth:** Required (Owner only - role_id: 1)

**Request:**
```bash
curl -X POST http://127.0.0.1:8000/api/payment/initiate \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "plan_id": 2
  }'
```

**Request Body:**
```json
{
  "plan_id": 2  // Starter or Professional plan ID
}
```

**Response: 200 OK**
```json
{
  "message": "Payment created successfully",
  "data": {
    "snap_token": "66e4fa55-fdac-4ef9-91b5-733b97d1b862",
    "invoice": {
      "id": 1,
      "business_id": 1,
      "subscription_plan_id": 2,
      "invoice_number": "INV-20251202-A3F9C1",
      "amount": "99000.00",
      "tax": "0.00",
      "total": "99000.00",
      "status": "pending",
      "due_date": "2025-12-03",
      "payment_url": "66e4fa55-fdac-4ef9-91b5-733b97d1b862"
    },
    "client_key": "SB-Mid-client-xxxxxxxxxxxx"
  }
}
```

**Response: 400 Bad Request** (If trying to purchase Trial)
```json
{
  "message": "Cannot purchase Trial plan. Trial is automatically assigned to new businesses."
}
```

**Response: 403 Forbidden** (If not owner)
```json
{
  "message": "Unauthorized"
}
```

**Flutter Implementation with Midtrans SDK:**
```dart
import 'package:midtrans_sdk/midtrans_sdk.dart';

class PaymentService {
  late MidtransSDK _midtrans;

  void initMidtrans(String clientKey) {
    _midtrans = MidtransSDK();
    _midtrans.init(
      config: MidtransConfig(
        clientKey: clientKey,
        merchantBaseUrl: "https://your-domain.com/api/",
        colorTheme: ColorTheme(
          colorPrimary: Colors.blue,
          colorPrimaryDark: Colors.blueAccent,
          colorSecondary: Colors.lightBlue,
        ),
      ),
    );
  }

  Future<void> initiatePayment(int planId) async {
    try {
      // 1. Call backend to get snap token
      final response = await dio.post(
        '/payment/initiate',
        data: {'plan_id': planId},
        options: Options(headers: {'Authorization': 'Bearer $token'}),
      );

      if (response.statusCode == 200) {
        final snapToken = response.data['data']['snap_token'];
        final clientKey = response.data['data']['client_key'];

        // 2. Initialize Midtrans SDK
        initMidtrans(clientKey);

        // 3. Start payment
        _midtrans.startPaymentUiFlow(
          token: snapToken,
        );
      }
    } catch (e) {
      print('Payment initiation failed: $e');
    }
  }

  void setupPaymentCallback() {
    _midtrans.setUIFlowObserver(
      onTransactionFinished: (result) {
        if (result.transactionStatus == TransactionResultStatus.settlement ||
            result.transactionStatus == TransactionResultStatus.capture) {
          // Payment success - refresh subscription
          _showSuccessDialog();
          _refreshSubscription();
        } else if (result.transactionStatus == TransactionResultStatus.pending) {
          _showPendingDialog();
        } else {
          _showFailedDialog();
        }
      },
    );
  }
}
```

### 6. Check Payment Status

Check current status of a payment transaction.

**Endpoint:** `POST /payment/check-status`
**Auth:** Required

**Request:**
```bash
curl -X POST http://127.0.0.1:8000/api/payment/check-status \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "order_id": "INV-20251202-A3F9C1"
  }'
```

**Response: 200 OK**
```json
{
  "message": "Payment status retrieved",
  "data": {
    "status_code": "200",
    "status_message": "Success, transaction found",
    "transaction_id": "fa05cba8-f491-4dd1-a08f-d46adb89e202",
    "order_id": "INV-20251202-A3F9C1",
    "gross_amount": "99000.00",
    "payment_type": "credit_card",
    "transaction_time": "2025-12-02 10:15:30",
    "transaction_status": "settlement",
    "fraud_status": "accept"
  }
}
```

**Flutter Implementation:**
```dart
Future<PaymentStatus> checkPaymentStatus(String orderId) async {
  final response = await dio.post(
    '/payment/check-status',
    data: {'order_id': orderId},
    options: Options(headers: {'Authorization': 'Bearer $token'}),
  );

  if (response.statusCode == 200) {
    return PaymentStatus.fromJson(response.data['data']);
  }
  throw Exception('Failed to check payment status');
}
```

### 7. Payment Webhook

**⚠️ Internal Endpoint - Called by Midtrans only**

**Endpoint:** `POST /payment/webhook`
**Auth:** Not required (Midtrans signature verification)

This endpoint is automatically called by Midtrans when payment status changes. Configure in Midtrans Dashboard:

**Midtrans Dashboard Settings:**
```
Payment Notification URL: https://your-domain.com/api/payment/webhook
```

---

## Error Codes

### Limit Reached Errors

When a user tries to exceed their subscription limits, the API returns specific error codes:

#### 1. Outlet Limit Reached

**Response: 403 Forbidden**
```json
{
  "message": "You have reached the maximum number of outlets (1) for your plan. Please upgrade to add more outlets.",
  "code": "OUTLET_LIMIT_REACHED",
  "current_plan": "Trial",
  "max_outlets": 1
}
```

#### 2. User Limit Reached

**Response: 403 Forbidden**
```json
{
  "message": "You have reached the maximum number of users (2) for your plan. Please upgrade to add more users.",
  "code": "USER_LIMIT_REACHED",
  "current_plan": "Trial",
  "max_users": 2
}
```

#### 3. Product Limit Reached

**Response: 403 Forbidden**
```json
{
  "message": "You have reached the maximum number of products (50) for your plan. Please upgrade to add more products.",
  "code": "PRODUCT_LIMIT_REACHED",
  "current_plan": "Trial",
  "max_products": 50
}
```

#### 4. Transaction Limit Reached

**Response: 403 Forbidden**
```json
{
  "message": "You have reached the maximum number of transactions (100) for this month. Please upgrade your plan.",
  "code": "TRANSACTION_LIMIT_REACHED",
  "current_plan": "Trial",
  "max_transactions_per_month": 100
}
```

#### 5. Subscription Expired

**Response: 403 Forbidden**
```json
{
  "message": "Your subscription has expired. Please renew to continue.",
  "code": "SUBSCRIPTION_EXPIRED"
}
```

### Flutter Error Handling

```dart
Future<void> addOutlet(OutletData data) async {
  try {
    final response = await dio.post(
      '/add-outlet',
      data: data.toJson(),
      options: Options(headers: {'Authorization': 'Bearer $token'}),
    );

    if (response.statusCode == 201) {
      _showSuccessMessage('Outlet added successfully');
    }
  } on DioException catch (e) {
    if (e.response?.statusCode == 403) {
      final errorCode = e.response?.data['code'];

      switch (errorCode) {
        case 'OUTLET_LIMIT_REACHED':
          _showUpgradeDialog(
            title: 'Outlet Limit Reached',
            message: e.response?.data['message'],
            currentPlan: e.response?.data['current_plan'],
          );
          break;

        case 'USER_LIMIT_REACHED':
          _showUpgradeDialog(
            title: 'User Limit Reached',
            message: e.response?.data['message'],
            currentPlan: e.response?.data['current_plan'],
          );
          break;

        case 'PRODUCT_LIMIT_REACHED':
          _showUpgradeDialog(
            title: 'Product Limit Reached',
            message: e.response?.data['message'],
            currentPlan: e.response?.data['current_plan'],
          );
          break;

        case 'TRANSACTION_LIMIT_REACHED':
          _showUpgradeDialog(
            title: 'Transaction Limit Reached',
            message: e.response?.data['message'],
            currentPlan: e.response?.data['current_plan'],
          );
          break;

        case 'SUBSCRIPTION_EXPIRED':
          _showRenewalDialog();
          break;

        default:
          _showErrorMessage('Access denied');
      }
    }
  }
}

void _showUpgradeDialog({
  required String title,
  required String message,
  required String currentPlan,
}) {
  showDialog(
    context: context,
    builder: (context) => AlertDialog(
      title: Text(title),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(message),
          SizedBox(height: 16),
          Text('Current Plan: $currentPlan', style: TextStyle(fontWeight: FontWeight.bold)),
        ],
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.pop(context),
          child: Text('Cancel'),
        ),
        ElevatedButton(
          onPressed: () {
            Navigator.pop(context);
            _navigateToSubscriptionPage();
          },
          child: Text('Upgrade Now'),
        ),
      ],
    ),
  );
}
```

---

## Testing Guide

### Prerequisites

1. **Add Midtrans credentials to `.env`:**
```env
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

2. **Run seeder for plans:**
```bash
php artisan db:seed --class=SubscriptionPlanSeeder
```

### Test Scenarios

#### Scenario 1: New User Registration
```bash
# 1. Register new business
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "business_name": "Test Business",
    "address": "Jakarta"
  }'

# Response: User gets Trial subscription automatically
# Check subscription:
curl -X GET http://127.0.0.1:8000/api/subscription/current \
  -H "Authorization: Bearer {token}"
```

**Expected:**
- User created successfully
- Business created
- Trial subscription assigned (14 days, 1 outlet, 2 users, 50 products, 100 transactions/month)

#### Scenario 2: Check Limits
```bash
# Get current usage
curl -X GET http://127.0.0.1:8000/api/subscription/usage \
  -H "Authorization: Bearer {token}"
```

**Expected:**
- Shows current usage vs limits
- `can_add` flags indicate what can be added

#### Scenario 3: Hit Limit
```bash
# Try adding 3rd user (Trial limit is 2)
curl -X POST http://127.0.0.1:8000/api/add-staff \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Third User",
    "email": "user3@example.com",
    "password": "password",
    "outlet_id": 1,
    "role_id": 3
  }'
```

**Expected:**
- 403 Forbidden
- Error code: `USER_LIMIT_REACHED`
- Message shows max users for plan

#### Scenario 4: Upgrade to Starter (via Payment)
```bash
# 1. Get plans
curl -X GET http://127.0.0.1:8000/api/subscription/plans

# 2. Initiate payment for Starter (plan_id: 2)
curl -X POST http://127.0.0.1:8000/api/payment/initiate \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "plan_id": 2
  }'

# Response includes snap_token
{
  "snap_token": "66e4fa55-fdac-4ef9-91b5-733b97d1b862",
  "client_key": "SB-Mid-client-xxxx"
}

# 3. Use snap_token in Midtrans Snap payment
# Frontend opens Midtrans payment popup

# 4. Complete payment using test cards:
# Card Number: 4811 1111 1111 1114
# CVV: 123
# Exp: 01/25

# 5. Midtrans sends webhook to /payment/webhook (automatic)

# 6. Check subscription updated
curl -X GET http://127.0.0.1:8000/api/subscription/current \
  -H "Authorization: Bearer {token}"
```

**Expected:**
- Invoice created with status "pending"
- Snap token generated
- After payment success, webhook received
- Invoice status changed to "paid"
- Subscription upgraded to Starter
- New limits: 1 outlet, 5 users, unlimited products/transactions

#### Scenario 5: Add Resource After Upgrade
```bash
# Now can add 3rd, 4th, 5th user (Starter limit is 5)
curl -X POST http://127.0.0.1:8000/api/add-staff \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Third User",
    "email": "user3@example.com",
    "password": "password",
    "outlet_id": 1,
    "role_id": 3
  }'
```

**Expected:**
- Staff created successfully
- No limit error

#### Scenario 6: Upgrade to Professional
```bash
# Initiate payment for Professional (plan_id: 3)
curl -X POST http://127.0.0.1:8000/api/payment/initiate \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "plan_id": 3
  }'

# Complete payment
# After success, can add up to 3 outlets and 15 users
```

**Expected:**
- Subscription upgraded to Professional
- Can add 2 more outlets (total 3)
- Can add 10 more users (total 15)

### Midtrans Test Cards

For testing payment in Sandbox mode:

**Success:**
```
Card: 4811 1111 1111 1114
CVV: 123
Exp: 01/25
OTP: 112233
```

**Pending:**
```
Card: 4911 1111 1111 1113
CVV: 123
Exp: 01/25
```

**Failed:**
```
Card: 4411 1111 1111 1118
CVV: 123
Exp: 01/25
```

### Testing Webhooks Locally

Use ngrok to test webhooks on local:

```bash
# 1. Start ngrok
ngrok http 8000

# 2. Copy ngrok URL: https://xxxx-xx-xx-xxx-xxx.ngrok-free.app

# 3. Set in Midtrans Dashboard:
# https://xxxx-xx-xx-xxx-xxx.ngrok-free.app/api/payment/webhook

# 4. Make payment - webhook will hit your local server
```

---

## Complete Flutter Integration Example

### 1. Models

```dart
// lib/models/subscription_plan.dart
class SubscriptionPlan {
  final int id;
  final String name;
  final String description;
  final double price;
  final String billingCycle;
  final int trialDays;
  final PlanLimits limits;
  final List<String> features;
  final bool isPopular;

  SubscriptionPlan({
    required this.id,
    required this.name,
    required this.description,
    required this.price,
    required this.billingCycle,
    required this.trialDays,
    required this.limits,
    required this.features,
    required this.isPopular,
  });

  factory SubscriptionPlan.fromJson(Map<String, dynamic> json) {
    return SubscriptionPlan(
      id: json['id'],
      name: json['name'],
      description: json['description'],
      price: double.parse(json['price']),
      billingCycle: json['billing_cycle'],
      trialDays: json['trial_days'],
      limits: PlanLimits.fromJson(json['limits']),
      features: List<String>.from(json['features']),
      isPopular: json['is_popular'],
    );
  }
}

class PlanLimits {
  final int? maxOutlets;
  final int? maxUsers;
  final int? maxProducts;
  final int? maxTransactionsPerMonth;

  PlanLimits({
    this.maxOutlets,
    this.maxUsers,
    this.maxProducts,
    this.maxTransactionsPerMonth,
  });

  factory PlanLimits.fromJson(Map<String, dynamic> json) {
    return PlanLimits(
      maxOutlets: json['max_outlets'],
      maxUsers: json['max_users'],
      maxProducts: json['max_products'],
      maxTransactionsPerMonth: json['max_transactions_per_month'],
    );
  }
}
```

### 2. API Service

```dart
// lib/services/subscription_service.dart
import 'package:dio/dio.dart';

class SubscriptionService {
  final Dio _dio;
  final String _baseUrl = 'http://your-domain.com/api';

  SubscriptionService(this._dio);

  Future<List<SubscriptionPlan>> getPlans() async {
    try {
      final response = await _dio.get('$_baseUrl/subscription/plans');

      if (response.statusCode == 200) {
        List<dynamic> data = response.data['data'];
        return data.map((json) => SubscriptionPlan.fromJson(json)).toList();
      }
      throw Exception('Failed to load plans');
    } catch (e) {
      rethrow;
    }
  }

  Future<SubscriptionDetails> getCurrentSubscription(String token) async {
    try {
      final response = await _dio.get(
        '$_baseUrl/subscription/current',
        options: Options(headers: {'Authorization': 'Bearer $token'}),
      );

      if (response.statusCode == 200) {
        return SubscriptionDetails.fromJson(response.data['data']);
      }
      throw Exception('Failed to load subscription');
    } catch (e) {
      rethrow;
    }
  }

  Future<PaymentInitResponse> initiatePayment(String token, int planId) async {
    try {
      final response = await _dio.post(
        '$_baseUrl/payment/initiate',
        data: {'plan_id': planId},
        options: Options(headers: {'Authorization': 'Bearer $token'}),
      );

      if (response.statusCode == 200) {
        return PaymentInitResponse.fromJson(response.data['data']);
      }
      throw Exception('Failed to initiate payment');
    } on DioException catch (e) {
      if (e.response?.statusCode == 400) {
        throw Exception(e.response?.data['message']);
      }
      rethrow;
    }
  }
}
```

### 3. UI Screen

```dart
// lib/screens/subscription_screen.dart
import 'package:flutter/material.dart';
import 'package:midtrans_sdk/midtrans_sdk.dart';

class SubscriptionScreen extends StatefulWidget {
  @override
  _SubscriptionScreenState createState() => _SubscriptionScreenState();
}

class _SubscriptionScreenState extends State<SubscriptionScreen> {
  late SubscriptionService _subscriptionService;
  late MidtransSDK _midtrans;
  List<SubscriptionPlan> _plans = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadPlans();
  }

  Future<void> _loadPlans() async {
    setState(() => _isLoading = true);

    try {
      final plans = await _subscriptionService.getPlans();
      setState(() {
        _plans = plans;
        _isLoading = false;
      });
    } catch (e) {
      setState(() => _isLoading = false);
      _showError('Failed to load plans: $e');
    }
  }

  Future<void> _subscribeToPlan(SubscriptionPlan plan) async {
    try {
      // Show loading
      showDialog(
        context: context,
        barrierDismissible: false,
        builder: (context) => Center(child: CircularProgressIndicator()),
      );

      // 1. Initiate payment
      final token = await _getAuthToken();
      final paymentInit = await _subscriptionService.initiatePayment(token, plan.id);

      // Close loading
      Navigator.pop(context);

      // 2. Initialize Midtrans
      _initMidtrans(paymentInit.clientKey);

      // 3. Start payment UI
      _midtrans.startPaymentUiFlow(token: paymentInit.snapToken);

    } catch (e) {
      Navigator.pop(context);
      _showError('Failed to start payment: $e');
    }
  }

  void _initMidtrans(String clientKey) {
    _midtrans = MidtransSDK();
    _midtrans.init(
      config: MidtransConfig(
        clientKey: clientKey,
        merchantBaseUrl: "https://your-domain.com/api/",
        colorTheme: ColorTheme(
          colorPrimary: Theme.of(context).primaryColor,
          colorPrimaryDark: Theme.of(context).primaryColorDark,
          colorSecondary: Theme.of(context).colorScheme.secondary,
        ),
      ),
    );

    _midtrans.setUIFlowObserver(
      onTransactionFinished: (result) {
        _handlePaymentResult(result);
      },
    );
  }

  void _handlePaymentResult(TransactionResult result) {
    if (result.transactionStatus == TransactionResultStatus.settlement ||
        result.transactionStatus == TransactionResultStatus.capture) {
      _showSuccess('Payment successful! Your subscription has been upgraded.');
      // Refresh subscription data
      _refreshSubscription();
    } else if (result.transactionStatus == TransactionResultStatus.pending) {
      _showInfo('Payment pending. We will notify you when completed.');
    } else {
      _showError('Payment failed or cancelled.');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Subscription Plans')),
      body: _isLoading
          ? Center(child: CircularProgressIndicator())
          : ListView.builder(
              padding: EdgeInsets.all(16),
              itemCount: _plans.length,
              itemBuilder: (context, index) {
                final plan = _plans[index];
                return _buildPlanCard(plan);
              },
            ),
    );
  }

  Widget _buildPlanCard(SubscriptionPlan plan) {
    return Card(
      margin: EdgeInsets.only(bottom: 16),
      elevation: plan.isPopular ? 8 : 2,
      child: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (plan.isPopular)
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
              plan.name,
              style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
            ),
            SizedBox(height: 8),
            Text(
              plan.description,
              style: TextStyle(color: Colors.grey),
            ),
            SizedBox(height: 16),
            Text(
              plan.price > 0
                  ? 'Rp ${plan.price.toStringAsFixed(0)}/month'
                  : 'FREE',
              style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
            ),
            SizedBox(height: 16),
            ...plan.features.map((feature) => Padding(
                  padding: EdgeInsets.only(bottom: 8),
                  child: Row(
                    children: [
                      Icon(Icons.check, color: Colors.green, size: 20),
                      SizedBox(width: 8),
                      Text(feature),
                    ],
                  ),
                )),
            SizedBox(height: 16),
            if (plan.name != 'Trial')
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: () => _subscribeToPlan(plan),
                  child: Text('Subscribe Now'),
                ),
              ),
          ],
        ),
      ),
    );
  }

  void _showSuccess(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), backgroundColor: Colors.green),
    );
  }

  void _showError(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), backgroundColor: Colors.red),
    );
  }

  void _showInfo(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message)),
    );
  }

  Future<String> _getAuthToken() async {
    // Get token from secure storage
    return 'your-auth-token';
  }

  Future<void> _refreshSubscription() async {
    // Refresh subscription data
  }
}
```

---

## Summary

### API Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/subscription/plans` | No | Get all plans |
| GET | `/subscription/current` | Yes | Get current subscription |
| GET | `/subscription/usage` | Yes | Get usage stats |
| POST | `/subscription/check-limit` | Yes | Check specific limit |
| POST | `/payment/initiate` | Yes (Owner) | Initiate payment |
| POST | `/payment/webhook` | No | Midtrans webhook |
| POST | `/payment/check-status` | Yes | Check payment status |

### Error Codes

- `OUTLET_LIMIT_REACHED`
- `USER_LIMIT_REACHED`
- `PRODUCT_LIMIT_REACHED`
- `TRANSACTION_LIMIT_REACHED`
- `SUBSCRIPTION_EXPIRED`

### Payment Flow

1. User selects plan
2. Frontend calls `/payment/initiate`
3. Backend returns `snap_token`
4. Frontend opens Midtrans Snap
5. User completes payment
6. Midtrans sends webhook
7. Subscription auto-upgraded

---

**Need help? Contact: support@yourdomain.com**
