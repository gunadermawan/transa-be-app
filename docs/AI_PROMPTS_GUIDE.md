# AI Prompts Guide - Academy POS Flutter Development

## 📝 Template Prompts untuk AI Assistant

Gunakan prompt-prompt ini dengan AI Assistant (Claude, ChatGPT, dll) untuk mempercepat development.

---

## 🚀 Phase 1: Generate TodoList

### Prompt 1: TodoList Week 1 (Authentication & Dashboard)
```
Saya sedang develop Flutter app untuk Academy POS.
Tolong buatkan todolist detail untuk Week 1 (Authentication & Dashboard) berdasarkan dokumentasi berikut:

[COPY-PASTE isi dari FLUTTER_IMPLEMENTATION_ROADMAP.md - Week 1 section]

Format todolist:
- [ ] Task name (estimasi waktu)
  - Sub-task 1
  - Sub-task 2
  - File yang harus dibuat: path/to/file.dart
  - Testing: apa yang harus ditest

Pisahkan berdasarkan:
- Project Setup
- Authentication System
- Dashboard Implementation

Include estimasi waktu untuk setiap task.
```

---

### Prompt 2: TodoList Week 2 (Products & Categories)
```
Buatkan todolist detail untuk Week 2 (Product Management) dengan breakdown:

[COPY-PASTE isi dari FLUTTER_IMPLEMENTATION_ROADMAP.md - Week 2 section]

Focus pada:
1. Category CRUD
2. Product CRUD dengan image upload
3. Product list dengan search & filter
4. Responsive layout (phone vs tablet)

Format sama seperti sebelumnya dengan estimasi waktu.
```

---

### Prompt 3: TodoList Week 3 (POS & Orders)
```
Ini adalah fitur paling penting (POS System).
Buatkan todolist sangat detail untuk Week 3:

[COPY-PASTE isi dari FLUTTER_IMPLEMENTATION_ROADMAP.md - Week 3 section]

Plus tambahan dari FLUTTER_RESPONSIVE_DESIGN.md untuk POS Screen:
[COPY-PASTE section POS dari responsive design]

Pisahkan:
1. POS Screen (Product Selection)
2. Cart Management
3. Checkout Flow
4. Order Creation & Stock Deduction
5. Order Management
6. Receipt

Berikan step-by-step yang SANGAT detail karena ini core feature.
```

---

## 🛠️ Phase 2: Code Generation & Problem Solving

### Prompt 4: Generate Model Class
```
Tolong buatkan Flutter model class untuk Product berdasarkan API response ini:

[COPY-PASTE dari API_ENDPOINTS_REFERENCE.md - Product response]

Requirements:
1. Include fromJson factory
2. Include toJson method
3. Include helper getters (imageUrl, totalStock, isLowStock)
4. Follow Dart naming conventions
5. Add proper null safety

File: lib/models/product.dart
```

---

### Prompt 5: Generate Service Class
```
Buatkan ProductService class yang handle semua API calls untuk products.

API Endpoints yang harus di-cover:
[COPY-PASTE dari API_ENDPOINTS_REFERENCE.md - Products section]

Requirements:
1. Use Dio for HTTP calls
2. Handle ApiException properly
3. Include all CRUD operations
4. Support image upload with FormData
5. Add proper error handling

File: lib/services/product_service.dart
```

---

### Prompt 6: Generate Provider Class
```
Buatkan ProductProvider dengan ChangeNotifier untuk state management.

Features needed:
1. Product list dengan loading state
2. Search & filter functionality
3. Add product (dengan image)
4. Update product
5. Delete product
6. Error handling
7. Pagination support (optional)

Use Provider pattern yang sudah di-setup di AuthProvider.

File: lib/providers/product_provider.dart
```

---

### Prompt 7: Generate Responsive Screen
```
Buatkan POS Screen yang responsive untuk phone dan tablet.

Requirements berdasarkan FLUTTER_RESPONSIVE_DESIGN.md:

Phone Layout:
- Product grid full screen
- Cart di bottom sheet
- Floating action button untuk checkout

Tablet Layout:
- Split screen (products 65% | cart 35%)
- Side-by-side layout
- Always visible cart

Include:
1. Product selection grid
2. Cart management
3. Checkout panel
4. Payment method selector

File: lib/screens/pos/pos_screen.dart
```

