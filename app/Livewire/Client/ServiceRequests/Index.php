<?php

namespace App\Livewire\Client\ServiceRequests;

use App\Models\Commune;
use App\Models\Region;
use App\Models\ServiceCategory;
use App\Models\ServiceFormField;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestAttachment;
use App\Services\ServiceRequestPdfService;
use App\Services\ServiceRequestService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $title = '';

    /** Búsqueda y orden para DataTable Mis Solicitudes */
    public string $search = '';

    public string $sortField = 'id';

    public string $sortDirection = 'desc';

    /** Modal eliminar */
    public ?int $deletingId = null;

    public string $deletingTitle = '';

    /** Modal solicitud creada */
    public bool $showCreatedModal = false;

    /** Modal Ver solicitud */
    public ?int $viewingId = null;

    /** Modal confirmar Editar */
    public ?int $editConfirmId = null;

    public string $editConfirmTitle = '';

    /** Modal confirmar Publicar */
    public ?int $publishConfirmId = null;

    public string $publishConfirmTitle = '';

    protected $paginationTheme = 'tailwind';

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

    public function removePhoto(int $index): void
    {
        if (! array_key_exists($index, $this->photos)) {
            return;
        }

        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function applySearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    #[Computed]
    public function serviceRequestsPaginated(): LengthAwarePaginator
    {
        return ServiceRequest::query()
            ->where('tenant_id', auth()->user()->current_tenant_id)
            ->with(['category', 'category.parent'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('contact_name', 'like', '%'.$this->search.'%')
                        ->orWhereHas('category', fn ($c) => $c->where('name', 'like', '%'.$this->search.'%'));
                });
            })
            ->when($this->sortField === 'category', function ($query) {
                $query->leftJoin('service_categories as sort_cat', 'service_requests.category_id', '=', 'sort_cat.id')
                    ->orderBy('sort_cat.name', $this->sortDirection)
                    ->select('service_requests.*');
            }, function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            })
            ->paginate(15);
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

    public function confirmDelete(int $serviceRequestId): void
    {
        $request = ServiceRequest::query()
            ->where('tenant_id', auth()->user()->current_tenant_id)
            ->findOrFail($serviceRequestId);

        if ($request->awarded_bid_id !== null) {
            $this->dispatch('toast', [['message' => __('No se puede eliminar una solicitud adjudicada (ya tiene Orden de Trabajo).'), 'type' => 'error']]);
            return;
        }

        $this->deletingId = $request->id;
        $this->deletingTitle = $request->title;
    }

    public function closeDeleteModal(): void
    {
        $this->reset('deletingId', 'deletingTitle');
    }

    public function closeCreatedModal(): void
    {
        $this->reset('showCreatedModal');
    }

    public function openViewModal(int $id): void
    {
        $request = ServiceRequest::query()
            ->where('tenant_id', auth()->user()->current_tenant_id)
            ->with(['category', 'category.parent'])
            ->findOrFail($id);
        $this->viewingId = $request->id;
    }

    public function closeViewModal(): void
    {
        $this->reset('viewingId');
    }

    public function openEditConfirmModal(int $id, string $title): void
    {
        $this->editConfirmId = $id;
        $this->editConfirmTitle = $title;
    }

    public function closeEditConfirmModal(): void
    {
        $this->reset('editConfirmId', 'editConfirmTitle');
    }

    public function goToEdit(): void
    {
        $id = $this->editConfirmId;
        $this->closeEditConfirmModal();
        if ($id) {
            $request = ServiceRequest::find($id);
            if ($request) {
                $this->redirect(route('client.requests.edit', $request), navigate: true);
            }
        }
    }

    public function openPublishConfirmModal(int $id, string $title): void
    {
        $this->publishConfirmId = $id;
        $this->publishConfirmTitle = $title;
    }

    public function closePublishConfirmModal(): void
    {
        $this->reset('publishConfirmId', 'publishConfirmTitle');
    }

    public function confirmPublish(ServiceRequestService $serviceRequestService): void
    {
        if (! $this->publishConfirmId) {
            return;
        }
        $id = $this->publishConfirmId;
        $this->closePublishConfirmModal();
        $this->publish($id, $serviceRequestService);
    }

    #[Computed]
    public function viewRequest(): ?ServiceRequest
    {
        if (! $this->viewingId) {
            return null;
        }

        return ServiceRequest::query()
            ->where('tenant_id', auth()->user()->current_tenant_id)
            ->with(['category', 'category.parent'])
            ->find($this->viewingId);
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

    public function create(ServiceRequestService $serviceRequestService, ServiceRequestPdfService $pdfService): void
    {
        $this->authorize('create', ServiceRequest::class);

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
            'photos' => ['array', 'max:4'],
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
            regionId: (int) $validated['regionId'],
            communeId: (int) $validated['communeId'],
        );

        $contactPhone = $validated['contact_phone_country'].$validated['contact_phone_number'];
        $region = Region::find($validated['regionId']);
        $commune = Commune::find($validated['communeId']);
        $locationText = $commune?->name && $region?->name
            ? $commune->name.', '.$region->name
            : ($commune?->name ?? $region?->name ?? '');

        $serviceRequest->update([
            'contact_name' => $validated['contact_name'],
            'contact_email' => $validated['contact_email'],
            'contact_phone' => $contactPhone,
            'location_text' => $locationText,
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

        $pdfPath = $pdfService->generate($serviceRequest);
        if ($pdfPath !== null) {
            $serviceRequest->update(['pdf_path' => $pdfPath]);
        }

        $this->reset(
            'topCategoryId',
            'subcategoryId',
            'answers',
            'title',
            'description',
            'contact_name',
            'contact_email',
            'contact_phone_country',
            'contact_phone_number',
            'regionId',
            'communeId',
            'address',
            'photos',
        );

        $this->dispatch('toast', [['message' => __('Solicitud creada correctamente. Puedes publicarla cuando esté lista.'), 'type' => 'success']]);
        $this->showCreatedModal = true;
    }

    public function publish(int $serviceRequestId, ServiceRequestService $serviceRequestService): void
    {
        $serviceRequest = ServiceRequest::query()
            ->where('tenant_id', auth()->user()->current_tenant_id)
            ->findOrFail($serviceRequestId);

        $this->authorize('publish', $serviceRequest);

        $serviceRequestService->publish($serviceRequest);
        $this->dispatch('toast', [['message' => __('Solicitud publicada correctamente.'), 'type' => 'success']]);
    }

    public function delete(): void
    {
        if (! $this->deletingId) {
            return;
        }

        $serviceRequest = ServiceRequest::query()
            ->where('tenant_id', auth()->user()->current_tenant_id)
            ->findOrFail($this->deletingId);

        if ($serviceRequest->awarded_bid_id !== null) {
            $this->closeDeleteModal();
            $this->dispatch('toast', [['message' => __('No se puede eliminar una solicitud adjudicada (ya tiene Orden de Trabajo).'), 'type' => 'error']]);
            return;
        }

        $this->authorize('delete', $serviceRequest);

        $serviceRequest->delete();
        $this->closeDeleteModal();
        $this->dispatch('toast', [['message' => __('Solicitud eliminada correctamente.'), 'type' => 'success']]);
    }

    public function render()
    {
        return view('livewire.client.service-requests.index')
            ->layout('layouts.app', ['title' => __('Mis solicitudes')]);
    }
}
