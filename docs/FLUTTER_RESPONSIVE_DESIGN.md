# Flutter Responsive Design Guide - Academy POS

## 📱 Device Strategy

### Target Devices
- **📱 Phone (Small):** 320px - 600px width
  - Owner/Manager untuk monitoring on-the-go
  - Dashboard, Reports, Settings
  - Single column layout

- **📱 Tablet (Medium):** 600px - 1024px width
  - Kasir untuk POS transactions
  - POS Screen dengan dual-pane
  - Two column layouts

- **🖥️ Desktop (Large):** 1024px+ width (Optional)
  - Admin dashboard
  - Analytics & Reports
  - Multi-column layouts

---

## 🎯 Responsive Architecture

### 1. Screen Size Detection Utility

```dart
// lib/core/utils/screen_size.dart
import 'package:flutter/material.dart';

enum DeviceType {
  phone,
  tablet,
  desktop,
}

class ScreenSize {
  static const double phoneMaxWidth = 600;
  static const double tabletMaxWidth = 1024;

  static DeviceType getDeviceType(BuildContext context) {
    final width = MediaQuery.of(context).size.width;

    if (width < phoneMaxWidth) {
      return DeviceType.phone;
    } else if (width < tabletMaxWidth) {
      return DeviceType.tablet;
    } else {
      return DeviceType.desktop;
    }
  }

  static bool isPhone(BuildContext context) {
    return getDeviceType(context) == DeviceType.phone;
  }

  static bool isTablet(BuildContext context) {
    return getDeviceType(context) == DeviceType.tablet;
  }

  static bool isDesktop(BuildContext context) {
    return getDeviceType(context) == DeviceType.desktop;
  }

  static bool isTabletOrLarger(BuildContext context) {
    return !isPhone(context);
  }

  // Get responsive value
  static T responsive<T>(
    BuildContext context, {
    required T phone,
    T? tablet,
    T? desktop,
  }) {
    final deviceType = getDeviceType(context);

    switch (deviceType) {
      case DeviceType.phone:
        return phone;
      case DeviceType.tablet:
        return tablet ?? phone;
      case DeviceType.desktop:
        return desktop ?? tablet ?? phone;
    }
  }

  // Responsive padding
  static EdgeInsets responsivePadding(BuildContext context) {
    return EdgeInsets.all(
      responsive(
        context,
        phone: 16.0,
        tablet: 24.0,
        desktop: 32.0,
      ),
    );
  }

  // Responsive grid columns
  static int gridColumns(BuildContext context) {
    return responsive(
      context,
      phone: 2,      // 2 columns on phone
      tablet: 3,     // 3 columns on tablet
      desktop: 4,    // 4 columns on desktop
    );
  }

  // Responsive font size
  static double fontSize(BuildContext context, {
    required double base,
  }) {
    return base * responsive(
      context,
      phone: 1.0,
      tablet: 1.1,
      desktop: 1.2,
    );
  }
}
```

---

### 2. Responsive Widget Base Class

```dart
// lib/core/widgets/responsive_widget.dart
import 'package:flutter/material.dart';
import '../utils/screen_size.dart';

class ResponsiveWidget extends StatelessWidget {
  final Widget phone;
  final Widget? tablet;
  final Widget? desktop;

  const ResponsiveWidget({
    Key? key,
    required this.phone,
    this.tablet,
    this.desktop,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return ScreenSize.responsive(
      context,
      phone: phone,
      tablet: tablet,
      desktop: desktop,
    );
  }
}
```

---

### 3. Responsive Layout Builder

```dart
// lib/core/widgets/responsive_layout.dart
import 'package:flutter/material.dart';
import '../utils/screen_size.dart';

class ResponsiveLayout extends StatelessWidget {
  final Widget Function(BuildContext context, DeviceType deviceType) builder;

  const ResponsiveLayout({
    Key? key,
    required this.builder,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final deviceType = ScreenSize.getDeviceType(context);
    return builder(context, deviceType);
  }
}
```

---

## 📱 Implementation Examples

### Example 1: Dashboard Screen (Responsive)

