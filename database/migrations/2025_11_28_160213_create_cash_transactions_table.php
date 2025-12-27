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
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_drawer_session_id')->constrained('cash_drawer_sessions')->onDelete('cascade');
            $table->string('type');
            $table->decimal('amount', 12, 2);
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['cash_drawer_session_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};
