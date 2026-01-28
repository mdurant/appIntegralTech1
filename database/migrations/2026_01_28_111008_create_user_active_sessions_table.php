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
        Schema::create('user_active_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->unique(); // ID de sesión de Laravel
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_name')->nullable(); // Ej: "Macbook Pro 15 Retina (M1)"
            $table->string('device_type')->nullable(); // desktop, mobile, tablet
            $table->string('browser_name')->nullable(); // Chrome, Safari, Firefox
            $table->string('browser_version')->nullable(); // 116, 12, etc.
            $table->string('operating_system')->nullable(); // macOS, Windows, iOS, Android
            $table->string('os_version')->nullable(); // Ventura 13.5, Android 13, etc.
            $table->string('location')->nullable(); // Ciudad, País
            $table->boolean('is_current')->default(false); // Si es la sesión actual
            $table->boolean('is_terminated')->default(false); // Soft delete visual
            $table->timestamp('last_activity')->nullable();
            $table->timestamp('terminated_at')->nullable(); // Cuando el usuario "terminó" la sesión
            $table->timestamps();

            $table->index(['user_id', 'is_terminated']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_active_sessions');
    }
};
