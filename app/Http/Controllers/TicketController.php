<?php

namespace App\Http\Controllers;

use App\Enums\TicketPriority;
use App\Models\Ticket;
use App\Models\TicketDepartment;
use App\Models\TicketReply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TicketController extends Controller
{
    /**
     * List the authenticated user's support tickets.
     */
    public function index(Request $request): Response
    {
        $tickets = Ticket::query()
            ->where('user_id', $request->user()->id)
            ->with('department')
            ->latest()
            ->paginate(15)
            ->through(fn (Ticket $ticket) => [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'department' => $ticket->department?->name,
                'priority' => $ticket->priority->value,
                'priority_label' => $ticket->priority->label(),
                'status' => $ticket->status->value,
                'status_label' => $ticket->status->label(),
                'status_color' => $ticket->status->color(),
                'last_reply_at' => $ticket->last_reply_at?->toIso8601String(),
                'created_at' => $ticket->created_at?->toIso8601String(),
            ]);

        return Inertia::render('Tickets/Index', [
            'tickets' => $tickets,
        ]);
    }

    /**
     * Show the form for opening a new ticket.
     */
    public function create(): Response
    {
        return Inertia::render('Tickets/Create', [
            'departments' => TicketDepartment::active()->get(['id', 'name']),
            'priorities' => collect(TicketPriority::cases())->map(fn (TicketPriority $p) => [
                'value' => $p->value,
                'label' => $p->label(),
            ]),
        ]);
    }

    /**
     * Store a newly opened ticket plus its first message.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'department_id' => ['nullable', 'exists:ticket_departments,id'],
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
            'message' => ['required', 'string'],
        ]);

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'department_id' => $validated['department_id'] ?? null,
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'status' => 'open',
            'last_reply_at' => now(),
        ]);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
            'is_staff' => false,
        ]);

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Ticket aberto com sucesso.');
    }

    /**
     * Show a single ticket with its conversation thread.
     */
    public function show(Request $request, Ticket $ticket): Response
    {
        abort_unless((int) $ticket->user_id === $request->user()->id, 403);

        $ticket->load(['department', 'replies.user']);

        return Inertia::render('Tickets/Show', [
            'ticket' => [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'department' => $ticket->department?->name,
                'priority_label' => $ticket->priority->label(),
                'status' => $ticket->status->value,
                'status_label' => $ticket->status->label(),
                'status_color' => $ticket->status->color(),
                'is_closed' => $ticket->status->value === 'closed',
                'created_at' => $ticket->created_at?->toIso8601String(),
                'replies' => $ticket->replies->map(fn (TicketReply $reply) => [
                    'id' => $reply->id,
                    'message' => $reply->message,
                    'is_staff' => $reply->is_staff,
                    'author' => $reply->user?->name,
                    'created_at' => $reply->created_at?->toIso8601String(),
                ]),
            ],
        ]);
    }

    /**
     * Post a customer reply to one of their tickets.
     */
    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        abort_unless((int) $ticket->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'message' => ['required', 'string'],
        ]);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
            'is_staff' => false,
        ]);

        return back()->with('success', 'Resposta enviada.');
    }
}
