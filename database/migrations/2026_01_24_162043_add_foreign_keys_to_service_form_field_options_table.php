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
        Schema::table('service_form_field_options', function (Blueprint $table) {
            $table->foreign('service_form_field_id')
                ->references('id')
                ->on('service_form_fields')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_form_field_options', function (Blueprint $table) {
            $table->dropForeign(['service_form_field_id']);
        });
    }
};
