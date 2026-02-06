<?php

namespace App\Console\Commands;

use App\Models\ServiceRequest;
use Illuminate\Console\Command;

class BackfillServiceRequestReferenceIds extends Command
{
    /**
     * @var string
     */
    protected $signature = 'service-requests:backfill-reference-ids';

    /**
     * @var string
     */
    protected $description = 'Asigna reference_id (SC-DDMMAA-NNNNNN) a solicitudes que aún no lo tienen.';

    public function handle(): int
    {
        $query = ServiceRequest::query()->whereNull('reference_id');

        $total = $query->count();
        if ($total === 0) {
            $this->info('No hay solicitudes sin reference_id.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->cursor()->each(function (ServiceRequest $request) use ($bar): void {
            $request->reference_id = ServiceRequest::generateUniqueReferenceId($request->created_at);
            $request->saveQuietly();
            $bar->advance();
        });

        $bar->finish();
        $this->newLine();
        $this->info("Se asignaron {$total} reference_id.");

        return self::SUCCESS;
    }
}
