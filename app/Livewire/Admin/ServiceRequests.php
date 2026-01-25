<?php

namespace App\Livewire\Admin;

use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ServiceRequests extends Component
{
    #[Computed]
    public function serviceRequests(): Collection
    {
        return ServiceRequest::query()
            ->with(['tenant', 'category', 'creator'])
            ->latest('id')
            ->get();
    }

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdministrator(), 403);
    }

    public function delete(int $serviceRequestId): void
    {
        ServiceRequest::query()->findOrFail($serviceRequestId)->delete();
    }

    public function render()
    {
        return view('livewire.admin.service-requests')
            ->layout('layouts.app', ['title' => __('Admin · Solicitudes')]);
    }
}

