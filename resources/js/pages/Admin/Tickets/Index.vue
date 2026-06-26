<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, Search, Ticket } from '@lucide/vue';
import { ref, watch } from 'vue';
import DataTable from '@/components/admin/DataTable.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import Pagination from '@/components/admin/Pagination.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Input } from '@/components/ui/input';
import type { Paginator } from '@/types';

interface Department {
    id: number;
    name: string;
}

interface AdminTicket {
    id: number;
    ticket_number: string;
    subject: string;
    priority: string;
    status: string;
    user?: { name: string } | null;
    department?: { name: string } | null;
}

const props = defineProps<{
    tickets: Paginator<AdminTicket>;
    filters: { search?: string; status?: string; priority?: string; department_id?: string };
    departments: Department[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Tickets', href: '/admin/tickets' },
        ],
    }),
});

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');
const departmentId = ref(props.filters.department_id ?? '');

let timer: ReturnType<typeof setTimeout>;
watch([search, status, departmentId], () => {
    clearTimeout(timer);
    timer = setTimeout(() => {
        router.get(
            '/admin/tickets',
            {
                search: search.value || undefined,
                status: status.value || undefined,
                department_id: departmentId.value || undefined,
            },
            { preserveState: true, replace: true, preserveScroll: true },
        );
    }, 350);
});

const statusLabel: Record<string, string> = {
    open: 'Aberto',
    in_progress: 'Em progresso',
    waiting_customer: 'Aguardando cliente',
    waiting_staff: 'Aguardando equipe',
    closed: 'Fechado',
};

const statusColor: Record<string, string> = {
    open: 'blue',
    in_progress: 'yellow',
    waiting_customer: 'orange',
    waiting_staff: 'purple',
    closed: 'gray',
};

const priorityLabel: Record<string, string> = {
    low: 'Baixa',
    medium: 'Média',
    high: 'Alta',
    urgent: 'Urgente',
};

const priorityColor: Record<string, string> = {
    low: 'gray',
    medium: 'blue',
    high: 'orange',
    urgent: 'red',
};
</script>

<template>
    <Head title="Tickets" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Tickets de suporte"
            description="Atenda e acompanhe os chamados dos clientes."
        />

        <div class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <Search
                    :size="16"
                    class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="search"
                    placeholder="Buscar por número, assunto ou cliente..."
                    class="pl-9"
                />
            </div>
            <select
                v-model="status"
                class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring sm:w-52"
            >
                <option value="">Todos os status</option>
                <option value="open">Aberto</option>
                <option value="in_progress">Em progresso</option>
                <option value="waiting_customer">Aguardando cliente</option>
                <option value="waiting_staff">Aguardando equipe</option>
                <option value="closed">Fechado</option>
            </select>
            <select
                v-if="departments.length"
                v-model="departmentId"
                class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring sm:w-52"
            >
                <option value="">Todos os departamentos</option>
                <option
                    v-for="department in departments"
                    :key="department.id"
                    :value="String(department.id)"
                >
                    {{ department.name }}
                </option>
            </select>
        </div>

        <EmptyState
            v-if="tickets.data.length === 0"
            title="Nenhum ticket encontrado"
            :icon="Ticket"
        />

        <DataTable
            v-else
            :columns="['Ticket', 'Cliente', 'Prioridade', 'Status', '']"
        >
            <tr
                v-for="ticket in tickets.data"
                :key="ticket.id"
                class="transition-colors hover:bg-muted/40"
            >
                <td class="px-4 py-3">
                    <div class="font-medium">{{ ticket.subject }}</div>
                    <div class="text-xs text-muted-foreground">
                        {{ ticket.ticket_number }}
                    </div>
                </td>
                <td class="px-4 py-3">{{ ticket.user?.name ?? '—' }}</td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="priorityLabel[ticket.priority] ?? ticket.priority"
                        :color="priorityColor[ticket.priority] ?? 'gray'"
                    />
                </td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="statusLabel[ticket.status] ?? ticket.status"
                        :color="statusColor[ticket.status] ?? 'gray'"
                    />
                </td>
                <td class="px-4 py-3 text-right">
                    <Link
                        :href="`/admin/tickets/${ticket.id}`"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-muted-foreground transition-colors hover:bg-accent"
                    >
                        <Eye :size="15" />
                    </Link>
                </td>
            </tr>
        </DataTable>

        <Pagination :meta="tickets" />
    </div>
</template>
