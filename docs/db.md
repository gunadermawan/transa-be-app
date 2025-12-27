# Database Schema Documentation - POS SaaS System

## Executive Summary

This is a comprehensive Point of Sale (POS) SaaS (Software as a Service) system designed for multi-tenant businesses with multiple outlets. The database architecture supports **43 interconnected tables** across 8 major feature modules, enabling complete business management from inventory to employee attendance, from subscription billing to customer loyalty programs.

### System Capabilities
- **Multi-tenant** architecture with business isolation
- **Multi-outlet** support with individual stock management
- **Multi-user** with role-based access control
- **SaaS subscription** with flexible pricing tiers
- **Complete audit trail** for all transactions
- **Real-time notifications** and alerts

---

## Database Architecture Overview

### Total Statistics
- **43 Tables** with proper relationships
- **20+ Models** with Eloquent ORM
- **30 Migrations** for version control
- **Comprehensive Indexes** for performance optimization
- **Soft Deletes** for data archival
- **JSON Fields** for flexible data storage

### Technology Stack
- **Database:** MySQL/PostgreSQL compatible
- **ORM:** Laravel Eloquent
- **Version Control:** Laravel Migrations
- **Seeding:** Comprehensive demo data included

---

## Table Groups by Feature Module

### 1. Core System (5 tables)
- `users` - User accounts and authentication
- `roles` - Role-based access control
- `businesses` - Tenant/business entities
- `outlets` - Physical store locations
- `business_settings` - Configurable settings (tax, charges)

### 2. Subscription & Billing (4 tables)
- `subscription_plans` - Pricing tiers
- `business_subscriptions` - Active subscriptions
- `subscription_invoices` - Billing records
- `subscription_payments` - Payment transactions

### 3. Product Management (4 tables)
- `products` - Product master data
- `product_variants` - Size, color, and attribute variations
- `categories` - Product categorization
- `stocks` - Inventory per outlet

### 4. Inventory & Supply Chain (7 tables)
- `suppliers` - Supplier master data
- `purchase_orders` - Purchase order headers
- `purchase_order_items` - Purchase order details
- `stock_transfers` - Inter-outlet transfers
- `stock_transfer_items` - Transfer details
- `stock_histories` - Stock movement audit trail

### 5. Sales & Orders (6 tables)
- `orders` - Sales order headers
- `order_items` - Order line items
- `order_payments` - Split payment support
- `order_taxes` - Tax applications
- `order_discounts` - Discount applications
- `cash_drawer_sessions` - Cash management per shift

### 6. Returns & Refunds (3 tables)
- `order_returns` - Return headers
- `order_return_items` - Return details
- `cash_transactions` - Cash drawer movements

### 7. Customer & Loyalty (1 table)
- `customers` - Customer master with loyalty points

### 8. Marketing & Promotions (3 tables)
- `promotions` - Campaign management
- `promotion_product` - Product-specific promos
- `promotion_category` - Category-wide promos

### 9. Employee Management (3 tables)
- `shifts` - Work shift definitions
- `attendances` - Employee check-in/out
- `printers` - Receipt printer configuration

### 10. Reporting & Analytics (2 tables)
- `sales_summaries` - Aggregated daily sales
- `sales_transactions` - Transaction-level details

### 11. System & Monitoring (2 tables)
- `activity_logs` - Complete audit trail
- `notifications` - User notifications

### 12. Laravel Standard (3 tables)
- `sessions` - User sessions
- `cache` - Cache storage
- `personal_access_tokens` - API tokens

---

## Detailed Table Descriptions

### Core System Tables

#### 1. `users`
**Purpose:** User authentication and profile management

**Key Fields:**
- `id` - Primary key
- `name` - Full name
- `email` - Unique email (login)
- `password` - Hashed password
- `role_id` - Foreign key to roles
- `business_id` - Foreign key to businesses (multi-tenant)
- `outlet_id` - Assigned outlet (nullable)
- `phone` - Contact number

**Relationships:**
- `belongsTo(Role)` - User role
- `belongsTo(Business)` - Tenant business
- `belongsTo(Outlet)` - Assigned outlet
- `hasMany(Order)` - Orders created by user
- `hasMany(Attendance)` - Attendance records

**Use Case:**
Owner can create manager, cashier accounts. Each user belongs to one business and optionally to one outlet.

---

#### 2. `roles`
**Purpose:** Role-based access control

**Key Fields:**
- `id` - Primary key
- `name` - Role name (super_admin, business_owner, manager, cashier, staff)

