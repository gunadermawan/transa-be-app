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
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_invoice_id')->constrained('subscription_invoices')->onDelete('cascade');
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->string('payment_method');
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->string('transaction_id')->nullable();
            $table->string('gateway')->nullable();
            $table->string('status')->default('pending');
            $table->json('gateway_response')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['subscription_invoice_id', 'status']);
            $table->index(['business_id', 'payment_date']);
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
