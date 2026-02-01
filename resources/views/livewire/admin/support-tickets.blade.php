<section class="mx-auto w-full max-w-6xl space-y-6">
    <div class="flex items-end justify-between gap-4">
        <div class="space-y-2">
            <flux:heading size="lg">{{ __('Tickets de reversa') }}</flux:heading>
            <flux:text>{{ __('Aprobar o rechazar solicitudes de reembolso (solo Admin).') }}</flux:text>
        </div>
        <a href="{{ route('admin.dashboard') }}" wire:navigate>
            <flux:button>{{ __('Volver') }}</flux:button>
        </a>
    </div>

    <div class="rounded-xl border border-app-border bg-app-surface p-4">
        <flux:field>
            <flux:label>{{ __('Comentario admin (opcional, se aplica al siguiente Aprobar/Rechazar)') }}</flux:label>
            <flux:textarea wire:model="admin_comment" rows="2" placeholder="{{ __('Ej: Reembolso aprobado.') }}" />
        </flux:field>
    </div>

    <div class="space-y-3">
        @forelse ($tickets as $ticket)
            <div wire:key="ticket-{{ $ticket->id }}" class="flex flex-col gap-3 rounded-xl border border-app-border bg-app-surface p-4 md:flex-row md:items-center md:justify-between">
                <div class="space-y-1">
                    <flux:heading size="sm">{{ $ticket->user?->email }} · {{ $ticket->paymentSimulation?->serviceRequest?->title ?? __('Solicitud #:id', ['id' => $ticket->paymentSimulation?->service_request_id]) }}</flux:heading>
                    <flux:text class="text-sm">
                        ${{ \App\Helpers\ChileanDataHelper::formatChileanCurrency($ticket->paymentSimulation?->amount) }} ·
                        {{ $ticket->paymentSimulation?->paid_at?->format('d/m/Y') ?? '—' }} ·
                        @if ($ticket->status === \App\Models\SupportTicket::StatusPending)
                            <flux:badge variant="warning">{{ __('Pendiente') }}</flux:badge>
                        @elseif ($ticket->status === \App\Models\SupportTicket::StatusApproved)
                            <flux:badge variant="success">{{ __('Aprobado') }}</flux:badge>
                        @else
                            <flux:badge variant="danger">{{ __('Rechazado') }}</flux:badge>
                        @endif
                    </flux:text>
                    @if ($ticket->user_comment)
                        <flux:text class="text-xs text-app-muted">{{ __('Usuario') }}: {{ $ticket->user_comment }}</flux:text>
                    @endif
                </div>
                @if ($ticket->isPending())
                    <div class="flex gap-2">
                        <flux:button variant="primary" size="sm" wire:click="approve({{ $ticket->id }})" wire:confirm="{{ __('¿Aprobar reversa y acreditar saldo en Wallet?') }}">
                            {{ __('Aprobar') }}
                        </flux:button>
                        <flux:button variant="danger" size="sm" wire:click="reject({{ $ticket->id }})" wire:confirm="{{ __('¿Rechazar la solicitud de reversa?') }}">
                            {{ __('Rechazar') }}
                        </flux:button>
                    </div>
                @endif
            </div>
        @empty
            <flux:callout variant="info">{{ __('No hay tickets de reversa.') }}</flux:callout>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $tickets->links() }}
    </div>
</section>
