<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2 } from '@lucide/vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Spinner } from '@/components/ui/spinner';

interface InvoiceItem {
    id: number;
    description: string;
    quantity: number;
    unit_price: string;
    amount: string;
}

interface AdminInvoice {
    id: number;
    invoice_number: string;
    status: string;
    subtotal: string;
    tax: string;
    discount: string;
    total: string;
    due_date: string;
    paid_at: string | null;
    notes: string | null;
    user?: { id: number; name: string; email: string } | null;
    items: InvoiceItem[];
}

const props = defineProps<{
    invoice: AdminInvoice;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Faturas', href: '/admin/invoices' },
            { title: 'Detalhes', href: '#' },
        ],
    }),
});

const statusLabel: Record<string, string> = {
    draft: 'Rascunho',
    unpaid: 'Não paga',
    paid: 'Paga',
    cancelled: 'Cancelada',
    refunded: 'Reembolsada',
    overdue: 'Vencida',
};

const statusColor: Record<string, string> = {
    draft: 'gray',
    unpaid: 'yellow',
    paid: 'green',
    cancelled: 'red',
    refunded: 'purple',
    overdue: 'red',
};

const brl = (value: string) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(Number(value));

const formatDate = (date: string | null) =>
    date ? new Date(date).toLocaleDateString('pt-BR') : '—';
</script>

<template>
    <Head :title="props.invoice.invoice_number" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            :title="props.invoice.invoice_number"
            :description="props.invoice.user?.name ?? 'Cliente'"
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/invoices">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
                <Form
                    v-if="props.invoice.status !== 'paid'"
                    :action="`/admin/invoices/${props.invoice.id}/mark-paid`"
                    method="post"
                    v-slot="{ processing }"
                >
                    <Button type="submit" :disabled="processing">
                        <Spinner v-if="processing" />
                        <CheckCircle2 v-else :size="16" />
                        Marcar como paga
                    </Button>
                </Form>
            </template>
        </PageHeader>

        <div class="grid gap-6 lg:grid-cols-3">
            <Card class="lg:col-span-2">
                <CardHeader class="flex-row items-center justify-between">
                    <CardTitle class="text-base">Itens da fatura</CardTitle>
                    <StatusBadge
                        :label="statusLabel[props.invoice.status] ?? props.invoice.status"
                        :color="statusColor[props.invoice.status] ?? 'gray'"
                    />
                </CardHeader>
                <CardContent>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-left text-xs uppercase text-muted-foreground">
                                <th class="pb-2">Descrição</th>
                                <th class="pb-2 text-center">Qtd</th>
                                <th class="pb-2 text-right">Unitário</th>
                                <th class="pb-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr
                                v-for="item in props.invoice.items"
                                :key="item.id"
                            >
                                <td class="py-2">{{ item.description }}</td>
                                <td class="py-2 text-center">{{ item.quantity }}</td>
                                <td class="py-2 text-right">{{ brl(item.unit_price) }}</td>
                                <td class="py-2 text-right font-medium">{{ brl(item.amount) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <dl class="mt-4 space-y-1 border-t pt-4 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-muted-foreground">Subtotal</dt>
                            <dd>{{ brl(props.invoice.subtotal) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted-foreground">Impostos</dt>
                            <dd>{{ brl(props.invoice.tax) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted-foreground">Desconto</dt>
                            <dd>− {{ brl(props.invoice.discount) }}</dd>
                        </div>
                        <div class="flex justify-between border-t pt-2 text-base font-bold">
                            <dt>Total</dt>
                            <dd>{{ brl(props.invoice.total) }}</dd>
                        </div>
                    </dl>
                </CardContent>
            </Card>

            <Card class="h-fit">
                <CardHeader>
                    <CardTitle class="text-base">Informações</CardTitle>
                </CardHeader>
                <CardContent>
                    <dl class="divide-y divide-border text-sm">
                        <div class="flex justify-between py-3 first:pt-0">
                            <dt class="text-muted-foreground">Cliente</dt>
                            <dd>{{ props.invoice.user?.name ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between py-3">
                            <dt class="text-muted-foreground">Vencimento</dt>
                            <dd>{{ formatDate(props.invoice.due_date) }}</dd>
                        </div>
                        <div class="flex justify-between py-3 last:pb-0">
                            <dt class="text-muted-foreground">Pago em</dt>
                            <dd>{{ formatDate(props.invoice.paid_at) }}</dd>
                        </div>
                    </dl>
                    <p
                        v-if="props.invoice.notes"
                        class="mt-3 rounded-md bg-muted p-3 text-xs text-muted-foreground"
                    >
                        {{ props.invoice.notes }}
                    </p>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
