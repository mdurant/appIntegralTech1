<?php

namespace App\Livewire\Admin;

use App\Models\ServiceBid;
use App\Models\ServiceRequest;
use App\Models\SupportTicket;
use App\Models\Tenant;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()->isAdministrator(), 403);
    }

    public function render()
    {
        return view('livewire.admin.dashboard', [
            'tenantsCount' => Tenant::query()->count(),
            'usersCount' => User::query()->count(),
            'requestsCount' => ServiceRequest::query()->count(),
            'bidsCount' => ServiceBid::query()->count(),
            'supportTicketsPendingCount' => SupportTicket::where('status', SupportTicket::StatusPending)->count(),
        ])->layout('layouts.app', ['title' => __('Admin')]);
    }
}
