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
        if (! Schema::hasTable('service_requests')) {
            return;
        }

        Schema::table('service_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('service_requests', 'contact_name')) {
                $table->string('contact_name')->nullable()->after('created_by_user_id');
            }
            if (! Schema::hasColumn('service_requests', 'contact_email')) {
                $table->string('contact_email')->nullable()->after('contact_name');
            }
            if (! Schema::hasColumn('service_requests', 'contact_phone')) {
                $table->string('contact_phone')->nullable()->after('contact_email');
            }
            if (! Schema::hasColumn('service_requests', 'location_text')) {
                $table->string('location_text')->nullable()->after('description');
            }
            if (! Schema::hasColumn('service_requests', 'address')) {
                $table->string('address')->nullable()->after('location_text');
            }
            if (! Schema::hasColumn('service_requests', 'notes')) {
                $table->text('notes')->nullable()->after('address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('service_requests')) {
            return;
        }

        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'contact_name')) {
                $table->dropColumn('contact_name');
            }
            if (Schema::hasColumn('service_requests', 'contact_email')) {
                $table->dropColumn('contact_email');
            }
            if (Schema::hasColumn('service_requests', 'contact_phone')) {
                $table->dropColumn('contact_phone');
            }
            if (Schema::hasColumn('service_requests', 'location_text')) {
                $table->dropColumn('location_text');
            }
            if (Schema::hasColumn('service_requests', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('service_requests', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
