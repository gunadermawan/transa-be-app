<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('business_id')->constrained('suppliers')->onDelete('set null');
            $table->foreignId('tax_id')->nullable()->after('supplier_id')->constrained('business_settings')->onDelete('set null');
            $table->string('unit_type')->default('pcs')->after('sku');
            $table->integer('reorder_point')->default(0)->after('stock_minimum');
            $table->integer('optimal_stock_level')->nullable()->after('reorder_point');
            $table->string('product_type')->default('physical')->after('optimal_stock_level');
            $table->boolean('is_featured')->default(false)->after('product_type');

            $table->index('supplier_id');
            $table->index('tax_id');
            $table->index(['is_featured', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_featured', 'status']);
            $table->dropIndex(['tax_id']);
            $table->dropIndex(['supplier_id']);
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['tax_id']);
            $table->dropColumn([
                'supplier_id',
                'tax_id',
                'unit_type',
                'reorder_point',
                'optimal_stock_level',
                'product_type',
                'is_featured',
            ]);
        });
    }
};
