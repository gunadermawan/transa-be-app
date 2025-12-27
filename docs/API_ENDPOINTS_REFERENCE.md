# API Endpoints Quick Reference

## Base URL
```
http://127.0.0.1:8000/api
```

## Authentication Required
All endpoints except `/register` and `/login` require:
```
Headers: Authorization: Bearer {access_token}
```

---

## 🔐 Authentication

### 1. Register
```http
POST /register
```
**Body:**
```json
{
  "name": "string (required)",
  "email": "string (required, email, unique)",
  "password": "string (required)",
  "business_name": "string (required)",
  "address": "string (required)"
}
```
**Response:** 201
```json
{
  "access_token": "string",
  "data": { User }
}
```

---

### 2. Login
```http
POST /login
```
**Body:**
```json
{
  "email": "string (required)",
  "password": "string (required)"
}
```
**Response:** 200
```json
{
  "access_token": "string",
  "data": { User }
}
```
**Error:** 401 - Invalid credentials

---

### 3. Logout
```http
POST /logout
```
**Response:** 200
```json
{
  "message": "Logged out"
}
```

---

### 4. Get Current User
```http
GET /me
```
**Response:** 200
```json
{
  "data": {
    "id": 1,
    "name": "string",
    "email": "string",
    "phone": "string|null",
    "role_id": 1,
    "business_id": 1,
    "outlet_id": 1,
    "business": { Business },
    "outlet": { Outlet },
    "role": { Role }
  }
}
```

---

### 5. Get My Outlet
```http
GET /my-outlet
```
**Response:** 200
```json
{
  "data": { Outlet }
}
```

---

## 📊 Dashboard

### Get Dashboard Stats
```http
GET /dashboard?outlet_id={id}&date={YYYY-MM-DD}
```
**Query Params:**
- `outlet_id` (optional): Filter by outlet
- `date` (optional): Specific date (default: today)

**Response:** 200
```json
{
  "today": {
    "date": "2025-12-01",
    "sales": 5000000.00,
    "transactions": 45,
    "customers": 32
  },
  "this_month": {
    "sales": 50000000.00,
    "transactions": 500,
    "average_per_day": 1666666.67
  },
  "alerts": {
    "low_stock_count": 5,
    "pending_orders": 0
  },
  "top_products": [
    {
      "product_id": 1,
      "product_name": "string",
      "quantity_sold": 50,
      "revenue": 2500000.00
    }
  ],
  "payment_methods": {
    "cash": 3000000.00,
    "card": 1500000.00,
    "qris": 500000.00
  }
}
```

---

## 📁 Categories

### 1. Get All Categories
```http
GET /get-categories
```
**Response:** 200
```json
{
  "data": [
    {
      "id": 1,
      "name": "string",
      "business_id": 1
    }
  ]
}
```

---

### 2. Add Category
```http
POST /add-category
```
**Permission:** Owner, Manager (role: 1,2)

**Body:**
```json
{
  "name": "string (required)"
}
```
**Response:** 201
```json
{
  "message": "Category added successfully",
  "data": { Category }
}
```

---

### 3. Update Category
```http
PUT /update-category/{id}
```
**Permission:** Owner, Manager (role: 1,2)

**Body:**
```json
{
  "name": "string (required)"
}
```
**Response:** 200
```json
{
  "message": "Category updated successfully",
  "data": { Category }
}
```
**Error:** 403 - Unauthorized access to this category

---

## 🛍️ Products

### 1. Get All Products
```http
GET /get-products
```
**Response:** 200
```json
{
  "data": [
    {
      "id": 1,
      "name": "string",
      "category_id": 1,
      "business_id": 1,
      "description": "string",
      "image": "string|null",
      "color": "string|null",
      "price": "decimal",
      "cost": "decimal",
      "barcode": "string",
      "sku": "string",
      "status": "active|inactive",
      "is_stock_managed": true,
      "stock_minimum": 10,
      "category": { Category },
      "stocks": [ { Stock } ]
    }
  ]
}
```

---

### 2. Get Single Product
```http
GET /get-product/{id}
```
**Response:** 200
```json
{
  "data": { Product }
}
```
**Error:** 404 - Product not found

---

### 3. Add Product
```http
POST /add-product
Content-Type: multipart/form-data
```
**Permission:** Owner, Manager (role: 1,2)

