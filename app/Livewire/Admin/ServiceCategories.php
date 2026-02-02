<?php

namespace App\Livewire\Admin;

use App\Models\ServiceCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceCategories extends Component
{
    use WithPagination;

    // Formulario de creación
    public string $name = '';

    public string $key = '';

    public int|string|null $parentId = null;

    public int $sortOrder = 0;

    // Búsqueda y filtros
    public string $search = '';

    public string $sortField = 'name';

    public string $sortDirection = 'asc';

    // Modal de edición
    public ?int $editingId = null;

    public string $editName = '';

    public string $editKey = '';

    public int|string|null $editParentId = null;

    public int $editSortOrder = 0;

    // Modal de eliminación
    public ?int $deletingId = null;

    public string $deletingName = '';

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdministrator(), 403);
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
    public function categories(): LengthAwarePaginator
    {
        return ServiceCategory::query()
            ->with('parent')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('key', 'like', '%'.$this->search.'%')
                        ->orWhere('slug', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(20);
    }

    #[Computed]
    public function allCategoriesForSelect(): \Illuminate\Database\Eloquent\Collection
    {
        return ServiceCategory::query()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function create(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'key' => ['required', 'string', 'max:255', 'unique:service_categories,key'],
            'parentId' => ['nullable', 'integer', 'exists:service_categories,id'],
            'sortOrder' => ['required', 'integer', 'min:0'],
        ]);

        // Generar slug si no se proporciona
        $slug = Str::slug($validated['name']);

        // Obtener el siguiente reference_code disponible
        $maxCode = ServiceCategory::query()->max('reference_code') ?? 0;
        $referenceCode = $maxCode + 1;

        ServiceCategory::create([
            'name' => $validated['name'],
            'key' => $validated['key'],
            'slug' => $slug,
            'reference_code' => $referenceCode,
            'parent_id' => $validated['parentId'] ? (int) $validated['parentId'] : null,
            'sort_order' => (int) $validated['sortOrder'],
        ]);

        $this->reset('name', 'key', 'parentId', 'sortOrder');
        $this->dispatch('category-created');
    }

    public function edit(int $categoryId): void
    {
        $category = ServiceCategory::query()->findOrFail($categoryId);

        $this->editingId = $category->id;
        $this->editName = $category->name;
        $this->editKey = $category->key;
        $this->editParentId = $category->parent_id;
        $this->editSortOrder = $category->sort_order;
    }

    public function update(): void
    {
        $validated = $this->validate([
            'editName' => ['required', 'string', 'max:255'],
            'editKey' => ['required', 'string', 'max:255', 'unique:service_categories,key,'.$this->editingId],
            'editParentId' => ['nullable', 'integer', 'exists:service_categories,id'],
            'editSortOrder' => ['required', 'integer', 'min:0'],
        ]);

        $category = ServiceCategory::query()->findOrFail($this->editingId);

        $category->update([
            'name' => $validated['editName'],
            'key' => $validated['editKey'],
            'slug' => Str::slug($validated['editName']),
            'parent_id' => $validated['editParentId'] ? (int) $validated['editParentId'] : null,
            'sort_order' => (int) $validated['editSortOrder'],
        ]);

        $this->closeEditModal();
        $this->dispatch('toast', [['message' => __('Categoría actualizada correctamente.'), 'type' => 'success']]);
        $this->dispatch('category-updated');
    }

    public function closeEditModal(): void
    {
        $this->reset('editingId', 'editName', 'editKey', 'editParentId', 'editSortOrder');
    }

    public function confirmDelete(int $categoryId): void
    {
        $category = ServiceCategory::query()->findOrFail($categoryId);

        $this->deletingId = $category->id;
        $this->deletingName = $category->name;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            ServiceCategory::query()->findOrFail($this->deletingId)->delete();
            $this->closeDeleteModal();
            $this->dispatch('toast', [['message' => __('Categoría eliminada correctamente.'), 'type' => 'success']]);
            $this->dispatch('category-deleted');
        }
    }

    public function closeDeleteModal(): void
    {
        $this->reset('deletingId', 'deletingName');
    }

    public function render()
    {
        return view('livewire.admin.service-categories')
            ->layout('layouts.app', ['title' => __('Admin · Categorías')]);
    }
}
