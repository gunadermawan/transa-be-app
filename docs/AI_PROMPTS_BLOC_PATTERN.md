# AI Prompts for BLoC Pattern - Academy POS

## 🎯 Template Prompts for Clean Architecture + BLoC

---

## 📝 Generate Complete Feature

### Prompt 1: Generate Complete Feature Structure
```
Saya ingin implement feature "Products" dengan Clean Architecture + BLoC pattern.

Arsitektur yang saya pakai:
- Data Layer: DataSource (Remote & Local) → Repository Implementation
- Domain Layer: Entity → Repository Interface → UseCase
- Presentation Layer: BLoC (Event, State, Bloc) → UI

Tech stack:
- flutter_bloc untuk state management
- freezed untuk models
- dartz untuk Either<Failure, Success>
- http untuk HTTP client
- sqflite untuk local database
- shared_preferences untuk cache

Referensi API endpoint dari API_ENDPOINTS_REFERENCE.md:
[PASTE Products section]

Tolong buatkan struktur folder dan list file yang harus dibuat untuk feature Products:
```

**Expected Output:**
```
lib/features/products/
├── data/
│   ├── datasources/
│   │   ├── product_remote_datasource.dart
│   │   └── product_local_datasource.dart
│   ├── models/
│   │   ├── product_model.dart (with Freezed)
│   │   └── product_model.g.dart (generated)
│   └── repositories/
│       └── product_repository_impl.dart
├── domain/
│   ├── entities/
│   │   └── product.dart
│   ├── repositories/
│   │   └── product_repository.dart
│   └── usecases/
│       ├── get_products_usecase.dart
│       ├── add_product_usecase.dart
│       └── delete_product_usecase.dart
└── presentation/
    ├── bloc/
    │   ├── product_bloc.dart
    │   ├── product_event.dart
    │   └── product_state.dart
    └── pages/
        └── products_page.dart
```

---

## 🎨 Generate Models with Freezed

### Prompt 2: Generate Freezed Model
```
Buatkan Product model dengan Freezed berdasarkan API response:

API Response:
[PASTE dari API_ENDPOINTS_REFERENCE.md - GET /get-products response]

Requirements:
1. Use @freezed annotation
2. Include JSON serialization (fromJson, toJson)
3. Add toEntity() method untuk convert ke domain entity
4. Add fromEntity() untuk convert dari domain entity
5. Handle nullable fields dengan benar
6. Use @JsonKey untuk field name mapping (snake_case ke camelCase)

File: lib/features/products/data/models/product_model.dart
```

---

### Prompt 3: Generate Entity (Domain)
```
Buatkan Product entity (domain layer) dengan Equatable.

Fields yang dibutuhkan berdasarkan model:
[LIST fields dari Product]

Tambahkan helper methods:
- imageUrl getter (combine base URL dengan image path)
- totalStock getter (sum stock dari semua outlets)
- isLowStock getter (check if stock <= minimum)

Requirements:
- Extend Equatable
- Immutable (use final fields)
- No JSON serialization (pure entity)

File: lib/features/products/domain/entities/product.dart
```

---

## 🔌 Generate DataSource Layer

### Prompt 4: Generate Remote DataSource
```
Buatkan ProductRemoteDataSource untuk handle API calls.

Endpoints yang harus di-cover:
[PASTE dari API_ENDPOINTS_REFERENCE.md - Products endpoints]

Requirements:
1. Abstract class untuk interface
2. Implementation class yang inject HttpClient
3. Handle multipart untuk image upload
4. Throw proper exceptions (ServerException, ValidationException, etc)
5. Return model objects, bukan entities

Methods needed:
- getProducts()
- getProduct(id)
- addProduct(product, imageFile)
- updateProduct(id, product)
- deleteProduct(id)

File: lib/features/products/data/datasources/product_remote_datasource.dart
```

---