**Body (FormData):**
```
name: string (required)
category_id: integer (required)
description: string (required)
price: numeric (required)
cost: numeric (required)
barcode: string (required)
color: string (optional)
image: file (optional)
```
**Response:** 201
```json
{
  "message": "Product added successfully",
  "data": { Product }
}
```

---

### 4. Update Product (without image)
```http
PUT /update-product/{id}
```
**Permission:** Owner, Manager (role: 1,2)

**Body:**
```json
{
  "name": "string (required)",
  "category_id": "integer (required)",
  "description": "string (required)",
  "price": "numeric (required)",
  "cost": "numeric (required)",
  "barcode": "string (optional)",
  "color": "string (optional)"
}
```
**Response:** 200
```json
{
  "message": "Product updated successfully",
  "data": { Product }
}
```
**Error:** 403 - Unauthorized access to this product

---

### 5. Update Product (with image)
```http
POST /update-product-with-image/{id}
Content-Type: multipart/form-data
```
**Permission:** Owner, Manager (role: 1,2)

**Body (FormData):**
```
name: string (required)
category_id: integer (required)
description: string (required)
price: numeric (required)
cost: numeric (required)
barcode: string (optional)
color: string (optional)
image: file (optional)
```
**Response:** 200
```json
{
  "message": "Product updated successfully",
  "data": { Product }
}
```

---

### 6. Delete Product
```http
DELETE /delete-product/{id}
```
**Permission:** Owner, Manager (role: 1,2)

**Response:** 200
```json
{
  "message": "Product deleted successfully"
}
```
**Error:** 403 - Unauthorized access to this product

---

## 📦 Stock Management

### 1. Get All Stocks
```http
GET /get-stocks
```
**Response:** 200
```json
{
  "data": [
    {
      "id": 1,
      "product_id": 1,
      "quantity": 50,
      "outlet_id": 1,
      "product": { Product },
      "outlet": { Outlet }
    }
  ]
}
```

---

### 2. Get Single Stock
```http
GET /get-stock/{id}
```
**Response:** 200
```json
{
  "data": { Stock }
}
```

---

### 3. Update Stock
```http
PUT /update-stock/{id}
```
**Permission:** Owner, Manager (role: 1,2)

**Body:**
```json
{
  "quantity": "integer (required)",
  "type": "add|deduct (required)",
  "note": "string (required)",
  "reference": "string (optional)"
}
```
**Response:** 200
```json
{
  "message": "Stock updated successfully",
  "data": { StockHistory }
}
```
**Error:** 403 - Unauthorized access to this stock

---

## 🛒 Orders

### 1. Create Order
```http
POST /add-order
```
**Body:**
```json
{
  "outlet_id": "integer (required)",
  "items": [
    {
      "product_id": "integer (required)",
      "quantity": "integer (required, min:1)",
      "price": "numeric (required)",
      "total": "numeric (optional, calculated if not provided)",
      "notes": "string (optional)"
    }
  ],
  "sub_total": "numeric (required)",
  "total_price": "numeric (required)",
  "total_items": "integer (required)",
  "tax": "numeric (required)",
  "discount": "numeric (required)",
  "payment_method": "cash|card|qris|transfer (required)",
  "amount_received": "numeric (optional, required for cash)",
  "notes": "string (optional)",
  "customer_id": "integer (optional)"
}
```
**Response:** 201
```json
{
  "success": true,
  "message": "Order created successfully",
  "data": {
    "id": 1,
    "order_number": "ORD-20251201-000001",
    "grand_total": 88000,
    "payment_method": "cash",
    "amount_received": 100000,
    "change": 12000,
    "created_at": "2025-12-01 10:30:00"
  }
}
```

**Notes:**
- Order number format: `ORD-YYYYMMDD-XXXXXX`
- Stock automatically deducts ONLY if `product.is_stock_managed = true`
- Cashier ID auto-filled from authenticated user

---

### 2. Get All Orders (with filters)
```http
GET /get-orders?outlet_id={id}&status={status}&payment_method={method}&start_date={date}&end_date={date}&search={keyword}&per_page={number}
```
**Query Params:**
- `outlet_id` (optional): Filter by outlet
- `status` (optional): success|pending|void
- `payment_method` (optional): cash|card|qris|transfer
- `start_date` (optional): YYYY-MM-DD
- `end_date` (optional): YYYY-MM-DD
- `search` (optional): Search order number
- `per_page` (optional): Items per page (default: 20)

