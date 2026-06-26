<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { LifeBuoy, MessageSquare, Plus } from '@lucide/vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';

interface TicketItem {
    id: number;
    ticket_number: string;
    subject: string;
    department: string | null;
    priority: string;
    priority_label: string;
    status: string;
    status_label: string;
    status_color: string;
    last_reply_at: string | null;
    created_at: string | null;
}

interface Paginated<T> {
    data: T[];
}

defineProps<{
    tickets: Paginated<TicketItem>;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [{ title: 'Suporte', href: '/tickets' }],
    }),
});

const formatDate = (date: string | null) =>
    date ? new Date(date).toLocaleString('pt-BR') : '—';
</script>

<template>
    <Head title="Suporte" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Suporte</h1>
                <p class="text-sm text-muted-foreground">
                    Abra um chamado e acompanhe suas conversas com nossa equipe.
                </p>
            </div>
            <Button as-child>
                <Link href="/tickets/create">
                    <Plus :size="16" /> Abrir ticket
                </Link>
            </Button>
        </div>

        <div
            v-if="tickets.data.length === 0"
            class="flex flex-col items-center justify-center rounded-lg border border-dashed py-16 text-center"
        >
            <div
                class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-muted text-muted-foreground"
            >
                <LifeBuoy :size="22" />
            </div>
            <p class="text-sm font-medium">Nenhum ticket ainda</p>
            <p class="mt-1 text-xs text-muted-foreground">
                Precisa de ajuda? Abra seu primeiro chamado.
            </p>
            <Button as-child class="mt-4">
                <Link href="/tickets/create">Abrir ticket</Link>
            </Button>
        </div>

        <div v-else class="grid gap-4">
            <Card v-for="ticket in tickets.data" :key="ticket.id">
                <CardContent
                    class="flex flex-col gap-4 py-5 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-muted text-muted-foreground"
                        >
                            <MessageSquare :size="18" />
                        </div>
                        <div>
                            <p class="font-medium">{{ ticket.subject }}</p>
                            <p class="text-xs text-muted-foreground">
                                {{ ticket.ticket_number }} ·
                                {{ ticket.department ?? 'Geral' }} ·
                                {{ formatDate(ticket.created_at) }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <StatusBadge
                            :label="ticket.status_label"
                            :color="ticket.status_color"
                        />
                        <Button as-child variant="outline" size="sm">
                            <Link :href="`/tickets/${ticket.id}`">Abrir</Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
