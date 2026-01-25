<?php

namespace App\Livewire\Admin;

use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ServiceCategories extends Component
{
    public string $name = '';

    public string $key = '';

    public int|string|null $parentId = null;

    public int $sortOrder = 0;

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdministrator(), 403);
    }

    #[Computed]
    public function topCategories(): Collection
    {
        return ServiceCategory::query()
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function allCategories(): Collection
    {
        return ServiceCategory::query()
            ->orderBy('parent_id')
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

        ServiceCategory::create([
            'name' => $validated['name'],
            'key' => $validated['key'],
            'parent_id' => $validated['parentId'] ? (int) $validated['parentId'] : null,
            'sort_order' => (int) $validated['sortOrder'],
        ]);

        $this->reset('name', 'key', 'parentId', 'sortOrder');
    }

    public function delete(int $categoryId): void
    {
        ServiceCategory::query()->findOrFail($categoryId)->delete();
    }

    public function render()
    {
        return view('livewire.admin.service-categories')
            ->layout('layouts.app', ['title' => __('Admin · Categorías')]);
    }
}

