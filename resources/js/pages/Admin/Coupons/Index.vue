<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Search, TicketPercent } from '@lucide/vue';
import { ref, watch } from 'vue';
import DataTable from '@/components/admin/DataTable.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import Pagination from '@/components/admin/Pagination.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { Paginator } from '@/types';

interface AdminCoupon {
    id: number;
    code: string;
    type: string;
    value: string;
    total_uses: number;
    max_uses: number | null;
    is_active: boolean;
    usages_count?: number;
}

const props = defineProps<{
    coupons: Paginator<AdminCoupon>;
    filters: { search?: string; status?: string };
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Cupons', href: '/admin/coupons' },
        ],
    }),
});

const search = ref(props.filters.search ?? '');

let timer: ReturnType<typeof setTimeout>;
watch(search, () => {
    clearTimeout(timer);
    timer = setTimeout(() => {
        router.get(
            '/admin/coupons',
            { search: search.value || undefined },
            { preserveState: true, replace: true, preserveScroll: true },
        );
    }, 350);
});

const formatValue = (coupon: AdminCoupon) =>
    coupon.type === 'percentage'
        ? `${Number(coupon.value)}%`
        : new Intl.NumberFormat('pt-BR', {
              style: 'currency',
              currency: 'BRL',
          }).format(Number(coupon.value));
</script>

<template>
    <Head title="Cupons" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Cupons de desconto"
            description="Crie e gerencie cupons promocionais."
        >
            <template #actions>
                <Button as-child>
                    <Link href="/admin/coupons/create">Novo cupom</Link>
                </Button>
            </template>
        </PageHeader>

        <div class="relative">
            <Search
                :size="16"
                class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"
            />
            <Input v-model="search" placeholder="Buscar cupons..." class="pl-9" />
        </div>

        <EmptyState
            v-if="coupons.data.length === 0"
            title="Nenhum cupom cadastrado"
            :icon="TicketPercent"
        >
            <template #action>
                <Button as-child>
                    <Link href="/admin/coupons/create">Novo cupom</Link>
                </Button>
            </template>
        </EmptyState>

        <DataTable
            v-else
            :columns="['Código', 'Desconto', 'Usos', 'Status', '']"
        >
            <tr
                v-for="coupon in coupons.data"
                :key="coupon.id"
                class="transition-colors hover:bg-muted/40"
            >
                <td class="px-4 py-3 font-mono font-medium">{{ coupon.code }}</td>
                <td class="px-4 py-3 font-semibold">{{ formatValue(coupon) }}</td>
                <td class="px-4 py-3 text-muted-foreground">
                    {{ coupon.total_uses }}{{ coupon.max_uses ? ` / ${coupon.max_uses}` : '' }}
                </td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="coupon.is_active ? 'Ativo' : 'Inativo'"
                        :color="coupon.is_active ? 'green' : 'red'"
                    />
                </td>
                <td class="px-4 py-3 text-right">
                    <Link
                        :href="`/admin/coupons/${coupon.id}/edit`"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-muted-foreground transition-colors hover:bg-accent"
                    >
                        <Pencil :size="15" />
                    </Link>
                </td>
            </tr>
        </DataTable>

        <Pagination :meta="coupons" />
    </div>
</template>