---

## 🐛 Phase 3: Debugging & Problem Solving

### Prompt 8: Debug Error
```
Saya mendapat error saat implementasi AuthProvider:

Error:
[PASTE ERROR MESSAGE]

Code saya:
[PASTE CODE]

Expected behavior:
[EXPLAIN WHAT SHOULD HAPPEN]

Context dari dokumentasi:
[PASTE RELEVANT SECTION dari FLUTTER_API_INTEGRATION.md]

Tolong bantu identify masalahnya dan berikan solusi.
```

---

### Prompt 9: Fix API Integration Issue
```
API call saya gagal dengan error 403 Forbidden.

Endpoint: POST /add-product
Headers:
[PASTE HEADERS]

Body:
[PASTE REQUEST BODY]

User role: Manager (role_id: 2)

Dari API_ENDPOINTS_REFERENCE.md, endpoint ini butuh permission:
[PASTE PERMISSION INFO]

Kenapa saya dapat 403? Apa yang salah?
```

---

### Prompt 10: Performance Optimization
```
Product list saya lambat saat load banyak data (100+ products).

Current implementation:
[PASTE CODE]

Tolong optimize dengan:
1. Lazy loading
2. Pagination
3. Image caching
4. ListView.builder best practices

Dari dokumentasi responsive design:
[PASTE PERFORMANCE SECTION]
```

---

## 📱 Phase 4: Responsive Design

### Prompt 11: Convert to Responsive
```
Saya punya screen ini yang belum responsive:

[PASTE CURRENT CODE]

Tolong convert menjadi responsive dengan:
1. Phone: Single column layout
2. Tablet: Two column layout
3. Use ScreenSize utility dari FLUTTER_RESPONSIVE_DESIGN.md

Berikan full code yang sudah responsive.
```

---

### Prompt 12: Tablet-Specific Layout
```
Buatkan layout khusus untuk tablet di POS screen.

Requirements:
1. Split screen (products | cart)
2. Category tabs horizontal scroll
3. Product grid 3 columns
4. Always visible cart panel
5. Larger touch targets for kasir

Referensi dari FLUTTER_RESPONSIVE_DESIGN.md:
[PASTE POS TABLET LAYOUT SECTION]
```

---

## 🎨 Phase 5: UI/UX Enhancement

### Prompt 13: Improve UI
```
Tolong improve UI untuk Product Card saya:

Current code:
[PASTE CODE]

Yang perlu ditingkatkan:
1. Better visual hierarchy
2. Show low stock indicator
3. Category badge
4. Better spacing
5. Smooth animations

Tablet version harus show lebih banyak info.
```

---

### Prompt 14: Add Loading States
```
Saya perlu tambahkan loading states yang proper.

Screens yang perlu:
1. Login screen - saat login
2. Product list - saat fetch data
3. POS - saat process order

Requirements:
1. Shimmer effect untuk list
2. Spinner untuk buttons
3. Skeleton screens
4. Error states dengan retry button

Best practices dari dokumentasi?
```

---

## 🧪 Phase 6: Testing

### Prompt 15: Generate Test Cases
```
Buatkan test cases untuk AuthProvider:

Features to test:
1. Login dengan credentials valid
2. Login dengan credentials invalid
3. Logout
4. Token persistence
5. Auto-login
6. Error handling

Format:
- Test description
- Setup needed
- Expected behavior
- How to verify

Berdasarkan testing checklist di FLUTTER_IMPLEMENTATION_ROADMAP.md
```

---

### Prompt 16: Integration Testing
```
Buatkan integration test untuk flow:
Login → Dashboard → Create Order → Logout

Steps:
[LIST STEPS]

Expected hasil di setiap step.

Include:
1. Setup test data
2. Mock API responses
3. Verify UI updates
4. Check navigation
5. Cleanup
```

---

## 🚀 Phase 7: Advanced Features

### Prompt 17: Implement Offline Mode
```
Tolong implement offline mode untuk POS:

Requirements:
1. Queue orders saat offline
2. Sync saat online kembali
3. Show offline indicator
4. Prevent duplicate orders
5. Handle conflicts

Architecture suggestion?
```

