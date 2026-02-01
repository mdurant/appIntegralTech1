<?php

namespace App\Livewire;

use App\Services\CookieConsentService;
use Livewire\Component;

class CookieConsentModal extends Component
{
    public bool $show = false;

    public bool $showSettings = false;

    public bool $marketingEnabled = false;

    public bool $userExperienceEnabled = false;

    protected CookieConsentService $cookieService;

    public function boot(CookieConsentService $cookieService): void
    {
        $this->cookieService = $cookieService;
    }

    public function mount(): void
    {
        // Solo mostrar el modal si el usuario no ha dado su consentimiento
        $this->show = ! $this->cookieService->hasConsent();

        // Si ya hay preferencias guardadas, cargarlas
        if ($this->cookieService->hasConsent()) {
            $preferences = $this->cookieService->getPreferences();
            $this->marketingEnabled = $preferences['marketing'] ?? false;
            $this->userExperienceEnabled = $preferences['user_experience'] ?? false;
        }
    }

    public function acceptAll(): void
    {
        $this->marketingEnabled = true;
        $this->userExperienceEnabled = true;
        $this->savePreferences();
    }

    public function rejectAll(): void
    {
        $this->marketingEnabled = false;
        $this->userExperienceEnabled = false;
        $this->savePreferences();
    }

    public function openSettings(): void
    {
        $this->showSettings = true;
    }

    public function closeSettings(): void
    {
        $this->showSettings = false;
    }

    public function saveSettings(): void
    {
        $this->savePreferences();
        $this->showSettings = false;
    }

    private function savePreferences(): void
    {
        $preferences = [
            'marketing' => $this->marketingEnabled,
            'user_experience' => $this->userExperienceEnabled,
        ];

        $this->cookieService->setConsentInResponse($preferences);

        $this->dispatch('cookie-consent-saved', $preferences);

        $this->show = false;
    }

    public function render()
    {
        return view('livewire.cookie-consent-modal');
    }
}
