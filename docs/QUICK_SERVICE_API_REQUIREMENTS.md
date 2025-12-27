# Quick Service API Requirements

Dokumen ini berisi daftar lengkap API endpoints yang diperlukan untuk mengimplementasikan fitur Quick Service (Dine In & Take Away) secara full online.

## 📋 Overview
Quick Service adalah fitur untuk mengelola transaksi Dine In dan Take Away dengan flow:
1. Buat transaksi baru (pilih mode & jumlah pax)
2. Tambah items ke order
3. Simpan order (draft)
4. Pilih metode pembayaran
5. Review & konfirmasi pembayaran
6. Cetak nota & struk dapur

---

## 🔐 Authentication
Semua endpoint memerlukan Bearer Token authentication:
```
Authorization: Bearer {access_token}
```

---

## 📍 Required API Endpoints

### 1. **GET /api/quick-service/orders**
Mengambil daftar order yang tersimpan (draft & completed)

**Query Parameters:**
- `status` (optional): `draft`, `completed`, `all`
- `mode` (optional): `dine_in`, `take_away`, `all`
- `date` (optional): `YYYY-MM-DD`
- `page` (optional): pagination
- `per_page` (optional): default 20

**Response Success (200):**
```json
{
  "success": true,
  "message": "Orders retrieved successfully",
  "data": {
    "orders": [
      {
        "id": 1,
        "order_number": "QS-001-2025",
        "type": "DINE IN", // or "TAKE AWAY"
        "status": "draft", // draft, completed, cancelled
        "pax": 4,
        "table_number": "A12",
        "customer_name": "John Doe",
        "items_count": 3,
        "subtotal": 75000,
        "tax": 7500,
        "service_charge": 3750,
        "discount": 0,
        "total": 86250,
        "created_at": "2025-12-06T20:13:07Z",
        "updated_at": "2025-12-06T20:13:07Z",
        "cashier": {
          "id": 1,
          "name": "DEWA ESB"
        },
        "items": [
          {
            "product_id": 10,
            "product_name": "Nasi Goreng Kampung",
            "quantity": 2,
            "price": 25000,
            "subtotal": 50000,
            "notes": ""
          }
        ]
      }
    ],
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

### 2. **POST /api/quick-service/orders**
Membuat order baru (draft)

**Request Body:**
```json
{
  "type": "DINE IN", // or "TAKE AWAY"
  "pax": 4,
  "table_number": "A12", // optional
  "customer_name": "John Doe", // optional
  "notes": "" // optional
}
```

**Response Success (201):**
```json
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
      "table_number": "A12",
      "customer_name": "John Doe",
      "items": [],
      "subtotal": 0,
      "tax": 0,
      "service_charge": 0,
      "total": 0,
      "created_at": "2025-12-06T20:13:07Z"
    }
  }
}
```

---

### 3. **GET /api/quick-service/orders/{order_id}**
Mengambil detail order

**Response Success (200):**
```json
{
  "success": true,
  "message": "Order detail retrieved successfully",
  "data": {
    "order": {
      "id": 1,
      "order_number": "QS-001-2025",
      "type": "DINE IN",
      "status": "draft",
      "pax": 4,
      "table_number": "A12",
      "customer_name": "John Doe",
      "notes": "",
      "items": [
        {
          "id": 1,
          "product_id": 10,
          "product_name": "Nasi Goreng Kampung",
          "product_image": "https://...",
          "quantity": 2,
          "price": 25000,
          "subtotal": 50000,
          "notes": ""
        }
      ],
      "subtotal": 75000,
      "tax": 7500,
      "tax_percentage": 10,
      "service_charge": 3750,
      "service_charge_percentage": 5,
      "discount": 0,
      "discount_percentage": 0,
      "total": 86250,
      "created_at": "2025-12-06T20:13:07Z",
      "updated_at": "2025-12-06T20:13:07Z",
      "cashier": {
        "id": 1,
        "name": "DEWA ESB",
        "email": "dewa@example.com"
      }
    }
  }
}
```

---

### 4. **PUT /api/quick-service/orders/{order_id}**
Update order info (table number, customer name, pax, dll)

**Request Body:**
```json
{
  "pax": 5,
  "table_number": "B15",
  "customer_name": "Jane Doe",
  "notes": "Extra pedas"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Order updated successfully",
  "data": {
    "order": {
      "id": 1,
      "order_number": "QS-001-2025",
      "pax": 5,
      "table_number": "B15",
      "customer_name": "Jane Doe",
      "notes": "Extra pedas",
      "updated_at": "2025-12-06T20:15:00Z"
    }
  }
}
```

---

### 5. **POST /api/quick-service/orders/{order_id}/items**
Menambah item ke order

**Request Body:**
```json
{
  "product_id": 10,
  "quantity": 2,
  "notes": "Tanpa timun" // optional
}
```

**Response Success (201):**
```json
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

