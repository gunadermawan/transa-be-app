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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('variant_name');
            $table->string('sku')->unique();
            $table->string('barcode')->nullable();
            $table->decimal('price_adjustment', 12, 2)->default(0);
            $table->decimal('cost_adjustment', 12, 2)->default(0);
            $table->json('attributes')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('status')->default('active');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['product_id', 'status']);
            $table->index('sku');
            $table->index('barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
