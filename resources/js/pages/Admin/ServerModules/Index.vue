<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Pencil, Server } from '@lucide/vue';
import DataTable from '@/components/admin/DataTable.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';

interface Module {
    id: number;
    name: string;
    module_key: string;
    server_hostname: string | null;
    is_active: boolean;
}

defineProps<{
    modules: Module[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Servidores', href: '/admin/server-modules' },
        ],
    }),
});
</script>

<template>
    <Head title="Módulos de servidor" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Módulos de servidor"
            description="Configure os servidores para provisionamento automático."
        >
            <template #actions>
                <Button as-child>
                    <Link href="/admin/server-modules/create">Novo módulo</Link>
                </Button>
            </template>
        </PageHeader>

        <EmptyState
            v-if="modules.length === 0"
            title="Nenhum módulo configurado"
            :icon="Server"
        >
            <template #action>
                <Button as-child>
                    <Link href="/admin/server-modules/create">Novo módulo</Link>
                </Button>
            </template>
        </EmptyState>

        <DataTable v-else :columns="['Nome', 'Tipo', 'Servidor', 'Status', '']">
            <tr
                v-for="module in modules"
                :key="module.id"
                class="transition-colors hover:bg-muted/40"
            >
                <td class="px-4 py-3 font-medium">{{ module.name }}</td>
                <td class="px-4 py-3 font-mono text-xs text-muted-foreground">
                    {{ module.module_key }}
                </td>
                <td class="px-4 py-3 text-muted-foreground">
                    {{ module.server_hostname ?? '—' }}
                </td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="module.is_active ? 'Ativo' : 'Inativo'"
                        :color="module.is_active ? 'green' : 'red'"
                    />
                </td>
                <td class="px-4 py-3 text-right">
                    <Link
                        :href="`/admin/server-modules/${module.id}/edit`"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-muted-foreground transition-colors hover:bg-accent"
                    >
                        <Pencil :size="15" />
                    </Link>
                </td>
            </tr>
        </DataTable>
    </div>
</template>
