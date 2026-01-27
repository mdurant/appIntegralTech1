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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->enum('gender', ['hombre', 'mujer'])->nullable()->after('last_name');
            $table->date('birth_date')->nullable()->after('gender');
            $table->string('fantasy_name')->nullable()->after('avatar_path');
            $table->string('economic_activity')->nullable()->after('fantasy_name');
            $table->foreignId('region_id')->nullable()->after('economic_activity')->constrained('regions')->nullOnDelete();
            $table->foreignId('commune_id')->nullable()->after('region_id')->constrained('communes')->nullOnDelete();

            $table->index('fantasy_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropForeign(['commune_id']);
            $table->dropIndex(['fantasy_name']);
            $table->dropColumn([
                'first_name',
                'last_name',
                'gender',
                'birth_date',
                'fantasy_name',
                'economic_activity',
                'region_id',
                'commune_id',
            ]);
        });
    }
};
