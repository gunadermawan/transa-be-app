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
        Schema::create('order_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('outlet_id')->constrained('outlets')->onDelete('cascade');
            $table->foreignId('cashier_id')->constrained('users')->onDelete('cascade');
            $table->string('return_number')->unique();
            $table->date('return_date');
            $table->decimal('total_refund', 12, 2);
            $table->string('refund_method')->nullable();
            $table->string('reason');
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['business_id', 'return_date']);
            $table->index('outlet_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_returns');
    }
};
