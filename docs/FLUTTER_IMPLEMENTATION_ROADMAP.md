# Flutter Implementation Roadmap - Academy POS

## 📅 5-Week Development Plan

---

## WEEK 1: Foundation & Authentication

### Day 1-2: Project Setup
**Goal:** Setup Flutter project dengan arsitektur yang proper

**Tasks:**
1. Create new Flutter project
   ```bash
   flutter create academy_pos_flutter
   cd academy_pos_flutter
   ```

2. Add dependencies to `pubspec.yaml`:
   ```yaml
   dependencies:
     flutter:
       sdk: flutter

     # State Management
     provider: ^6.1.1

     # HTTP & API
     dio: ^5.4.0

     # Secure Storage
     flutter_secure_storage: ^9.0.0

     # UI Components
     google_fonts: ^6.1.0
     flutter_spinkit: ^5.2.0
     cached_network_image: ^3.3.0

     # Utils
     intl: ^0.18.1
     image_picker: ^1.0.5
   ```

3. Create folder structure:
   ```
   lib/
   ├── config/
   │   └── api_config.dart
   ├── core/
   │   ├── constants/
   │   ├── theme/
   │   └── utils/
   ├── models/
   │   ├── user.dart
   │   ├── product.dart
   │   ├── order.dart
   │   ├── category.dart
   │   └── api_exception.dart
   ├── providers/
   │   ├── auth_provider.dart
   │   ├── product_provider.dart
   │   └── order_provider.dart
   ├── services/
   │   ├── api_client.dart
   │   ├── auth_service.dart
   │   ├── product_service.dart
   │   └── order_service.dart
   └── screens/
       ├── auth/
       ├── dashboard/
       ├── products/
       └── pos/
   ```

**Deliverable:** ✅ Clean project structure ready for development

---

### Day 3-4: Authentication System
**Goal:** User dapat register, login, dan logout

**Implementation:**

#### 1. Create Models
```dart
// lib/models/user.dart
class User {
  final int id;
  final String name;
  final String email;
  final String? phone;
  final int roleId;
  final int? businessId;
  final int? outletId;
  final Business? business;
  final Outlet? outlet;
  final Role? role;

  User({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    required this.roleId,
    this.businessId,
    this.outletId,
    this.business,
    this.outlet,
    this.role,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      phone: json['phone'],
      roleId: json['role_id'],
      businessId: json['business_id'],
      outletId: json['outlet_id'],
      business: json['business'] != null
          ? Business.fromJson(json['business'])
          : null,
      outlet: json['outlet'] != null
          ? Outlet.fromJson(json['outlet'])
          : null,
      role: json['role'] != null ? Role.fromJson(json['role']) : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'phone': phone,
      'role_id': roleId,
      'business_id': businessId,
      'outlet_id': outletId,
    };
  }

  // Helper getters
  bool get isOwner => roleId == 1;
  bool get isManager => roleId == 2;
  bool get isStaff => roleId == 3;

  bool get canManageProducts => isOwner || isManager;
  bool get canManageOutlets => isOwner;
  bool get canVoidOrders => isOwner || isManager;
}
```

