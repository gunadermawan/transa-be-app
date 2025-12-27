# 🏢 Multi-Outlet/Multi-Merchant Feature

## 🎯 Marketing Power: PREMIUM DIFFERENTIATOR!

**This feature is a GAME CHANGER for SaaS revenue!**

### Why Multi-Outlet is Powerful for Marketing:

1. **💰 Premium Pricing Justification**
   - Basic Plan: 1 outlet (Rp 99k/month)
   - Pro Plan: 3 outlets (Rp 249k/month)
   - Enterprise: Unlimited outlets (Rp 999k/month)

2. **🎯 Target Market Expansion**
   - ✅ Franchise businesses (50+ cabang)
   - ✅ Retail chains (Indomaret, Alfamart wannabes)
   - ✅ F&B chains (cafe/resto dengan multiple cabang)
   - ✅ Fashion retail (boutique dengan beberapa toko)

3. **📊 Unique Selling Points**
   - "Kelola semua cabang dari 1 dashboard!"
   - "Bandingkan performa tiap cabang real-time!"
   - "Transfer stock antar cabang dengan 1 klik!"
   - "Cashier bisa pindah cabang tanpa logout!"

4. **🚀 Viral Content Potential**
   - Video: "Cara owner pantau 50 cabang dari HP"
   - Video: "Cabang mana yang paling laris? Lihat ranking real-time!"
   - Video: "Stock habis? Transfer dari cabang lain instant!"

---

## ✅ Features Implemented

### 1. **Get My Outlets** (Outlet List)
```http
GET /api/my-outlets
Headers: Authorization: Bearer {token}

Response:
{
  "data": [
    {
      "id": 1,
      "name": "Cabang Gatot Subroto",
      "business_id": 1,
      "address": "Jl. Gatot Subroto No. 123",
      "phone": "+62 21 1234 5678",
      "orders_count": 150,
      "business": {
        "id": 1,
        "name": "Toko Maju Jaya"
      }
    },
    {
      "id": 2,
      "name": "Cabang Sudirman",
      "business_id": 1,
      "address": "Jl. Sudirman No. 456",
      "phone": "+62 21 8765 4321",
      "orders_count": 230
    }
  ],
  "current_outlet_id": 1
}
```

**Flutter Implementation:**
```dart
class OutletSwitcher extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return FutureBuilder<OutletList>(
      future: apiService.getMyOutlets(),
      builder: (context, snapshot) {
        if (!snapshot.hasData) return LoadingWidget();

        final outlets = snapshot.data!.outlets;
        final currentId = snapshot.data!.currentOutletId;

        return DropdownButton<int>(
          value: currentId,
          items: outlets.map((outlet) {
            return DropdownMenuItem(
              value: outlet.id,
              child: Row(
                children: [
                  Icon(Icons.store),
                  SizedBox(width: 8),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(outlet.name,
                        style: TextStyle(fontWeight: FontWeight.bold)),
                      Text(outlet.address,
                        style: TextStyle(fontSize: 12, color: Colors.grey)),
                    ],
                  ),
                ],
              ),
            );
          }).toList(),
          onChanged: (newOutletId) {
            // Switch outlet
            apiService.switchOutlet(newOutletId).then((_) {
              // Refresh UI
              setState(() {});
            });
          },
        );
      },
    );
  }
}
```

---

### 2. **Switch Outlet** (Change Active Outlet)
```http
POST /api/switch-outlet
Headers: Authorization: Bearer {token}

Body:
{
  "outlet_id": 2
}

Response:
{
  "message": "Switched to Cabang Sudirman",
  "data": {
    "outlet_id": 2,
    "outlet_name": "Cabang Sudirman",
    "business_id": 1
  }
}
```

**Use Case:**
- Manager yang handle 3 cabang bisa switch antar cabang
- Cashier yang dipindahkan ke cabang lain bisa update outletnya
- Owner bisa lihat data tiap cabang dengan switch

