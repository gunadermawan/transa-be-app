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
        Schema::create('quick_service_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('outlet_id')->constrained('outlets');
            $table->foreignId('cashier_id')->constrained('users');
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->enum('type', ['DINE IN', 'TAKE AWAY']);
            $table->enum('status', ['draft', 'completed', 'cancelled'])->default('draft');
            $table->integer('pax')->nullable();
            $table->string('table_number')->nullable();
            $table->string('customer_name')->nullable();
            $table->text('notes')->nullable();

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('service_charge', 12, 2)->default(0);
            $table->decimal('service_charge_percentage', 5, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->string('invoice_number')->nullable()->unique();
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('change', 12, 2)->default(0);

            $table->timestamp('saved_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['outlet_id', 'status', 'type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quick_service_orders');
    }
};
