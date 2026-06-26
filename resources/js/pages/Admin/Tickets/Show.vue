<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Send } from '@lucide/vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Spinner } from '@/components/ui/spinner';

interface Reply {
    id: number;
    message: string;
    is_staff: boolean;
    created_at: string;
    user?: { name: string } | null;
}

interface AdminTicket {
    id: number;
    ticket_number: string;
    subject: string;
    priority: string;
    status: string;
    created_at: string;
    user?: { name: string; email: string } | null;
    department?: { name: string } | null;
    replies: Reply[];
}

const props = defineProps<{
    ticket: AdminTicket;
    staff: { id: number; name: string }[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Tickets', href: '/admin/tickets' },
            { title: 'Detalhes', href: '#' },
        ],
    }),
});

const statusLabel: Record<string, string> = {
    open: 'Aberto',
    in_progress: 'Em progresso',
    waiting_customer: 'Aguardando cliente',
    waiting_staff: 'Aguardando equipe',
    closed: 'Fechado',
};

const statusColor: Record<string, string> = {
    open: 'blue',
    in_progress: 'yellow',
    waiting_customer: 'orange',
    waiting_staff: 'purple',
    closed: 'gray',
};

const priorityLabel: Record<string, string> = {
    low: 'Baixa',
    medium: 'Média',
    high: 'Alta',
    urgent: 'Urgente',
};

const formatDate = (date: string) => new Date(date).toLocaleString('pt-BR');
</script>

<template>
    <Head :title="props.ticket.subject" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            :title="props.ticket.subject"
            :description="`${props.ticket.ticket_number} · ${props.ticket.user?.name ?? 'Cliente'}`"
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/tickets">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
                <Form
                    v-if="props.ticket.status !== 'closed'"
                    :action="`/admin/tickets/${props.ticket.id}/close`"
                    method="post"
                >
                    <Button type="submit" variant="outline">Fechar ticket</Button>
                </Form>
                <Form
                    v-else
                    :action="`/admin/tickets/${props.ticket.id}/reopen`"
                    method="post"
                >
                    <Button type="submit" variant="outline">Reabrir</Button>
                </Form>
            </template>
        </PageHeader>

        <div class="flex flex-wrap items-center gap-2">
            <StatusBadge
                :label="statusLabel[props.ticket.status] ?? props.ticket.status"
                :color="statusColor[props.ticket.status] ?? 'gray'"
            />
            <StatusBadge
                :label="`Prioridade: ${priorityLabel[props.ticket.priority] ?? props.ticket.priority}`"
                color="blue"
            />
            <span
                v-if="props.ticket.department"
                class="text-xs text-muted-foreground"
            >
                {{ props.ticket.department.name }}
            </span>
        </div>

        <Card>
            <CardHeader>
                <CardTitle class="text-base">Conversa</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <div
                    v-for="reply in props.ticket.replies"
                    :key="reply.id"
                    :class="[
                        'rounded-lg border p-4',
                        reply.is_staff
                            ? 'border-emerald-600/20 bg-emerald-50/50 dark:bg-emerald-950/20'
                            : 'bg-muted/40',
                    ]"
                >
                    <div class="mb-1 flex items-center justify-between">
                        <span class="text-sm font-semibold">
                            {{ reply.user?.name ?? 'Usuário' }}
                            <span
                                v-if="reply.is_staff"
                                class="ml-1 text-xs font-normal text-emerald-600"
                                >(equipe)</span
                            >
                        </span>
                        <span class="text-xs text-muted-foreground">
                            {{ formatDate(reply.created_at) }}
                        </span>
                    </div>
                    <p class="whitespace-pre-line text-sm text-muted-foreground">
                        {{ reply.message }}
                    </p>
                </div>

                <p
                    v-if="props.ticket.replies.length === 0"
                    class="py-4 text-center text-sm text-muted-foreground"
                >
                    Nenhuma resposta ainda.
                </p>
            </CardContent>
        </Card>

        <Card v-if="props.ticket.status !== 'closed'">
            <CardHeader>
                <CardTitle class="text-base">Responder</CardTitle>
            </CardHeader>
            <CardContent>
                <Form
                    :action="`/admin/tickets/${props.ticket.id}/reply`"
                    method="post"
                    :reset-on-success="['message']"
                    v-slot="{ processing }"
                    class="flex flex-col gap-3"
                >
                    <textarea
                        name="message"
                        rows="4"
                        required
                        placeholder="Escreva sua resposta..."
                        class="rounded-md border bg-transparent px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                    ></textarea>
                    <div class="flex justify-end">
                        <Button type="submit" :disabled="processing">
                            <Spinner v-if="processing" />
                            <Send v-else :size="16" />
                            Enviar resposta
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>
