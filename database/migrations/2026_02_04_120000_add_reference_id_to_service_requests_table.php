<?php

use App\Models\ServiceRequest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * reference_id: identificador lógico único SC-DDMMAA-NNNNNN (Solicitud Cotización).
     * Se rellenan los registros existentes para prod/dev (Laravel Cloud).
     */
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->string('reference_id', 20)->nullable()->unique()->after('id');
        });

        ServiceRequest::query()
            ->whereNull('reference_id')
            ->each(function (ServiceRequest $request): void {
                $request->reference_id = ServiceRequest::generateUniqueReferenceId($request->created_at);
                $request->saveQuietly();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropUnique(['reference_id']);
            $table->dropColumn('reference_id');
        });
    }
};
