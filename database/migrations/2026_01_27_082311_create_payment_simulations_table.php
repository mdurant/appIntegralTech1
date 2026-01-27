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
        Schema::create('payment_simulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_request_id')->constrained('service_requests')->cascadeOnDelete();
            $table->decimal('amount', 12, 2)->comment('Monto pagado');
            $table->string('card_last_four', 4)->comment('Últimos 4 dígitos de la tarjeta');
            $table->string('cardholder_name')->comment('Nombre del titular');
            $table->string('status')->default('pending')->comment('pending, approved, rejected');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'service_request_id']);
            $table->index('status');
            $table->unique(['user_id', 'service_request_id']); // Un usuario solo puede pagar una vez por solicitud
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_simulations');
    }
};