#### 2. Create Auth Service
```dart
// lib/services/auth_service.dart
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../config/api_config.dart';
import '../models/user.dart';
import '../models/api_exception.dart';
import 'dart:convert';

class AuthService {
  final Dio _dio;
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  AuthService(this._dio);

  Future<User> register({
    required String name,
    required String email,
    required String password,
    required String businessName,
    required String address,
  }) async {
    try {
      final response = await _dio.post(
        ApiConfig.register,
        data: {
          'name': name,
          'email': email,
          'password': password,
          'business_name': businessName,
          'address': address,
        },
      );

      // Save token
      await _storage.write(
        key: 'auth_token',
        value: response.data['access_token'],
      );

      // Save user
      final user = User.fromJson(response.data['data']);
      await _storage.write(
        key: 'user_data',
        value: jsonEncode(user.toJson()),
      );

      return user;
    } on DioException catch (e) {
      throw ApiException.fromDioError(e);
    }
  }

  Future<User> login({
    required String email,
    required String password,
  }) async {
    try {
      final response = await _dio.post(
        ApiConfig.login,
        data: {
          'email': email,
          'password': password,
        },
      );

      // Save token
      await _storage.write(
        key: 'auth_token',
        value: response.data['access_token'],
      );

      // Save user
      final user = User.fromJson(response.data['data']);
      await _storage.write(
        key: 'user_data',
        value: jsonEncode(user.toJson()),
      );

      return user;
    } on DioException catch (e) {
      if (e.response?.statusCode == 401) {
        throw ApiException('Email atau password salah');
      }
      throw ApiException.fromDioError(e);
    }
  }

  Future<void> logout() async {
    try {
      await _dio.post(ApiConfig.logout);
    } finally {
      await _storage.delete(key: 'auth_token');
      await _storage.delete(key: 'user_data');
    }
  }

  Future<User?> getCurrentUser() async {
    try {
      final token = await _storage.read(key: 'auth_token');
      if (token == null) return null;

      final response = await _dio.get(ApiConfig.me);
      final user = User.fromJson(response.data['data']);

      // Update cached data
      await _storage.write(
        key: 'user_data',
        value: jsonEncode(user.toJson()),
      );

      return user;
    } catch (e) {
      return null;
    }
  }

  Future<User?> getCachedUser() async {
    try {
      final userData = await _storage.read(key: 'user_data');
      if (userData == null) return null;
      return User.fromJson(jsonDecode(userData));
    } catch (e) {
      return null;
    }
  }

  Future<String?> getToken() async {
    return await _storage.read(key: 'auth_token');
  }

  Future<bool> isLoggedIn() async {
    final token = await getToken();
    return token != null;
  }
}
```

#### 3. Create Auth Provider
```dart
// lib/providers/auth_provider.dart
import 'package:flutter/foundation.dart';
import '../models/user.dart';
import '../services/auth_service.dart';

class AuthProvider with ChangeNotifier {
  final AuthService _authService;
  User? _user;
  bool _isLoading = false;
  String? _error;

  AuthProvider(this._authService) {
    _init();
  }

  User? get user => _user;
  bool get isLoading => _isLoading;
  String? get error => _error;
  bool get isAuthenticated => _user != null;

  Future<void> _init() async {
    _isLoading = true;
    notifyListeners();

    try {
      _user = await _authService.getCurrentUser();
    } catch (e) {
      // Try cached user
      _user = await _authService.getCachedUser();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> register({
    required String name,
    required String email,
    required String password,
    required String businessName,
    required String address,
  }) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      _user = await _authService.register(
        name: name,
        email: email,
        password: password,
        businessName: businessName,
        address: address,
      );
      _isLoading = false;
      notifyListeners();
      return true;
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> login({
    required String email,
    required String password,
  }) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      _user = await _authService.login(
        email: email,
        password: password,
      );
      _isLoading = false;
      notifyListeners();
      return true;
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<void> logout() async {
    await _authService.logout();
    _user = null;
    notifyListeners();
  }

  void clearError() {
    _error = null;
    notifyListeners();
  }
}
```

