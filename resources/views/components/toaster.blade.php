@php
    $initialToast = session('toast');
    $initialToasts = $initialToast ? [$initialToast] : [];
@endphp
<div
    x-data="toaster({{ json_encode($initialToasts) }})"
    x-init="
        Livewire.on('toast', (payload) => {
            if (payload && payload.length > 0) {
                const { message, type = 'success' } = payload[0];
                add(message, type);
            }
        });
    "
    aria-live="polite"
    class="pointer-events-none fixed top-4 right-4 z-[9999] flex max-w-sm flex-col gap-2 sm:top-6 sm:right-6"
>
    <template x-for="(toast, index) in toasts" :key="index">
        <div
            x-show="toast.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-full"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-full"
            :class="{
                'bg-success/95 text-white dark:bg-success': toast.type === 'success',
                'bg-danger/95 text-white dark:bg-danger': toast.type === 'error',
                'bg-warning/95 text-zinc-900 dark:bg-warning dark:text-zinc-900': toast.type === 'warning',
                'bg-info/95 text-white dark:bg-info': toast.type === 'info',
            }"
            class="pointer-events-auto flex items-center gap-3 rounded-xl px-4 py-3 shadow-lg ring-1 ring-black/5"
        >
            <span class="flex-1 text-sm font-medium" x-text="toast.message"></span>
            <button
                type="button"
                @click="remove(toast.id)"
                class="shrink-0 rounded p-1 opacity-80 hover:opacity-100"
                aria-label="{{ __('Cerrar') }}"
            >
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('toaster', (initialToasts = []) => ({
        toasts: initialToasts.map((t, i) => ({
            id: 'toast-' + Date.now() + '-' + i,
            message: t.message,
            type: t.type || 'success',
            visible: true,
        })),

        add(message, type = 'success') {
            const id = 'toast-' + Date.now() + '-' + Math.random().toString(36).slice(2);
            this.toasts.push({ id, message, type, visible: true });
            setTimeout(() => this.remove(id), 5000);
        },

        remove(id) {
            const t = this.toasts.find(x => x.id === id);
            if (t) t.visible = false;
            setTimeout(() => {
                this.toasts = this.toasts.filter(x => x.id !== id);
            }, 200);
        },
    }));
});
</script>
