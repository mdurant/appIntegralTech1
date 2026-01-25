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
        if (! Schema::hasTable('service_categories')) {
            Schema::create('service_categories', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->string('name');
                $table->timestamps();
            });
        }

        Schema::table('service_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('service_categories', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            }

            if (! Schema::hasColumn('service_categories', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('name');
            }

            $table->index(['parent_id', 'sort_order']);
        });

        Schema::table('service_categories', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('service_categories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id', 'sort_order']);
            $table->dropColumn('parent_id');
            $table->dropColumn('sort_order');
        });
    }
};
