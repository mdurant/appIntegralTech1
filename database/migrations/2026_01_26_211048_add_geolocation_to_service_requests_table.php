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
        Schema::table('service_requests', function (Blueprint $table) {
            $table->foreignId('region_id')->nullable()->after('location_text')->constrained('regions')->nullOnDelete();
            $table->foreignId('commune_id')->nullable()->after('region_id')->constrained('communes')->nullOnDelete();
            $table->index(['region_id', 'commune_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['commune_id']);
            $table->dropForeign(['region_id']);
            $table->dropIndex(['region_id', 'commune_id']);
            $table->dropColumn(['region_id', 'commune_id']);
        });
    }
};
