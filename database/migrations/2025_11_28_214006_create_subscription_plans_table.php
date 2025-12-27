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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('billing_cycle')->default('monthly');
            $table->integer('trial_days')->default(0);
            $table->integer('max_outlets')->nullable();
            $table->integer('max_users')->nullable();
            $table->integer('max_products')->nullable();
            $table->integer('max_transactions_per_month')->nullable();
            $table->json('features')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->integer('sort_order')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index(['status', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
