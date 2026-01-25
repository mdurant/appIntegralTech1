<?php

namespace App\Livewire\Admin;

use App\Models\ServiceCategory;
use App\Models\ServiceFormField;
use App\Models\ServiceFormFieldOption;
use App\ServiceFormFieldType;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ServiceFormFields extends Component
{
    public int|string|null $subcategoryId = null;

    public string $key = '';

    public string $label = '';

    public string $type = 'text';

    public bool $required = false;

    public int $sortOrder = 0;

    public string $optionValue = '';

    public string $optionLabel = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdministrator(), 403);
    }

    #[Computed]
    public function subcategories(): Collection
    {
        return ServiceCategory::query()
            ->whereNotNull('parent_id')
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function fields(): Collection
    {
        if (! $this->subcategoryId) {
            return ServiceFormField::query()->whereRaw('1=0')->get();
        }

        return ServiceFormField::query()
            ->where('service_category_id', (int) $this->subcategoryId)
            ->with('options')
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();
    }

    public function createField(): void
    {
        $validated = $this->validate([
            'subcategoryId' => ['required', 'integer', 'exists:service_categories,id'],
            'key' => ['required', 'string', 'max:255'],
            'label' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:select,text,number,textarea,date'],
            'required' => ['boolean'],
            'sortOrder' => ['required', 'integer', 'min:0'],
        ]);

        ServiceFormField::query()->create([
            'service_category_id' => (int) $validated['subcategoryId'],
            'key' => $validated['key'],
            'label' => $validated['label'],
            'type' => $validated['type'],
            'required' => (bool) $validated['required'],
            'sort_order' => (int) $validated['sortOrder'],
        ]);

        $this->reset('key', 'label', 'type', 'required', 'sortOrder');
    }

    public function deleteField(int $fieldId): void
    {
        ServiceFormField::query()->findOrFail($fieldId)->delete();
    }

    public function addOption(int $fieldId): void
    {
        $field = ServiceFormField::query()->findOrFail($fieldId);

        abort_unless($field->type === ServiceFormFieldType::Select, 422);

        $validated = $this->validate([
            'optionValue' => ['required', 'string', 'max:255'],
            'optionLabel' => ['required', 'string', 'max:255'],
        ]);

        ServiceFormFieldOption::query()->create([
            'service_form_field_id' => $field->id,
            'value' => $validated['optionValue'],
            'label' => $validated['optionLabel'],
            'sort_order' => 0,
        ]);

        $this->reset('optionValue', 'optionLabel');
    }

    public function deleteOption(int $optionId): void
    {
        ServiceFormFieldOption::query()->findOrFail($optionId)->delete();
    }

    public function render()
    {
        return view('livewire.admin.service-form-fields', [
            'types' => ServiceFormFieldType::cases(),
        ])->layout('layouts.app', ['title' => __('Admin · Formulario')]);
    }
}

