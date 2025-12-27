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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('outlet_id')->constrained('customers')->onDelete('set null');
            $table->string('payment_status')->default('pending')->after('payment_method');
            $table->decimal('cash_received', 12, 2)->nullable()->after('payment_status');
            $table->decimal('change', 12, 2)->nullable()->after('cash_received');
            $table->text('notes')->nullable()->after('change');

            $table->index(['outlet_id', 'created_at']);
            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['outlet_id', 'created_at']);
            $table->dropIndex(['customer_id']);
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['customer_id', 'payment_status', 'cash_received', 'change', 'notes']);
        });
    }
};
