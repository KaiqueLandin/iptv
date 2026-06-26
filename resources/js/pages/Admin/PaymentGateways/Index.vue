<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CreditCard, Pencil } from '@lucide/vue';
import DataTable from '@/components/admin/DataTable.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';

interface Gateway {
    id: number;
    name: string;
    gateway_key: string;
    is_active: boolean;
    is_sandbox: boolean;
}

defineProps<{
    gateways: Gateway[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Gateways', href: '/admin/payment-gateways' },
        ],
    }),
});
</script>

<template>
    <Head title="Gateways de pagamento" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Gateways de pagamento"
            description="Configure os meios de pagamento aceitos."
        >
            <template #actions>
                <Button as-child>
                    <Link href="/admin/payment-gateways/create">Novo gateway</Link>
                </Button>
            </template>
        </PageHeader>

        <EmptyState
            v-if="gateways.length === 0"
            title="Nenhum gateway configurado"
            :icon="CreditCard"
        >
            <template #action>
                <Button as-child>
                    <Link href="/admin/payment-gateways/create">Novo gateway</Link>
                </Button>
            </template>
        </EmptyState>

        <DataTable
            v-else
            :columns="['Gateway', 'Chave', 'Ambiente', 'Status', '']"
        >
            <tr
                v-for="gateway in gateways"
                :key="gateway.id"
                class="transition-colors hover:bg-muted/40"
            >
                <td class="px-4 py-3 font-medium">{{ gateway.name }}</td>
                <td class="px-4 py-3 font-mono text-xs text-muted-foreground">
                    {{ gateway.gateway_key }}
                </td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="gateway.is_sandbox ? 'Sandbox' : 'Produção'"
                        :color="gateway.is_sandbox ? 'amber' : 'blue'"
                    />
                </td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="gateway.is_active ? 'Ativo' : 'Inativo'"
                        :color="gateway.is_active ? 'green' : 'red'"
                    />
                </td>
                <td class="px-4 py-3 text-right">
                    <Link
                        :href="`/admin/payment-gateways/${gateway.id}/edit`"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-muted-foreground transition-colors hover:bg-accent"
                    >
                        <Pencil :size="15" />
                    </Link>
                </td>
            </tr>
        </DataTable>
    </div>
</template>
