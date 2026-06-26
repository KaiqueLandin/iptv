<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, Search, ShoppingCart } from '@lucide/vue';
import { ref, watch } from 'vue';
import DataTable from '@/components/admin/DataTable.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import Pagination from '@/components/admin/Pagination.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Input } from '@/components/ui/input';
import type { Paginator } from '@/types';

interface AdminOrder {
    id: number;
    order_number: string;
    status: string;
    amount: string;
    user?: { name: string } | null;
    product?: { name: string } | null;
    created_at: string;
}

const props = defineProps<{
    orders: Paginator<AdminOrder>;
    filters: { search?: string; status?: string };
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Pedidos', href: '/admin/orders' },
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
            '/admin/orders',
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
    active: 'Ativo',
    suspended: 'Suspenso',
    cancelled: 'Cancelado',
    completed: 'Completo',
};

const statusColor: Record<string, string> = {
    pending: 'yellow',
    active: 'green',
    suspended: 'orange',
    cancelled: 'red',
    completed: 'blue',
};

const brl = (value: string) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(Number(value));
</script>

<template>
    <Head title="Pedidos" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Pedidos"
            description="Acompanhe e gerencie os pedidos dos clientes."
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
                <option value="pending">Pendente</option>
                <option value="active">Ativo</option>
                <option value="suspended">Suspenso</option>
                <option value="cancelled">Cancelado</option>
                <option value="completed">Completo</option>
            </select>
        </div>

        <EmptyState
            v-if="orders.data.length === 0"
            title="Nenhum pedido encontrado"
            :icon="ShoppingCart"
        />

        <DataTable
            v-else
            :columns="['Pedido', 'Cliente', 'Produto', 'Valor', 'Status', '']"
        >
            <tr
                v-for="order in orders.data"
                :key="order.id"
                class="transition-colors hover:bg-muted/40"
            >
                <td class="px-4 py-3 font-medium">{{ order.order_number }}</td>
                <td class="px-4 py-3">{{ order.user?.name ?? '—' }}</td>
                <td class="px-4 py-3 text-muted-foreground">
                    {{ order.product?.name ?? '—' }}
                </td>
                <td class="px-4 py-3 font-semibold">{{ brl(order.amount) }}</td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="statusLabel[order.status] ?? order.status"
                        :color="statusColor[order.status] ?? 'gray'"
                    />
                </td>
                <td class="px-4 py-3 text-right">
                    <Link
                        :href="`/admin/orders/${order.id}`"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-muted-foreground transition-colors hover:bg-accent"
                    >
                        <Eye :size="15" />
                    </Link>
                </td>
            </tr>
        </DataTable>

        <Pagination :meta="orders" />
    </div>
</template>