**Pre-defined Roles:**
- **super_admin** - Platform administrator
- **business_owner** - Business owner (full access)
- **manager** - Outlet manager (outlet-level access)
- **cashier** - Point of sale operator
- **staff** - General staff (limited access)

**Use Case:**
Permission-based feature access. Owner sees all outlets, Manager sees assigned outlet only.

---

#### 3. `businesses`
**Purpose:** Multi-tenant business entities

**Key Fields:**
- `id` - Primary key
- `name` - Business name (unique)
- `owner_id` - Foreign key to users
- `address`, `phone`, `email` - Contact details
- `tax_id` - Tax registration number
- `logo` - Business logo path
- `current_subscription_id` - Active subscription
- `subscription_status` - trial/active/past_due/cancelled
- `status` - pending/active/suspended
- `activated_at`, `expired_at` - Lifecycle timestamps

**Relationships:**
- `belongsTo(User, 'owner_id')` - Business owner
- `hasMany(Outlet)` - Multiple outlets
- `hasMany(User)` - Employees
- `hasMany(Product)` - Product catalog
- `hasMany(Customer)` - Customer base
- `belongsTo(BusinessSubscription)` - Current subscription

**Use Case:**
Each business is isolated tenant. "Toko ABC" cannot see data from "Toko XYZ".

---

#### 4. `outlets`
**Purpose:** Physical store locations

**Key Fields:**
- `id` - Primary key
- `business_id` - Foreign key to businesses
- `name` - Outlet name
- `address` - Physical address
- `phone` - Contact number
- `description` - Optional notes

**Relationships:**
- `belongsTo(Business)` - Parent business
- `hasMany(User)` - Assigned employees
- `hasMany(Stock)` - Inventory per outlet
- `hasMany(Order)` - Sales per outlet
- `hasMany(Shift)` - Work shifts

**Use Case:**
"Toko ABC" has "Cabang Jakarta" and "Cabang Bandung". Each has independent stock and sales.

---

#### 5. `business_settings`
**Purpose:** Configurable business settings (taxes, charges)

**Key Fields:**
- `id` - Primary key
- `business_id` - Foreign key to businesses
- `name` - Setting name (e.g., "PPN 11%")
- `charge_type` - percentage/fixed
- `type` - tax/service/discount
- `value` - Setting value

**Use Case:**
Business can configure PPN 11%, Service Charge 5%, Member Discount 10%.

---

### Subscription & Billing Module

#### 6. `subscription_plans`
**Purpose:** SaaS pricing tiers

**Key Fields:**
- `id` - Primary key
- `name` - Plan name (Starter, Business, Enterprise)
- `description` - Plan description
- `price` - Monthly/yearly price
- `billing_cycle` - monthly/yearly
- `trial_days` - Free trial period
- `max_outlets` - Maximum outlets allowed
- `max_users` - Maximum users allowed
- `max_products` - Maximum SKUs allowed
- `max_transactions_per_month` - Transaction limit
- `features` - JSON array of features
- `is_popular` - Badge for UI
- `sort_order` - Display order
- `status` - active/inactive

**Example Plans:**
```json
{
  "Starter": {
    "price": 99000,
    "max_outlets": 1,
    "max_users": 3,
    "max_products": 100,
    "features": ["Basic reporting", "Email support"]
  },
  "Business": {
    "price": 299000,
    "max_outlets": 5,
    "max_users": 15,
    "max_products": 1000,
    "features": ["Advanced reporting", "API access", "Priority support"]
  },
  "Enterprise": {
    "price": 999000,
    "max_outlets": null,
    "max_users": null,
    "features": ["Unlimited everything", "Dedicated support", "SLA"]
  }
}
```

---

#### 7. `business_subscriptions`
**Purpose:** Active subscriptions per business

**Key Fields:**
- `id` - Primary key
- `business_id` - Foreign key to businesses
- `subscription_plan_id` - Foreign key to subscription_plans
- `start_date`, `end_date` - Subscription period
- `trial_ends_at` - Trial expiry date
- `next_billing_date` - Next charge date
- `status` - trial/active/past_due/cancelled/expired
- `auto_renew` - Boolean
- `cancelled_at` - Cancellation timestamp
- `cancellation_reason` - Why cancelled

**Workflow:**
1. Signup → Status: `trial` (14 days)
2. Trial ends → Status: `past_due` (grace period)
3. Payment success → Status: `active`
4. Payment failed → Status: `past_due` (retry billing)
5. Cancel → Status: `cancelled`

