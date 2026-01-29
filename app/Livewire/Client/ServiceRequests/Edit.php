<?php

namespace App\Livewire\Client\ServiceRequests;

use App\Models\Commune;
use App\Models\Region;
use App\Models\ServiceCategory;
use App\Models\ServiceFormField;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestAttachment;
use App\ServiceRequestStatus;
use App\Services\ServiceRequestService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public ServiceRequest $serviceRequest;

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

    public string $contact_phone_country = '+56';

    public string $contact_phone_number = '';

    public int|string|null $regionId = null;

    public int|string|null $communeId = null;

    public string $address = '';

    /**
     * @var array<int, mixed>
     */
    public array $photos = [];

    public function mount(ServiceRequest $serviceRequest): void
    {
        abort_unless(auth()->user()->isClient(), 403);
        abort_unless(auth()->user()->belongsToTenant($serviceRequest->tenant), 403);
        abort_unless($serviceRequest->status === ServiceRequestStatus::Draft, 403);

        $this->serviceRequest = $serviceRequest->load([
            'category',
            'category.parent',
            'fieldAnswers',
            'attachments',
        ]);

        $this->title = $this->serviceRequest->title;
        $this->description = (string) ($this->serviceRequest->notes ?? $this->serviceRequest->description);
        $this->contact_name = (string) $this->serviceRequest->contact_name;
        $this->contact_email = (string) $this->serviceRequest->contact_email;
        $this->address = (string) $this->serviceRequest->address;

        $this->parseContactPhone((string) $this->serviceRequest->contact_phone);
        $this->regionId = $this->serviceRequest->region_id;
        $this->communeId = $this->serviceRequest->commune_id;

        $category = $this->serviceRequest->category;
        if ($category) {
            $this->subcategoryId = $category->id;
            $this->topCategoryId = $category->parent_id ?? $category->id;
        }

        foreach ($this->serviceRequest->fieldAnswers as $answer) {
            $this->answers[$answer->service_form_field_id] = $answer->value;
        }
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

    public function removePhoto(int $index): void
    {
        if (! array_key_exists($index, $this->photos)) {
            return;
        }

        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }

    public function updatedTopCategoryId(): void
    {
        $this->reset('subcategoryId', 'answers');
    }

    public function updatedSubcategoryId(): void
    {
        $this->reset('answers');
    }

    public function updatedRegionId(): void
    {
        $this->reset('communeId');
    }

    #[Computed]
    public function regions(): Collection
    {
        return Region::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function communes(): Collection
    {
        if (! $this->regionId) {
            return new Collection([]);
        }

        return Commune::query()
            ->where('region_id', (int) $this->regionId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function parseContactPhone(string $phone): void
    {
        $phone = preg_replace('/\s+/', '', $phone);
        $codes = ['+56', '+54', '+57', '+58', '+51', '+52', '+593', '+595', '+598', '+1'];
        foreach ($codes as $code) {
            $digits = ltrim($code, '+');
            if (str_starts_with($phone, $code) || str_starts_with($phone, $digits)) {
                $this->contact_phone_country = $code;
                $num = substr($phone, strlen($digits));
                $this->contact_phone_number = preg_replace('/\D/', '', $num);
                if (strlen($this->contact_phone_number) > 9) {
                    $this->contact_phone_number = substr($this->contact_phone_number, -9);
                }

                return;
            }
        }
        $this->contact_phone_country = '+56';
        $this->contact_phone_number = preg_replace('/\D/', '', $phone);
        if (strlen($this->contact_phone_number) > 9) {
            $this->contact_phone_number = substr($this->contact_phone_number, -9);
        }
    }

    public function save(ServiceRequestService $serviceRequestService): void
    {
        $this->authorize('update', $this->serviceRequest);

        $validated = $this->validate([
            'topCategoryId' => ['required', 'integer', 'exists:service_categories,id'],
            'subcategoryId' => ['required', 'integer', 'exists:service_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone_country' => ['required', 'string', 'in:+56,+54,+1,+57,+58,+51,+52,+593,+595,+598'],
            'contact_phone_number' => ['required', 'string', 'regex:/^\d{8,9}$/', 'max:9'],
            'regionId' => ['required', 'integer', 'exists:regions,id'],
            'communeId' => ['required', 'integer', 'exists:communes,id'],
            'address' => ['required', 'string', 'max:255'],
            'photos' => ['array', 'max:'.max(0, 4 - $this->serviceRequest->attachments()->count())],
            'photos.*' => ['image', 'max:2048'],
        ], [
            'contact_phone_number.required' => __('El número de teléfono es obligatorio.'),
            'contact_phone_number.regex' => __('El número debe tener 8 o 9 dígitos (formato celular Chile).'),
            'regionId.required' => __('Debe seleccionar una región.'),
            'communeId.required' => __('Debe seleccionar una comuna.'),
        ]);

        $subcategory = ServiceCategory::query()
            ->whereNotNull('parent_id')
            ->where('parent_id', (int) $validated['topCategoryId'])
            ->findOrFail((int) $validated['subcategoryId']);

        $dynamicRules = [];
        foreach ($this->formFields as $field) {
            $rule = [$field->required ? 'required' : 'nullable'];
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

        $serviceRequestService->updateDraft(
            $this->serviceRequest,
            $validated['title'],
            $validated['description'],
            $subcategory,
        );

        $contactPhone = $validated['contact_phone_country'].$validated['contact_phone_number'];
        $region = Region::find($validated['regionId']);
        $commune = Commune::find($validated['communeId']);
        $locationText = $commune?->name && $region?->name
            ? $commune->name.', '.$region->name
            : ($commune?->name ?? $region?->name ?? '');

        $this->serviceRequest->update([
            'contact_name' => $validated['contact_name'],
            'contact_email' => $validated['contact_email'],
            'contact_phone' => $contactPhone,
            'location_text' => $locationText,
            'region_id' => (int) $validated['regionId'],
            'commune_id' => (int) $validated['communeId'],
            'address' => $validated['address'],
            'notes' => $validated['description'],
        ]);

        $currentFieldIds = $this->formFields->pluck('id')->all();
        $this->serviceRequest->fieldAnswers()->whereNotIn('service_form_field_id', $currentFieldIds)->delete();

        foreach ($this->formFields as $field) {
            $value = $this->answers[$field->id] ?? null;
            if ($value === null || $value === '') {
                continue;
            }
            $this->serviceRequest->fieldAnswers()->updateOrCreate(
                ['service_form_field_id' => $field->id],
                ['value' => is_array($value) ? json_encode($value) : (string) $value],
            );
        }

        $existingCount = $this->serviceRequest->attachments()->count();
        $sortOrder = $existingCount * 10;
        foreach ($this->photos as $photo) {
            $path = $photo->storePublicly('service-requests/'.$this->serviceRequest->id, 'public');
            ServiceRequestAttachment::create([
                'service_request_id' => $this->serviceRequest->id,
                'path' => $path,
                'mime' => $photo->getMimeType(),
                'size' => $photo->getSize(),
                'sort_order' => $sortOrder,
            ]);
            $sortOrder += 10;
        }

        $this->redirect(route('client.requests.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.client.service-requests.edit')
            ->layout('layouts.app', ['title' => __('Editar solicitud')]);
    }
}
