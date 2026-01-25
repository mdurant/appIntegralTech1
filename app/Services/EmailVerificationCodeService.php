<?php

namespace App\Services;

use App\EmailCodeVerificationResult;
use App\Models\EmailVerificationCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmailVerificationCodeService
{
    /**
     * @return array{code: string, expiresAt: \Illuminate\Support\CarbonInterface}
     */
    public function issue(User $user): array
    {
        EmailVerificationCode::query()
            ->where('user_id', $user->id)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(15);

        EmailVerificationCode::create([
            'user_id' => $user->id,
            'code_hash' => Hash::make($code),
            'sent_to_email' => $user->email,
            'expires_at' => $expiresAt,
        ]);

        return [
            'code' => $code,
            'expiresAt' => $expiresAt,
        ];
    }

    public function hasActiveCode(User $user): bool
    {
        return EmailVerificationCode::query()
            ->where('user_id', $user->id)
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->exists();
    }

    public function verify(User $user, string $code): EmailCodeVerificationResult
    {
        if ($user->hasVerifiedEmail()) {
            return EmailCodeVerificationResult::AlreadyVerified;
        }

        $latest = EmailVerificationCode::query()
            ->where('user_id', $user->id)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (! $latest || $latest->expires_at->isPast()) {
            return EmailCodeVerificationResult::Expired;
        }

        if (! Hash::check($code, $latest->code_hash)) {
            return EmailCodeVerificationResult::Invalid;
        }

        $latest->forceFill(['consumed_at' => now()])->save();
        $user->forceFill(['email_verified_at' => now()])->save();

        return EmailCodeVerificationResult::Verified;
    }
}
