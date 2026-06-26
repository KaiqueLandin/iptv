<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { CreditCard, Eye, Search } from '@lucide/vue';
import { ref, watch } from 'vue';
import DataTable from '@/components/admin/DataTable.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import Pagination from '@/components/admin/Pagination.vue';
import StatCard from '@/components/admin/StatCard.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Input } from '@/components/ui/input';
import type { Paginator } from '@/types';

interface Payment {
    id: number;
    transaction_id: string;
    amount: string;
    status: string;
    payment_method: string | null;
    user?: { name: string } | null;
    gateway?: { name: string } | null;
}

interface Stats {
    total_completed: number;
    total_pending: number;
    total_fees: number;
}

const props = defineProps<{
    payments: Paginator<Payment>;
    stats: Stats;
    filters: { search?: string; status?: string };
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Pagamentos', href: '/admin/payments' },
        ],
    }),
});

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');

let timer: ReturnType<typeof setTimeout>;
watch([search, status], () => {
    clearTimeout(timer);
    timer = setTimeout(() => {
        router.get(
            '/admin/payments',
            {
                search: search.value || undefined,
                status: status.value || undefined,
            },
            { preserveState: true, replace: true, preserveScroll: true },
        );
    }, 350);
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

const brl = (value: number | string) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(Number(value));
</script>

<template>
    <Head title="Pagamentos" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Pagamentos"
            description="Histórico de transações processadas."
        />

        <div class="grid gap-4 sm:grid-cols-3">
            <StatCard
                label="Total recebido"
                :value="brl(props.stats.total_completed)"
                tone="emerald"
            />
            <StatCard
                label="Pendente"
                :value="brl(props.stats.total_pending)"
                tone="amber"
            />
            <StatCard
                label="Taxas pagas"
                :value="brl(props.stats.total_fees)"
                tone="rose"
            />
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <Search
                    :size="16"
                    class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="search"
                    placeholder="Buscar por transação ou cliente..."
                    class="pl-9"
                />
            </div>
            <select
                v-model="status"
                class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring sm:w-48"
            >
                <option value="">Todos os status</option>
                <option value="completed">Completo</option>
                <option value="pending">Pendente</option>
                <option value="failed">Falhou</option>
                <option value="refunded">Reembolsado</option>
            </select>
        </div>

        <EmptyState
            v-if="payments.data.length === 0"
            title="Nenhum pagamento encontrado"
            :icon="CreditCard"
        />

        <DataTable
            v-else
            :columns="['Transação', 'Cliente', 'Gateway', 'Valor', 'Status', '']"
        >
            <tr
                v-for="payment in payments.data"
                :key="payment.id"
                class="transition-colors hover:bg-muted/40"
            >
                <td class="px-4 py-3 font-mono text-xs font-medium">
                    {{ payment.transaction_id }}
                </td>
                <td class="px-4 py-3">{{ payment.user?.name ?? '—' }}</td>
                <td class="px-4 py-3 text-muted-foreground">
                    {{ payment.gateway?.name ?? payment.payment_method ?? '—' }}
                </td>
                <td class="px-4 py-3 font-semibold">{{ brl(payment.amount) }}</td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="statusLabel[payment.status] ?? payment.status"
                        :color="statusColor[payment.status] ?? 'gray'"
                    />
                </td>
                <td class="px-4 py-3 text-right">
                    <Link
                        :href="`/admin/payments/${payment.id}`"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-muted-foreground transition-colors hover:bg-accent"
                    >
                        <Eye :size="15" />
                    </Link>
                </td>
            </tr>
        </DataTable>

        <Pagination :meta="payments" />
    </div>
</template>