```dart
// lib/screens/dashboard/dashboard_screen.dart
import 'package:flutter/material.dart';
import '../../core/utils/screen_size.dart';
import '../../core/widgets/responsive_widget.dart';

class DashboardScreen extends StatelessWidget {
  const DashboardScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard'),
      ),
      body: ResponsiveWidget(
        phone: _DashboardPhoneView(),
        tablet: _DashboardTabletView(),
      ),
    );
  }
}

// Phone Layout: Single Column
class _DashboardPhoneView extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Sales Card (Full Width)
          _SalesCard(),
          const SizedBox(height: 16),

          // Monthly Stats (Full Width)
          _MonthlyStatsCard(),
          const SizedBox(height: 16),

          // Alerts (Full Width)
          _AlertsCard(),
          const SizedBox(height: 16),

          // Top Products (List View)
          _TopProductsList(),
        ],
      ),
    );
  }
}

// Tablet Layout: Two Columns
class _DashboardTabletView extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(24),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Left Column (60%)
          Expanded(
            flex: 6,
            child: Column(
              children: [
                _SalesCard(),
                const SizedBox(height: 16),
                _MonthlyStatsCard(),
                const SizedBox(height: 16),
                _TopProductsGrid(), // Grid instead of list
              ],
            ),
          ),
          const SizedBox(width: 24),

          // Right Column (40%)
          Expanded(
            flex: 4,
            child: Column(
              children: [
                _AlertsCard(),
                const SizedBox(height: 16),
                _QuickActionsCard(),
                const SizedBox(height: 16),
                _PaymentMethodsChart(),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
```

---

### Example 2: Product List (Responsive Grid)

```dart
// lib/screens/products/products_screen.dart
import 'package:flutter/material.dart';
import '../../core/utils/screen_size.dart';

class ProductsScreen extends StatelessWidget {
  final List<Product> products;

  const ProductsScreen({
    Key? key,
    required this.products,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Products'),
      ),
      body: Padding(
        padding: ScreenSize.responsivePadding(context),
        child: GridView.builder(
          gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: ScreenSize.gridColumns(context),
            crossAxisSpacing: ScreenSize.responsive(
              context,
              phone: 12.0,
              tablet: 16.0,
            ),
            mainAxisSpacing: ScreenSize.responsive(
              context,
              phone: 12.0,
              tablet: 16.0,
            ),
            childAspectRatio: ScreenSize.responsive(
              context,
              phone: 0.75,    // Portrait card on phone
              tablet: 0.85,   // Slightly wider on tablet
            ),
          ),
          itemCount: products.length,
          itemBuilder: (context, index) {
            return ProductCard(
              product: products[index],
              // Use larger card on tablet
              showExtendedInfo: ScreenSize.isTabletOrLarger(context),
            );
          },
        ),
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          // Navigate to add product
        },
        child: const Icon(Icons.add),
      ),
    );
  }
}

class ProductCard extends StatelessWidget {
  final Product product;
  final bool showExtendedInfo;

  const ProductCard({
    Key? key,
    required this.product,
    this.showExtendedInfo = false,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Card(
      clipBehavior: Clip.antiAlias,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Product Image
          Expanded(
            flex: 3,
            child: Container(
              width: double.infinity,
              color: product.image != null
                  ? null
                  : Color(int.parse(product.color?.replaceFirst('#', '0xFF') ?? '0xFF9E9E9E')),
              child: product.image != null
                  ? Image.network(
                      product.imageUrl!,
                      fit: BoxFit.cover,
                    )
                  : Center(
                      child: Icon(
                        Icons.image,
                        size: 48,
                        color: Colors.white70,
                      ),
                    ),
            ),
          ),

          // Product Info
          Expanded(
            flex: showExtendedInfo ? 2 : 1,
            child: Padding(
              padding: const EdgeInsets.all(12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  // Product Name
                  Text(
                    product.name,
                    style: Theme.of(context).textTheme.titleMedium?.copyWith(
                      fontWeight: FontWeight.bold,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),

                  // Extended info for tablet
                  if (showExtendedInfo) ...[
                    const SizedBox(height: 4),
                    Text(
                      product.category?.name ?? '',
                      style: Theme.of(context).textTheme.bodySmall,
                    ),
                    const SizedBox(height: 8),
                  ],

                  // Price
                  Text(
                    'Rp ${_formatCurrency(product.price)}',
                    style: Theme.of(context).textTheme.titleMedium?.copyWith(
                      color: Colors.green,
                      fontWeight: FontWeight.bold,
                    ),
                  ),

                  // Stock info (tablet only)
                  if (showExtendedInfo) ...[
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        Icon(
                          Icons.inventory_2_outlined,
                          size: 16,
                          color: product.isLowStock ? Colors.red : Colors.grey,
                        ),
                        const SizedBox(width: 4),
                        Text(
                          'Stock: ${product.totalStock}',
                          style: TextStyle(
                            fontSize: 12,
                            color: product.isLowStock ? Colors.red : Colors.grey[600],
                          ),
                        ),
                      ],
                    ),
                  ],
                ],
              ),
            ),
          ),
        ],
      ),
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

---

### Example 3: POS Screen (Tablet Optimized)

```dart
// lib/screens/pos/pos_screen.dart
import 'package:flutter/material.dart';
import '../../core/utils/screen_size.dart';
import '../../core/widgets/responsive_widget.dart';

