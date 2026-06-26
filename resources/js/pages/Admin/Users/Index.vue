<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Search, UserCog } from '@lucide/vue';
import { ref, watch } from 'vue';
import DataTable from '@/components/admin/DataTable.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import Pagination from '@/components/admin/Pagination.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Input } from '@/components/ui/input';
import type { Paginator } from '@/types';

interface AdminUser {
    id: number;
    name: string;
    email: string;
    role: string;
    is_active: boolean;
    created_at: string;
}

const props = defineProps<{
    users: Paginator<AdminUser>;
    filters: { search?: string; role?: string; status?: string };
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Clientes', href: '/admin/users' },
        ],
    }),
});

const search = ref(props.filters.search ?? '');
const role = ref(props.filters.role ?? '');

let timer: ReturnType<typeof setTimeout>;
watch([search, role], () => {
    clearTimeout(timer);
    timer = setTimeout(() => {
        router.get(
            '/admin/users',
            {
                search: search.value || undefined,
                role: role.value || undefined,
            },
            { preserveState: true, replace: true, preserveScroll: true },
        );
    }, 350);
});

const roleColor: Record<string, string> = {
    user: 'gray',
    admin: 'blue',
    superadmin: 'purple',
};

const roleLabel: Record<string, string> = {
    user: 'Usuário',
    admin: 'Administrador',
    superadmin: 'Super Admin',
};

const formatDate = (date: string) =>
    new Date(date).toLocaleDateString('pt-BR');
</script>

<template>
    <Head title="Clientes" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Clientes"
            description="Gerencie os usuários, papéis e status de acesso."
        />

        <div class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <Search
                    :size="16"
                    class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="search"
                    placeholder="Buscar por nome ou email..."
                    class="pl-9"
                />
            </div>
            <select
                v-model="role"
                class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring sm:w-48"
            >
                <option value="">Todos os papéis</option>
                <option value="user">Usuário</option>
                <option value="admin">Administrador</option>
                <option value="superadmin">Super Admin</option>
            </select>
        </div>

        <EmptyState
            v-if="users.data.length === 0"
            title="Nenhum cliente encontrado"
            description="Ajuste os filtros ou aguarde novos cadastros."
            :icon="UserCog"
        />

        <DataTable
            v-else
            :columns="['Cliente', 'Papel', 'Status', 'Cadastro', '']"
        >
            <tr
                v-for="user in users.data"
                :key="user.id"
                class="transition-colors hover:bg-muted/40"
            >
                <td class="px-4 py-3">
                    <div class="font-medium">{{ user.name }}</div>
                    <div class="text-xs text-muted-foreground">
                        {{ user.email }}
                    </div>
                </td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="roleLabel[user.role] ?? user.role"
                        :color="roleColor[user.role] ?? 'gray'"
                    />
                </td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="user.is_active ? 'Ativo' : 'Inativo'"
                        :color="user.is_active ? 'green' : 'red'"
                    />
                </td>
                <td class="px-4 py-3 text-muted-foreground">
                    {{ formatDate(user.created_at) }}
                </td>
                <td class="px-4 py-3 text-right">
                    <Link
                        :href="`/admin/users/${user.id}/edit`"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-muted-foreground transition-colors hover:bg-accent"
                    >
                        <Pencil :size="15" />
                    </Link>
                </td>
            </tr>
        </DataTable>

        <Pagination :meta="users" />
    </div>
</template>