### Prompt 5: Generate Local DataSource (SQFlite)
```
Buatkan ProductLocalDataSource untuk cache products di local database.

Requirements:
1. Use DatabaseHelper (already created in core)
2. CRUD operations untuk products table
3. Handle sync status untuk offline mode
4. Return ProductModel objects

Methods needed:
- cacheProducts(List<ProductModel> products)
- getCachedProducts()
- getCachedProduct(id)
- clearProductCache()
- markAsStale() // untuk offline sync

Table structure:
[PASTE dari FLUTTER_CLEAN_ARCHITECTURE_BLOC.md - products table]

File: lib/features/products/data/datasources/product_local_datasource.dart
```

---

## 🏪 Generate Repository Layer

### Prompt 6: Generate Repository Interface
```
Buatkan ProductRepository interface (abstract class) untuk domain layer.

Requirements:
1. Use Dartz Either<Failure, Success> untuk return types
2. Return entities, bukan models
3. Abstract methods only (no implementation)

Methods needed berdasarkan use cases:
- getProducts() → Either<Failure, List<Product>>
- getProduct(id) → Either<Failure, Product>
- addProduct(product, imageFile) → Either<Failure, Product>
- updateProduct(id, product, imageFile) → Either<Failure, Product>
- deleteProduct(id) → Either<Failure, void>

File: lib/features/products/domain/repositories/product_repository.dart
```

---

### Prompt 7: Generate Repository Implementation
```
Buatkan ProductRepositoryImpl yang implement ProductRepository.

Requirements:
1. Inject ProductRemoteDataSource dan ProductLocalDataSource
2. Implement all methods dari interface
3. Try remote first, fallback to cache
4. Handle all exceptions dan convert ke Failures
5. Convert models ke entities sebelum return

Pattern untuk setiap method:
```dart
try {
  // Try remote
  final result = await remoteDataSource.method();
  // Cache result
  await localDataSource.cache(result);
  // Convert to entity and return
  return Right(result.toEntity());
} on SpecificException catch (e) {
  return Left(SpecificFailure(e.message));
} catch (e) {
  // Try cache as fallback
  try {
    final cached = await localDataSource.getCached();
    return Right(cached.toEntity());
  } catch (_) {
    return Left(CacheFailure());
  }
}
```

File: lib/features/products/data/repositories/product_repository_impl.dart
```

---

## 🎯 Generate BLoC Layer

### Prompt 8: Generate BLoC Events
```
Buatkan ProductEvent untuk Products feature.

Events needed:
1. LoadProducts - fetch all products
2. LoadProduct(id) - fetch single product
3. AddProduct(product, imageFile) - add new product
4. UpdateProduct(id, product, imageFile) - update product
5. DeleteProduct(id) - delete product
6. SearchProducts(query) - search products
7. FilterProducts(categoryId) - filter by category
8. RefreshProducts - refresh from API

Requirements:
- All events extend Equatable
- Add relevant fields untuk setiap event
- Override props untuk Equatable

File: lib/features/products/presentation/bloc/product_event.dart
```

---

### Prompt 9: Generate BLoC States
```
Buatkan ProductState untuk Products feature.

States needed:
1. ProductInitial - initial state
2. ProductLoading - saat fetch data
3. ProductLoaded(products) - success dengan data
4. ProductDetailLoaded(product) - single product loaded
5. ProductError(message) - error state
6. ProductActionSuccess(message) - untuk add/update/delete success

Requirements:
- All states extend Equatable
- Add relevant fields
- Override props

Optional: Use freezed union untuk states jika lebih prefer.

File: lib/features/products/presentation/bloc/product_state.dart
```

---

### Prompt 10: Generate BLoC Implementation
```
Buatkan ProductBloc yang handle semua events dan emit states.

Requirements:
1. Inject ProductRepository
2. Use on<Event> handlers untuk setiap event
3. Emit loading state sebelum async operation
4. Handle Either<Failure, Success> dari repository
5. Convert failure message ke user-friendly error

Events to handle:
[LIST events dari prompt 8]

Pattern untuk setiap handler:
```dart
Future<void> _onEventName(
  EventName event,
  Emitter<ProductState> emit,
) async {
  emit(ProductLoading());

  final result = await repository.method(event.params);

  result.fold(
    (failure) => emit(ProductError(message: failure.message)),
    (data) => emit(ProductLoaded(products: data)),
  );
}
```

File: lib/features/products/presentation/bloc/product_bloc.dart
```

