# Flutter Clean Architecture with BLoC - Academy POS

## 🏗️ Architecture Overview

```
Presentation Layer (UI)
    ↓
BLoC Layer (Business Logic)
    ↓
Service/Repository Layer
    ↓
DataSource Layer (Remote & Local)
    ↓
Models (with Freezed)
```

### Flow:
```
UI → Event → BLoC → Service → DataSource → API/DB
                ↓
UI ← State ← BLoC ← Service ← DataSource ← API/DB
```

---

## 📦 Dependencies

### pubspec.yaml
```yaml
dependencies:
  flutter:
    sdk: flutter

  # State Management
  flutter_bloc: ^8.1.3
  equatable: ^2.0.5

  # Functional Programming
  dartz: ^0.10.1

  # HTTP Client
  http: ^1.1.0

  # Local Storage
  shared_preferences: ^2.2.2
  sqflite: ^2.3.0
  path: ^1.8.3

  # Code Generation
  freezed_annotation: ^2.4.1
  json_annotation: ^4.8.1

  # Utils
  intl: ^0.18.1
  cached_network_image: ^3.3.0
  image_picker: ^1.0.5
  google_fonts: ^6.1.0

dev_dependencies:
  flutter_test:
    sdk: flutter

  # Code Generators
  build_runner: ^2.4.7
  freezed: ^2.4.5
  json_serializable: ^6.7.1

  # Linting
  flutter_lints: ^3.0.1

  # Testing
  bloc_test: ^9.1.5
  mocktail: ^1.0.2
```

---

## 📁 Folder Structure

```
lib/
├── core/
│   ├── constants/
│   │   ├── api_constants.dart
│   │   └── app_constants.dart
│   ├── errors/
│   │   ├── exceptions.dart
│   │   └── failures.dart
│   ├── network/
│   │   ├── http_client.dart
│   │   └── network_info.dart
│   ├── storage/
│   │   ├── local_storage.dart
│   │   └── database_helper.dart
│   └── utils/
│       ├── screen_size.dart
│       └── formatters.dart
│
├── features/
│   ├── auth/
│   │   ├── data/
│   │   │   ├── datasources/
│   │   │   │   ├── auth_remote_datasource.dart
│   │   │   │   └── auth_local_datasource.dart
│   │   │   ├── models/
│   │   │   │   ├── user_model.dart
│   │   │   │   └── user_model.freezed.dart
│   │   │   └── repositories/
│   │   │       └── auth_repository_impl.dart
│   │   ├── domain/
│   │   │   ├── entities/
│   │   │   │   └── user.dart
│   │   │   ├── repositories/
│   │   │   │   └── auth_repository.dart
│   │   │   └── usecases/
│   │   │       ├── login_usecase.dart
│   │   │       └── logout_usecase.dart
│   │   └── presentation/
│   │       ├── bloc/
│   │       │   ├── auth_bloc.dart
│   │       │   ├── auth_event.dart
│   │       │   └── auth_state.dart
│   │       ├── pages/
│   │       │   ├── login_page.dart
│   │       │   └── register_page.dart
│   │       └── widgets/
│   │           └── login_form.dart
│   │
│   ├── dashboard/
│   │   ├── data/
│   │   ├── domain/
│   │   └── presentation/
│   │
│   ├── products/
│   │   ├── data/
│   │   ├── domain/
│   │   └── presentation/
│   │
│   └── pos/
│       ├── data/
│       ├── domain/
│       └── presentation/
│
├── injection.dart (Dependency Injection)
└── main.dart
```

---

## 🔧 Core Setup

### 1. API Constants
```dart
// lib/core/constants/api_constants.dart
class ApiConstants {
  static const String baseUrl = 'http://127.0.0.1:8000/api';
  static const String storageUrl = 'http://127.0.0.1:8000/storage';

  // Auth endpoints
  static const String login = '/login';
  static const String register = '/register';
  static const String logout = '/logout';
  static const String me = '/me';

  // Products endpoints
  static const String products = '/get-products';
  static const String addProduct = '/add-product';
  static const String updateProduct = '/update-product';
  static const String deleteProduct = '/delete-product';

  // Orders endpoints
  static const String orders = '/get-orders';
  static const String addOrder = '/add-order';

  // Dashboard
  static const String dashboard = '/dashboard';
}
```

