<?php

namespace App\Console\Commands;

use App\Models\ServiceBid;
use App\ServiceBidStatus;
use Illuminate\Console\Command;

class MarkExpiredBids extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quotes:mark-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marca las cotizaciones vencidas como Expired';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredCount = ServiceBid::query()
            ->where('status', ServiceBidStatus::Submitted->value)
            ->whereNotNull('valid_until')
            ->where('valid_until', '<', now())
            ->update(['status' => ServiceBidStatus::Expired->value]);

        $this->info("Se marcaron {$expiredCount} cotizaciones como vencidas.");

        return Command::SUCCESS;
    }
}
