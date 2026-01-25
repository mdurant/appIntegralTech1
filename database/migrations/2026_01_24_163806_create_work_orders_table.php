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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_request_id');
            $table->unsignedBigInteger('service_bid_id');
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('awarded_to_user_id');
            $table->string('status')->default('open');
            $table->timestamps();

            $table->unique('service_request_id');
            $table->index('tenant_id');
            $table->index('awarded_to_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
