<?php

namespace App\Livewire\Admin;

use App\Models\SupportTicket;
use App\Services\WalletService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

class SupportTickets extends Component
{
    use WithPagination;

    public string $admin_comment = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdministrator(), 403);
    }

    public function approve(int $ticketId): void
    {
        $ticket = SupportTicket::with(['user', 'paymentSimulation.serviceRequest'])
            ->where('status', SupportTicket::StatusPending)
            ->findOrFail($ticketId);

        $payment = $ticket->paymentSimulation;
        $amount = (float) $payment->amount;
        $user = $ticket->user;

        try {
            app(WalletService::class)->creditForRefund(
                $user,
                $amount,
                $ticket->id,
                __('Reembolso por reversa aprobada (compra: :title)', ['title' => $payment->serviceRequest?->title ?? '#'.$payment->service_request_id])
            );
        } catch (\RuntimeException $e) {
            $this->dispatch('toast', [['message' => $e->getMessage(), 'type' => 'error']]);

            return;
        }

        $adminComment = $this->admin_comment ?: null;
        $ticket->update([
            'status' => SupportTicket::StatusApproved,
            'resolved_by_user_id' => auth()->id(),
            'admin_comment' => $adminComment,
            'resolved_at' => now(),
        ]);

        $user = $ticket->user;
        $payment = $ticket->paymentSimulation;
        Mail::send('emails.support-ticket-approved', [
            'user' => $user,
            'payment' => $payment,
            'amount' => $amount,
            'adminComment' => $adminComment,
        ], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                ->subject(__('Reversa aprobada - Saldo acreditado en Wallet'));
        });

        $this->admin_comment = '';
        $this->dispatch('toast', [['message' => __('Reversa aprobada y saldo acreditado.'), 'type' => 'success']]);
    }

    public function reject(int $ticketId): void
    {
        $ticket = SupportTicket::with(['user', 'paymentSimulation.serviceRequest'])
            ->where('status', SupportTicket::StatusPending)
            ->findOrFail($ticketId);

        $adminComment = $this->admin_comment ?: null;
        $ticket->update([
            'status' => SupportTicket::StatusRejected,
            'resolved_by_user_id' => auth()->id(),
            'admin_comment' => $adminComment,
            'resolved_at' => now(),
        ]);

        $user = $ticket->user;
        $payment = $ticket->paymentSimulation;
        Mail::send('emails.support-ticket-rejected', [
            'user' => $user,
            'payment' => $payment,
            'adminComment' => $adminComment,
        ], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                ->subject(__('Solicitud de reversa rechazada'));
        });

        $this->admin_comment = '';
        $this->dispatch('toast', [['message' => __('Reversa rechazada.'), 'type' => 'success']]);
    }

    public function getTicketsProperty(): LengthAwarePaginator
    {
        return SupportTicket::with(['user', 'paymentSimulation.serviceRequest'])
            ->latest('id')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.support-tickets', [
            'tickets' => $this->tickets,
        ])
            ->layout('layouts.app', ['title' => __('Admin · Tickets de reversa')]);
    }
}