**Flutter Implementation:**
```dart
Future<void> switchOutlet(int outletId) async {
  try {
    final response = await dio.post('/api/switch-outlet',
      data: {'outlet_id': outletId},
    );

    // Update local state
    await prefs.setInt('current_outlet_id', outletId);

    // Refresh products, orders, stocks for new outlet
    await refreshData();

    showSnackbar('Berhasil pindah ke ${response.data['data']['outlet_name']}');
  } catch (e) {
    showSnackbar('Gagal pindah outlet: ${e.toString()}');
  }
}
```

---

### 3. **Cross-Outlet Dashboard** ⭐ SCREENSHOT GOLD!
```http
GET /api/cross-outlet-dashboard
Headers: Authorization: Bearer {token}

Query Parameters:
  ?date=2025-11-30  // optional
  &business_id=1    // optional (for super admin)

Response:
{
  "date": "2025-11-30",
  "total_outlets": 5,
  "grand_total": {
    "today": 12500000,
    "this_month": 375000000
  },
  "outlets": [
    {
      "outlet_id": 2,
      "outlet_name": "Cabang Sudirman",
      "address": "Jl. Sudirman No. 456",
      "today": {
        "sales": 5000000,
        "transactions": 120
      },
      "this_month": {
        "sales": 150000000,
        "transactions": 3500
      },
      "alerts": {
        "low_stock_count": 5
      }
    },
    {
      "outlet_id": 1,
      "outlet_name": "Cabang Gatot Subroto",
      "address": "Jl. Gatot Subroto No. 123",
      "today": {
        "sales": 3500000,
        "transactions": 85
      },
      "this_month": {
        "sales": 105000000,
        "transactions": 2400
      },
      "alerts": {
        "low_stock_count": 3
      }
    }
  ]
}
```

**Flutter Dashboard:**
```dart
class CrossOutletDashboard extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return FutureBuilder<CrossOutletStats>(
      future: apiService.getCrossOutletDashboard(),
      builder: (context, snapshot) {
        if (!snapshot.hasData) return LoadingWidget();

        final stats = snapshot.data!;

        return Column(
          children: [
            // Grand Total Card
            Card(
              color: Colors.blue[800],
              child: Padding(
                padding: EdgeInsets.all(16),
                child: Column(
                  children: [
                    Text('Total Semua Cabang',
                      style: TextStyle(color: Colors.white, fontSize: 16)),
                    SizedBox(height: 8),
                    Text(formatCurrency(stats.grandTotal.today),
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 32,
                        fontWeight: FontWeight.bold)),
                    Text('Hari Ini',
                      style: TextStyle(color: Colors.white70)),
                    Divider(color: Colors.white30),
                    Text(formatCurrency(stats.grandTotal.thisMonth),
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 24,
                        fontWeight: FontWeight.bold)),
                    Text('Bulan Ini',
                      style: TextStyle(color: Colors.white70)),
                  ],
                ),
              ),
            ),

            // Outlets List (sorted by performance)
            ListView.builder(
              shrinkWrap: true,
              physics: NeverScrollableScrollPhysics(),
              itemCount: stats.outlets.length,
              itemBuilder: (context, index) {
                final outlet = stats.outlets[index];
                final isTopPerformer = index == 0;

                return Card(
                  color: isTopPerformer ? Colors.green[50] : null,
                  child: ListTile(
                    leading: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text('#${index + 1}',
                          style: TextStyle(
                            fontSize: 20,
                            fontWeight: FontWeight.bold,
                            color: isTopPerformer ? Colors.green : Colors.grey,
                          )),
                        if (isTopPerformer)
                          Icon(Icons.emoji_events, color: Colors.amber, size: 20),
                      ],
                    ),
                    title: Text(outlet.outletName,
                      style: TextStyle(fontWeight: FontWeight.bold)),
                    subtitle: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(outlet.address,
                          style: TextStyle(fontSize: 12)),
                        SizedBox(height: 4),
                        Text('${outlet.today.transactions} transaksi hari ini',
                          style: TextStyle(fontSize: 12, color: Colors.blue)),
                      ],
                    ),
                    trailing: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        Text(formatCurrency(outlet.today.sales),
                          style: TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 16)),
                        Text('hari ini',
                          style: TextStyle(fontSize: 10, color: Colors.grey)),
                      ],
                    ),
                    onTap: () {
                      // Navigate to outlet detail
                      Navigator.push(context,
                        MaterialPageRoute(
                          builder: (_) => OutletDetailScreen(outlet: outlet),
                        ),
                      );
                    },
                  ),
                );
              },
            ),
          ],
        );
      },
    );
  }
}
```