---

### 2. HTTP Client
```dart
// lib/core/network/http_client.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import '../storage/local_storage.dart';
import '../errors/exceptions.dart';

class HttpClient {
  final http.Client client;
  final LocalStorage localStorage;

  HttpClient({
    required this.client,
    required this.localStorage,
  });

  Future<Map<String, String>> _getHeaders() async {
    final token = await localStorage.getToken();
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  Future<dynamic> get(String url) async {
    try {
      final headers = await _getHeaders();
      final response = await client.get(
        Uri.parse(url),
        headers: headers,
      );

      return _handleResponse(response);
    } catch (e) {
      throw ServerException(message: e.toString());
    }
  }

  Future<dynamic> post(
    String url, {
    Map<String, dynamic>? body,
  }) async {
    try {
      final headers = await _getHeaders();
      final response = await client.post(
        Uri.parse(url),
        headers: headers,
        body: body != null ? jsonEncode(body) : null,
      );

      return _handleResponse(response);
    } catch (e) {
      throw ServerException(message: e.toString());
    }
  }

  Future<dynamic> postMultipart(
    String url, {
    required Map<String, String> fields,
    Map<String, String>? files,
  }) async {
    try {
      final token = await localStorage.getToken();
      final request = http.MultipartRequest('POST', Uri.parse(url));

      if (token != null) {
        request.headers['Authorization'] = 'Bearer $token';
      }

      request.fields.addAll(fields);

      if (files != null) {
        for (var entry in files.entries) {
          request.files.add(
            await http.MultipartFile.fromPath(entry.key, entry.value),
          );
        }
      }

      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);

      return _handleResponse(response);
    } catch (e) {
      throw ServerException(message: e.toString());
    }
  }

  Future<dynamic> put(
    String url, {
    Map<String, dynamic>? body,
  }) async {
    try {
      final headers = await _getHeaders();
      final response = await client.put(
        Uri.parse(url),
        headers: headers,
        body: body != null ? jsonEncode(body) : null,
      );

      return _handleResponse(response);
    } catch (e) {
      throw ServerException(message: e.toString());
    }
  }

  Future<dynamic> delete(String url) async {
    try {
      final headers = await _getHeaders();
      final response = await client.delete(
        Uri.parse(url),
        headers: headers,
      );

      return _handleResponse(response);
    } catch (e) {
      throw ServerException(message: e.toString());
    }
  }

  dynamic _handleResponse(http.Response response) {
    if (response.statusCode >= 200 && response.statusCode < 300) {
      if (response.body.isEmpty) return null;
      return jsonDecode(response.body);
    } else if (response.statusCode == 401) {
      throw UnauthorizedException();
    } else if (response.statusCode == 403) {
      throw ForbiddenException(
        message: jsonDecode(response.body)['message'] ?? 'Forbidden',
      );
    } else if (response.statusCode == 404) {
      throw NotFoundException();
    } else if (response.statusCode == 422) {
      final data = jsonDecode(response.body);
      throw ValidationException(
        message: data['message'],
        errors: data['errors'],
      );
    } else {
      throw ServerException(
        message: jsonDecode(response.body)['message'] ?? 'Server error',
      );
    }
  }
}
```

---

### 3. Exceptions
```dart
// lib/core/errors/exceptions.dart
class ServerException implements Exception {
  final String message;
  ServerException({this.message = 'Server error occurred'});
}

class UnauthorizedException implements Exception {
  final String message;
  UnauthorizedException({this.message = 'Unauthorized'});
}

class ForbiddenException implements Exception {
  final String message;
  ForbiddenException({this.message = 'Forbidden'});
}

class NotFoundException implements Exception {
  final String message;
  NotFoundException({this.message = 'Not found'});
}

class ValidationException implements Exception {
  final String message;
  final Map<String, dynamic>? errors;
  ValidationException({this.message = 'Validation error', this.errors});
}

class CacheException implements Exception {
  final String message;
  CacheException({this.message = 'Cache error'});
}

class NetworkException implements Exception {
  final String message;
  NetworkException({this.message = 'Network error'});
}
```

---

