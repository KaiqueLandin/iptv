<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { CheckCircle2, Coins } from '@lucide/vue';
import { ref, watch } from 'vue';
import DataTable from '@/components/admin/DataTable.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import Pagination from '@/components/admin/Pagination.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import type { Paginator } from '@/types';

interface Commission {
    id: number;
    commission_amount: string;
    commission_rate: string;
    status: string;
    affiliate?: { user?: { name: string } | null } | null;
    user?: { name: string } | null;
}

const props = defineProps<{
    commissions: Paginator<Commission>;
    filters: { status?: string };
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Afiliados', href: '/admin/affiliates' },
            { title: 'Comissões', href: '/admin/affiliate-commissions' },
        ],
    }),
});

const status = ref(props.filters.status ?? '');

watch(status, () => {
    router.get(
        '/admin/affiliate-commissions',
        { status: status.value || undefined },
        { preserveState: true, replace: true, preserveScroll: true },
    );
});

const statusColor: Record<string, string> = {
    pending: 'yellow',
    approved: 'blue',
    paid: 'green',
    cancelled: 'red',
};

const brl = (value: string) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(Number(value));

const approve = (id: number) => {
    router.post(
        `/admin/affiliate-commissions/${id}/approve`,
        {},
        { preserveScroll: true },
    );
};
</script>

<template>
    <Head title="Comissões de afiliados" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Comissões"
            description="Aprove e acompanhe as comissões geradas."
        />

        <select
            v-model="status"
            class="h-9 w-48 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
        >
            <option value="">Todos os status</option>
            <option value="pending">Pendente</option>
            <option value="approved">Aprovada</option>
            <option value="paid">Paga</option>
            <option value="cancelled">Cancelada</option>
        </select>

        <EmptyState
            v-if="commissions.data.length === 0"
            title="Nenhuma comissão encontrada"
            :icon="Coins"
        />

        <DataTable
            v-else
            :columns="['Afiliado', 'Indicado', 'Valor', 'Status', '']"
        >
            <tr
                v-for="commission in commissions.data"
                :key="commission.id"
                class="transition-colors hover:bg-muted/40"
            >
                <td class="px-4 py-3 font-medium">
                    {{ commission.affiliate?.user?.name ?? '—' }}
                </td>
                <td class="px-4 py-3">{{ commission.user?.name ?? '—' }}</td>
                <td class="px-4 py-3 font-semibold">
                    {{ brl(commission.commission_amount) }}
                </td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="commission.status"
                        :color="statusColor[commission.status] ?? 'gray'"
                    />
                </td>
                <td class="px-4 py-3 text-right">
                    <Button
                        v-if="commission.status === 'pending'"
                        size="sm"
                        variant="outline"
                        @click="approve(commission.id)"
                    >
                        <CheckCircle2 :size="14" />
                        Aprovar
                    </Button>
                </td>
            </tr>
        </DataTable>

        <Pagination :meta="commissions" />
    </div>
</template>