---

### 4. **Outlet Comparison Report**
```http
GET /api/outlet-comparison
Headers: Authorization: Bearer {token}

Query Parameters:
  ?start_date=2025-11-01
  &end_date=2025-11-30
  &business_id=1  // optional (for super admin)

Response:
{
  "period": {
    "start_date": "2025-11-01",
    "end_date": "2025-11-30"
  },
  "comparison": [
    {
      "outlet_id": 2,
      "outlet_name": "Cabang Sudirman",
      "address": "Jl. Sudirman No. 456",
      "metrics": {
        "total_sales": 150000000,
        "total_transactions": 3500,
        "total_items_sold": 12000,
        "average_transaction": 42857
      },
      "payment_methods": {
        "cash": 90000000,
        "card": 40000000,
        "qris": 20000000
      },
      "best_selling_products": [
        {
          "product_name": "Indomie Goreng",
          "quantity": 500,
          "revenue": 1750000
        }
      ]
    }
  ]
}
```

**Marketing Use Case:**
- "Cabang mana yang paling laris bulan ini?"
- "Metode pembayaran apa yang paling banyak dipakai?"
- "Produk apa yang laku di cabang A tapi gak laku di cabang B?"

---

### 5. **Outlet Ranking**
```http
GET /api/outlet-ranking
Headers: Authorization: Bearer {token}

Query Parameters:
  ?period=month  // today, week, month, year
  &business_id=1  // optional (for super admin)

Response:
{
  "period": "month",
  "ranking": [
    {
      "rank": 1,
      "outlet_id": 2,
      "outlet_name": "Cabang Sudirman",
      "sales": 150000000,
      "transactions": 3500
    },
    {
      "rank": 2,
      "outlet_id": 1,
      "outlet_name": "Cabang Gatot Subroto",
      "sales": 105000000,
      "transactions": 2400
    },
    {
      "rank": 3,
      "outlet_id": 3,
      "outlet_name": "Cabang Thamrin",
      "sales": 80000000,
      "transactions": 1800
    }
  ]
}
```

**Flutter Leaderboard Widget:**
```dart
class OutletRanking extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Card(
      child: Column(
        children: [
          ListTile(
            title: Text('🏆 Ranking Cabang Bulan Ini',
              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
          ),
          FutureBuilder<Ranking>(
            future: apiService.getOutletRanking(period: 'month'),
            builder: (context, snapshot) {
              if (!snapshot.hasData) return LoadingWidget();

              return ListView.builder(
                shrinkWrap: true,
                physics: NeverScrollableScrollPhysics(),
                itemCount: snapshot.data!.ranking.length,
                itemBuilder: (context, index) {
                  final item = snapshot.data!.ranking[index];

                  return ListTile(
                    leading: CircleAvatar(
                      backgroundColor: index == 0
                        ? Colors.amber
                        : index == 1
                          ? Colors.grey[400]
                          : index == 2
                            ? Colors.orange[300]
                            : Colors.blue[200],
                      child: Text('${item.rank}',
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          color: Colors.white)),
                    ),
                    title: Text(item.outletName),
                    subtitle: Text('${item.transactions} transaksi'),
                    trailing: Text(formatCurrency(item.sales),
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 16)),
                  );
                },
              );
            },
          ),
        ],
      ),
    );
  }
}
```

---

## 🎬 Marketing Demo Script: Multi-Outlet Feature

### Video Title: "Kelola 50 Cabang dari 1 HP!"

**[0:00-0:15] Problem Hook**
> "Punya banyak cabang tapi susah pantau satu-satu? Gak tau cabang mana yang paling laris? Stock cabang A banyak, cabang B kosong?"

**[0:15-0:30] Solution Overview**
> *Screen: Cross-outlet dashboard with 5 outlets*
> "Dengan POS ini, semua cabang kelihatan dalam 1 dashboard!"

**[0:30-0:50] Feature Showcase**
> *Screen: Outlet ranking*
> "Lihat langsung cabang mana yang paling laris bulan ini. Cabang Sudirman juara 1 dengan sales 150 juta!"