### 4. Failures (for Dartz)
```dart
// lib/core/errors/failures.dart
import 'package:equatable/equatable.dart';

abstract class Failure extends Equatable {
  final String message;

  const Failure(this.message);

  @override
  List<Object?> get props => [message];
}

class ServerFailure extends Failure {
  const ServerFailure([String message = 'Server error']) : super(message);
}

class UnauthorizedFailure extends Failure {
  const UnauthorizedFailure([String message = 'Unauthorized']) : super(message);
}

class ForbiddenFailure extends Failure {
  const ForbiddenFailure([String message = 'Forbidden']) : super(message);
}

class NotFoundFailure extends Failure {
  const NotFoundFailure([String message = 'Not found']) : super(message);
}

class ValidationFailure extends Failure {
  final Map<String, dynamic>? errors;

  const ValidationFailure({
    String message = 'Validation error',
    this.errors,
  }) : super(message);

  @override
  List<Object?> get props => [message, errors];
}

class CacheFailure extends Failure {
  const CacheFailure([String message = 'Cache error']) : super(message);
}

class NetworkFailure extends Failure {
  const NetworkFailure([String message = 'Network error']) : super(message);
}
```

---

### 5. Local Storage (SharedPreferences)
```dart
// lib/core/storage/local_storage.dart
import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';

class LocalStorage {
  final SharedPreferences sharedPreferences;

  LocalStorage({required this.sharedPreferences});

  // Token
  static const String _tokenKey = 'auth_token';

  Future<bool> saveToken(String token) async {
    return await sharedPreferences.setString(_tokenKey, token);
  }

  String? getToken() {
    return sharedPreferences.getString(_tokenKey);
  }

  Future<bool> removeToken() async {
    return await sharedPreferences.remove(_tokenKey);
  }

  // User Data
  static const String _userKey = 'user_data';

  Future<bool> saveUser(Map<String, dynamic> userData) async {
    return await sharedPreferences.setString(
      _userKey,
      jsonEncode(userData),
    );
  }

  Map<String, dynamic>? getUser() {
    final userString = sharedPreferences.getString(_userKey);
    if (userString == null) return null;
    return jsonDecode(userString) as Map<String, dynamic>;
  }

  Future<bool> removeUser() async {
    return await sharedPreferences.remove(_userKey);
  }

  // Clear all
  Future<bool> clearAll() async {
    return await sharedPreferences.clear();
  }

  // Generic methods
  Future<bool> saveString(String key, String value) async {
    return await sharedPreferences.setString(key, value);
  }

  String? getString(String key) {
    return sharedPreferences.getString(key);
  }

  Future<bool> saveBool(String key, bool value) async {
    return await sharedPreferences.setBool(key, value);
  }

  bool? getBool(String key) {
    return sharedPreferences.getBool(key);
  }

  Future<bool> saveInt(String key, int value) async {
    return await sharedPreferences.setInt(key, value);
  }

  int? getInt(String key) {
    return sharedPreferences.getInt(key);
  }

  Future<bool> remove(String key) async {
    return await sharedPreferences.remove(key);
  }
}
```

---

