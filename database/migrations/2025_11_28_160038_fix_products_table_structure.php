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
        Schema::table('products', function (Blueprint $table) {
            // Drop redundant stock column (use stocks table instead)
            $table->dropColumn('stock');

            // Change decimal precision for price and cost
            $table->decimal('price', 12, 2)->change();
            $table->decimal('cost', 12, 2)->change();

            // Add soft deletes
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Restore stock column
            $table->integer('stock')->after('cost');

            // Restore original decimal precision
            $table->decimal('price', 8, 2)->change();
            $table->decimal('cost', 8, 2)->change();

            // Remove soft deletes
            $table->dropSoftDeletes();
        });
    }
};
