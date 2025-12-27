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
        Schema::table('sales_summaries', function (Blueprint $table) {
            $table->decimal('total_sales', 12, 2)->change();
            $table->decimal('total_tax', 12, 2)->change();
            $table->decimal('total_discount', 12, 2)->change();
            $table->decimal('total_profit', 12, 2)->change();
        });

        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->decimal('total_sales', 12, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_summaries', function (Blueprint $table) {
            $table->integer('total_sales')->change();
            $table->integer('total_tax')->change();
            $table->integer('total_discount')->change();
            $table->integer('total_profit')->change();
        });

        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->integer('total_sales')->change();
        });
    }
};
