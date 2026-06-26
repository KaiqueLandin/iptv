<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Payment {
    id: number;
    transaction_id: string;
    gateway_transaction_id: string | null;
    amount: string;
    fee: string;
    net_amount: string;
    status: string;
    payment_method: string | null;
    completed_at: string | null;
    created_at: string;
    user?: { name: string; email: string } | null;
    gateway?: { name: string } | null;
    invoice?: { id: number; invoice_number: string } | null;
}

const props = defineProps<{
    payment: Payment;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Pagamentos', href: '/admin/payments' },
            { title: 'Detalhes', href: '#' },
        ],
    }),
});

const statusLabel: Record<string, string> = {
    pending: 'Pendente',
    processing: 'Processando',
    completed: 'Completo',
    failed: 'Falhou',
    refunded: 'Reembolsado',
    cancelled: 'Cancelado',
};

const statusColor: Record<string, string> = {
    pending: 'yellow',
    processing: 'blue',
    completed: 'green',
    failed: 'red',
    refunded: 'purple',
    cancelled: 'gray',
};

const brl = (value: string) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(Number(value));

const formatDate = (date: string | null) =>
    date ? new Date(date).toLocaleString('pt-BR') : '—';
</script>

<template>
    <Head :title="props.payment.transaction_id" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            :title="props.payment.transaction_id"
            :description="props.payment.user?.name ?? 'Cliente'"
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/payments">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <Card class="max-w-2xl">
            <CardHeader>
                <CardTitle class="flex items-center justify-between text-base">
                    Detalhes da transação
                    <StatusBadge
                        :label="statusLabel[props.payment.status] ?? props.payment.status"
                        :color="statusColor[props.payment.status] ?? 'gray'"
                    />
                </CardTitle>
            </CardHeader>
            <CardContent>
                <dl class="divide-y divide-border text-sm">
                    <div class="flex justify-between py-3 first:pt-0">
                        <dt class="text-muted-foreground">Valor</dt>
                        <dd class="font-semibold">{{ brl(props.payment.amount) }}</dd>
                    </div>
                    <div class="flex justify-between py-3">
                        <dt class="text-muted-foreground">Taxa</dt>
                        <dd>{{ brl(props.payment.fee) }}</dd>
                    </div>
                    <div class="flex justify-between py-3">
                        <dt class="text-muted-foreground">Valor líquido</dt>
                        <dd class="font-semibold">{{ brl(props.payment.net_amount) }}</dd>
                    </div>
                    <div class="flex justify-between py-3">
                        <dt class="text-muted-foreground">Gateway</dt>
                        <dd>{{ props.payment.gateway?.name ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between py-3">
                        <dt class="text-muted-foreground">Método</dt>
                        <dd>{{ props.payment.payment_method ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between py-3">
                        <dt class="text-muted-foreground">ID no gateway</dt>
                        <dd class="font-mono text-xs">
                            {{ props.payment.gateway_transaction_id ?? '—' }}
                        </dd>
                    </div>
                    <div class="flex justify-between py-3">
                        <dt class="text-muted-foreground">Fatura</dt>
                        <dd>
                            <Link
                                v-if="props.payment.invoice"
                                :href="`/admin/invoices/${props.payment.invoice.id}`"
                                class="text-emerald-600 hover:underline"
                            >
                                {{ props.payment.invoice.invoice_number }}
                            </Link>
                            <span v-else>—</span>
                        </dd>
                    </div>
                    <div class="flex justify-between py-3 last:pb-0">
                        <dt class="text-muted-foreground">Concluído em</dt>
                        <dd>{{ formatDate(props.payment.completed_at) }}</dd>
                    </div>
                </dl>
            </CardContent>
        </Card>
    </div>
</template>