**[0:50-1:10] Outlet Switcher**
> *Screen: Switch outlet dropdown*
> "Manager bisa pindah-pindah cabang tanpa logout. Klik, langsung lihat data cabang lain!"

**[1:10-1:30] Comparison Report**
> *Screen: Outlet comparison chart*
> "Bandingkan performa tiap cabang. Produk apa yang laku di cabang A tapi gak laku di cabang B. Data lengkap!"

**[1:30-1:45] CTA**
> "Punya franchise atau chain store? POS ini cocok banget! Link di deskripsi!"

---

## 💰 Pricing Strategy

### Tiered Plans Based on Outlets:

**Starter Plan - Rp 99k/month**
- 1 outlet
- 100 products
- Basic reports

**Pro Plan - Rp 249k/month** ⭐ Most Popular
- 3 outlets
- Unlimited products
- Cross-outlet dashboard
- Outlet comparison
- Stock transfer between outlets (future)

**Enterprise Plan - Rp 999k/month**
- Unlimited outlets
- Unlimited everything
- Custom reports
- API access
- Dedicated support

---

## 📊 Marketing Metrics to Highlight

### Target Customer Success Stories:

**Case Study 1: Franchise Kopi**
- 15 cabang di Jakarta
- Sebelum: Excel hell, gak tau stock tiap cabang
- Sesudah: 1 dashboard, pantau semua, stock auto sync
- Result: Hemat 20 jam/week untuk admin

**Case Study 2: Retail Fashion**
- 5 boutique di mall berbeda
- Sebelum: Tiap cabang manual, sering salah stock
- Sesudah: Real-time stock, transfer antar cabang
- Result: Sales naik 30% karena stock selalu available

**Case Study 3: Minimarket Chain**
- 50+ toko di seluruh Indonesia
- Sebelum: Report manual, 1 minggu baru ada
- Sesudah: Real-time dashboard, ranking otomatis
- Result: Owner bisa decision making cepat

---

## 🚀 Future Enhancements (V2)

1. **Stock Transfer Between Outlets**
   - Transfer stock dari cabang A ke cabang B
   - Auto create stock history
   - Notification to both outlets

2. **Employee Transfer**
   - Pindahkan kasir dari cabang ke cabang
   - Attendance tracking per outlet
   - Performance comparison

3. **Outlet-Specific Promotions**
   - Promo hanya berlaku di cabang tertentu
   - Track redemption per outlet

4. **Inter-Outlet Purchase Order**
   - Cabang bisa "order" dari cabang lain
   - Internal transfer pricing

5. **Centralized Inventory Management**
   - Central warehouse
   - Distribution to outlets
   - Auto replenishment

---

## ✅ Technical Implementation Notes

### Database Structure:
```
businesses (tenant/merchant)
  └── outlets (branches/stores)
      ├── users (cashiers, managers assigned to outlet)
      ├── stocks (stock per outlet)
      ├── orders (transactions per outlet)
      └── printers (thermal printers per outlet)
```

### Access Control:
- **Super Admin**: All businesses, all outlets
- **Business Owner**: All outlets in their business
- **Manager**: Assigned outlets (can be multiple)
- **Cashier**: Only their assigned outlet

### Data Isolation:
- All queries filtered by business_id (tenant isolation)
- Outlet-level filtering for transactions, stocks
- Cross-outlet features only for owners/managers

---

## 🎯 Marketing Channels

### Where to Promote Multi-Outlet Feature:

1. **LinkedIn** - Target franchise owners, retail chains
2. **Instagram Business** - Case studies, success metrics
3. **Facebook Groups** - Grup franchise, retail, UMKM
4. **YouTube** - Tutorial lengkap multi-outlet management
5. **Cold Outreach** - Email to franchise associations
6. **Trade Shows** - Franchise expo, retail expo

### Keywords to Target:
- "POS untuk franchise"
- "Sistem kasir multi cabang"
- "Software retail chain"
- "Kelola banyak toko"
- "POS untuk chain store"

---

**This feature alone can justify 3x higher pricing! 🚀**

Multi-outlet = Premium customer = Higher LTV = Sustainable SaaS business!