### 6. Database Helper (SQFlite)
```dart
// lib/core/storage/database_helper.dart
import 'package:sqflite/sqflite.dart';
import 'package:path/path.dart';

class DatabaseHelper {
  static final DatabaseHelper _instance = DatabaseHelper._internal();
  static Database? _database;

  factory DatabaseHelper() => _instance;

  DatabaseHelper._internal();

  Future<Database> get database async {
    if (_database != null) return _database!;
    _database = await _initDatabase();
    return _database!;
  }

  Future<Database> _initDatabase() async {
    final databasesPath = await getDatabasesPath();
    final path = join(databasesPath, 'academy_pos.db');

    return await openDatabase(
      path,
      version: 1,
      onCreate: _onCreate,
      onUpgrade: _onUpgrade,
    );
  }

  Future<void> _onCreate(Database db, int version) async {
    // Products table (offline cache)
    await db.execute('''
      CREATE TABLE products (
        id INTEGER PRIMARY KEY,
        name TEXT NOT NULL,
        category_id INTEGER,
        business_id INTEGER,
        description TEXT,
        image TEXT,
        color TEXT,
        price REAL,
        cost REAL,
        barcode TEXT,
        sku TEXT,
        status TEXT,
        is_stock_managed INTEGER,
        stock_minimum INTEGER,
        created_at TEXT,
        updated_at TEXT
      )
    ''');

    // Categories table
    await db.execute('''
      CREATE TABLE categories (
        id INTEGER PRIMARY KEY,
        name TEXT NOT NULL,
        business_id INTEGER,
        created_at TEXT,
        updated_at TEXT
      )
    ''');

    // Orders table (offline queue)
    await db.execute('''
      CREATE TABLE pending_orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_data TEXT NOT NULL,
        created_at TEXT,
        synced INTEGER DEFAULT 0
      )
    ''');

    // Stocks table (cache)
    await db.execute('''
      CREATE TABLE stocks (
        id INTEGER PRIMARY KEY,
        product_id INTEGER,
        outlet_id INTEGER,
        quantity INTEGER,
        updated_at TEXT
      )
    ''');
  }

  Future<void> _onUpgrade(Database db, int oldVersion, int newVersion) async {
    // Handle database upgrades
  }

  // Generic CRUD operations
  Future<int> insert(String table, Map<String, dynamic> data) async {
    final db = await database;
    return await db.insert(table, data);
  }

  Future<List<Map<String, dynamic>>> query(
    String table, {
    String? where,
    List<dynamic>? whereArgs,
    String? orderBy,
    int? limit,
  }) async {
    final db = await database;
    return await db.query(
      table,
      where: where,
      whereArgs: whereArgs,
      orderBy: orderBy,
      limit: limit,
    );
  }

  Future<int> update(
    String table,
    Map<String, dynamic> data, {
    String? where,
    List<dynamic>? whereArgs,
  }) async {
    final db = await database;
    return await db.update(
      table,
      data,
      where: where,
      whereArgs: whereArgs,
    );
  }

  Future<int> delete(
    String table, {
    String? where,
    List<dynamic>? whereArgs,
  }) async {
    final db = await database;
    return await db.delete(
      table,
      where: where,
      whereArgs: whereArgs,
    );
  }

  Future<void> clearTable(String table) async {
    final db = await database;
    await db.delete(table);
  }

  Future<void> closeDatabase() async {
    final db = await database;
    await db.close();
  }
}
```

---

## 🎯 Feature Implementation: Authentication

### 1. Entity (Domain Layer)
```dart
// lib/features/auth/domain/entities/user.dart
import 'package:equatable/equatable.dart';

class User extends Equatable {
  final int id;
  final String name;
  final String email;
  final String? phone;
  final int roleId;
  final int? businessId;
  final int? outletId;

  const User({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    required this.roleId,
    this.businessId,
    this.outletId,
  });

  @override
  List<Object?> get props => [
        id,
        name,
        email,
        phone,
        roleId,
        businessId,
        outletId,
      ];

  // Helper methods
  bool get isOwner => roleId == 1;
  bool get isManager => roleId == 2;
  bool get isStaff => roleId == 3;

  bool get canManageProducts => isOwner || isManager;
  bool get canManageOutlets => isOwner;
  bool get canVoidOrders => isOwner || isManager;
}
```

---

### 2. Model with Freezed (Data Layer)
```dart
// lib/features/auth/data/models/user_model.dart
import 'package:freezed_annotation/freezed_annotation.dart';
import '../../domain/entities/user.dart';

part 'user_model.freezed.dart';
part 'user_model.g.dart';

@freezed
class UserModel with _$UserModel {
  const UserModel._();

  const factory UserModel({
    required int id,
    required String name,
    required String email,
    String? phone,
    @JsonKey(name: 'role_id') required int roleId,
    @JsonKey(name: 'business_id') int? businessId,
    @JsonKey(name: 'outlet_id') int? outletId,
    @JsonKey(name: 'created_at') String? createdAt,
    @JsonKey(name: 'updated_at') String? updatedAt,
  }) = _UserModel;

  factory UserModel.fromJson(Map<String, dynamic> json) =>
      _$UserModelFromJson(json);

  // Convert to entity
  User toEntity() {
    return User(
      id: id,
      name: name,
      email: email,
      phone: phone,
      roleId: roleId,
      businessId: businessId,
      outletId: outletId,
    );
  }

  // Convert from entity
  factory UserModel.fromEntity(User user) {
    return UserModel(
      id: user.id,
      name: user.name,
      email: user.email,
      phone: user.phone,
      roleId: user.roleId,
      businessId: user.businessId,
      outletId: user.outletId,
    );
  }
}
```

