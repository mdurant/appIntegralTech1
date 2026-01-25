<?php

namespace App\Livewire\Settings;

use App\Concerns\ProfileValidationRules;
use App\Jobs\SendEmailVerificationCodeJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use ProfileValidationRules;
    use WithFileUploads;

    public string $name = '';

    public string $email = '';

    public mixed $avatar = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($this->avatar) {
            $this->validate([
                'avatar' => ['image', 'max:2048'],
            ]);

            $path = $this->avatar->storePublicly('avatars/'.$user->id, 'public');

            $user->forceFill(['avatar_path' => $path])->save();

            $this->reset('avatar');
        }

        if ($user->wasChanged('email')) {
            SendEmailVerificationCodeJob::dispatch($user->id, force: true);
        }

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        SendEmailVerificationCodeJob::dispatch($user->id, force: true);

        Session::flash('status', 'verification-code-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return Auth::user()->hasVerifiedEmail();
    }
}
