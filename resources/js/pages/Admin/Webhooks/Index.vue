<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Pencil, ScrollText, Webhook as WebhookIcon } from '@lucide/vue';
import DataTable from '@/components/admin/DataTable.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';

interface WebhookItem {
    id: number;
    event: string;
    url: string;
    is_active: boolean;
    logs_count?: number;
}

defineProps<{
    webhooks: WebhookItem[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Webhooks', href: '/admin/webhooks' },
        ],
    }),
});
</script>

<template>
    <Head title="Webhooks" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Webhooks"
            description="Notifique sistemas externos sobre eventos da plataforma."
        >
            <template #actions>
                <Button as-child>
                    <Link href="/admin/webhooks/create">Novo webhook</Link>
                </Button>
            </template>
        </PageHeader>

        <EmptyState
            v-if="webhooks.length === 0"
            title="Nenhum webhook configurado"
            :icon="WebhookIcon"
        >
            <template #action>
                <Button as-child>
                    <Link href="/admin/webhooks/create">Novo webhook</Link>
                </Button>
            </template>
        </EmptyState>

        <DataTable v-else :columns="['Evento', 'URL', 'Status', '']">
            <tr
                v-for="webhook in webhooks"
                :key="webhook.id"
                class="transition-colors hover:bg-muted/40"
            >
                <td class="px-4 py-3 font-mono text-xs font-medium">
                    {{ webhook.event }}
                </td>
                <td class="max-w-xs truncate px-4 py-3 text-muted-foreground">
                    {{ webhook.url }}
                </td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="webhook.is_active ? 'Ativo' : 'Inativo'"
                        :color="webhook.is_active ? 'green' : 'red'"
                    />
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="inline-flex gap-1">
                        <Link
                            :href="`/admin/webhooks/${webhook.id}/logs`"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-muted-foreground transition-colors hover:bg-accent"
                        >
                            <ScrollText :size="15" />
                        </Link>
                        <Link
                            :href="`/admin/webhooks/${webhook.id}/edit`"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-muted-foreground transition-colors hover:bg-accent"
                        >
                            <Pencil :size="15" />
                        </Link>
                    </div>
                </td>
            </tr>
        </DataTable>
    </div>
</template>
