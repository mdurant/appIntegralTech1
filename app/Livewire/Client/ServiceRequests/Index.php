<?php

namespace App\Livewire\Client\ServiceRequests;

use App\Models\ServiceCategory;
use App\Models\ServiceFormField;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestAttachment;
use App\Services\ServiceRequestService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public string $title = '';

    public string $description = '';

    public int|string|null $topCategoryId = null;

    public int|string|null $subcategoryId = null;

    /**
     * @var array<int|string, mixed>
     */
    public array $answers = [];

    public string $contact_name = '';

    public string $contact_email = '';

    public string $contact_phone = '';

    public string $location_text = '';

    public string $address = '';

    /**
     * @var array<int, mixed>
     */
    public array $photos = [];

    public function removePhoto(int $index): void
    {
        if (! array_key_exists($index, $this->photos)) {
            return;
        }

        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }

    #[Computed]
    public function topCategories(): Collection
    {
        return ServiceCategory::query()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function subcategories(): Collection
    {
        if (! $this->topCategoryId) {
            return ServiceCategory::query()->whereRaw('1 = 0')->get();
        }

        return ServiceCategory::query()
            ->where('parent_id', (int) $this->topCategoryId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function formFields(): Collection
    {
        if (! $this->subcategoryId) {
            return ServiceFormField::query()->whereRaw('1 = 0')->get();
        }

        return ServiceFormField::query()
            ->where('service_category_id', (int) $this->subcategoryId)
            ->with('options')
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();
    }

    #[Computed]
    public function serviceRequests(): Collection
    {
        return ServiceRequest::query()
            ->where('tenant_id', auth()->user()->current_tenant_id)
            ->with(['category', 'category.parent'])
            ->latest('id')
            ->get();
    }

    public function mount(): void
    {
        abort_unless(auth()->user()->isClient(), 403);
        abort_unless(auth()->user()->current_tenant_id, 403);
    }

    public function updatedTopCategoryId(): void
    {
        $this->reset('subcategoryId', 'answers');
    }

    public function updatedSubcategoryId(): void
    {
        $this->reset('answers');
    }

    public function create(ServiceRequestService $serviceRequestService): void
    {
        $this->authorize('create', ServiceRequest::class);

        $validated = $this->validate([
            'topCategoryId' => ['required', 'integer', 'exists:service_categories,id'],
            'subcategoryId' => ['required', 'integer', 'exists:service_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'location_text' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'photos' => ['array', 'max:4'],
            'photos.*' => ['image', 'max:2048'],
        ]);

        $subcategory = ServiceCategory::query()
            ->whereNotNull('parent_id')
            ->where('parent_id', (int) $validated['topCategoryId'])
            ->findOrFail((int) $validated['subcategoryId']);

        $dynamicRules = [];

        foreach ($this->formFields as $field) {
            $rule = [];
            $rule[] = $field->required ? 'required' : 'nullable';

            $type = $field->type?->value;

            if ($type === 'select') {
                $allowed = $field->options->pluck('value')->all();
                $rule[] = 'string';
                if (count($allowed) > 0) {
                    $rule[] = 'in:'.implode(',', $allowed);
                }
            } elseif ($type === 'number') {
                $rule[] = 'numeric';
            } elseif ($type === 'date') {
                $rule[] = 'date';
            } else {
                $rule[] = 'string';
                $rule[] = 'max:5000';
            }

            $dynamicRules['answers.'.$field->id] = $rule;
        }

        if (count($dynamicRules) > 0) {
            $this->validate($dynamicRules);
        }

        $serviceRequest = $serviceRequestService->createDraft(
            actor: auth()->user(),
            tenant: auth()->user()->currentTenant,
            category: $subcategory,
            title: $validated['title'],
            description: $validated['description'],
            answers: $this->answers,
        );

        $serviceRequest->update([
            'contact_name' => $validated['contact_name'],
            'contact_email' => $validated['contact_email'],
            'contact_phone' => $validated['contact_phone'],
            'location_text' => $validated['location_text'],
            'address' => $validated['address'],
            'notes' => $validated['description'],
        ]);

        foreach ($this->photos as $idx => $photo) {
            $path = $photo->storePublicly('service-requests/'.$serviceRequest->id, 'public');

            ServiceRequestAttachment::create([
                'service_request_id' => $serviceRequest->id,
                'path' => $path,
                'mime' => $photo->getMimeType(),
                'size' => $photo->getSize(),
                'sort_order' => $idx * 10,
            ]);
        }

        $this->reset(
            'topCategoryId',
            'subcategoryId',
            'answers',
            'title',
            'description',
            'contact_name',
            'contact_email',
            'contact_phone',
            'location_text',
            'address',
            'photos',
        );
    }

    public function publish(int $serviceRequestId, ServiceRequestService $serviceRequestService): void
    {
        $serviceRequest = ServiceRequest::query()
            ->where('tenant_id', auth()->user()->current_tenant_id)
            ->findOrFail($serviceRequestId);

        $this->authorize('publish', $serviceRequest);

        $serviceRequestService->publish($serviceRequest);
    }

    public function delete(int $serviceRequestId): void
    {
        $serviceRequest = ServiceRequest::query()
            ->where('tenant_id', auth()->user()->current_tenant_id)
            ->findOrFail($serviceRequestId);

        $this->authorize('delete', $serviceRequest);

        $serviceRequest->delete();
    }

    public function render()
    {
        return view('livewire.client.service-requests.index')
            ->layout('layouts.app', ['title' => __('Mis solicitudes')]);
    }
}