class POSScreen extends StatelessWidget {
  const POSScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Point of Sale'),
      ),
      body: ResponsiveWidget(
        phone: _POSPhoneView(),
        tablet: _POSTabletView(),
      ),
    );
  }
}

// Phone Layout: Single View with Bottom Sheet Cart
class _POSPhoneView extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        // Product Grid (Full Screen)
        Expanded(
          child: ProductSelectionGrid(),
        ),

        // Cart Summary Bar (Sticky Bottom)
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            boxShadow: [
              BoxShadow(
                color: Colors.black12,
                blurRadius: 4,
                offset: Offset(0, -2),
              ),
            ],
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text(
                    '3 items',
                    style: TextStyle(fontSize: 12),
                  ),
                  Text(
                    'Rp 150,000',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ],
              ),
              ElevatedButton(
                onPressed: () {
                  // Show cart bottom sheet
                  showModalBottomSheet(
                    context: context,
                    isScrollControlled: true,
                    builder: (context) => CartBottomSheet(),
                  );
                },
                child: const Text('VIEW CART'),
              ),
            ],
          ),
        ),
      ],
    );
  }
}

// Tablet Layout: Split Screen (Products + Cart)
class _POSTabletView extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        // Left: Product Selection (65%)
        Expanded(
          flex: 65,
          child: Container(
            color: Colors.grey[100],
            child: Column(
              children: [
                // Search Bar
                Padding(
                  padding: const EdgeInsets.all(16),
                  child: TextField(
                    decoration: InputDecoration(
                      hintText: 'Search products...',
                      prefixIcon: const Icon(Icons.search),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                      filled: true,
                      fillColor: Colors.white,
                    ),
                  ),
                ),

                // Category Tabs (Horizontal Scroll)
                Container(
                  height: 60,
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: ListView(
                    scrollDirection: Axis.horizontal,
                    children: [
                      _CategoryChip('All', isSelected: true),
                      _CategoryChip('Beverages'),
                      _CategoryChip('Food'),
                      _CategoryChip('Snacks'),
                    ],
                  ),
                ),

                // Product Grid
                Expanded(
                  child: ProductSelectionGrid(),
                ),
              ],
            ),
          ),
        ),

        // Divider
        VerticalDivider(width: 1),

        // Right: Cart & Checkout (35%)
        Expanded(
          flex: 35,
          child: Container(
            color: Colors.white,
            child: CartAndCheckoutPanel(),
          ),
        ),
      ],
    );
  }
}