---

## 🎨 Generate UI Layer

### Prompt 11: Generate Products Page with BLoC
```
Buatkan ProductsPage dengan BLoC integration.

Requirements:
1. Use BlocProvider untuk provide ProductBloc
2. Use BlocBuilder untuk rebuild UI on state changes
3. Use BlocListener untuk side effects (navigation, snackbar)
4. Handle all states:
   - Loading: show loading indicator
   - Loaded: show product grid
   - Error: show error message dengan retry button
5. Add pull-to-refresh
6. Responsive layout (phone vs tablet)
7. Search bar di AppBar
8. FAB untuk add product (owner/manager only)

Layout:
- Phone: 2 column grid
- Tablet: 3-4 column grid

Referensi responsive dari FLUTTER_RESPONSIVE_DESIGN.md

File: lib/features/products/presentation/pages/products_page.dart
```

---

### Prompt 12: Generate Add Product Form
```
Buatkan Add Product page dengan form dan image picker.

Requirements:
1. Use BlocConsumer<ProductBloc, ProductState>
2. Form fields:
   - Product name (required)
   - Category dropdown (from CategoryBloc)
   - Description (required)
   - Price (required, number)
   - Cost (required, number)
   - Barcode (required)
   - Color picker (optional)
   - Image picker (optional)
   - Stock management toggle
   - Minimum stock (if stock managed)
3. Image picker dengan preview
4. Form validation
5. Submit button yang disabled saat loading
6. Show success snackbar dan navigate back saat success
7. Show error snackbar saat error

Handle BLoC states:
- Loading: disable form, show loading indicator
- Success: show snackbar, navigate back
- Error: show error message

File: lib/features/products/presentation/pages/add_product_page.dart
```

---

## 🧩 Generate Complete Flow

### Prompt 13: Generate Complete POS Feature
```
Buatkan complete POS feature dengan BLoC pattern.

Flow:
1. Select products → add to cart
2. Adjust quantities
3. Apply tax & discount
4. Select payment method
5. Process order → create via API
6. Show receipt

Requirements:
- CartBloc untuk manage cart state
- OrderBloc untuk process orders
- Offline queue jika network error
- Stock validation sebelum order
- Print receipt integration ready

Struktur:
lib/features/pos/
├── data/
│   ├── datasources/
│   ├── models/
│   └── repositories/
├── domain/
│   ├── entities/
│   ├── repositories/
│   └── usecases/
└── presentation/
    ├── blocs/
    │   ├── cart/ (CartBloc)
    │   └── order/ (OrderBloc)
    └── pages/
        ├── pos_page.dart
        └── receipt_page.dart

Buat step-by-step implementation plan.
```

---

## 🐛 Debugging BLoC

### Prompt 14: Debug BLoC Not Emitting States
```
BLoC saya tidak emit states dengan benar.

Code:
[PASTE BLoC code]

Problem:
- Event di-add tapi state tidak berubah
- UI tidak rebuild

Debug steps yang sudah saya coba:
1. [LIST what you tried]

Tolong bantu identify masalahnya.
```

---

### Prompt 15: Debug Repository Not Returning Data
```
Repository saya return Left(Failure) padahal API success.

Code:
[PASTE repository implementation]

API Response:
[PASTE actual response]

Error message:
[PASTE error]

Sepertinya issue di:
- [ ] Exception handling?
- [ ] Model parsing?
- [ ] DataSource implementation?

Bantu analyze.
```

---

## 🧪 Generate Tests

### Prompt 16: Generate BLoC Tests
```
Buatkan unit tests untuk ProductBloc menggunakan bloc_test.

Tests needed:
1. Initial state should be ProductInitial
2. LoadProducts emits [Loading, Loaded] on success
3. LoadProducts emits [Loading, Error] on failure
4. AddProduct emits [Loading, ActionSuccess] on success
5. DeleteProduct emits [Loading, ActionSuccess] on success

Requirements:
- Use bloc_test package
- Use mocktail untuk mock repository
- Cover happy path dan error cases
- Verify props equality

File: test/features/products/presentation/bloc/product_bloc_test.dart
```

---