#### 4. Create Login Screen
```dart
// lib/screens/auth/login_screen.dart
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({Key? key}) : super(key: key);

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _obscurePassword = true;

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _handleLogin() async {
    if (!_formKey.currentState!.validate()) return;

    final authProvider = context.read<AuthProvider>();

    final success = await authProvider.login(
      email: _emailController.text.trim(),
      password: _passwordController.text,
    );

    if (!mounted) return;

    if (success) {
      Navigator.of(context).pushReplacementNamed('/dashboard');
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(authProvider.error ?? 'Login failed'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(24.0),
            child: Form(
              key: _formKey,
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  // Logo
                  Icon(
                    Icons.point_of_sale,
                    size: 80,
                    color: Theme.of(context).primaryColor,
                  ),
                  const SizedBox(height: 16),

                  // Title
                  Text(
                    'Academy POS',
                    style: Theme.of(context).textTheme.headlineMedium,
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Sign in to continue',
                    style: Theme.of(context).textTheme.bodyMedium,
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 48),

                  // Email
                  TextFormField(
                    controller: _emailController,
                    keyboardType: TextInputType.emailAddress,
                    decoration: const InputDecoration(
                      labelText: 'Email',
                      prefixIcon: Icon(Icons.email_outlined),
                      border: OutlineInputBorder(),
                    ),
                    validator: (value) {
                      if (value == null || value.isEmpty) {
                        return 'Please enter your email';
                      }
                      if (!value.contains('@')) {
                        return 'Please enter a valid email';
                      }
                      return null;
                    },
                  ),
                  const SizedBox(height: 16),

                  // Password
                  TextFormField(
                    controller: _passwordController,
                    obscureText: _obscurePassword,
                    decoration: InputDecoration(
                      labelText: 'Password',
                      prefixIcon: const Icon(Icons.lock_outlined),
                      border: const OutlineInputBorder(),
                      suffixIcon: IconButton(
                        icon: Icon(
                          _obscurePassword
                              ? Icons.visibility_outlined
                              : Icons.visibility_off_outlined,
                        ),
                        onPressed: () {
                          setState(() {
                            _obscurePassword = !_obscurePassword;
                          });
                        },
                      ),
                    ),
                    validator: (value) {
                      if (value == null || value.isEmpty) {
                        return 'Please enter your password';
                      }
                      return null;
                    },
                  ),
                  const SizedBox(height: 24),

                  // Login Button
                  Consumer<AuthProvider>(
                    builder: (context, authProvider, child) {
                      return ElevatedButton(
                        onPressed: authProvider.isLoading ? null : _handleLogin,
                        style: ElevatedButton.styleFrom(
                          padding: const EdgeInsets.all(16),
                        ),
                        child: authProvider.isLoading
                            ? const SizedBox(
                                height: 20,
                                width: 20,
                                child: CircularProgressIndicator(
                                  strokeWidth: 2,
                                  valueColor: AlwaysStoppedAnimation<Color>(
                                      Colors.white),
                                ),
                              )
                            : const Text(
                                'LOGIN',
                                style: TextStyle(fontSize: 16),
                              ),
                      );
                    },
                  ),
                  const SizedBox(height: 16),

                  // Register Link
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Text('Don\'t have an account? '),
                      TextButton(
                        onPressed: () {
                          Navigator.of(context).pushNamed('/register');
                        },
                        child: const Text('Register'),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
```

**Testing Checklist:**
- [ ] Register new account
- [ ] Login with valid credentials
- [ ] Login with invalid credentials (should show error)
- [ ] Logout
- [ ] Token persists after app restart
- [ ] Auto-login when token valid

**Deliverable:** ✅ Working authentication system

---

### Day 5: Dashboard
**Goal:** Display dashboard dengan real-time stats

**Implementation:**