**Generate code:**
```bash
flutter pub run build_runner build --delete-conflicting-outputs
```

---

### 3. Remote DataSource
```dart
// lib/features/auth/data/datasources/auth_remote_datasource.dart
import 'package:dartz/dartz.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/network/http_client.dart';
import '../../../../core/errors/exceptions.dart';
import '../models/user_model.dart';

abstract class AuthRemoteDataSource {
  Future<Map<String, dynamic>> login({
    required String email,
    required String password,
  });

  Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String password,
    required String businessName,
    required String address,
  });

  Future<void> logout();

  Future<UserModel> getCurrentUser();
}

class AuthRemoteDataSourceImpl implements AuthRemoteDataSource {
  final HttpClient httpClient;

  AuthRemoteDataSourceImpl({required this.httpClient});

  @override
  Future<Map<String, dynamic>> login({
    required String email,
    required String password,
  }) async {
    final response = await httpClient.post(
      '${ApiConstants.baseUrl}${ApiConstants.login}',
      body: {
        'email': email,
        'password': password,
      },
    );

    return {
      'token': response['access_token'] as String,
      'user': UserModel.fromJson(response['data']),
    };
  }

  @override
  Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String password,
    required String businessName,
    required String address,
  }) async {
    final response = await httpClient.post(
      '${ApiConstants.baseUrl}${ApiConstants.register}',
      body: {
        'name': name,
        'email': email,
        'password': password,
        'business_name': businessName,
        'address': address,
      },
    );

    return {
      'token': response['access_token'] as String,
      'user': UserModel.fromJson(response['data']),
    };
  }

  @override
  Future<void> logout() async {
    await httpClient.post('${ApiConstants.baseUrl}${ApiConstants.logout}');
  }

  @override
  Future<UserModel> getCurrentUser() async {
    final response = await httpClient.get(
      '${ApiConstants.baseUrl}${ApiConstants.me}',
    );

    return UserModel.fromJson(response['data']);
  }
}
```

---

### 4. Local DataSource
```dart
// lib/features/auth/data/datasources/auth_local_datasource.dart
import '../../../../core/storage/local_storage.dart';
import '../../../../core/errors/exceptions.dart';
import '../models/user_model.dart';

abstract class AuthLocalDataSource {
  Future<void> cacheToken(String token);
  Future<String?> getToken();
  Future<void> removeToken();

  Future<void> cacheUser(UserModel user);
  Future<UserModel?> getCachedUser();
  Future<void> removeCachedUser();

  Future<void> clearAuth();
}

class AuthLocalDataSourceImpl implements AuthLocalDataSource {
  final LocalStorage localStorage;

  AuthLocalDataSourceImpl({required this.localStorage});

  @override
  Future<void> cacheToken(String token) async {
    await localStorage.saveToken(token);
  }

  @override
  Future<String?> getToken() async {
    return localStorage.getToken();
  }

  @override
  Future<void> removeToken() async {
    await localStorage.removeToken();
  }

  @override
  Future<void> cacheUser(UserModel user) async {
    await localStorage.saveUser(user.toJson());
  }

  @override
  Future<UserModel?> getCachedUser() async {
    try {
      final userData = localStorage.getUser();
      if (userData == null) return null;
      return UserModel.fromJson(userData);
    } catch (e) {
      throw CacheException();
    }
  }

  @override
  Future<void> removeCachedUser() async {
    await localStorage.removeUser();
  }

  @override
  Future<void> clearAuth() async {
    await localStorage.removeToken();
    await localStorage.removeUser();
  }
}
```

---

### 5. Repository Interface (Domain Layer)
```dart
// lib/features/auth/domain/repositories/auth_repository.dart
import 'package:dartz/dartz.dart';
import '../../../../core/errors/failures.dart';
import '../entities/user.dart';

abstract class AuthRepository {
  Future<Either<Failure, User>> login({
    required String email,
    required String password,
  });

  Future<Either<Failure, User>> register({
    required String name,
    required String email,
    required String password,
    required String businessName,
    required String address,
  });

  Future<Either<Failure, void>> logout();

  Future<Either<Failure, User>> getCurrentUser();

  Future<Either<Failure, User?>> getCachedUser();

  Future<bool> isLoggedIn();
}
```

---

