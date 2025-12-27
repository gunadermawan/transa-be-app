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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('discount_type');
            $table->decimal('discount_value', 12, 2);
            $table->decimal('min_purchase_amount', 12, 2)->nullable();
            $table->decimal('max_discount_amount', 12, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_limit_per_customer')->nullable();
            $table->integer('used_count')->default(0);
            $table->date('valid_from');
            $table->date('valid_until');
            $table->string('status')->default('active');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['business_id', 'status']);
            $table->index('code');
            $table->index(['valid_from', 'valid_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