```dart
// lib/screens/dashboard/dashboard_screen.dart
class DashboardScreen extends StatefulWidget {
  const DashboardScreen({Key? key}) : super(key: key);

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  final DashboardService _dashboardService = DashboardService();
  DashboardStats? _stats;
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadDashboard();
  }

  Future<void> _loadDashboard() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final stats = await _dashboardService.getStats();
      setState(() {
        _stats = stats;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadDashboard,
          ),
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: () async {
              await context.read<AuthProvider>().logout();
              Navigator.of(context).pushReplacementNamed('/login');
            },
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _error != null
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text('Error: $_error'),
                      ElevatedButton(
                        onPressed: _loadDashboard,
                        child: const Text('Retry'),
                      ),
                    ],
                  ),
                )
              : RefreshIndicator(
                  onRefresh: _loadDashboard,
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Today's Sales
                        _buildSalesCard(_stats!.today),
                        const SizedBox(height: 16),

                        // Monthly Stats
                        _buildMonthlyCard(_stats!.thisMonth),
                        const SizedBox(height: 16),

                        // Alerts
                        if (_stats!.alerts.lowStockCount > 0)
                          _buildAlertCard(_stats!.alerts),
                        const SizedBox(height: 16),

                        // Top Products
                        _buildTopProductsSection(_stats!.topProducts),
                      ],
                    ),
                  ),
                ),
    );
  }

  Widget _buildSalesCard(TodayStats today) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'Today\'s Sales',
                  style: Theme.of(context).textTheme.titleLarge,
                ),
                Text(
                  today.date,
                  style: Theme.of(context).textTheme.bodySmall,
                ),
              ],
            ),
            const SizedBox(height: 16),
            Text(
              'Rp ${_formatCurrency(today.sales)}',
              style: Theme.of(context).textTheme.headlineMedium?.copyWith(
                    color: Colors.green,
                    fontWeight: FontWeight.bold,
                  ),
            ),
            const SizedBox(height: 16),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                _buildStatItem(
                  icon: Icons.receipt_long,
                  label: 'Transactions',
                  value: '${today.transactions}',
                ),
                _buildStatItem(
                  icon: Icons.people,
                  label: 'Customers',
                  value: '${today.customers}',
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatItem({
    required IconData icon,
    required String label,
    required String value,
  }) {
    return Column(
      children: [
        Icon(icon, size: 32),
        const SizedBox(height: 8),
        Text(
          value,
          style: Theme.of(context).textTheme.titleLarge?.copyWith(
                fontWeight: FontWeight.bold,
              ),
        ),
        Text(
          label,
          style: Theme.of(context).textTheme.bodySmall,
        ),
      ],
    );
  }

  String _formatCurrency(double amount) {
    return amount.toStringAsFixed(0).replaceAllMapped(
          RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
          (Match m) => '${m[1]},',
        );
  }
}
```

**Testing Checklist:**
- [ ] Dashboard loads successfully
- [ ] Shows correct today's stats
- [ ] Shows monthly stats
- [ ] Pull to refresh works
- [ ] Low stock alerts display correctly
- [ ] Logout button works

**Deliverable:** ✅ Working dashboard with stats

**Week 1 Complete!** 🎉

---

## WEEK 2: Product & Category Management

### Day 6-7: Category Management
**Goal:** User dapat manage categories

**Implementation:**
- Category list screen
- Add category dialog
- Edit category dialog
- Delete category confirmation

**Testing Checklist:**
- [ ] View category list
- [ ] Create new category
- [ ] Update category name
- [ ] Permission check (owner/manager only)

---

### Day 8-10: Product Management
**Goal:** User dapat manage products dengan image upload

**Key Features:**
1. Product list with search & filter
2. Add product with image picker
3. Update product
4. Delete product
5. View product detail
6. Stock indicator (low stock alert)

**Testing Checklist:**
- [ ] View product list
- [ ] Search products by name
- [ ] Filter by category
- [ ] Add product with image
- [ ] Add product without image (show color)
- [ ] Update product
- [ ] Delete product
- [ ] Low stock indicator visible
- [ ] Permission check

**Deliverable:** ✅ Complete product management

**Week 2 Complete!** 🎉

---

## WEEK 3: Point of Sale (POS)

### Day 11-13: POS Screen
**Goal:** User dapat create orders (main feature!)

**Key Components:**
1. Product grid/list selection
2. Cart management
3. Quantity adjustment
4. Tax & discount
5. Payment method selection
6. Cash calculator (with change)
7. Order confirmation

**Testing Checklist:**
- [ ] Add products to cart
- [ ] Adjust quantity in cart
- [ ] Remove items from cart
- [ ] Calculate tax correctly
- [ ] Apply discount
- [ ] Select payment method
- [ ] Cash payment with change calculation
- [ ] Card/QRIS payment (exact amount)
- [ ] Create order successfully
- [ ] Stock deducts for stock-managed products
- [ ] Stock unchanged for non-stock-managed products

