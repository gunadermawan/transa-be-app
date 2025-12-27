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
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('from_outlet_id')->constrained('outlets')->onDelete('cascade');
            $table->foreignId('to_outlet_id')->constrained('outlets')->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('sent_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('transfer_number')->unique();
            $table->date('transfer_date');
            $table->date('sent_date')->nullable();
            $table->date('received_date')->nullable();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['business_id', 'status']);
            $table->index(['from_outlet_id', 'to_outlet_id']);
            $table->index('transfer_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