class ProductSelectionGrid extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return GridView.builder(
      padding: const EdgeInsets.all(16),
      gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: ScreenSize.responsive(
          context,
          phone: 2,
          tablet: 3,
        ),
        crossAxisSpacing: 12,
        mainAxisSpacing: 12,
        childAspectRatio: 0.75,
      ),
      itemCount: 20, // Replace with actual products
      itemBuilder: (context, index) {
        return ProductSelectionCard(
          onTap: () {
            // Add to cart
          },
        );
      },
    );
  }
}

class ProductSelectionCard extends StatelessWidget {
  final VoidCallback onTap;

  const ProductSelectionCard({
    Key? key,
    required this.onTap,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Card(
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: onTap,
        child: Column(
          children: [
            Expanded(
              child: Container(
                color: Colors.blue[100],
                child: const Center(
                  child: Icon(Icons.coffee, size: 48),
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(8),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Espresso',
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: ScreenSize.fontSize(context, base: 14),
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 4),
                  Text(
                    'Rp 25,000',
                    style: TextStyle(
                      color: Colors.green,
                      fontWeight: FontWeight.bold,
                      fontSize: ScreenSize.fontSize(context, base: 13),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class CartAndCheckoutPanel extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        // Header
        Container(
          padding: const EdgeInsets.all(16),
          color: Colors.grey[100],
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Current Order',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
              IconButton(
                icon: const Icon(Icons.delete_outline),
                onPressed: () {
                  // Clear cart
                },
              ),
            ],
          ),
        ),

        // Cart Items List
        Expanded(
          child: ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: 3, // Replace with actual cart items
            itemBuilder: (context, index) {
              return CartItemWidget();
            },
          ),
        ),

        // Checkout Section
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            boxShadow: [
              BoxShadow(
                color: Colors.black12,
                blurRadius: 4,
                offset: Offset(0, -2),
              ),
            ],
          ),
          child: Column(
            children: [
              _TotalRow('Subtotal', 'Rp 80,000'),
              _TotalRow('Tax (10%)', 'Rp 8,000'),
              _TotalRow('Discount', '- Rp 0'),
              Divider(height: 24),
              _TotalRow(
                'TOTAL',
                'Rp 88,000',
                isBold: true,
                fontSize: 20,
              ),
              const SizedBox(height: 16),

              // Payment Method
              DropdownButtonFormField<String>(
                decoration: InputDecoration(
                  labelText: 'Payment Method',
                  border: OutlineInputBorder(),
                ),
                value: 'cash',
                items: [
                  DropdownMenuItem(value: 'cash', child: Text('Cash')),
                  DropdownMenuItem(value: 'card', child: Text('Card')),
                  DropdownMenuItem(value: 'qris', child: Text('QRIS')),
                ],
                onChanged: (value) {},
              ),
              const SizedBox(height: 16),

              // Process Button
              SizedBox(
                width: double.infinity,
                height: 56,
                child: ElevatedButton(
                  onPressed: () {
                    // Process order
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.green,
                  ),
                  child: const Text(
                    'PROCESS ORDER',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _TotalRow(String label, String amount, {
    bool isBold = false,
    double fontSize = 14,
  }) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: TextStyle(
              fontSize: fontSize,
              fontWeight: isBold ? FontWeight.bold : FontWeight.normal,
            ),
          ),
          Text(
            amount,
            style: TextStyle(
              fontSize: fontSize,
              fontWeight: isBold ? FontWeight.bold : FontWeight.normal,
              color: isBold ? Colors.green : null,
            ),
          ),
        ],
      ),
    );
  }
}

class CartItemWidget extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.only(bottom: 8),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Row(
          children: [
            // Product Image
            Container(
              width: 60,
              height: 60,
              decoration: BoxDecoration(
                color: Colors.blue[100],
                borderRadius: BorderRadius.circular(8),
              ),
              child: const Icon(Icons.coffee),
            ),
            const SizedBox(width: 12),

            // Product Info
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Espresso',
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 16,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    'Rp 25,000',
                    style: TextStyle(
                      color: Colors.grey[600],
                      fontSize: 14,
                    ),
                  ),
                ],
              ),
            ),

            // Quantity Controls
            Row(
              children: [
                IconButton(
                  icon: const Icon(Icons.remove_circle_outline),
                  onPressed: () {
                    // Decrease quantity
                  },
                ),
                Text(
                  '2',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                IconButton(
                  icon: const Icon(Icons.add_circle_outline),
                  onPressed: () {
                    // Increase quantity
                  },
                ),
              ],
            ),

            // Total
            Text(
              'Rp 50,000',
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
                color: Colors.green,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _CategoryChip extends StatelessWidget {
  final String label;
  final bool isSelected;

  const _CategoryChip(this.label, {this.isSelected = false});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(right: 8),
      child: FilterChip(
        label: Text(label),
        selected: isSelected,
        onSelected: (selected) {},
      ),
    );
  }
}

// Phone: Cart Bottom Sheet
class CartBottomSheet extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Container(
      height: MediaQuery.of(context).size.height * 0.8,
      child: CartAndCheckoutPanel(),
    );
  }
}
```

---

## 📐 Layout Patterns

### Pattern 1: Stack to Row
```dart
// Phone: Vertical Stack
Column(
  children: [
    Widget1(),
    Widget2(),
  ],
)

// Tablet: Horizontal Row
Row(
  children: [
    Expanded(child: Widget1()),
    Expanded(child: Widget2()),
  ],
)
```

### Pattern 2: Full Width to Grid
```dart
// Phone: Single Column
ListView(
  children: items.map((item) => ItemCard(item)).toList(),
)

// Tablet: Grid
GridView.builder(
  gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
    crossAxisCount: 3,
  ),
  itemBuilder: (context, index) => ItemCard(items[index]),
)
```

### Pattern 3: Bottom Sheet to Side Panel
```dart
// Phone: Show Bottom Sheet
showModalBottomSheet(
  context: context,
  builder: (context) => DetailPanel(),
)