---

### Prompt 18: Add Print Receipt
```
Implement print receipt functionality:

Platform: Android & iOS
Printer: Bluetooth thermal printer

Flow:
1. Order success
2. Show receipt preview
3. Print via Bluetooth
4. Handle print errors

Library recommendation dan implementation guide?
```

---

## 💡 Tips Menggunakan Prompt

### 1. Berikan Konteks Lengkap
```
JANGAN:
"Buatkan product screen"

LAKUKAN:
"Buatkan product screen dengan fitur:
- List products dari API
- Search & filter
- Responsive phone/tablet
- Permission check (manager only untuk edit)

Referensi:
[PASTE relevant docs]
```

### 2. Attach Dokumentasi yang Relevan
Selalu copy-paste bagian dokumentasi yang relevan ke prompt.

### 3. Specific Error Information
```
Include:
- Full error message
- Stack trace
- Relevant code
- What you've tried
- Expected vs actual behavior
```

### 4. Iterative Prompts
```
1st Prompt: "Buatkan ProductService"
AI Response: [code]

2nd Prompt: "Bagus! Sekarang tambahkan:
- Pagination support
- Search functionality
- Error retry mechanism"
```

### 5. Ask for Explanation
```
Tambahkan di akhir prompt:
"Tolong explain juga:
- Kenapa pakai approach ini?
- Best practices apa yang diikuti?
- Trade-offs yang ada?"
```

---

## 📚 Dokumentasi Reference Quick Links

Saat membuat prompt, sering reference ke:

1. **API Endpoints:** `API_ENDPOINTS_REFERENCE.md`
   - Request/Response format
   - Permission requirements
   - Error codes

2. **Integration Guide:** `FLUTTER_API_INTEGRATION.md`
   - Code examples
   - Model structures
   - Error handling patterns

3. **Roadmap:** `FLUTTER_IMPLEMENTATION_ROADMAP.md`
   - Implementation steps
   - Testing checklists
   - Success metrics

4. **Responsive Design:** `FLUTTER_RESPONSIVE_DESIGN.md`
   - Screen size utilities
   - Layout patterns
   - Component examples

---

## 🎯 Example: Complete Workflow

### Scenario: Implement Product List Screen

**Step 1: Generate TodoList**
```
Prompt: "Buatkan todolist untuk implement Product List Screen
berdasarkan Week 2 Day 8-10 di FLUTTER_IMPLEMENTATION_ROADMAP.md"
```

**Step 2: Generate Model**
```
Prompt: "Buatkan Product model berdasarkan API response di
API_ENDPOINTS_REFERENCE.md - GET /get-products"
```

**Step 3: Generate Service**
```
Prompt: "Buatkan ProductService untuk fetch products dari API"
```

**Step 4: Generate Provider**
```
Prompt: "Buatkan ProductProvider dengan state management"
```

**Step 5: Generate Screen**
```
Prompt: "Buatkan ProductListScreen responsive (phone/tablet)
menggunakan ScreenSize utility dari FLUTTER_RESPONSIVE_DESIGN.md"
```

**Step 6: Fix Issues**
```
Prompt: "Error saat fetch: [paste error]
Code: [paste code]
Bantu fix"
```

**Step 7: Add Tests**
```
Prompt: "Buatkan test cases untuk ProductListScreen"
```

---

## ✅ Checklist Sebelum Ask AI

- [ ] Sudah baca dokumentasi yang relevan?
- [ ] Sudah coba solve sendiri?
- [ ] Error message lengkap?
- [ ] Code context cukup?
- [ ] Expected behavior jelas?
- [ ] Reference dokumentasi di-attach?

---

## 🚫 Common Mistakes to Avoid

### ❌ JANGAN:
```
"Buatkan POS app"
```

### ✅ LAKUKAN:
```
"Buatkan POS Screen untuk tablet dengan:
- Split layout (products | cart)
- Product grid 3 columns
- Real-time cart update
- Multiple payment methods

Referensi layout dari FLUTTER_RESPONSIVE_DESIGN.md section POS:
[PASTE CODE EXAMPLE]

API endpoint: POST /add-order
[PASTE API SPEC]
```

---

_Happy Prompting! 🚀_
_Let AI be your pair programmer!_
