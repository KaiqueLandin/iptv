<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, ScrollText } from '@lucide/vue';
import { ref, watch } from 'vue';
import DataTable from '@/components/admin/DataTable.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import Pagination from '@/components/admin/Pagination.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import type { Paginator } from '@/types';

interface WebhookModel {
    id: number;
    event: string;
    url: string;
}

interface WebhookLog {
    id: number;
    event: string;
    status: string;
    status_code: number | null;
    attempt: number;
    created_at: string;
}

const props = defineProps<{
    webhook: WebhookModel;
    logs: Paginator<WebhookLog>;
    filters: { status?: string };
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Webhooks', href: '/admin/webhooks' },
            { title: 'Logs', href: '#' },
        ],
    }),
});

const status = ref(props.filters.status ?? '');

watch(status, () => {
    router.get(
        `/admin/webhooks/${props.webhook.id}/logs`,
        { status: status.value || undefined },
        { preserveState: true, replace: true, preserveScroll: true },
    );
});

const statusColor: Record<string, string> = {
    pending: 'yellow',
    success: 'green',
    failed: 'red',
};

const formatDate = (date: string) => new Date(date).toLocaleString('pt-BR');
</script>

<template>
    <Head title="Logs do webhook" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Logs do webhook"
            :description="`${props.webhook.event} · ${props.webhook.url}`"
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/webhooks">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <select
            v-model="status"
            class="h-9 w-48 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
        >
            <option value="">Todos os status</option>
            <option value="pending">Pendente</option>
            <option value="success">Sucesso</option>
            <option value="failed">Falhou</option>
        </select>

        <EmptyState
            v-if="logs.data.length === 0"
            title="Nenhum log registrado"
            :icon="ScrollText"
        />

        <DataTable
            v-else
            :columns="['Evento', 'Status', 'Código', 'Tentativa', 'Data']"
        >
            <tr v-for="log in logs.data" :key="log.id">
                <td class="px-4 py-3 font-mono text-xs">{{ log.event }}</td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="log.status"
                        :color="statusColor[log.status] ?? 'gray'"
                    />
                </td>
                <td class="px-4 py-3 text-muted-foreground">
                    {{ log.status_code ?? '—' }}
                </td>
                <td class="px-4 py-3 text-muted-foreground">{{ log.attempt }}</td>
                <td class="px-4 py-3 text-muted-foreground">
                    {{ formatDate(log.created_at) }}
                </td>
            </tr>
        </DataTable>

        <Pagination :meta="logs" />
    </div>
</template>
