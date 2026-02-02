<?php

namespace App\Livewire\Auth;

use App\EmailCodeVerificationResult;
use App\Jobs\SendEmailVerificationCodeJob;
use App\Services\EmailVerificationCodeService;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Validate;
use Livewire\Component;

class VerifyCode extends Component
{
    #[Validate('required|string|size:6', onUpdate: false)]
    public string $code = '';

    public function mount(EmailVerificationCodeService $service): void
    {
        if (auth()->user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        if (! $service->hasActiveCode(auth()->user())) {
            SendEmailVerificationCodeJob::dispatch(auth()->id());
        }
    }

    public function verify(EmailVerificationCodeService $service): void
    {
        $this->validate();

        $result = $service->verify(auth()->user(), $this->code);

        if ($result === EmailCodeVerificationResult::Verified || $result === EmailCodeVerificationResult::AlreadyVerified) {
            \App\Helpers\Toaster::success(__('Correo verificado correctamente.'));
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        if ($result === EmailCodeVerificationResult::Expired) {
            $this->addError('code', __('El código ha expirado. Solicita uno nuevo.'));

            return;
        }

        $this->addError('code', __('Código incorrecto. Intenta nuevamente.'));
    }

    public function resend(): void
    {
        $key = 'email-code-resend:'.auth()->id();

        $maxAttempts = 6;
        $decaySeconds = 120;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            $this->addError('code', __('Has solicitado demasiados códigos. Intenta nuevamente en :seconds segundos.', [
                'seconds' => $seconds,
            ]));

            return;
        }

        RateLimiter::hit($key, $decaySeconds);

        SendEmailVerificationCodeJob::dispatch(auth()->id(), force: true);

        $this->dispatch('toast', [['message' => __('Código enviado a tu correo.'), 'type' => 'info']]);
        $this->reset('code');
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.auth.verify-code')
            ->layout('layouts.auth');
    }
}