// Tablet: Always Visible Side Panel
Row(
  children: [
    Expanded(child: MainContent()),
    Container(
      width: 400,
      child: DetailPanel(),
    ),
  ],
)
```

---

## 🎨 Responsive Components Library

### Responsive Card
```dart
// lib/core/widgets/responsive_card.dart
class ResponsiveCard extends StatelessWidget {
  final Widget child;
  final EdgeInsets? padding;

  const ResponsiveCard({
    Key? key,
    required this.child,
    this.padding,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: EdgeInsets.all(
        ScreenSize.responsive(
          context,
          phone: 8.0,
          tablet: 12.0,
        ),
      ),
      child: Padding(
        padding: padding ?? ScreenSize.responsivePadding(context),
        child: child,
      ),
    );
  }
}
```

### Responsive Dialog
```dart
// lib/core/widgets/responsive_dialog.dart
class ResponsiveDialog extends StatelessWidget {
  final String title;
  final Widget content;
  final List<Widget>? actions;

  const ResponsiveDialog({
    Key? key,
    required this.title,
    required this.content,
    this.actions,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final isPhone = ScreenSize.isPhone(context);

    if (isPhone) {
      // Full screen dialog on phone
      return Scaffold(
        appBar: AppBar(
          title: Text(title),
          leading: IconButton(
            icon: const Icon(Icons.close),
            onPressed: () => Navigator.pop(context),
          ),
        ),
        body: SingleChildScrollView(
          padding: const EdgeInsets.all(16),
          child: content,
        ),
        bottomNavigationBar: actions != null
            ? SafeArea(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Row(
                    children: actions!,
                  ),
                ),
              )
            : null,
      );
    } else {
      // Standard dialog on tablet
      return AlertDialog(
        title: Text(title),
        content: Container(
          width: 600,
          child: content,
        ),
        actions: actions,
      );
    }
  }