### 6. Repository Implementation (Data Layer)
```dart
// lib/features/auth/data/repositories/auth_repository_impl.dart
import 'package:dartz/dartz.dart';
import '../../../../core/errors/exceptions.dart';
import '../../../../core/errors/failures.dart';
import '../../domain/entities/user.dart';
import '../../domain/repositories/auth_repository.dart';
import '../datasources/auth_remote_datasource.dart';
import '../datasources/auth_local_datasource.dart';

class AuthRepositoryImpl implements AuthRepository {
  final AuthRemoteDataSource remoteDataSource;
  final AuthLocalDataSource localDataSource;

  AuthRepositoryImpl({
    required this.remoteDataSource,
    required this.localDataSource,
  });

  @override
  Future<Either<Failure, User>> login({
    required String email,
    required String password,
  }) async {
    try {
      final result = await remoteDataSource.login(
        email: email,
        password: password,
      );

      // Cache token and user
      await localDataSource.cacheToken(result['token']);
      await localDataSource.cacheUser(result['user']);

      return Right(result['user'].toEntity());
    } on UnauthorizedException catch (e) {
      return Left(UnauthorizedFailure(e.message));
    } on ServerException catch (e) {
      return Left(ServerFailure(e.message));
    } on NetworkException catch (e) {
      return Left(NetworkFailure(e.message));
    } catch (e) {
      return Left(ServerFailure(e.toString()));
    }
  }

  @override
  Future<Either<Failure, User>> register({
    required String name,
    required String email,
    required String password,
    required String businessName,
    required String address,
  }) async {
    try {
      final result = await remoteDataSource.register(
        name: name,
        email: email,
        password: password,
        businessName: businessName,
        address: address,
      );

      // Cache token and user
      await localDataSource.cacheToken(result['token']);
      await localDataSource.cacheUser(result['user']);

      return Right(result['user'].toEntity());
    } on ValidationException catch (e) {
      return Left(ValidationFailure(message: e.message, errors: e.errors));
    } on ServerException catch (e) {
      return Left(ServerFailure(e.message));
    } catch (e) {
      return Left(ServerFailure(e.toString()));
    }
  }

  @override
  Future<Either<Failure, void>> logout() async {
    try {
      await remoteDataSource.logout();
      await localDataSource.clearAuth();
      return const Right(null);
    } on ServerException catch (e) {
      // Even if server logout fails, clear local data
      await localDataSource.clearAuth();
      return Left(ServerFailure(e.message));
    } catch (e) {
      await localDataSource.clearAuth();
      return Left(ServerFailure(e.toString()));
    }
  }

  @override
  Future<Either<Failure, User>> getCurrentUser() async {
    try {
      final user = await remoteDataSource.getCurrentUser();
      await localDataSource.cacheUser(user);
      return Right(user.toEntity());
    } on UnauthorizedException catch (e) {
      return Left(UnauthorizedFailure(e.message));
    } on ServerException catch (e) {
      return Left(ServerFailure(e.message));
    } catch (e) {
      return Left(ServerFailure(e.toString()));
    }
  }

  @override
  Future<Either<Failure, User?>> getCachedUser() async {
    try {
      final user = await localDataSource.getCachedUser();
      return Right(user?.toEntity());
    } on CacheException catch (e) {
      return Left(CacheFailure(e.message));
    } catch (e) {
      return Left(CacheFailure(e.toString()));
    }
  }

  @override
  Future<bool> isLoggedIn() async {
    final token = await localDataSource.getToken();
    return token != null;
  }
}
```

---

### 7. BLoC Events
```dart
// lib/features/auth/presentation/bloc/auth_event.dart
import 'package:equatable/equatable.dart';

abstract class AuthEvent extends Equatable {
  const AuthEvent();

  @override
  List<Object?> get props => [];
}

class LoginRequested extends AuthEvent {
  final String email;
  final String password;

  const LoginRequested({
    required this.email,
    required this.password,
  });

  @override
  List<Object?> get props => [email, password];
}

class RegisterRequested extends AuthEvent {
  final String name;
  final String email;
  final String password;
  final String businessName;
  final String address;

  const RegisterRequested({
    required this.name,
    required this.email,
    required this.password,
    required this.businessName,
    required this.address,
  });

  @override
  List<Object?> get props => [name, email, password, businessName, address];
}

class LogoutRequested extends AuthEvent {
  const LogoutRequested();
}

class CheckAuthStatus extends AuthEvent {
  const CheckAuthStatus();
}

class GetCurrentUser extends AuthEvent {
  const GetCurrentUser();
}
```

