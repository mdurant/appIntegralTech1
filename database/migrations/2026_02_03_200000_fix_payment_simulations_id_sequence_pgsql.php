<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * En PostgreSQL (Laravel Cloud), si la columna "id" de payment_simulations
     * no tiene DEFAULT o la secuencia está desincronizada, los INSERT sin "id"
     * devuelven null y provocan "Not null violation". Esta migración repara
     * la secuencia y el DEFAULT solo para pgsql.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver !== 'pgsql') {
            return;
        }

        $table = 'payment_simulations';
        $seq = $table . '_id_seq';

        // Asegurar que la secuencia exista y esté vinculada a la columna id
        DB::statement("
            DO $$
            BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM pg_sequences WHERE schemaname = 'public' AND sequencename = ?
                ) THEN
                    CREATE SEQUENCE public.{$seq} OWNED BY public.{$table}.id;
                END IF;
            END $$;
        ", [$seq]);

        // Establecer DEFAULT para id si no lo tiene
        DB::statement("
            ALTER TABLE {$table}
            ALTER COLUMN id SET DEFAULT nextval('public.{$seq}'::regclass)
        ");

        // Sincronizar la secuencia con el máximo id actual (evita duplicados y nulls)
        DB::statement("
            SELECT setval(
                'public.{$seq}'::regclass,
                COALESCE((SELECT MAX(id) FROM public.{$table}), 1)
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revertir: dejar la secuencia y el default como están
    }
};
