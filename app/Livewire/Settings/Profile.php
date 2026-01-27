<?php

namespace App\Livewire\Settings;

use App\Concerns\ProfileValidationRules;
use App\Jobs\SendEmailVerificationCodeJob;
use App\Models\Commune;
use App\Models\Region;
use App\Models\ServiceCategory;
use App\Rules\ChileanRut;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use ProfileValidationRules;
    use WithFileUploads;

    public string $name = '';

    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public ?string $gender = null;

    public ?string $birth_date = null;

    public mixed $avatar = null;

    public ?string $rut = null;

    public ?string $fantasy_name = null;

    public ?string $economic_activity = null;

    public ?int $region_id = null;

    public ?int $commune_id = null;

    public array $service_category_ids = [];

    public string $avatarPreview = '';

    public function updatedRegionId(): void
    {
        // Reset commune when region changes
        $this->commune_id = null;
    }

    public function updatedAvatar(): void
    {
        $this->validate([
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($this->avatar) {
            $this->avatarPreview = $this->avatar->temporaryUrl();
        } else {
            $this->avatarPreview = Auth::user()->avatarUrl() ?? '';
        }
    }

    public function updatedRut($value): void
    {
        // Formatear RUT mientras el usuario escribe: xx.xxx.xxx-x
        if ($value) {
            // Remover todo excepto números y guión
            $clean = preg_replace('/[^0-9kK-]/', '', $value);

            // Si tiene más de 1 carácter, formatear
            if (strlen($clean) > 1) {
                // Separar dígito verificador si existe
                $parts = explode('-', $clean);
                $numbers = $parts[0];
                $dv = isset($parts[1]) ? '-'.$parts[1] : '';

                // Formatear números con puntos
                if (strlen($numbers) > 0) {
                    $formatted = '';
                    $reversed = strrev($numbers);

                    for ($i = 0; $i < strlen($reversed); $i++) {
                        if ($i > 0 && $i % 3 == 0) {
                            $formatted = '.'.$formatted;
                        }
                        $formatted = $reversed[$i].$formatted;
                    }

                    $this->rut = $formatted.$dv;
                }
            } else {
                $this->rut = $clean;
            }
        }
    }

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->first_name = $user->first_name ?? '';
        $this->last_name = $user->last_name ?? '';
        $this->email = $user->email;
        $this->gender = $user->gender;
        $this->birth_date = $user->birth_date?->format('d-m-Y');
        $this->rut = $user->rut;
        $this->fantasy_name = $user->fantasy_name;
        $this->economic_activity = $user->economic_activity;
        $this->region_id = $user->region_id;
        $this->commune_id = $user->commune_id;
        $this->service_category_ids = $user->serviceCategories->pluck('id')->toArray();
        $this->avatarPreview = $user->avatarUrl() ?? '';
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        // Parse birth_date from DD-MM-YYYY to YYYY-MM-DD
        if (! empty($validated['birth_date'])) {
            $date = \DateTime::createFromFormat('d-m-Y', $validated['birth_date']);
            if ($date) {
                $validated['birth_date'] = $date->format('Y-m-d');
            } else {
                unset($validated['birth_date']);
            }
        }

        $user->fill($validated);
        $user->save();

        // Sync service categories (max 10)
        if (count($this->service_category_ids) > 10) {
            $this->service_category_ids = array_slice($this->service_category_ids, 0, 10);
        }
        $user->serviceCategories()->sync($this->service_category_ids);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $user->save();
        }

        if ($this->avatar) {
            $this->validate([
                'avatar' => ['image', 'max:2048'],
            ]);

            // Eliminar avatar anterior si existe
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $path = $this->avatar->storePublicly('avatars/'.$user->id, 'public');

            $user->forceFill(['avatar_path' => $path])->save();

            // Refresh user to get updated avatar
            $user->refresh();
            Auth::setUser($user); // Actualizar el usuario en la sesión

            // Actualizar preview con la nueva URL
            $this->avatarPreview = $user->avatarUrl() ?? '';

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

    #[Computed]
    public function regions(): \Illuminate\Database\Eloquent\Collection
    {
        return Region::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
    }

    #[Computed]
    public function communes(): \Illuminate\Database\Eloquent\Collection
    {
        if (! $this->region_id) {
            return collect();
        }

        return Commune::where('region_id', $this->region_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function serviceCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return ServiceCategory::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->orderBy('sort_order')->orderBy('name');
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get the validation rules used to validate user profiles.
     *
     * @return array<string, array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>>
     */
    protected function profileRules(?int $userId = null): array
    {
        return [
            'name' => $this->nameRules(),
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => $this->emailRules($userId),
            'gender' => ['nullable', 'in:hombre,mujer'],
            'birth_date' => ['nullable', 'date_format:d-m-Y'],
            'rut' => ['nullable', new ChileanRut],
            'fantasy_name' => ['nullable', 'string', 'max:255'],
            'economic_activity' => ['nullable', 'string', 'max:255'],
            'region_id' => ['nullable', 'exists:regions,id'],
            'commune_id' => ['nullable', 'exists:communes,id'],
            'service_category_ids' => ['nullable', 'array', 'max:10'],
            'service_category_ids.*' => ['exists:service_categories,id'],
        ];
    }
}
