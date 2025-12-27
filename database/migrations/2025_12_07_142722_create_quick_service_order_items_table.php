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
        if (Schema::hasTable('quick_service_order_items')) {
            return;
        }

        Schema::create('quick_service_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quick_service_order_id')->constrained('quick_service_orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->string('product_name');
            $table->string('product_image')->nullable();
            $table->integer('quantity');
            $table->decimal('price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['quick_service_order_id', 'product_id'], 'qs_order_items_order_product_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quick_service_order_items');
    }
};