---

### 8. BLoC States
```dart
// lib/features/auth/presentation/bloc/auth_state.dart
import 'package:equatable/equatable.dart';
import '../../domain/entities/user.dart';

abstract class AuthState extends Equatable {
  const AuthState();

  @override
  List<Object?> get props => [];
}

class AuthInitial extends AuthState {
  const AuthInitial();
}

class AuthLoading extends AuthState {
  const AuthLoading();
}

class Authenticated extends AuthState {
  final User user;

  const Authenticated({required this.user});

  @override
  List<Object?> get props => [user];
}

class Unauthenticated extends AuthState {
  const Unauthenticated();
}

class AuthError extends AuthState {
  final String message;

  const AuthError({required this.message});

  @override
  List<Object?> get props => [message];
}
```

---

### 9. BLoC Implementation
```dart
// lib/features/auth/presentation/bloc/auth_bloc.dart
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../domain/repositories/auth_repository.dart';
import 'auth_event.dart';
import 'auth_state.dart';

class AuthBloc extends Bloc<AuthEvent, AuthState> {
  final AuthRepository authRepository;

  AuthBloc({required this.authRepository}) : super(const AuthInitial()) {
    on<LoginRequested>(_onLoginRequested);
    on<RegisterRequested>(_onRegisterRequested);
    on<LogoutRequested>(_onLogoutRequested);
    on<CheckAuthStatus>(_onCheckAuthStatus);
    on<GetCurrentUser>(_onGetCurrentUser);
  }

  Future<void> _onLoginRequested(
    LoginRequested event,
    Emitter<AuthState> emit,
  ) async {
    emit(const AuthLoading());

    final result = await authRepository.login(
      email: event.email,
      password: event.password,
    );

    result.fold(
      (failure) => emit(AuthError(message: failure.message)),
      (user) => emit(Authenticated(user: user)),
    );
  }

  Future<void> _onRegisterRequested(
    RegisterRequested event,
    Emitter<AuthState> emit,
  ) async {
    emit(const AuthLoading());

    final result = await authRepository.register(
      name: event.name,
      email: event.email,
      password: event.password,
      businessName: event.businessName,
      address: event.address,
    );

    result.fold(
      (failure) => emit(AuthError(message: failure.message)),
      (user) => emit(Authenticated(user: user)),
    );
  }

  Future<void> _onLogoutRequested(
    LogoutRequested event,
    Emitter<AuthState> emit,
  ) async {
    emit(const AuthLoading());

    final result = await authRepository.logout();

    result.fold(
      (failure) => emit(const Unauthenticated()),
      (_) => emit(const Unauthenticated()),
    );
  }

  Future<void> _onCheckAuthStatus(
    CheckAuthStatus event,
    Emitter<AuthState> emit,
  ) async {
    emit(const AuthLoading());

    final isLoggedIn = await authRepository.isLoggedIn();

    if (!isLoggedIn) {
      emit(const Unauthenticated());
      return;
    }

    final result = await authRepository.getCachedUser();

    result.fold(
      (failure) => emit(const Unauthenticated()),
      (user) {
        if (user != null) {
          emit(Authenticated(user: user));
        } else {
          emit(const Unauthenticated());
        }
      },
    );
  }

  Future<void> _onGetCurrentUser(
    GetCurrentUser event,
    Emitter<AuthState> emit,
  ) async {
    final result = await authRepository.getCurrentUser();

    result.fold(
      (failure) => emit(AuthError(message: failure.message)),
      (user) => emit(Authenticated(user: user)),
    );
  }
}
```

---

