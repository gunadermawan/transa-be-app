<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Dummy Data Seeder...');

        // Get the latest business (newest created)
        $business = \App\Models\Business::latest()->first();

        if (! $business) {
            $this->command->error('❌ No business found! Please create a business first by registering a user.');

            return;
        }

        $this->command->info("✓ Using business: {$business->name} (ID: {$business->id})");

        // Get or create the first outlet for this business
        $outlet = \App\Models\Outlet::where('business_id', $business->id)->first();

        if (! $outlet) {
            $this->command->error('❌ No outlet found! Please create an outlet first.');

            return;
        }

        $this->command->info("✓ Using outlet: {$outlet->name} (ID: {$outlet->id})");

        // Clear existing data for this business (optional - comment out if you want to keep existing data)
        $this->command->info('🗑️  Clearing existing data...');
        \App\Models\Stock::whereHas('product', function ($query) use ($business) {
            $query->where('business_id', $business->id);
        })->delete();
        \App\Models\Product::where('business_id', $business->id)->forceDelete();
        \App\Models\Category::where('business_id', $business->id)->delete();

        // Create Categories
        $this->command->info("\n[1/2] Creating Categories...");
        $categories = $this->createCategories($business);
        $this->command->info('✓ Categories created: '.count($categories));

        // Create Products & Stocks
        $this->command->info("\n[2/2] Creating Products & Stocks...");
        $totalProducts = $this->createProducts($business, $outlet, $categories);
        $this->command->info("✓ Products created: {$totalProducts}");

        $this->command->info("\n╔════════════════════════════════════════════╗");
        $this->command->info('║            ✓ SELESAI!                      ║');
        $this->command->info('╚════════════════════════════════════════════╝');
        $this->command->info("\nSummary:");
        $this->command->info('  • Categories created: '.count($categories));
        $this->command->info("  • Products created: {$totalProducts}");
        $this->command->info("\nHappy testing with Jago Academy POS! 🚀");
    }

    private function createCategories(\App\Models\Business $business): array
    {
        $categoryNames = [
            'Makanan Utama',
            'Minuman Panas',
            'Minuman Dingin',
            'Snack & Cemilan',
            'Dessert',
            'Paket Combo',
        ];

        $categories = [];

        foreach ($categoryNames as $name) {
            $category = \App\Models\Category::create([
                'name' => $name,
                'business_id' => $business->id,
            ]);

            $categories[$name] = $category;
            $this->command->info("  Creating: {$name}");
            $this->command->info("  ✓ Created with ID: {$category->id}");
        }

        return $categories;
    }

    private function createProducts(\App\Models\Business $business, \App\Models\Outlet $outlet, array $categories): int
    {
        $productsData = [
            'Makanan Utama' => [
                ['name' => 'Nasi Goreng Spesial', 'price' => 25000, 'cost' => 15000, 'stock' => 50, 'description' => 'Nasi goreng dengan ayam, telur, dan sayuran'],
                ['name' => 'Mie Goreng Jawa', 'price' => 22000, 'cost' => 13000, 'stock' => 45, 'description' => 'Mie goreng ala Jawa dengan bumbu khas'],
                ['name' => 'Nasi Ayam Geprek', 'price' => 28000, 'cost' => 17000, 'stock' => 40, 'description' => 'Nasi dengan ayam geprek pedas level 5'],
                ['name' => 'Soto Ayam Komplit', 'price' => 23000, 'cost' => 14000, 'stock' => 35, 'description' => 'Soto ayam dengan nasi, telur, dan kerupuk'],
                ['name' => 'Nasi Rendang', 'price' => 27000, 'cost' => 16000, 'stock' => 30, 'description' => 'Nasi dengan rendang daging sapi'],
                ['name' => 'Mie Ayam Bakso', 'price' => 20000, 'cost' => 12000, 'stock' => 55, 'description' => 'Mie ayam dengan bakso dan pangsit'],
                ['name' => 'Nasi Capcay', 'price' => 24000, 'cost' => 14000, 'stock' => 40, 'description' => 'Nasi dengan capcay sayuran segar'],
                ['name' => 'Kwetiau Goreng', 'price' => 26000, 'cost' => 15000, 'stock' => 35, 'description' => 'Kwetiau goreng seafood spesial'],
            ],
            'Minuman Panas' => [
                ['name' => 'Kopi Hitam', 'price' => 8000, 'cost' => 3000, 'stock' => 100, 'description' => 'Kopi hitam robusta pilihan'],
                ['name' => 'Kopi Susu', 'price' => 12000, 'cost' => 5000, 'stock' => 100, 'description' => 'Kopi susu gula aren'],
                ['name' => 'Cappuccino', 'price' => 15000, 'cost' => 7000, 'stock' => 80, 'description' => 'Cappuccino dengan foam halus'],
                ['name' => 'Teh Tarik', 'price' => 10000, 'cost' => 4000, 'stock' => 90, 'description' => 'Teh tarik manis'],
                ['name' => 'Teh Jahe', 'price' => 9000, 'cost' => 3500, 'stock' => 85, 'description' => 'Teh jahe hangat'],
                ['name' => 'Coklat Panas', 'price' => 13000, 'cost' => 6000, 'stock' => 75, 'description' => 'Coklat panas premium'],
                ['name' => 'Green Tea Latte', 'price' => 14000, 'cost' => 6500, 'stock' => 70, 'description' => 'Green tea latte creamy'],
            ],
            'Minuman Dingin' => [
                ['name' => 'Es Teh Manis', 'price' => 5000, 'cost' => 2000, 'stock' => 150, 'description' => 'Es teh manis segar'],
                ['name' => 'Es Jeruk', 'price' => 8000, 'cost' => 3500, 'stock' => 120, 'description' => 'Es jeruk peras segar'],
                ['name' => 'Es Kelapa Muda', 'price' => 12000, 'cost' => 6000, 'stock' => 60, 'description' => 'Es kelapa muda asli'],
                ['name' => 'Es Cappuccino', 'price' => 16000, 'cost' => 7500, 'stock' => 90, 'description' => 'Cappuccino dingin'],
                ['name' => 'Jus Alpukat', 'price' => 15000, 'cost' => 7000, 'stock' => 70, 'description' => 'Jus alpukat kental'],
                ['name' => 'Jus Mangga', 'price' => 14000, 'cost' => 6500, 'stock' => 75, 'description' => 'Jus mangga manis'],
                ['name' => 'Milkshake Strawberry', 'price' => 18000, 'cost' => 8500, 'stock' => 65, 'description' => 'Milkshake strawberry creamy'],
                ['name' => 'Milkshake Chocolate', 'price' => 18000, 'cost' => 8500, 'stock' => 65, 'description' => 'Milkshake coklat premium'],
                ['name' => 'Thai Tea', 'price' => 13000, 'cost' => 6000, 'stock' => 85, 'description' => 'Thai tea original'],
                ['name' => 'Lemon Tea', 'price' => 10000, 'cost' => 4500, 'stock' => 95, 'description' => 'Lemon tea segar'],
            ],
            'Snack & Cemilan' => [
                ['name' => 'Kentang Goreng', 'price' => 12000, 'cost' => 5000, 'stock' => 80, 'description' => 'Kentang goreng crispy dengan saus'],
                ['name' => 'Pisang Goreng', 'price' => 8000, 'cost' => 3500, 'stock' => 100, 'description' => 'Pisang goreng crispy'],
                ['name' => 'Lumpia Goreng', 'price' => 10000, 'cost' => 4500, 'stock' => 90, 'description' => 'Lumpia goreng isi sayuran'],
                ['name' => 'Risoles Mayo', 'price' => 9000, 'cost' => 4000, 'stock' => 85, 'description' => 'Risoles dengan mayones'],
                ['name' => 'Tahu Crispy', 'price' => 7000, 'cost' => 3000, 'stock' => 95, 'description' => 'Tahu goreng crispy'],
                ['name' => 'Onion Rings', 'price' => 11000, 'cost' => 5000, 'stock' => 75, 'description' => 'Onion rings crispy'],
                ['name' => 'Chicken Nugget', 'price' => 12000, 'cost' => 5500, 'stock' => 80, 'description' => 'Chicken nugget 6 pcs'],
            ],
            'Dessert' => [
                ['name' => 'Es Krim Vanilla', 'price' => 10000, 'cost' => 4500, 'stock' => 60, 'description' => 'Es krim vanilla 2 scoop'],
                ['name' => 'Es Krim Coklat', 'price' => 10000, 'cost' => 4500, 'stock' => 60, 'description' => 'Es krim coklat 2 scoop'],
                ['name' => 'Pancake Nutella', 'price' => 15000, 'cost' => 7000, 'stock' => 50, 'description' => 'Pancake dengan topping nutella'],
                ['name' => 'Puding Coklat', 'price' => 8000, 'cost' => 3500, 'stock' => 70, 'description' => 'Puding coklat lembut'],
                ['name' => 'Banana Split', 'price' => 18000, 'cost' => 8500, 'stock' => 45, 'description' => 'Banana split with ice cream'],
                ['name' => 'Brownies Ice Cream', 'price' => 16000, 'cost' => 7500, 'stock' => 55, 'description' => 'Brownies with vanilla ice cream'],
            ],
            'Paket Combo' => [
                ['name' => 'Paket Hemat A', 'price' => 30000, 'cost' => 18000, 'stock' => 40, 'description' => 'Nasi Goreng + Es Teh + Kerupuk'],
                ['name' => 'Paket Hemat B', 'price' => 32000, 'cost' => 19000, 'stock' => 35, 'description' => 'Mie Goreng + Jus Jeruk + Tahu Crispy'],
                ['name' => 'Paket Snack', 'price' => 25000, 'cost' => 15000, 'stock' => 50, 'description' => 'Kentang Goreng + Onion Rings + Es Teh'],
                ['name' => 'Paket Minum Berdua', 'price' => 35000, 'cost' => 20000, 'stock' => 45, 'description' => '2 Kopi Susu + 2 Pisang Goreng'],
                ['name' => 'Paket Keluarga', 'price' => 85000, 'cost' => 50000, 'stock' => 20, 'description' => '3 Nasi Ayam Geprek + 3 Es Jeruk + Kerupuk'],
            ],
        ];

        $totalProducts = 0;
        $colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E2'];

        foreach ($productsData as $categoryName => $products) {
            $category = $categories[$categoryName];
            $this->command->info("\n  Category: {$categoryName}");

            foreach ($products as $index => $productData) {
                // Generate unique SKU and barcode
                $sku = strtoupper(substr($categoryName, 0, 3)).'-'.str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                $barcode = '890'.str_pad($category->id, 3, '0', STR_PAD_LEFT).str_pad($index + 1, 6, '0', STR_PAD_LEFT);

                // Create product
                $product = \App\Models\Product::create([
                    'name' => $productData['name'],
                    'category_id' => $category->id,
                    'business_id' => $business->id,
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'cost' => $productData['cost'],
                    'sku' => $sku,
                    'barcode' => $barcode,
                    'color' => $colors[$index % count($colors)],
                    'status' => 'active',
                    'is_stock_managed' => true,
                    'stock_minimum' => 10,
                ]);

                // Create stock for this product at the outlet
                \App\Models\Stock::create([
                    'product_id' => $product->id,
                    'outlet_id' => $outlet->id,
                    'quantity' => $productData['stock'],
                ]);

                $this->command->info("  ✓ {$productData['name']} (Rp ".number_format($productData['price'], 0, ',', '.')." - Stock: {$productData['stock']})");
                $totalProducts++;
            }
        }

        return $totalProducts;
    }
}