---

### Day 14-15: Order Management
**Goal:** View and manage orders

**Key Features:**
1. Order list with filters
2. Order detail view
3. Void order (owner/manager only)
4. Receipt printing

**Testing Checklist:**
- [ ] View order list
- [ ] Filter by date
- [ ] Filter by status
- [ ] Filter by payment method
- [ ] View order detail
- [ ] Void order (manager only)
- [ ] Stock returns when voided

**Deliverable:** ✅ Complete POS system

**Week 3 Complete!** 🎉

---

## WEEK 4: Inventory & Reports

### Day 16-17: Stock Management
**Goal:** Monitor and adjust stock

**Key Features:**
1. Stock list per product
2. Add stock (stock in)
3. Reduce stock (stock out)
4. Stock history
5. Low stock alerts

**Testing Checklist:**
- [ ] View stock per outlet
- [ ] Add stock
- [ ] Reduce stock
- [ ] View stock history
- [ ] Low stock alerts work

---

### Day 18-20: Reports
**Goal:** Business insights and analytics

**Key Features:**
1. Daily sales report
2. Monthly sales report
3. Top products
4. Payment method breakdown
5. Export to PDF (optional)

**Deliverable:** ✅ Reporting system

**Week 4 Complete!** 🎉

---

## WEEK 5: Multi-User & Polish

### Day 21-22: Staff Management
**Goal:** Manage team members

**Key Features:**
1. Staff list
2. Add staff/manager
3. Edit staff
4. Role-based UI (hide features based on role)

---

### Day 23-24: Outlet Management (Owner only)
**Goal:** Manage multiple outlets

**Key Features:**
1. Outlet list
2. Add outlet
3. Update outlet
4. Switch outlet

---

### Day 25: Testing & Bug Fixes
**Goal:** Comprehensive testing

**Focus Areas:**
- [ ] Multi-tenancy isolation
- [ ] Permission enforcement
- [ ] Error handling
- [ ] Offline behavior
- [ ] Performance optimization

---

### Day 26-30: Polish & Deployment
**Goal:** Production-ready app

**Tasks:**
1. UI/UX refinement
2. Loading states
3. Error messages
4. App icon & splash screen
5. Build APK/IPA
6. Internal testing

**Deliverable:** ✅ Production-ready app!

---

## Development Best Practices

### Code Organization
```dart
// Use const constructors
const SizedBox(height: 16)

// Extract widgets
Widget _buildCard() { }

// Use meaningful names
final isStockLow = product.totalStock <= product.stockMinimum;

// Null safety
final imageUrl = product.imageUrl ?? '';
```

### Error Handling
```dart
try {
  final result = await service.getData();
} on ApiException catch (e) {
  // Handle API errors
  if (e.statusCode == 403) {
    // Show permission denied
  }
} catch (e) {
  // Handle unexpected errors
  print('Error: $e');
}
```

### State Management
```dart
// Use Provider for global state
final user = context.watch<AuthProvider>().user;

// Use setState for local state
setState(() {
  _selectedIndex = index;
});
```

---

## Success Metrics

### Week 1
- [ ] User can register and login
- [ ] Dashboard shows correct data

### Week 2
- [ ] User can manage products
- [ ] Image upload works

### Week 3
- [ ] User can create orders
- [ ] Stock deducts correctly

### Week 4
- [ ] Reports are accurate
- [ ] Stock management works

### Week 5
- [ ] Multi-outlet works
- [ ] All permissions enforced
- [ ] App is production-ready

---

## Support & Resources

### Documentation
- Flutter Docs: https://docs.flutter.dev
- Provider: https://pub.dev/packages/provider
- Dio: https://pub.dev/packages/dio

### API Documentation
- See: `FLUTTER_API_INTEGRATION.md`
- See: `API_ENDPOINTS_REFERENCE.md`

---

_Happy Coding! 🚀_
