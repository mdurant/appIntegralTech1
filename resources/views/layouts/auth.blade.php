<x-layouts::auth.simple :title="$title ?? null">
    {{ $slot }}
    <x-toaster />
</x-layouts::auth.simple>
