<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Mail, Pencil } from '@lucide/vue';
import DataTable from '@/components/admin/DataTable.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import Pagination from '@/components/admin/Pagination.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import type { Paginator } from '@/types';

interface Template {
    id: number;
    name: string;
    slug: string;
    type: string;
    is_active: boolean;
}

defineProps<{
    templates: Paginator<Template>;
    filters: { type?: string; status?: string };
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Templates de email', href: '/admin/email-templates' },
        ],
    }),
});

const typeLabel: Record<string, string> = {
    invoice: 'Fatura',
    order: 'Pedido',
    ticket: 'Ticket',
    general: 'Geral',
    notification: 'Notificação',
};
</script>

<template>
    <Head title="Templates de email" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Templates de email"
            description="Personalize os emails automáticos da plataforma."
        >
            <template #actions>
                <Button as-child>
                    <Link href="/admin/email-templates/create">Novo template</Link>
                </Button>
            </template>
        </PageHeader>

        <EmptyState
            v-if="templates.data.length === 0"
            title="Nenhum template cadastrado"
            :icon="Mail"
        >
            <template #action>
                <Button as-child>
                    <Link href="/admin/email-templates/create">Novo template</Link>
                </Button>
            </template>
        </EmptyState>

        <DataTable v-else :columns="['Nome', 'Tipo', 'Status', '']">
            <tr
                v-for="template in templates.data"
                :key="template.id"
                class="transition-colors hover:bg-muted/40"
            >
                <td class="px-4 py-3">
                    <div class="font-medium">{{ template.name }}</div>
                    <div class="font-mono text-xs text-muted-foreground">
                        {{ template.slug }}
                    </div>
                </td>
                <td class="px-4 py-3 text-muted-foreground">
                    {{ typeLabel[template.type] ?? template.type }}
                </td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="template.is_active ? 'Ativo' : 'Inativo'"
                        :color="template.is_active ? 'green' : 'red'"
                    />
                </td>
                <td class="px-4 py-3 text-right">
                    <Link
                        :href="`/admin/email-templates/${template.id}/edit`"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-muted-foreground transition-colors hover:bg-accent"
                    >
                        <Pencil :size="15" />
                    </Link>
                </td>
            </tr>
        </DataTable>

        <Pagination :meta="templates" />
    </div>
</template>
