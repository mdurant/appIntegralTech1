<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if ($this->foreignKeyExists('service_form_fields', 'service_form_fields_service_category_id_foreign')) {
            return;
        }

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

    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            $result = DB::selectOne(
                "SELECT 1 FROM information_schema.table_constraints WHERE table_schema = current_schema() AND table_name = ? AND constraint_name = ? AND constraint_type = 'FOREIGN KEY'",
                [$table, $constraintName]
            );
            return $result !== null;
        }

        if ($driver === 'mysql') {
            $database = Schema::getConnection()->getDatabaseName();
            $result = DB::selectOne(
                "SELECT 1 FROM information_schema.table_constraints WHERE table_schema = ? AND table_name = ? AND constraint_name = ? AND constraint_type = 'FOREIGN KEY'",
                [$database, $table, $constraintName]
            );
            return $result !== null;
        }

        return false;
    }
};
