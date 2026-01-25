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
        Schema::create('service_bids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_request_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount', 12, 2);
            $table->string('currency')->default('CLP');
            $table->text('message')->nullable();
            $table->string('status')->default('submitted');
            $table->timestamps();

            $table->unique(['service_request_id', 'user_id']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_bids');
    }
};
