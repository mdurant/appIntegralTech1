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
            $table->decimal('budget_estimated', 12, 2)->nullable()->after('status')->comment('Presupuesto estimado del cliente');
            $table->decimal('final_price', 12, 2)->nullable()->after('budget_estimated')->comment('Precio final del profesional');
            $table->timestamp('started_at')->nullable()->after('final_price');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            $table->timestamp('paid_at')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn(['budget_estimated', 'final_price', 'started_at', 'completed_at', 'paid_at']);
        });
    }
};
