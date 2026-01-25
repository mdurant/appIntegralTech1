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
        Schema::table('service_form_fields', function (Blueprint $table) {
            $table->foreign('service_category_id')
                ->references('id')
                ->on('service_categories')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_form_fields', function (Blueprint $table) {
            $table->dropForeign(['service_category_id']);
        });
    }
};
