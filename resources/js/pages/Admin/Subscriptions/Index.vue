<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, RefreshCcw, Search } from '@lucide/vue';
import { ref, watch } from 'vue';
import DataTable from '@/components/admin/DataTable.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import Pagination from '@/components/admin/Pagination.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Input } from '@/components/ui/input';
import type { Paginator } from '@/types';

interface AdminSubscription {
    id: number;
    status: string;
    amount: string;
    next_billing_date: string | null;
    user?: { name: string } | null;
    product?: { name: string } | null;
}

const props = defineProps<{
    subscriptions: Paginator<AdminSubscription>;
    filters: { search?: string; status?: string };
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Assinaturas', href: '/admin/subscriptions' },
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
            '/admin/subscriptions',
            {
                search: search.value || undefined,
                status: status.value || undefined,
            },
            { preserveState: true, replace: true, preserveScroll: true },
        );
    }, 350);
});

const statusLabel: Record<string, string> = {
    active: 'Ativa',
    paused: 'Pausada',
    cancelled: 'Cancelada',
    expired: 'Expirada',
};

const statusColor: Record<string, string> = {
    active: 'green',
    paused: 'yellow',
    cancelled: 'red',
    expired: 'gray',
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
    <Head title="Assinaturas" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Assinaturas"
            description="Gerencie as assinaturas recorrentes dos clientes."
        />

        <div class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <Search
                    :size="16"
                    class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="search"
                    placeholder="Buscar por cliente..."
                    class="pl-9"
                />
            </div>
            <select
                v-model="status"
                class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring sm:w-48"
            >
                <option value="">Todos os status</option>
                <option value="active">Ativa</option>
                <option value="paused">Pausada</option>
                <option value="cancelled">Cancelada</option>
                <option value="expired">Expirada</option>
            </select>
        </div>

        <EmptyState
            v-if="subscriptions.data.length === 0"
            title="Nenhuma assinatura encontrada"
            :icon="RefreshCcw"
        />

        <DataTable
            v-else
            :columns="['Cliente', 'Produto', 'Valor', 'Próx. cobrança', 'Status', '']"
        >
            <tr
                v-for="subscription in subscriptions.data"
                :key="subscription.id"
                class="transition-colors hover:bg-muted/40"
            >
                <td class="px-4 py-3 font-medium">
                    {{ subscription.user?.name ?? '—' }}
                </td>
                <td class="px-4 py-3 text-muted-foreground">
                    {{ subscription.product?.name ?? '—' }}
                </td>
                <td class="px-4 py-3 font-semibold">
                    {{ brl(subscription.amount) }}
                </td>
                <td class="px-4 py-3 text-muted-foreground">
                    {{ formatDate(subscription.next_billing_date) }}
                </td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="statusLabel[subscription.status] ?? subscription.status"
                        :color="statusColor[subscription.status] ?? 'gray'"
                    />
                </td>
                <td class="px-4 py-3 text-right">
                    <Link
                        :href="`/admin/subscriptions/${subscription.id}`"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-muted-foreground transition-colors hover:bg-accent"
                    >
                        <Eye :size="15" />
                    </Link>
                </td>
            </tr>
        </DataTable>

        <Pagination :meta="subscriptions" />
    </div>
</template>
