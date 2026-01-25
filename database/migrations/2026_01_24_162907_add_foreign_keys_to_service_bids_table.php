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
        Schema::table('service_bids', function (Blueprint $table) {
            $table->foreign('service_request_id')->references('id')->on('service_requests')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_bids', function (Blueprint $table) {
            $table->dropForeign(['service_request_id']);
            $table->dropForeign(['user_id']);
        });
    }
};