### 6. **PUT /api/quick-service/orders/{order_id}/items/{item_id}**
Update quantity atau notes item

**Request Body:**
```json
{
  "quantity": 3,
  "notes": "Extra pedas"
}
```

**Response Success (200):**
```json
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

### 7. **DELETE /api/quick-service/orders/{order_id}/items/{item_id}**
Hapus item dari order

**Response Success (200):**
```json
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

### 8. **POST /api/quick-service/orders/{order_id}/save**
Simpan order sebagai draft (belum bayar)

**Request Body:**
```json
{
  "notes": "Order untuk meja VIP" // optional
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Order saved successfully",
  "data": {
    "order": {
      "id": 1,
      "order_number": "QS-001-2025",
      "status": "draft",
      "saved_at": "2025-12-06T20:15:00Z"
    }
  }
}
```

---

### 9. **POST /api/quick-service/orders/{order_id}/print-kitchen**
Print struk dapur

**Request Body:**
```json
{
  "printer_id": 2, // optional, ID printer dapur
  "copies": 1 // optional, default 1
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Kitchen order printed successfully",
  "data": {
    "print_job_id": "PRINT-001",
    "printed_at": "2025-12-06T20:15:00Z",
    "printer": {
      "id": 2,
      "name": "Kitchen Printer 1",
      "location": "Dapur Utama"
    }
  }
}
```

---

### 10. **POST /api/quick-service/orders/{order_id}/payments**
Proses pembayaran order

**Request Body:**
```json
{
  "payment_method": "CASH", // CASH, CREDIT_CARD, DEBIT_CARD, EWALLET, MEMBER_DEPOSIT
  "amount_paid": 100000,
  "notes": "" // optional
}
```

**Response Success (201):**
```json
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
      "payment_date": "2025-12-06T20:20:00Z"
    },
    "order": {
      "id": 1,
      "order_number": "QS-001-2025",
      "invoice_number": "NSGR0220251206001",
      "status": "completed",
      "total": 86250,
      "paid_amount": 100000,
      "change": 13750,
      "payment_status": "paid"
    }
  }
}
```

---

### 11. **GET /api/quick-service/orders/{order_id}/receipt**
Generate data untuk receipt/nota

**Response Success (200):**
```json
{
  "success": true,
  "message": "Receipt data retrieved successfully",
  "data": {
    "receipt": {
      "order_number": "QS-001-2025",
      "invoice_number": "NSGR0220251206001",
      "type": "DINE IN",
      "pax": 4,
      "table_number": "A12",
      "customer_name": "John Doe",
      "date": "2025-12-06",
      "time": "20:20:00",
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

### 12. **POST /api/quick-service/orders/{order_id}/print-receipt**
Print nota customer

**Request Body:**
```json
{
  "printer_id": 1, // optional, ID printer customer
  "copies": 1, // optional, default 1
  "send_email": false, // optional
  "email": "customer@example.com" // required if send_email = true
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Receipt printed successfully",
  "data": {
    "print_job_id": "PRINT-002",
    "printed_at": "2025-12-06T20:20:00Z",
    "email_sent": false
  }
}
```

---

### 13. **DELETE /api/quick-service/orders/{order_id}**
Hapus order (hanya draft yang bisa dihapus)

**Response Success (200):**
```json
{
  "success": true,
  "message": "Order deleted successfully"
}
```

**Response Error (400):**
```json
{
  "success": false,
  "message": "Cannot delete completed order",
  "errors": {
    "order": ["Only draft orders can be deleted"]
  }
}
```

---

### 14. **POST /api/quick-service/orders/{order_id}/cancel**
Cancel order yang sudah dibuat (untuk order yang sudah completed)

**Request Body:**
```json
{
  "reason": "Customer request" // required
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Order cancelled successfully",
  "data": {
    "order": {
      "id": 1,
      "status": "cancelled",
      "cancelled_at": "2025-12-06T20:25:00Z",
      "cancel_reason": "Customer request"
    }
  }
}
```

---

### 15. **GET /api/quick-service/statistics**
Statistik quick service untuk dashboard

**Query Parameters:**
- `date_from` (optional): `YYYY-MM-DD`
- `date_to` (optional): `YYYY-MM-DD`
- `mode` (optional): `dine_in`, `take_away`, `all`

**Response Success (200):**
```json
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

