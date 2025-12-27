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
        // Products indexes
        Schema::table('products', function (Blueprint $table) {
            $table->index('barcode');
            $table->index('sku');
            $table->index(['business_id', 'status']);
        });

        // Stocks indexes
        Schema::table('stocks', function (Blueprint $table) {
            $table->index(['product_id', 'outlet_id']);
        });

        // Sales transactions indexes
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->index(['date', 'product_id']);
            $table->index(['business_id', 'date']);
        });

        // Sales summaries indexes
        Schema::table('sales_summaries', function (Blueprint $table) {
            $table->index(['business_id', 'date']);
        });

        // Categories indexes
        Schema::table('categories', function (Blueprint $table) {
            $table->index('business_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['barcode']);
            $table->dropIndex(['sku']);
            $table->dropIndex(['business_id', 'status']);
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'outlet_id']);
        });

        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->dropIndex(['date', 'product_id']);
            $table->dropIndex(['business_id', 'date']);
        });

        Schema::table('sales_summaries', function (Blueprint $table) {
            $table->dropIndex(['business_id', 'date']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['business_id']);
        });
    }
};