  static Future<T?> show<T>(
    BuildContext context, {
    required String title,
    required Widget content,
    List<Widget>? actions,
  }) {
    if (ScreenSize.isPhone(context)) {
      return Navigator.push<T>(
        context,
        MaterialPageRoute(
          fullscreenDialog: true,
          builder: (context) => ResponsiveDialog(
            title: title,
            content: content,
            actions: actions,
          ),
        ),
      );
    } else {
      return showDialog<T>(
        context: context,
        builder: (context) => ResponsiveDialog(
          title: title,
          content: content,
          actions: actions,
        ),
      );
    }
  }
}
```

---

## 📱 Best Practices

### 1. Use LayoutBuilder for Complex Layouts
```dart
LayoutBuilder(
  builder: (context, constraints) {
    if (constraints.maxWidth > 600) {
      return TabletLayout();
    } else {
      return PhoneLayout();
    }
  },
)
```

### 2. Orientation Support
```dart
OrientationBuilder(
  builder: (context, orientation) {
    return GridView.count(
      crossAxisCount: orientation == Orientation.portrait ? 2 : 3,
      children: products.map((p) => ProductCard(p)).toList(),
    );
  },
)
```

### 3. Safe Area for Notches
```dart
SafeArea(
  child: YourWidget(),
)
```

### 4. Media Query Shortcuts
```dart
final screenWidth = MediaQuery.of(context).size.width;
final screenHeight = MediaQuery.of(context).size.height;
final isLandscape = MediaQuery.of(context).orientation == Orientation.landscape;
final devicePixelRatio = MediaQuery.of(context).devicePixelRatio;
```

---

## 🎯 Screen-Specific Recommendations

### Dashboard
- **Phone:** Single column, scrollable
- **Tablet:** Two columns, more data visible

### Products
- **Phone:** 2 column grid
- **Tablet:** 3-4 column grid with filters sidebar

### POS (MOST IMPORTANT!)
- **Phone:** Product grid + bottom sheet cart
- **Tablet:** Split screen (products left, cart right) ← OPTIMAL

### Orders
- **Phone:** List view
- **Tablet:** Master-detail (list + detail panel)

### Reports
- **Phone:** Vertical charts, scrollable
- **Tablet:** Dashboard layout with multiple charts

---

## 🚀 Implementation Checklist

### Setup
- [ ] Create `screen_size.dart` utility
- [ ] Create `ResponsiveWidget`
- [ ] Create `ResponsiveLayout`
- [ ] Test on different screen sizes

### Screens
- [ ] Dashboard (phone + tablet)
- [ ] Products (responsive grid)
- [ ] POS (split screen for tablet)
- [ ] Orders (master-detail)
- [ ] Settings (responsive form)

### Components
- [ ] Responsive cards
- [ ] Responsive dialogs
- [ ] Responsive navigation
- [ ] Responsive forms

### Testing
- [ ] Test on small phone (iPhone SE)
- [ ] Test on large phone (iPhone Pro Max)
- [ ] Test on tablet (iPad)
- [ ] Test landscape orientation
- [ ] Test on Android tablet

---

## 💡 Pro Tips

1. **Start with phone, enhance for tablet**
   - Design phone UI first
   - Add tablet enhancements later

2. **Use const constructors**
   - Better performance
   - `const SizedBox(height: 16)`

3. **Avoid hardcoded sizes**
   - Use `ScreenSize.responsive()`
   - Use MediaQuery

4. **Test early, test often**
   - Test on real devices
   - Use device simulators

5. **Consider landscape mode**
   - Especially for tablet POS

6. **Lazy load on tablet**
   - Show more data without overwhelming

---

## 📊 Performance Considerations

### Images
```dart
CachedNetworkImage(
  imageUrl: product.imageUrl,
  placeholder: (context, url) => CircularProgressIndicator(),
  errorWidget: (context, url, error) => Icon(Icons.error),
  fit: BoxFit.cover,
)
```

### List Performance
```dart
// Use ListView.builder for long lists
ListView.builder(
  itemCount: items.length,
  itemBuilder: (context, index) => ItemWidget(items[index]),
)

// Not: ListView(children: items.map(...).toList())
```

### Lazy Loading
```dart
// Load more when near bottom
ScrollController _scrollController = ScrollController();

@override
void initState() {
  super.initState();
  _scrollController.addListener(() {
    if (_scrollController.position.pixels ==
        _scrollController.position.maxScrollExtent) {
      _loadMore();
    }
  });
}
```

---

_Last Updated: 2025-12-01_
_For: Academy POS Flutter App_
