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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('discount_type');
            $table->decimal('discount_value', 12, 2);
            $table->decimal('min_purchase_amount', 12, 2)->nullable();
            $table->string('applicable_to')->default('all');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('max_uses')->nullable();
            $table->integer('used_count')->default(0);
            $table->string('status')->default('active');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['business_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
