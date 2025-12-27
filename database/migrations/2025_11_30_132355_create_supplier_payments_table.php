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
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('paid_by')->constrained('users')->onDelete('cascade');
            $table->string('payment_number')->unique();
            $table->date('payment_date');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method'); // cash, transfer, cheque
            $table->string('reference')->nullable(); // nomor transfer/giro
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['purchase_order_id', 'payment_date']);
            $table->index(['supplier_id', 'payment_date']);
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