---

#### 8. `subscription_invoices`
**Purpose:** Billing invoices

**Key Fields:**
- `id` - Primary key
- `business_id` - Foreign key to businesses
- `business_subscription_id` - Related subscription
- `invoice_number` - Unique invoice number
- `subtotal`, `tax`, `discount`, `total` - Invoice amounts
- `issue_date`, `due_date` - Invoice dates
- `paid_at` - Payment timestamp
- `status` - pending/paid/overdue/cancelled

**Auto-generation:**
Invoice created automatically every billing cycle (monthly/yearly).

---

#### 9. `subscription_payments`
**Purpose:** Payment transactions for subscriptions

**Key Fields:**
- `id` - Primary key
- `subscription_invoice_id` - Related invoice
- `business_id` - Foreign key to businesses
- `payment_method` - credit_card/bank_transfer/e-wallet
- `amount` - Payment amount
- `payment_date` - Transaction date
- `transaction_id` - Gateway reference
- `gateway` - midtrans/xendit/stripe
- `status` - pending/success/failed/refunded
- `gateway_response` - JSON response from gateway

**Integration Ready:**
Supports Midtrans, Xendit, Stripe, and other payment gateways.

---

### Product Management Module

#### 10. `products`
**Purpose:** Product master data

**Key Fields:**
- `id` - Primary key
- `business_id` - Foreign key to businesses
- `category_id` - Foreign key to categories
- `supplier_id` - Default supplier (nullable)
- `tax_id` - Default tax setting (nullable)
- `name` - Product name
- `description` - Product description
- `image` - Product image path
- `color` - Color (if applicable)
- `price` - Selling price (decimal 12,2)
- `cost` - Purchase cost (decimal 12,2)
- `barcode` - Barcode/EAN
- `sku` - Stock Keeping Unit (unique)
- `unit_type` - pcs/kg/liter/box/carton
- `stock_minimum` - Minimum stock alert
- `reorder_point` - Reorder alert threshold
- `optimal_stock_level` - Suggested stock level
- `product_type` - physical/service/digital
- `status` - active/inactive
- `is_stock_managed` - Boolean
- `is_featured` - Featured product flag
- `deleted_at` - Soft delete timestamp

**Relationships:**
- `belongsTo(Business)` - Owner business
- `belongsTo(Category)` - Product category
- `belongsTo(Supplier)` - Default supplier
- `hasMany(ProductVariant)` - Size/color variants
- `hasMany(Stock)` - Stock per outlet

**Use Case:**
"Laptop Gaming XYZ" - Price: Rp 15.000.000, Cost: Rp 12.000.000, Reorder at 10 units.

---

#### 11. `product_variants`
**Purpose:** Product variations (size, color, etc.)

**Key Fields:**
- `id` - Primary key
- `product_id` - Parent product
- `variant_name` - "Large - Red"
- `sku` - Unique SKU per variant
- `barcode` - Variant-specific barcode
- `price_adjustment` - Price difference (+/- from base)
- `cost_adjustment` - Cost difference
- `attributes` - JSON: `{"size": "L", "color": "Red"}`
- `image` - Variant-specific image
- `sort_order` - Display order
- `status` - active/inactive

**Example:**
```json
Product: "T-Shirt" (Base price: 100k)
Variants:
- "S - White" → adjustment: 0 → Final: 100k
- "L - White" → adjustment: 10000 → Final: 110k
- "XL - Black" → adjustment: 25000 → Final: 125k
```

---

#### 12. `categories`
**Purpose:** Product categorization

**Key Fields:**
- `id` - Primary key
- `business_id` - Foreign key to businesses
- `name` - Category name

**Examples:** Electronics, Fashion, Food & Beverage, Home & Living

---

#### 13. `stocks`
**Purpose:** Inventory tracking per outlet

**Key Fields:**
- `id` - Primary key
- `product_id` - Foreign key to products
- `outlet_id` - Foreign key to outlets
- `quantity` - Current stock quantity

**Relationships:**
- `belongsTo(Product)` - Product
- `belongsTo(Outlet)` - Outlet location

**Use Case:**
Laptop XYZ: 15 units in Jakarta, 8 units in Bandung (independent stock per outlet).

---

### Inventory & Supply Chain Module

#### 14. `suppliers`
**Purpose:** Supplier master data

