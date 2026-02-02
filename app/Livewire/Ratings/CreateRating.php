<?php

namespace App\Livewire\Ratings;

use App\Models\Rating;
use App\Models\WorkOrder;
use App\Services\RatingService;
use App\WorkOrderStatus;
use Livewire\Component;

class CreateRating extends Component
{
    public WorkOrder $workOrder;

    public int $rating = 5;

    public string $comment = '';

    public function mount(WorkOrder $workOrder): void
    {
        $this->workOrder = $workOrder;

        // Verificar que la OT esté completada
        abort_unless($workOrder->status === WorkOrderStatus::Completed, 403);
        // Verificar que el usuario sea el cliente del tenant
        abort_unless(
            auth()->user()->isClient() && auth()->user()->belongsToTenant($workOrder->tenant),
            403
        );
        // Verificar que no haya valorado ya
        abort_if(
            Rating::where('work_order_id', $workOrder->id)
                ->where('user_id', auth()->id())
                ->exists(),
            403,
            __('Ya has valorado esta orden de trabajo.')
        );
    }

    public function submit(RatingService $ratingService): void
    {
        $this->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $ratingService->create(
            workOrder: $this->workOrder,
            user: auth()->user(),
            rating: $this->rating,
            comment: $this->comment ?: null,
        );

        $this->dispatch('rating-created');

        $this->dispatch('toast', [['message' => __('Valoración creada exitosamente.'), 'type' => 'success']]);
    }

    public function render()
    {
        return view('livewire.ratings.create-rating');
    }
}
