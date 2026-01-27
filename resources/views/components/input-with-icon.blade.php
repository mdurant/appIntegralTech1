@props([
    'icon',
    'label' => null,
    'hint' => null,
])

<div class="input-group-wrapper">
    @if ($label)
        <label class="block text-sm font-medium text-app-text mb-2">{{ $label }}</label>
    @endif
    
    <div class="input-group">
        <div class="input-group-prefix">
            {!! $icon !!}
        </div>
        <div class="flex-1">
            {{ $slot }}
        </div>
    </div>
    
    @if ($hint)
        <p class="mt-1 text-xs text-app-muted">{{ $hint }}</p>
    @endif
</div>
