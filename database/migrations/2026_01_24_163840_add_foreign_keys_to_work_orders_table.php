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
        Schema::table('work_orders', function (Blueprint $table) {
            $table->foreign('service_request_id')->references('id')->on('service_requests')->cascadeOnDelete();
            $table->foreign('service_bid_id')->references('id')->on('service_bids')->cascadeOnDelete();
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('awarded_to_user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeign(['service_request_id']);
            $table->dropForeign(['service_bid_id']);
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['awarded_to_user_id']);
        });
    }
};