## 📦 Additional Data Models

### Order Status Flow
```
draft -> completed -> (optional) cancelled
```

### Payment Methods
- `CASH` - Tunai
- `CREDIT_CARD` - Kartu Kredit
- `DEBIT_CARD` - Kartu Debit
- `EWALLET` - Dompet Digital (GoPay, OVO, dll)
- `MEMBER_DEPOSIT` - Deposit Member

### Order Types
- `DINE IN` - Makan di tempat
- `TAKE AWAY` - Bungkus

---

## 🔔 Real-time Features (Optional - Nice to Have)

### WebSocket Events
Untuk real-time updates di kitchen display:

**Event: `quick-service.order.created`**
```json
{
  "event": "quick-service.order.created",
  "data": {
    "order_id": 1,
    "order_number": "QS-001-2025",
    "type": "DINE IN",
    "table_number": "A12",
    "items_count": 3
  }
}
```

**Event: `quick-service.order.updated`**
```json
{
  "event": "quick-service.order.updated",
  "data": {
    "order_id": 1,
    "updated_fields": ["items", "total"]
  }
}
```

**Event: `quick-service.order.completed`**
```json
{
  "event": "quick-service.order.completed",
  "data": {
    "order_id": 1,
    "order_number": "QS-001-2025",
    "invoice_number": "NSGR0220251206001"
  }
}
```

---

## ⚠️ Error Responses

**General Error Format:**
```json
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

**Common HTTP Status Codes:**
- `200` - Success
- `201` - Created
- `400` - Bad Request (validation error)
- `401` - Unauthorized (token invalid/expired)
- `403` - Forbidden (no permission)
- `404` - Not Found
- `422` - Unprocessable Entity (validation error)
- `500` - Internal Server Error

---

## 📝 Notes untuk Backend Team

1. **Business Logic:**
   - Perhitungan tax dan service charge mengikuti setting dari `business_settings`
   - Order hanya bisa diupdate jika status masih `draft`
   - Payment harus mencatat semua metode pembayaran (untuk split payment nantinya)
   - Stock produk berkurang saat order di-complete (bukan saat draft)

2. **Security:**
   - Validasi user hanya bisa akses order dari outlet sendiri
   - Log semua aktivitas cancel/delete order
   - Validasi role untuk aksi tertentu (cancel, delete, dll)

3. **Performance:**
   - Index pada: `outlet_id`, `status`, `type`, `created_at`
   - Cache untuk statistics endpoint
   - Lazy load items untuk list orders

4. **Database:**
   - Soft delete untuk orders (jangan hard delete)
   - Audit trail untuk payment transactions
   - Store printer settings per outlet

5. **Integration:**
   - Integrasi dengan existing product & category API
   - Integrasi dengan printer service (jika ada)
   - Email service untuk send receipt

---

## 🚀 Priority Implementation

### Phase 1 (Must Have - High Priority)
1. Create order
2. Add/Remove items
3. Save draft
4. Process payment
5. Get orders list
6. Get order detail

### Phase 2 (Should Have - Medium Priority)
7. Update order info
8. Print kitchen order
9. Print receipt
10. Get receipt data

### Phase 3 (Nice to Have - Low Priority)
11. Cancel order
12. Statistics
13. WebSocket events
14. Email receipt

---

**Document Version:** 1.0
**Created:** 2025-12-06
**Last Updated:** 2025-12-06
**Created By:** AI Assistant & Flutter Team