**Key Fields:**
- `id` - Primary key
- `business_id` - Foreign key to businesses
- `name` - Supplier name
- `code` - Supplier code (SUP001)
- `contact_person` - Contact name
- `phone`, `email` - Contact details
- `address` - Supplier address
- `payment_terms` - NET 30, NET 14, COD
- `notes` - Additional notes
- `status` - active/inactive

---

#### 15. `purchase_orders`
**Purpose:** Purchase order from suppliers

**Key Fields:**
- `id` - Primary key
- `business_id` - Foreign key to businesses
- `outlet_id` - Destination outlet
- `supplier_id` - Foreign key to suppliers
- `created_by` - User who created
- `received_by` - User who received (nullable)
- `po_number` - Unique PO number
- `reference` - External reference
- `order_date` - Order creation date
- `expected_date` - Expected delivery
- `received_date` - Actual receipt date
- `status` - pending/partial/received/cancelled
- `total_amount` - Total PO value

**Workflow:**
1. Create PO → Status: `pending`
2. Partial receipt → Status: `partial`
3. Full receipt → Status: `received`

---

#### 16. `purchase_order_items`
**Purpose:** PO line items

**Key Fields:**
- `id` - Primary key
- `purchase_order_id` - PO header
- `product_id` - Ordered product
- `quantity_ordered` - Quantity ordered
- `quantity_received` - Quantity actually received
- `unit_cost` - Cost per unit
- `total_cost` - Line total

**Use Case:**
Ordered 100 laptops, received 95 laptops (5 damaged in transit).

---

#### 17. `stock_transfers`
**Purpose:** Inter-outlet stock transfers

**Key Fields:**
- `id` - Primary key
- `business_id` - Foreign key to businesses
- `from_outlet_id` - Source outlet
- `to_outlet_id` - Destination outlet
- `requested_by`, `approved_by`, `sent_by`, `received_by` - User tracking
- `transfer_number` - Unique transfer number
- `transfer_date` - Request date
- `sent_date`, `received_date` - Actual dates
- `status` - pending/approved/sent/received/cancelled

**Workflow:**
1. Request transfer Jakarta → Bandung
2. Manager approves
3. Warehouse sends
4. Bandung receives and confirms

---

#### 18. `stock_transfer_items`
**Purpose:** Transfer line items

**Key Fields:**
- `id` - Primary key
- `stock_transfer_id` - Transfer header
- `product_id` - Transferred product
- `quantity_requested` - Requested qty
- `quantity_sent` - Actually sent
- `quantity_received` - Actually received

---

#### 19. `stock_histories`
**Purpose:** Complete stock movement audit trail

