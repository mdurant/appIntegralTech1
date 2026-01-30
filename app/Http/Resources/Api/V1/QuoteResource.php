<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service_request_id' => $this->service_request_id,
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'message' => $this->message,
            'status' => $this->status->value,
            'valid_until' => $this->valid_until?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'opportunity' => $this->whenLoaded('serviceRequest', fn () => new OpportunityResource($this->serviceRequest)),
        ];
    }
}
