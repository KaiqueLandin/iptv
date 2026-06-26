<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketDepartment;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AdminTicketController extends Controller
{
    public function index(Request $request): Response
    {
        $tickets = Ticket::query()
            ->with(['user', 'department', 'assignedTo'])
            ->when($request->search, function ($query, $search) {
                $query->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->priority, function ($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when($request->department_id, function ($query, $deptId) {
                $query->where('department_id', $deptId);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Tickets/Index', [
            'tickets' => $tickets,
            'filters' => $request->only(['search', 'status', 'priority', 'department_id']),
            'departments' => TicketDepartment::active()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Tickets/Create', [
            'departments' => TicketDepartment::active()->get(['id', 'name']),
            'priorities' => collect(TicketPriority::cases())->map(fn (TicketPriority $p) => [
                'value' => $p->value,
                'label' => $p->label(),
            ]),
            'users' => User::select('id', 'name', 'email')->orderBy('name')->get(),
            'staff' => User::where('role', '!=', 'user')->select('id', 'name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'subject' => ['required', 'string', 'max:255'],
            'department_id' => ['nullable', 'exists:ticket_departments,id'],
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'message' => ['required', 'string'],
        ]);

        $ticket = Ticket::create([
            'user_id' => $validated['user_id'],
            'department_id' => $validated['department_id'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'status' => TicketStatus::OPEN->value,
            'last_reply_at' => now(),
        ]);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_staff' => true,
        ]);

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Ticket criado com sucesso.');
    }

    public function show(Ticket $ticket): Response
    {
        $ticket->load(['user', 'department', 'assignedTo', 'replies.user']);

        return Inertia::render('Admin/Tickets/Show', [
            'ticket' => $ticket,
            'staff' => User::where('role', '!=', 'user')->select('id', 'name')->get(),
        ]);
    }

    public function edit(Ticket $ticket): Response
    {
        return Inertia::render('Admin/Tickets/Edit', [
            'ticket' => $ticket->load(['user', 'department', 'assignedTo']),
            'departments' => TicketDepartment::active()->get(['id', 'name']),
            'priorities' => collect(TicketPriority::cases())->map(fn (TicketPriority $p) => [
                'value' => $p->value,
                'label' => $p->label(),
            ]),
            'statuses' => collect(TicketStatus::cases())->map(fn (TicketStatus $s) => [
                'value' => $s->value,
                'label' => $s->label(),
            ]),
            'staff' => User::where('role', '!=', 'user')->select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'string', Rule::in(array_column(TicketStatus::cases(), 'value'))],
            'priority' => ['nullable', 'string', Rule::in(array_column(TicketPriority::cases(), 'value'))],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'department_id' => ['nullable', 'exists:ticket_departments,id'],
        ]);

        $ticket->update($validated);

        return back()->with('success', 'Ticket atualizado com sucesso.');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $ticket->replies()->delete();
        $ticket->delete();

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket deletado com sucesso.');
    }

    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string'],
            'attachments' => ['nullable', 'array'],
        ]);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_staff' => true,
            'attachments' => $validated['attachments'] ?? null,
        ]);

        return back()->with('success', 'Resposta enviada com sucesso.');
    }

    public function close(Ticket $ticket): RedirectResponse
    {
        $ticket->close();

        return back()->with('success', 'Ticket fechado com sucesso.');
    }

    public function reopen(Ticket $ticket): RedirectResponse
    {
        $ticket->reopen();

        return back()->with('success', 'Ticket reaberto com sucesso.');
    }
}
