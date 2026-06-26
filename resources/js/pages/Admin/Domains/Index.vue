<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, Globe, Search } from '@lucide/vue';
import { ref, watch } from 'vue';
import DataTable from '@/components/admin/DataTable.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import Pagination from '@/components/admin/Pagination.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { Paginator } from '@/types';

interface Domain {
    id: number;
    domain_name: string;
    registrar: string | null;
    status: string;
    expiry_date: string | null;
    user?: { name: string } | null;
}

const props = defineProps<{
    domains: Paginator<Domain>;
    filters: { search?: string; status?: string; registrar?: string };
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Domínios', href: '/admin/domains' },
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
            '/admin/domains',
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
    expired: 'Expirado',
    cancelled: 'Cancelado',
    transferred: 'Transferido',
};

const statusColor: Record<string, string> = {
    pending: 'yellow',
    active: 'green',
    expired: 'red',
    cancelled: 'gray',
    transferred: 'blue',
};

const formatDate = (date: string | null) =>
    date ? new Date(date).toLocaleDateString('pt-BR') : '—';
</script>

<template>
    <Head title="Domínios" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Domínios"
            description="Gerencie os domínios registrados pelos clientes."
        >
            <template #actions>
                <Button as-child>
                    <Link href="/admin/domains/create">Novo domínio</Link>
                </Button>
            </template>
        </PageHeader>

        <div class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <Search
                    :size="16"
                    class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="search"
                    placeholder="Buscar por domínio ou cliente..."
                    class="pl-9"
                />
            </div>
            <select
                v-model="status"
                class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring sm:w-48"
            >
                <option value="">Todos os status</option>
                <option value="active">Ativo</option>
                <option value="pending">Pendente</option>
                <option value="expired">Expirado</option>
                <option value="cancelled">Cancelado</option>
            </select>
        </div>

        <EmptyState
            v-if="domains.data.length === 0"
            title="Nenhum domínio encontrado"
            :icon="Globe"
        />

        <DataTable
            v-else
            :columns="['Domínio', 'Cliente', 'Registrar', 'Expira em', 'Status', '']"
        >
            <tr
                v-for="domain in domains.data"
                :key="domain.id"
                class="transition-colors hover:bg-muted/40"
            >
                <td class="px-4 py-3 font-medium">{{ domain.domain_name }}</td>
                <td class="px-4 py-3">{{ domain.user?.name ?? '—' }}</td>
                <td class="px-4 py-3 text-muted-foreground">
                    {{ domain.registrar ?? '—' }}
                </td>
                <td class="px-4 py-3 text-muted-foreground">
                    {{ formatDate(domain.expiry_date) }}
                </td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="statusLabel[domain.status] ?? domain.status"
                        :color="statusColor[domain.status] ?? 'gray'"
                    />
                </td>
                <td class="px-4 py-3 text-right">
                    <Link
                        :href="`/admin/domains/${domain.id}`"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-muted-foreground transition-colors hover:bg-accent"
                    >
                        <Eye :size="15" />
                    </Link>
                </td>
            </tr>
        </DataTable>

        <Pagination :meta="domains" />
    </div>
</template>
