<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Send } from '@lucide/vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Spinner } from '@/components/ui/spinner';
import { reply } from '@/routes/tickets';

interface Reply {
    id: number;
    message: string;
    is_staff: boolean;
    author: string | null;
    created_at: string | null;
}

interface CustomerTicket {
    id: number;
    ticket_number: string;
    subject: string;
    department: string | null;
    priority_label: string;
    status: string;
    status_label: string;
    status_color: string;
    is_closed: boolean;
    created_at: string | null;
    replies: Reply[];
}

const props = defineProps<{
    ticket: CustomerTicket;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Suporte', href: '/tickets' },
            { title: 'Detalhes', href: '#' },
        ],
    }),
});

const formatDate = (date: string | null) =>
    date ? new Date(date).toLocaleString('pt-BR') : '—';
</script>

<template>
    <Head :title="props.ticket.subject" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">
                    {{ props.ticket.subject }}
                </h1>
                <p class="text-sm text-muted-foreground">
                    {{ props.ticket.ticket_number }} ·
                    {{ props.ticket.department ?? 'Geral' }} ·
                    {{ formatDate(props.ticket.created_at) }}
                </p>
            </div>
            <Button as-child variant="outline">
                <Link href="/tickets">
                    <ArrowLeft :size="16" /> Voltar
                </Link>
            </Button>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <StatusBadge
                :label="props.ticket.status_label"
                :color="props.ticket.status_color"
            />
            <StatusBadge
                :label="`Prioridade: ${props.ticket.priority_label}`"
                color="blue"
            />
        </div>

        <Card>
            <CardHeader>
                <CardTitle class="text-base">Conversa</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <div
                    v-for="message in props.ticket.replies"
                    :key="message.id"
                    :class="[
                        'rounded-lg border p-4',
                        message.is_staff
                            ? 'border-emerald-600/20 bg-emerald-50/50 dark:bg-emerald-950/20'
                            : 'bg-muted/40',
                    ]"
                >
                    <div class="mb-1 flex items-center justify-between">
                        <span class="text-sm font-semibold">
                            {{ message.author ?? 'Você' }}
                            <span
                                v-if="message.is_staff"
                                class="ml-1 text-xs font-normal text-emerald-600"
                                >(equipe)</span
                            >
                        </span>
                        <span class="text-xs text-muted-foreground">
                            {{ formatDate(message.created_at) }}
                        </span>
                    </div>
                    <p class="whitespace-pre-line text-sm text-muted-foreground">
                        {{ message.message }}
                    </p>
                </div>

                <p
                    v-if="props.ticket.replies.length === 0"
                    class="py-4 text-center text-sm text-muted-foreground"
                >
                    Nenhuma mensagem ainda.
                </p>
            </CardContent>
        </Card>

        <Card v-if="!props.ticket.is_closed">
            <CardHeader>
                <CardTitle class="text-base">Responder</CardTitle>
            </CardHeader>
            <CardContent>
                <Form
                    v-bind="reply.form(props.ticket.id)"
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

        <p
            v-else
            class="rounded-lg border border-dashed py-4 text-center text-sm text-muted-foreground"
        >
            Este ticket está fechado.
        </p>
    </div>
</template>
