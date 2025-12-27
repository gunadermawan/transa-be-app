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
        Schema::table('stock_histories', function (Blueprint $table) {
            // Drop old user string column
            $table->dropColumn('user');

            // Add proper foreign keys
            $table->foreignId('user_id')->after('stock_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('outlet_id')->after('user_id')->constrained('outlets')->onDelete('cascade');

            // Add index for better query performance
            $table->index(['stock_id', 'created_at']);
            $table->index('outlet_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_histories', function (Blueprint $table) {
            // Remove indexes
            $table->dropIndex(['stock_id', 'created_at']);
            $table->dropIndex(['outlet_id']);

            // Remove foreign keys
            $table->dropForeign(['user_id']);
            $table->dropForeign(['outlet_id']);
            $table->dropColumn(['user_id', 'outlet_id']);

            // Restore user string column
            $table->string('user')->after('reference');
        });
    }
};
