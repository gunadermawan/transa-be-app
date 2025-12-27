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
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('address')->nullable()->after('name');
            $table->string('phone')->nullable()->after('address');
            $table->string('email')->nullable()->after('phone');
            $table->string('tax_id')->nullable()->after('email');
            $table->string('logo')->nullable()->after('tax_id');
            $table->foreignId('current_subscription_id')->nullable()->after('owner_id')->constrained('business_subscriptions')->onDelete('set null');
            $table->string('subscription_status')->default('trial')->after('current_subscription_id');

            $table->index('subscription_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropIndex(['subscription_status']);
            $table->dropForeign(['current_subscription_id']);
            $table->dropColumn([
                'address',
                'phone',
                'email',
                'tax_id',
                'logo',
                'current_subscription_id',
                'subscription_status',
            ]);
        });
    }
};