### 10. Login Page (UI)
```dart
// lib/features/auth/presentation/pages/login_page.dart
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../bloc/auth_bloc.dart';
import '../bloc/auth_event.dart';
import '../bloc/auth_state.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({Key? key}) : super(key: key);

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
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

  void _handleLogin() {
    if (!_formKey.currentState!.validate()) return;

    context.read<AuthBloc>().add(
          LoginRequested(
            email: _emailController.text.trim(),
            password: _passwordController.text,
          ),
        );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: BlocConsumer<AuthBloc, AuthState>(
          listener: (context, state) {
            if (state is Authenticated) {
              Navigator.of(context).pushReplacementNamed('/dashboard');
            } else if (state is AuthError) {
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(
                  content: Text(state.message),
                  backgroundColor: Colors.red,
                ),
              );
            }
          },
          builder: (context, state) {
            final isLoading = state is AuthLoading;

            return Center(
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
                        enabled: !isLoading,
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
                        enabled: !isLoading,
                      ),
                      const SizedBox(height: 24),

                      // Login Button
                      ElevatedButton(
                        onPressed: isLoading ? null : _handleLogin,
                        style: ElevatedButton.styleFrom(
                          padding: const EdgeInsets.all(16),
                        ),
                        child: isLoading
                            ? const SizedBox(
                                height: 20,
                                width: 20,
                                child: CircularProgressIndicator(
                                  strokeWidth: 2,
                                  valueColor: AlwaysStoppedAnimation<Color>(
                                    Colors.white,
                                  ),
                                ),
                              )
                            : const Text(
                                'LOGIN',
                                style: TextStyle(fontSize: 16),
                              ),
                      ),
                      const SizedBox(height: 16),

                      // Register Link
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Text('Don\'t have an account? '),
                          TextButton(
                            onPressed: isLoading
                                ? null
                                : () {
                                    Navigator.of(context)
                                        .pushNamed('/register');
                                  },
                            child: const Text('Register'),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            );
          },
        ),
      ),
    );
  }
}
```

---

## 📝 Code Generation Commands

```bash
# Generate Freezed models
flutter pub run build_runner build --delete-conflicting-outputs

# Watch for changes (development)
flutter pub run build_runner watch --delete-conflicting-outputs

# Clean before build
flutter pub run build_runner clean
flutter pub run build_runner build --delete-conflicting-outputs
```

---

## 🧪 Testing with bloc_test

```dart
// test/features/auth/presentation/bloc/auth_bloc_test.dart
import 'package:bloc_test/bloc_test.dart';
import 'package:dartz/dartz.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';

class MockAuthRepository extends Mock implements AuthRepository {}

void main() {
  late AuthBloc authBloc;
  late MockAuthRepository mockAuthRepository;

  setUp(() {
    mockAuthRepository = MockAuthRepository();
    authBloc = AuthBloc(authRepository: mockAuthRepository);
  });

  tearDown(() {
    authBloc.close();
  });

  const tUser = User(
    id: 1,
    name: 'Test User',
    email: 'test@test.com',
    roleId: 1,
  );

  group('LoginRequested', () {
    const tEmail = 'test@test.com';
    const tPassword = 'password';

    blocTest<AuthBloc, AuthState>(
      'emits [AuthLoading, Authenticated] when login succeeds',
      build: () {
        when(() => mockAuthRepository.login(
              email: any(named: 'email'),
              password: any(named: 'password'),
            )).thenAnswer((_) async => const Right(tUser));
        return authBloc;
      },
      act: (bloc) => bloc.add(const LoginRequested(
        email: tEmail,
        password: tPassword,
      )),
      expect: () => [
        const AuthLoading(),
        const Authenticated(user: tUser),
      ],
    );

    blocTest<AuthBloc, AuthState>(
      'emits [AuthLoading, AuthError] when login fails',
      build: () {
        when(() => mockAuthRepository.login(
              email: any(named: 'email'),
              password: any(named: 'password'),
            )).thenAnswer(
          (_) async => const Left(UnauthorizedFailure('Invalid credentials')),
        );
        return authBloc;
      },
      act: (bloc) => bloc.add(const LoginRequested(
        email: tEmail,
        password: tPassword,
      )),
      expect: () => [
        const AuthLoading(),
        const AuthError(message: 'Invalid credentials'),
      ],
    );
  });
}
```

---

## 📚 Next Steps

1. **Copy this architecture** untuk features lain:
   - Products
   - Orders/POS
   - Dashboard
   - etc.

2. **Generate Freezed models** untuk semua entities

3. **Implement offline-first** dengan SQFlite untuk POS

4. **Add interceptors** untuk auto-refresh token

5. **Implement error retry** mechanism

---

_Dokumentasi akan dilanjutkan dengan implementasi Products & POS di file berikutnya..._
