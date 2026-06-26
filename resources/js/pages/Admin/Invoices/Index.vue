<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, Receipt, Search } from '@lucide/vue';
import { ref, watch } from 'vue';
import DataTable from '@/components/admin/DataTable.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import Pagination from '@/components/admin/Pagination.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Input } from '@/components/ui/input';
import type { Paginator } from '@/types';

interface AdminInvoice {
    id: number;
    invoice_number: string;
    status: string;
    total: string;
    due_date: string;
    user?: { name: string } | null;
}

const props = defineProps<{
    invoices: Paginator<AdminInvoice>;
    filters: { search?: string; status?: string };
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Faturas', href: '/admin/invoices' },
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
            '/admin/invoices',
            {
                search: search.value || undefined,
                status: status.value || undefined,
            },
            { preserveState: true, replace: true, preserveScroll: true },
        );
    }, 350);
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

const formatDate = (date: string) =>
    new Date(date).toLocaleDateString('pt-BR');
</script>

<template>
    <Head title="Faturas" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Faturas"
            description="Controle as cobranças e pagamentos dos clientes."
        />

        <div class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <Search
                    :size="16"
                    class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="search"
                    placeholder="Buscar por número ou cliente..."
                    class="pl-9"
                />
            </div>
            <select
                v-model="status"
                class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring sm:w-48"
            >
                <option value="">Todos os status</option>
                <option value="unpaid">Não paga</option>
                <option value="paid">Paga</option>
                <option value="overdue">Vencida</option>
                <option value="cancelled">Cancelada</option>
                <option value="refunded">Reembolsada</option>
            </select>
        </div>

        <EmptyState
            v-if="invoices.data.length === 0"
            title="Nenhuma fatura encontrada"
            :icon="Receipt"
        />

        <DataTable
            v-else
            :columns="['Fatura', 'Cliente', 'Vencimento', 'Total', 'Status', '']"
        >
            <tr
                v-for="invoice in invoices.data"
                :key="invoice.id"
                class="transition-colors hover:bg-muted/40"
            >
                <td class="px-4 py-3 font-medium">
                    {{ invoice.invoice_number }}
                </td>
                <td class="px-4 py-3">{{ invoice.user?.name ?? '—' }}</td>
                <td class="px-4 py-3 text-muted-foreground">
                    {{ formatDate(invoice.due_date) }}
                </td>
                <td class="px-4 py-3 font-semibold">{{ brl(invoice.total) }}</td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="statusLabel[invoice.status] ?? invoice.status"
                        :color="statusColor[invoice.status] ?? 'gray'"
                    />
                </td>
                <td class="px-4 py-3 text-right">
                    <Link
                        :href="`/admin/invoices/${invoice.id}`"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-muted-foreground transition-colors hover:bg-accent"
                    >
                        <Eye :size="15" />
                    </Link>
                </td>
            </tr>
        </DataTable>

        <Pagination :meta="invoices" />
    </div>
</template>
