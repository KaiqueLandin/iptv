<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Banknote, CheckCircle2 } from '@lucide/vue';
import { ref, watch } from 'vue';
import DataTable from '@/components/admin/DataTable.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import Pagination from '@/components/admin/Pagination.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import type { Paginator } from '@/types';

interface Payout {
    id: number;
    amount: string;
    payment_method: string;
    status: string;
    created_at: string;
    affiliate?: { user?: { name: string } | null } | null;
}

const props = defineProps<{
    payouts: Paginator<Payout>;
    filters: { status?: string };
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Afiliados', href: '/admin/affiliates' },
            { title: 'Pagamentos', href: '/admin/affiliate-payouts' },
        ],
    }),
});

const status = ref(props.filters.status ?? '');

watch(status, () => {
    router.get(
        '/admin/affiliate-payouts',
        { status: status.value || undefined },
        { preserveState: true, replace: true, preserveScroll: true },
    );
});

const statusColor: Record<string, string> = {
    pending: 'yellow',
    processing: 'blue',
    completed: 'green',
    failed: 'red',
};

const brl = (value: string) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(Number(value));

const complete = (id: number) => {
    router.post(
        `/admin/affiliate-payouts/${id}/complete`,
        {},
        { preserveScroll: true },
    );
};
</script>

<template>
    <Head title="Pagamentos de afiliados" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Pagamentos de afiliados"
            description="Processe os saques solicitados pelos afiliados."
        />

        <select
            v-model="status"
            class="h-9 w-48 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
        >
            <option value="">Todos os status</option>
            <option value="pending">Pendente</option>
            <option value="processing">Processando</option>
            <option value="completed">Completo</option>
            <option value="failed">Falhou</option>
        </select>

        <EmptyState
            v-if="payouts.data.length === 0"
            title="Nenhum pagamento encontrado"
            :icon="Banknote"
        />

        <DataTable
            v-else
            :columns="['Afiliado', 'Método', 'Valor', 'Status', '']"
        >
            <tr
                v-for="payout in payouts.data"
                :key="payout.id"
                class="transition-colors hover:bg-muted/40"
            >
                <td class="px-4 py-3 font-medium">
                    {{ payout.affiliate?.user?.name ?? '—' }}
                </td>
                <td class="px-4 py-3 text-muted-foreground">
                    {{ payout.payment_method }}
                </td>
                <td class="px-4 py-3 font-semibold">{{ brl(payout.amount) }}</td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="payout.status"
                        :color="statusColor[payout.status] ?? 'gray'"
                    />
                </td>
                <td class="px-4 py-3 text-right">
                    <Button
                        v-if="payout.status !== 'completed'"
                        size="sm"
                        variant="outline"
                        @click="complete(payout.id)"
                    >
                        <CheckCircle2 :size="14" />
                        Concluir
                    </Button>
                </td>
            </tr>
        </DataTable>

        <Pagination :meta="payouts" />
    </div>
</template>
