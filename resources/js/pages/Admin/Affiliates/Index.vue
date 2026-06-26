<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, Search, Users } from '@lucide/vue';
import { ref, watch } from 'vue';
import DataTable from '@/components/admin/DataTable.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import Pagination from '@/components/admin/Pagination.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { Paginator } from '@/types';

interface Affiliate {
    id: number;
    referral_code: string;
    commission_rate: string;
    total_earned: string;
    pending_balance: string;
    total_referrals: number;
    is_active: boolean;
    user?: { name: string; email: string } | null;
}

const props = defineProps<{
    affiliates: Paginator<Affiliate>;
    filters: { search?: string; status?: string };
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Afiliados', href: '/admin/affiliates' },
        ],
    }),
});

const search = ref(props.filters.search ?? '');

let timer: ReturnType<typeof setTimeout>;
watch(search, () => {
    clearTimeout(timer);
    timer = setTimeout(() => {
        router.get(
            '/admin/affiliates',
            { search: search.value || undefined },
            { preserveState: true, replace: true, preserveScroll: true },
        );
    }, 350);
});

const brl = (value: string) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(Number(value));
</script>

<template>
    <Head title="Afiliados" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Afiliados"
            description="Gerencie o programa de afiliados e comissões."
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/affiliate-commissions">Comissões</Link>
                </Button>
                <Button as-child variant="outline">
                    <Link href="/admin/affiliate-payouts">Pagamentos</Link>
                </Button>
            </template>
        </PageHeader>

        <div class="relative">
            <Search
                :size="16"
                class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"
            />
            <Input
                v-model="search"
                placeholder="Buscar por afiliado ou código..."
                class="pl-9"
            />
        </div>

        <EmptyState
            v-if="affiliates.data.length === 0"
            title="Nenhum afiliado encontrado"
            :icon="Users"
        />

        <DataTable
            v-else
            :columns="['Afiliado', 'Código', 'Comissão', 'Ganhos', 'Status', '']"
        >
            <tr
                v-for="affiliate in affiliates.data"
                :key="affiliate.id"
                class="transition-colors hover:bg-muted/40"
            >
                <td class="px-4 py-3">
                    <div class="font-medium">{{ affiliate.user?.name ?? '—' }}</div>
                    <div class="text-xs text-muted-foreground">
                        {{ affiliate.total_referrals }} indicações
                    </div>
                </td>
                <td class="px-4 py-3 font-mono text-xs">
                    {{ affiliate.referral_code }}
                </td>
                <td class="px-4 py-3">{{ Number(affiliate.commission_rate) }}%</td>
                <td class="px-4 py-3 font-semibold">
                    {{ brl(affiliate.total_earned) }}
                </td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="affiliate.is_active ? 'Ativo' : 'Inativo'"
                        :color="affiliate.is_active ? 'green' : 'red'"
                    />
                </td>
                <td class="px-4 py-3 text-right">
                    <Link
                        :href="`/admin/affiliates/${affiliate.id}`"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-muted-foreground transition-colors hover:bg-accent"
                    >
                        <Eye :size="15" />
                    </Link>
                </td>
            </tr>
        </DataTable>

        <Pagination :meta="affiliates" />
    </div>
</template>