**Key Fields:**
- `id` - Primary key
- `stock_id` - Related stock record
- `user_id` - User who performed action
- `outlet_id` - Outlet location
- `quantity` - Quantity change (+/-)
- `current_stock` - Stock after change
- `type` - in/out/adjustment/transfer/sale/return
- `reference` - Reference document (PO#, Order#, Transfer#)
- `note` - Additional notes

**Use Case:**
Track every stock movement: purchase, sale, transfer, adjustment, return.

---

### Sales & Orders Module

#### 20. `orders`
**Purpose:** Sales order header

**Key Fields:**
- `id` - Primary key
- `order_number` - Unique order number (ORD-20250101-001)
- `outlet_id` - Selling outlet
- `customer_id` - Customer (nullable)
- `cashier_id` - Cashier who processed
- `sub_total` - Subtotal before tax/discount
- `total_price` - Final total
- `total_items` - Item count
- `tax` - Tax amount
- `discount` - Discount amount
- `payment_method` - Primary payment method
- `payment_status` - pending/completed/failed
- `cash_received` - Cash tendered
- `change` - Change given
- `notes` - Order notes
- `status` - pending/completed/cancelled

**Relationships:**
- `hasMany(OrderItem)` - Order line items
- `hasMany(OrderPayment)` - Split payment support
- `belongsTo(Customer)` - Customer
- `belongsTo(User, 'cashier_id')` - Cashier

---

#### 21. `order_items`
**Purpose:** Order line items

**Key Fields:**
- `id` - Primary key
- `order_id` - Order header
- `product_id` - Sold product
- `quantity` - Quantity sold
- `price` - Unit price at time of sale
- `total` - Line total

**Use Case:**
Order #123: 2 laptops @ 15jt = 30jt, 3 t-shirts @ 100k = 300k

---

#### 22. `order_payments`
**Purpose:** Split payment support

**Key Fields:**
- `id` - Primary key
- `order_id` - Related order
- `payment_method` - cash/card/qris/transfer
- `amount` - Payment amount
- `reference_number` - Card/transfer reference
- `status` - completed/pending/failed

**Use Case:**
Total: 500k → Cash 200k + Card 300k (split payment)

---

#### 23. `order_taxes`
**Purpose:** Tax application on orders

**Key Fields:**
- `id` - Primary key
- `order_id` - Related order
- `tax_id` - Business setting for tax

**Use Case:**
Apply PPN 11% to order.

---

#### 24. `order_discounts`
**Purpose:** Discount application on orders

**Key Fields:**
- `id` - Primary key
- `order_id` - Related order
- `discount_id` - Business setting for discount

---

#### 25. `cash_drawer_sessions`
**Purpose:** Cash management per shift

**Key Fields:**
- `id` - Primary key
- `outlet_id` - Outlet
- `user_id` - Cashier
- `session_number` - Unique session ID
- `opening_balance` - Starting cash
- `closing_balance` - Ending cash
- `expected_cash` - Expected based on sales
- `actual_cash` - Actual counted cash
- `difference` - Variance (over/short)
- `opened_at`, `closed_at` - Session times
- `status` - open/closed

**Workflow:**
1. Cashier opens drawer: 500k
2. Sells all day
3. Closes drawer: Expected 2.5jt, Actual 2.48jt, Short: 20k

---

#### 26. `cash_transactions`
**Purpose:** Cash drawer movements

**Key Fields:**
- `id` - Primary key
- `cash_drawer_session_id` - Related session
- `type` - cash_in/cash_out/sale/refund/adjustment
- `amount` - Transaction amount
- `reference` - Order number, reason
- `description` - Notes

---

### Returns & Refunds Module

#### 27. `order_returns`
**Purpose:** Return/refund header

**Key Fields:**
- `id` - Primary key
- `order_id` - Original order
- `business_id`, `outlet_id`, `cashier_id` - Context
- `return_number` - Unique return number
- `return_date` - Return date
- `total_refund` - Refund amount
- `refund_method` - cash/card/store_credit
- `reason` - Return reason
- `status` - pending/approved/rejected/completed

---

#### 28. `order_return_items`
**Purpose:** Return line items

**Key Fields:**
- `id` - Primary key
- `order_return_id` - Return header
- `order_item_id` - Original order item
- `product_id` - Returned product
- `quantity` - Return quantity
- `price` - Refund per unit
- `total_refund` - Line refund
- `condition` - good/damaged/defective
- `restock` - Boolean (add back to stock?)

**Use Case:**
Customer returns 1 damaged laptop → Refund but don't restock.

---

### Customer & Loyalty Module

#### 29. `customers`
**Purpose:** Customer master data with loyalty

**Key Fields:**
- `id` - Primary key
- `business_id` - Foreign key to businesses
- `name` - Customer name
- `phone` - Contact number
- `email` - Email address
- `address` - Customer address
- `loyalty_points` - Reward points balance
- `total_spent` - Lifetime spending
- `visit_count` - Number of visits
- `last_visit_at` - Last purchase date
- `birthdate` - Birthday (for birthday promos)
- `customer_group` - regular/vip/wholesale
- `notes` - Customer notes

**Use Case:**
Rina (VIP): 2.5jt spent, 150 points, 5 visits → Birthday promo 20% off

---

### Marketing & Promotions Module

#### 30. `promotions`
**Purpose:** Marketing campaigns

**Key Fields:**
- `id` - Primary key
- `business_id` - Foreign key to businesses
- `name` - Campaign name
- `description` - Campaign details
- `discount_type` - percentage/fixed/buy_x_get_y
- `discount_value` - Discount amount
- `min_purchase_amount` - Minimum purchase
- `applicable_to` - all/categories/products
- `start_date`, `end_date` - Campaign period
- `max_uses` - Usage limit
- `used_count` - Times used
- `status` - active/inactive/expired

**Examples:**
- Flash Sale: 50% off Electronics (Jan 1-7)
- Buy 2 Get 1 Free T-Shirts
- Minimum 500k → 10% off

---

#### 31. `promotion_product`
**Purpose:** Product-specific promotions (pivot)

**Key Fields:**
- `promotion_id` - Campaign
- `product_id` - Eligible product

---

#### 32. `promotion_category`
**Purpose:** Category-wide promotions (pivot)

**Key Fields:**
- `promotion_id` - Campaign
- `category_id` - Eligible category

---

### Employee Management Module

#### 33. `shifts`
**Purpose:** Work shift definitions

**Key Fields:**
- `id` - Primary key
- `outlet_id` - Outlet
- `name` - Shift name (Morning, Evening, Night)
- `start_time`, `end_time` - Shift hours
- `grace_period_minutes` - Late tolerance
- `status` - active/inactive

**Example:**
Morning: 08:00-16:00 (grace: 15 min)
Evening: 16:00-00:00 (grace: 15 min)

---

#### 34. `attendances`
**Purpose:** Employee attendance tracking

**Key Fields:**
- `id` - Primary key
- `user_id` - Employee
- `outlet_id` - Outlet
- `shift_id` - Assigned shift
- `date` - Attendance date
- `clock_in`, `clock_out` - Actual times
- `status` - present/absent/late/leave/sick
- `notes` - Attendance notes

**Constraint:** Unique (user_id, date) - One record per employee per day

**Auto-detection:**
Clock in 08:14 (shift starts 08:00, grace 15 min) → Status: `present`
Clock in 08:20 → Status: `late`

---

### Reporting & Analytics Module

#### 35. `sales_summaries`
**Purpose:** Aggregated daily sales data

**Key Fields:**
- `id` - Primary key
- `business_id` - Foreign key to businesses
- `date` - Summary date
- `total_sales` - Total revenue (decimal 12,2)
- `total_tax` - Total tax collected
- `total_discount` - Total discounts given
- `total_profit` - Total profit
- `total_quantity` - Items sold

**Use Case:**
Fast reporting without scanning thousands of order records.

---

#### 36. `sales_transactions`
**Purpose:** Transaction-level sales details

**Key Fields:**
- `id` - Primary key
- `business_id`, `outlet_id`, `product_id` - Context
- `date` - Transaction date
- `quantity_sold` - Quantity
- `total_sales` - Revenue

**Use Case:**
Product performance analysis, best-seller reports.

---

### System & Monitoring Module

#### 37. `activity_logs`
**Purpose:** Complete audit trail

**Key Fields:**
- `id` - Primary key
- `business_id` - Tenant
- `user_id` - Actor (who did it)
- `model_type` - Polymorphic (Product, Order, etc.)
- `model_id` - Record ID
- `action` - created/updated/deleted/restored
- `old_values` - JSON before
- `new_values` - JSON after
- `ip_address` - Client IP
- `user_agent` - Browser/app info

**Use Case:**
"Who changed laptop price from 15jt to 12jt on Jan 5?"
Answer: User #3 (Manager) at 14:30 from IP 192.168.1.10

---

#### 38. `notifications`
**Purpose:** User notifications (Laravel standard)

**Key Fields:**
- `id` - UUID primary key
- `type` - Notification class
- `notifiable_type`, `notifiable_id` - Polymorphic (User/Business)
- `data` - JSON payload
- `read_at` - Read timestamp

**Use Cases:**
- Low stock alert: "Product X has only 5 units left"
- Trial expiry: "Your trial expires in 3 days"
- Payment due: "Invoice #123 is overdue"
- New order: "New order #456 received"

---

#### 39. `printers`
**Purpose:** Receipt printer configuration

**Key Fields:**
- `id` - Primary key
- `outlet_id` - Outlet
- `name` - Printer name
- `connection_type` - bluetooth/network/usb
- `mac_address`, `ip_address` - Connection details
- `paper_width` - 58mm/80mm
- `default` - Default printer flag

---

## Database Relationships & Data Flow

### Multi-Tenant Architecture

```
Business (Tenant)
├── Outlets (1:many)
│   ├── Users (assigned employees)
│   ├── Stocks (inventory per outlet)
│   ├── Orders (sales per outlet)
│   └── Shifts (work schedules)
├── Products (1:many)
│   └── ProductVariants (1:many)
├── Customers (1:many)
├── Suppliers (1:many)
└── Subscription (1:1)
```

### Sales Transaction Flow

```
1. Customer walks in
2. Cashier creates Order
3. Add OrderItems (products)
4. Apply Promotions → OrderDiscounts
5. Calculate Tax → OrderTaxes
6. Process Payment → OrderPayments
7. Update Stock (decrease quantity)
8. Record StockHistory
9. Update SalesSummary
10. Update SalesTransaction
11. Update CashDrawerSession
12. Print receipt
13. Add loyalty points to Customer
```

### Purchase Order Flow

```
1. Manager creates PurchaseOrder
2. Add PurchaseOrderItems
3. Supplier delivers
4. Warehouse receives goods
5. Update Stock (increase quantity)
6. Record StockHistory
7. Update PO status to "received"
```

### Stock Transfer Flow

```
1. Outlet A requests stock from Outlet B
2. Create StockTransfer (status: pending)
3. Manager approves (status: approved)
4. Outlet B ships (status: sent)
   - Decrease Stock at Outlet B
   - Record StockHistory
5. Outlet A receives (status: received)
   - Increase Stock at Outlet A
   - Record StockHistory
```

### Subscription Lifecycle

```
1. Business signs up
2. Create BusinessSubscription (status: trial)
3. Trial ends
4. Generate SubscriptionInvoice
5. Process payment via gateway
6. Create SubscriptionPayment
7. Update subscription (status: active)
8. Schedule next billing
9. Repeat monthly/yearly
```

---

## Key Features Enabled by Database Design

### 1. Multi-Tenant Isolation
- Each business sees only their own data
- Enforced by `business_id` foreign key on all tables
- Performance: Indexed on `business_id`

### 2. Multi-Outlet Support
- Independent inventory per outlet
- Transfer stock between outlets
- Outlet-level reporting
- Outlet-specific users and shifts

### 3. Flexible Product Management
- Unlimited product variants (size, color, material)
- Variant-specific pricing
- Multiple unit types (pcs, kg, liter)
- Featured products
- Soft delete (archival)

### 4. Complete Inventory Tracking
- Real-time stock levels per outlet
- Purchase order management
- Stock transfer workflow
- Complete movement history
- Auto-reorder alerts
- Low stock notifications

### 5. Advanced Sales Features
- Split payment (cash + card)
- Multiple tax configurations
- Flexible discounts
- Customer loyalty points
- Return/refund management
- Cash drawer tracking

### 6. SaaS Business Model
- Tiered pricing (Starter, Business, Enterprise)
- Usage-based limits (outlets, users, products)
- Trial period support
- Auto-billing
- Invoice generation
- Multiple payment gateways

### 7. Customer Loyalty
- Points accumulation
- Purchase history tracking
- Customer segmentation (regular/vip)
- Birthday promotions
- Visit frequency tracking

### 8. Marketing Campaigns
- Date-based promotions
- Product/category-specific discounts
- Minimum purchase requirements
- Usage limits
- Campaign analytics

### 9. HR Management
- Shift scheduling
- Attendance tracking
- Late detection
- Working hours calculation
- Performance monitoring

### 10. Complete Audit Trail
- Every change tracked
- Before/after values stored
- User identification
- IP address logging
- Compliance-ready

### 11. Real-Time Notifications
- Low stock alerts
- Trial expiry warnings
- Payment due reminders
- New order notifications
- System announcements

### 12. Performance Optimizations
- Strategic indexes on high-traffic columns
- Composite indexes for filtered queries
- Soft deletes instead of hard deletes
- Denormalized reporting tables
- Efficient relationship queries

---

## Use Cases for Marketing Stories

### Story 1: "From Single Shop to Multi-Outlet Empire"
**Database Support:**
- One Business can have unlimited Outlets
- Each Outlet has independent Stock management
- Stock Transfers enable seamless distribution
- Outlet-level reporting shows performance

**Marketing Angle:**
"Start with one shop, scale to 10, 50, or 100 outlets without changing systems. Our multi-outlet architecture grows with your business."

---

### Story 2: "Never Lose a Sale to Stockouts"
**Database Support:**
- Real-time Stock tracking per outlet
- StockHistories provide complete audit trail
- Reorder point triggers automatic alerts
- PurchaseOrders streamline restocking

**Marketing Angle:**
"Know exactly what you have, where you have it, and when to reorder. Automated alerts prevent stockouts before they happen."

---

### Story 3: "Sell Anywhere, Track Everything"
**Database Support:**
- Multi-user with role-based access
- CashDrawerSessions per shift
- ActivityLogs track every action
- Real-time synchronization

**Marketing Angle:**
"Owner, manager, and cashiers work seamlessly together. Complete visibility and control from your phone."

---

### Story 4: "Turn Customers Into Fans"
**Database Support:**
- Customer master data with purchase history
- Loyalty points system
- Birthday tracking for personalized promos
- Customer segmentation (regular/vip)

**Marketing Angle:**
"Reward loyalty automatically. Remember birthdays. Turn one-time buyers into lifetime customers."

---

### Story 5: "Subscription Flexibility That Grows With You"
**Database Support:**
- Multiple SubscriptionPlans with different limits
- Usage tracking (outlets, users, products, transactions)
- Auto-billing with grace periods
- Easy upgrades/downgrades

**Marketing Angle:**
"Start small, dream big. Our flexible plans adapt to your growth. Only pay for what you need."

---

### Story 6: "Product Variations Made Simple"
**Database Support:**
- ProductVariants with unlimited attributes
- Variant-specific pricing and images
- Independent inventory per variant
- Flexible unit types

**Marketing Angle:**
"Sell the same product in multiple sizes, colors, and packages. One click to add variants, not duplicate products."

---

### Story 7: "Complete Financial Control"
**Database Support:**
- CashDrawerSessions track every rupiah
- Split payment support (cash + card)
- SubscriptionInvoices and Payments
- ActivityLogs for audit trail

**Marketing Angle:**
"Know where every rupiah goes. Cash drawer reconciliation. Full payment tracking. Audit-ready reports."

---

### Story 8: "Smart Returns, Happy Customers"
**Database Support:**
- OrderReturns with reason tracking
- Conditional restocking (good/damaged)
- Refund via cash/card/store credit
- Return analytics

**Marketing Angle:**
"Handle returns professionally. Automatic refunds. Choose whether to restock. Keep customers happy."

---

### Story 9: "Your Staff, Managed Effortlessly"
**Database Support:**
- Shifts with grace period
- Attendances with auto late-detection
- Working hours calculation
- Performance tracking

**Marketing Angle:**
"Shift scheduling in seconds. Automatic attendance tracking. No more manual timesheets. Focus on customers, not paperwork."

---

### Story 10: "Data-Driven Decisions"
**Database Support:**
- SalesSummaries for quick insights
- SalesTransactions for deep analysis
- Product performance tracking
- Customer purchase patterns

**Marketing Angle:**
"See what sells, what doesn't, and why. Best-seller reports. Customer trends. Profit margins by product. Make decisions based on data, not guesses."

---

## Technical Highlights for Marketing

### Enterprise-Grade Architecture
- **43 interconnected tables** - Comprehensive data model
- **100+ indexes** - Blazing fast queries
- **Soft deletes** - Never lose data accidentally
- **JSON fields** - Flexible configuration
- **Polymorphic relationships** - Advanced features

### Scalability
- **Multi-tenant** - Thousands of businesses on one instance
- **Unlimited outlets** - Scale horizontally
- **Millions of transactions** - Optimized for performance
- **Cloud-ready** - Deploy anywhere

### Security & Compliance
- **Complete audit trail** - Who, what, when, where
- **Role-based access** - Granular permissions
- **Data isolation** - Multi-tenant security
- **Soft deletes** - Data recovery
- **Activity logs** - Compliance-ready

### Developer-Friendly
- **Laravel Eloquent ORM** - Modern PHP framework
- **RESTful API ready** - Easy integrations
- **Comprehensive seeders** - Demo data included
- **Well-documented** - This document!

---

## Sample Data (From Seeders)

### Demo Business: "Toko Maju Jaya"
- **Status:** Trial (14 days remaining)
- **Plan:** Business (Rp 299.000/month)
- **Outlets:** 2 (Jakarta, Bandung)
- **Users:** 4 (Owner, Manager, 2 Cashiers)
- **Products:** 3 base + 8 variants
- **Customers:** 3 (1 VIP, 2 Regular)
- **Categories:** Electronics, Fashion, Food & Beverage
- **Suppliers:** 2 active suppliers

### Login Credentials
```
Owner:     owner@tokomajujaya.com     / password
Manager:   manager@tokomajujaya.com   / password
Cashier 1: cashier1@tokomajujaya.com  / password
Cashier 2: cashier2@tokomajujaya.com  / password
```

---

## Conclusion

This database schema represents a **production-ready, enterprise-grade POS SaaS system** capable of handling:
- Multiple businesses (multi-tenant)
- Multiple outlets per business
- Complex inventory management
- Complete sales cycle (order to payment to returns)
- Subscription billing
- Customer loyalty programs
- Employee management
- Marketing campaigns
- Complete audit trail

**Total Capabilities:** 43 tables, 20+ models, unlimited scalability.

**Perfect for:** Retail chains, F&B businesses, wholesale distributors, service businesses.

**Deployment:** Ready for cloud (AWS, Google Cloud, Azure) or on-premise.

---

*Document Version: 1.0*
*Last Updated: November 28, 2025*
*Database Schema Version: Production v1.0*


