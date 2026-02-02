<?php

namespace App\Livewire\Provider;

use App\Rules\ChileanRut;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileSettings extends Component
{
    use WithFileUploads;

    public string $rut = '';

    public string $giro_sii = '';

    public $logo = null;

    public string $logoPreview = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->rut = $user->rut ?? '';
        $this->giro_sii = $user->giro_sii ?? '';

        if ($user->avatar_path) {
            $this->logoPreview = $user->avatarUrl();
        }
    }

    public function updatedLogo(): void
    {
        $this->validate([
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($this->logo) {
            $this->logoPreview = $this->logo->temporaryUrl();
        }
    }

    public function save(): void
    {
        $this->validate([
            'rut' => ['nullable', new ChileanRut],
            'giro_sii' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $user = auth()->user();

        $user->update([
            'rut' => $this->rut ?: null,
            'giro_sii' => $this->giro_sii ?: null,
        ]);

        if ($this->logo) {
            $path = $this->logo->store('avatars', 'public');
            $user->update(['avatar_path' => $path]);
        }

        $this->dispatch('toast', [['message' => __('Perfil actualizado exitosamente.'), 'type' => 'success']]);
    }

    public function render()
    {
        return view('livewire.provider.profile-settings')
            ->layout('layouts.app', ['title' => __('Configuración de Perfil Profesional')]);
    }
}