**Response:** 200 (Paginated)
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "order_number": "string",
      "outlet_id": 1,
      "customer_id": 1|null,
      "sub_total": "decimal",
      "total_price": "decimal",
      "total_items": 3,
      "tax": "decimal",
      "discount": "decimal",
      "payment_method": "cash",
      "status": "success",
      "cashier_id": 1,
      "created_at": "datetime",
      "items": [ { OrderItem } ],
      "outlet": { Outlet },
      "cashier": { User },
      "customer": { Customer|null }
    }
  ],
  "last_page": 5,
  "per_page": 20,
  "total": 100
}
```

---

### 3. Get Single Order
```http
GET /get-order/{id}
```
**Response:** 200
```json
{
  "data": {
    Order with items, outlet, cashier, customer
  }
}
```

---

### 4. Get Orders by Outlet
```http
GET /get-orders-by-outlet/{outlet_id}
```
**Response:** 200
```json
{
  "data": [ { Order } ]
}
```

---

### 5. Void Order
```http
DELETE /delete-order/{id}
```
**Permission:** Owner, Manager (role: 1,2)

**Response:** 200
```json
{
  "message": "Order voided successfully",
  "data": { Order }
}
```

**Notes:**
- Stock returns to inventory
- Status changes to 'void'
- Record preserved (soft delete)
- Stock history created for void

---

## 👥 Staff Management

### 1. Get All Staff
```http
GET /get-staff/{businessId}
```
**Response:** 200
```json
{
  "data": [
    {
      "id": 1,
      "name": "string",
      "email": "string",
      "phone": "string|null",
      "role_id": 3,
      "business_id": 1,
      "outlet_id": 1,
      "role": { Role },
      "outlet": { Outlet }
    }
  ]
}
```
**Error:** 403 - Unauthorized access to this business

---

### 2. Add Staff
```http
POST /add-staff
```
**Permission:** Owner, Manager (role: 1,2)

**Body:**
```json
{
  "name": "string (required)",
  "email": "string (required, email, unique)",
  "password": "string (required)",
  "outlet_id": "integer (required)",
  "role_id": "integer (required)"
}
```
**Response:** 201
```json
{
  "data": { User }
}
```

---

### 3. Edit Staff
```http
PUT /edit-staff/{id}
```
**Permission:** Owner, Manager (role: 1,2)

**Body:**
```json
{
  "name": "string (required)",
  "email": "string (required, email)",
  "role_id": "integer (required)",
  "outlet_id": "integer (optional)",
  "password": "string (optional)"
}
```
**Response:** 200
```json
{
  "message": "Staff updated successfully",
  "data": { User }
}
```
**Error:** 403 - Unauthorized access to this staff

---

### 4. Add Manager
```http
POST /add-manager
```
**Permission:** Owner only (role: 1)

**Body:**
```json
{
  "name": "string (required)",
  "email": "string (required, email, unique)",
  "password": "string (required)",
  "outlet_id": "integer (required)"
}
```
**Response:** 201
```json
{
  "data": { User }
}
```

---

## 🏪 Outlet Management

### 1. Get Outlets by Business
```http
GET /get-outlets/{businessId}
```
**Response:** 200
```json
{
  "data": [
    {
      "id": 1,
      "name": "string",
      "business_id": 1,
      "address": "string",
      "phone": "string|null",
      "description": "string|null"
    }
  ]
}
```
**Error:** 403 - Unauthorized access to this business

---

### 2. Add Outlet
```http
POST /add-outlet
```
**Permission:** Owner only (role: 1)

**Body:**
```json
{
  "name": "string (required)",
  "address": "string (required)",
  "phone": "string (optional)",
  "description": "string (optional)"
}
```
**Response:** 201
```json
{
  "message": "Outlet added successfully",
  "data": { Outlet }
}
```

---

### 3. Update Outlet
```http
PUT /update-outlet/{id}
```
**Permission:** Owner only (role: 1)

**Body:**
```json
{
  "name": "string (required)",
  "address": "string (required)",
  "phone": "string (optional)",
  "description": "string (optional)"
}
```
**Response:** 200
```json
{
  "message": "Outlet updated successfully",
  "data": { Outlet }
}
```
**Error:** 403 - Unauthorized access to this outlet

---

## 📄 Reports

### Get Daily Sales Report
```http
POST /get-daily-sales-report
```
**Body:**
```json
{
  "outlet_id": "integer (required)",
  "date": "string (required, YYYY-MM-DD)"
}
```
**Response:** 200
```json
{
  "data": { /* Sales report data */ }
}
```

---

## 🖨️ Printers

### 1. Get Printers by Outlet
```http
GET /get-printers-by-outlet/{outlet_id}
```
**Response:** 200
```json
{
  "data": [ { Printer } ]
}
```

---

### 2. Add Printer
```http
POST /add-printer
```
**Body:**
```json
{
  "name": "string (required)",
  "outlet_id": "integer (required)",
  "type": "string (required)",
  "connection": "string (required)"
}
```
**Response:** 201

---

### 3. Delete Printer
```http
DELETE /delete-printer/{id}
```
**Response:** 200

---

## ⚙️ Business Settings

### 1. Get Business Settings
```http
GET /get-business-settings-by-business/{business_id}
```
**Response:** 200
```json
{
  "data": [ { BusinessSetting } ]
}
```

---

### 2. Add Business Setting
```http
POST /add-business-setting
```
**Body:**
```json
{
  "key": "string (required)",
  "value": "string (required)",
  "business_id": "integer (required)"
}
```
**Response:** 201

---

### 3. Update Business Setting
```http
PUT /update-business-setting/{id}
```
**Body:**
```json
{
  "value": "string (required)"
}
```
**Response:** 200

---

### 4. Delete Business Setting
```http
DELETE /delete-business-setting/{id}
```
**Response:** 200

---

## 🏢 Multi-Outlet Features

### 1. Get My Outlets
```http
GET /my-outlets
```
**Response:** 200
```json
{
  "data": [ { Outlet } ]
}
```

---

### 2. Switch Outlet
```http
POST /switch-outlet
```
**Body:**
```json
{
  "outlet_id": "integer (required)"
}
```
**Response:** 200

---

### 3. Cross Outlet Dashboard
```http
GET /cross-outlet-dashboard
```
**Response:** 200
```json
{
  "data": { /* Cross-outlet stats */ }
}
```

---

### 4. Outlet Comparison
```http
GET /outlet-comparison
```
**Response:** 200
```json
{
  "data": { /* Outlet comparison data */ }
}
```

---

### 5. Outlet Ranking
```http
GET /outlet-ranking
```
**Response:** 200
```json
{
  "data": [ { /* Outlet performance ranking */ } ]
}
```

---

## 🧾 Receipts

### 1. Get Single Receipt
```http
GET /receipts/{orderId}
```
**Response:** 200
```json
{
  "data": { /* Receipt data */ }
}
```

---

### 2. Get Multiple Receipts (Batch)
```http
POST /receipts/batch
```
**Body:**
```json
{
  "order_ids": [1, 2, 3]
}
```
**Response:** 200
```json
{
  "data": [ { Receipt } ]
}
```

---

## 📋 Common Response Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized (invalid/expired token) |
| 403 | Forbidden (insufficient permissions) |
| 404 | Not Found |
| 422 | Validation Error |
| 500 | Server Error |

---

## 🔑 Role IDs

| ID | Name | Description |
|----|------|-------------|
| 1 | business_owner | Full access to all features |
| 2 | manager | Can manage products, staff, orders |
| 3 | staff | Can create orders, view products |

---

## 💡 Important Notes

### Security
1. **Never send `business_id` in requests** - it's auto-filled from authenticated user
2. **Store token securely** - use flutter_secure_storage
3. **Handle 401 errors** - redirect to login
4. **Handle 403 errors** - show permission denied message

### Data Isolation
- All data is automatically filtered by business_id
- Users can only access data from their own business
- Global scopes ensure multi-tenancy isolation

### Stock Management
- Stock only deducts if `product.is_stock_managed = true`
- Useful for services that don't need inventory tracking
- Stock history tracks all changes

### Order Numbers
- Format: `ORD-YYYYMMDD-XXXXXX`
- Auto-generated, sequential per day
- Never reused

### Payment Methods
- `cash` - requires `amount_received`
- `card`, `qris`, `transfer` - exact amount

---

_Last Updated: 2025-12-01_