### Prompt 17: Generate Repository Tests
```
Buatkan unit tests untuk ProductRepositoryImpl.

Tests needed:
1. getProducts returns Right(products) when remote succeeds
2. getProducts returns cached data when remote fails
3. getProducts returns Left(Failure) when both fail
4. addProduct saves to remote and cache
5. deleteProduct removes from remote and cache

Requirements:
- Mock RemoteDataSource dan LocalDataSource
- Use dartz matchers untuk Either
- Verify method calls dengan verify()
- Test all edge cases

File: test/features/products/data/repositories/product_repository_impl_test.dart
```

---

## 📱 Offline-First Implementation

### Prompt 18: Implement Offline Queue for Orders
```
Saya ingin implement offline-first untuk POS.

Requirements:
1. Orders bisa dibuat saat offline
2. Queue orders di SQFlite
3. Auto-sync saat online kembali
4. Prevent duplicate orders
5. Show sync status di UI

Architecture:
- OrderLocalDataSource → pending_orders table
- SyncBloc untuk handle background sync
- ConnectivityBloc untuk monitor network

Buatkan implementation plan dan code structure.
```

---

## 🎯 Complete Workflow Example

### Example: Implement Products Feature dari Awal

**Step 1:**
```
Prompt: "Generate folder structure untuk Products feature"
[Use Prompt 1]
```

**Step 2:**
```
Prompt: "Generate Product entity"
[Use Prompt 3]
```

**Step 3:**
```
Prompt: "Generate ProductModel dengan Freezed"
[Use Prompt 2]

Then run: flutter pub run build_runner build
```

**Step 4:**
```
Prompt: "Generate ProductRemoteDataSource"
[Use Prompt 4]
```

**Step 5:**
```
Prompt: "Generate ProductLocalDataSource"
[Use Prompt 5]
```

**Step 6:**
```
Prompt: "Generate ProductRepository interface"
[Use Prompt 6]
```

**Step 7:**
```
Prompt: "Generate ProductRepositoryImpl"
[Use Prompt 7]
```

**Step 8:**
```
Prompt: "Generate ProductEvent"
[Use Prompt 8]
```

**Step 9:**
```
Prompt: "Generate ProductState"
[Use Prompt 9]
```

**Step 10:**
```
Prompt: "Generate ProductBloc"
[Use Prompt 10]
```

**Step 11:**
```
Prompt: "Generate ProductsPage dengan BLoC"
[Use Prompt 11]
```

**Step 12:**
```
Prompt: "Generate Add Product page"
[Use Prompt 12]
```

**Step 13:**
```
Prompt: "Generate tests untuk ProductBloc"
[Use Prompt 16]
```

---

## 💡 Pro Tips

### 1. Always Provide Context
```
GOOD:
"Generate ProductBloc with events: LoadProducts, AddProduct, DeleteProduct
Repository returns Either<Failure, Product>
States: Initial, Loading, Loaded, Error"

BAD:
"Generate ProductBloc"
```

### 2. Reference Existing Code
```
"Generate OrderBloc similar to ProductBloc structure:
[PASTE ProductBloc code as reference]

But for Orders with these differences:
- [LIST differences]"
```

### 3. Incremental Generation
```
Don't ask for everything at once.

Instead:
1. Generate structure
2. Generate models
3. Generate datasources
4. Generate repository
5. Generate bloc
6. Generate UI

One at a time, test each layer.
```

### 4. Attach Documentation
```
Always attach relevant docs:
- API_ENDPOINTS_REFERENCE.md untuk API specs
- FLUTTER_CLEAN_ARCHITECTURE_BLOC.md untuk architecture reference
- FLUTTER_RESPONSIVE_DESIGN.md untuk UI patterns
```

---

## ✅ Checklist Before Asking AI

- [ ] Tahu struktur yang mau dibuat?
- [ ] Punya API specification?
- [ ] Tahu layer mana yang sedang dikerjakan?
- [ ] Sudah generate models dengan build_runner?
- [ ] Error message lengkap (jika debugging)?
- [ ] Attach reference code dari layer lain?

---

_Let AI help you scaffold the boilerplate, you focus on business logic! 🚀_
