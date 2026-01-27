<?php

namespace App\Livewire\Provider\Bids;

use App\Models\ServiceBid;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
{
    public string $statusFilter = 'all';

    #[Computed]
    public function bids(): Collection
    {
        $query = ServiceBid::query()
            ->where('user_id', auth()->id())
            ->with(['serviceRequest.category', 'serviceRequest.tenant']);

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query->latest('id')->get();
    }

    #[Computed]
    public function stats(): array
    {
        $allBids = ServiceBid::query()
            ->where('user_id', auth()->id())
            ->get();

        return [
            'total' => $allBids->count(),
            'submitted' => $allBids->where('status', 'submitted')->count(),
            'accepted' => $allBids->where('status', 'accepted')->count(),
            'expired' => $allBids->where('status', 'expired')->count(),
            'rejected' => $allBids->where('status', 'rejected')->count(),
        ];
    }

    public function mount(): void
    {
        abort_unless(! auth()->user()->isGuest() && ! auth()->user()->isClient(), 403);
    }

    public function render()
    {
        return view('livewire.provider.bids.index')
            ->layout('layouts.app', ['title' => __('Mis Cotizaciones')]);
    }
}
