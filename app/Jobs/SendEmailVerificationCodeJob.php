<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\EmailVerificationCodeNotification;
use App\Services\EmailVerificationCodeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEmailVerificationCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $userId,
        public bool $force = false,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::query()->find($this->userId);

        if (! $user || $user->hasVerifiedEmail()) {
            return;
        }

        $service = app(EmailVerificationCodeService::class);

        if (! $this->force && $service->hasActiveCode($user)) {
            Log::channel('email_codes')->debug('Email verification code skipped (active code exists)', [
                'user_id' => $user->id,
                'email' => $user->email,
                'force' => $this->force,
            ]);

            return;
        }

        $issued = $service->issue($user);

        $plain = config('app.debug') && config('app.email_code_log_plaintext');

        Log::channel('email_codes')->info('Email verification code issued', [
            'user_id' => $user->id,
            'email' => $user->email,
            'code' => $plain
                ? $issued['code']
                : ('****'.substr($issued['code'], -2)),
            'expires_at' => $issued['expiresAt']->toISOString(),
            'force' => $this->force,
        ]);

        $user->notify(new EmailVerificationCodeNotification(
            code: $issued['code'],
            expiresAt: $issued['expiresAt'],
        ));

        Log::channel('email_codes')->info('Email verification notification dispatched', [
            'user_id' => $user->id,
            'email' => $user->email,
            'mail_mailer' => config('mail.default'),
            'mail_log_channel' => config('mail.mailers.log.channel'),
        ]);
    }
}
