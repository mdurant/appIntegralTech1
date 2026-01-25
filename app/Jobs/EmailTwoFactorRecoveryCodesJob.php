<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\TwoFactorRecoveryCodesNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class EmailTwoFactorRecoveryCodesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $userId,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::query()->find($this->userId);

        if (! $user || ! $user->hasEnabledTwoFactorAuthentication()) {
            return;
        }

        $cacheKey = 'two-factor-recovery-codes-emailed:'.$user->id;

        if (! Cache::add($cacheKey, true, now()->addHour())) {
            return;
        }

        /** @var array<int, string> $codes */
        $codes = (array) $user->recoveryCodes();

        $user->notify(new TwoFactorRecoveryCodesNotification($codes));
    }
}
