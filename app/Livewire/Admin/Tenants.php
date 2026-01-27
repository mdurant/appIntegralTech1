<?php

namespace App\Livewire\Admin;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Tenants extends Component
{
    #[Computed]
    public function tenants(): Collection
    {
        return Tenant::query()->latest('id')->get();
    }

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdministrator(), 403);
    }

    public function delete(int $tenantId): void
    {
        Tenant::query()->findOrFail($tenantId)->delete();
    }

    public function render()
    {
        return view('livewire.admin.tenants')
            ->layout('layouts.app', ['title' => __('Admin · Organizaciones')]);
    }
}
