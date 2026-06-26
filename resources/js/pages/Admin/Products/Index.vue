<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Package, Pencil, Plus, Search } from '@lucide/vue';
import { ref, watch } from 'vue';
import DataTable from '@/components/admin/DataTable.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import Pagination from '@/components/admin/Pagination.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { Paginator } from '@/types';

interface AdminProduct {
    id: number;
    name: string;
    type: string;
    price: string;
    billing_cycle: string;
    is_active: boolean;
    is_featured: boolean;
}

const props = defineProps<{
    products: Paginator<AdminProduct>;
    filters: { search?: string; type?: string; status?: string };
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Produtos', href: '/admin/products' },
        ],
    }),
});

const search = ref(props.filters.search ?? '');

let timer: ReturnType<typeof setTimeout>;
watch(search, () => {
    clearTimeout(timer);
    timer = setTimeout(() => {
        router.get(
            '/admin/products',
            { search: search.value || undefined },
            { preserveState: true, replace: true, preserveScroll: true },
        );
    }, 350);
});

const typeLabel: Record<string, string> = {
    hosting: 'Hospedagem',
    vps: 'VPS',
    dedicated: 'Dedicado',
    domain: 'Domínio',
    ssl: 'SSL',
    addon: 'Addon',
    other: 'Outro',
};

const cycleLabel: Record<string, string> = {
    monthly: 'Mensal',
    quarterly: 'Trimestral',
    semiannually: 'Semestral',
    annually: 'Anual',
    biennially: 'Bienal',
    triennially: 'Trienal',
    onetime: 'Único',
};

const brl = (value: string) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(Number(value));
</script>

<template>
    <Head title="Produtos" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Produtos"
            description="Catálogo de produtos e serviços oferecidos."
        >
            <template #actions>
                <Button as-child>
                    <Link href="/admin/products/create">
                        <Plus :size="16" />
                        Novo produto
                    </Link>
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
                placeholder="Buscar produtos..."
                class="pl-9"
            />
        </div>

        <EmptyState
            v-if="products.data.length === 0"
            title="Nenhum produto cadastrado"
            description="Crie seu primeiro produto para começar a vender."
            :icon="Package"
        >
            <template #action>
                <Button as-child>
                    <Link href="/admin/products/create">
                        <Plus :size="16" />
                        Novo produto
                    </Link>
                </Button>
            </template>
        </EmptyState>

        <DataTable
            v-else
            :columns="['Produto', 'Tipo', 'Preço', 'Ciclo', 'Status', '']"
        >
            <tr
                v-for="product in products.data"
                :key="product.id"
                class="transition-colors hover:bg-muted/40"
            >
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2 font-medium">
                        {{ product.name }}
                        <StatusBadge
                            v-if="product.is_featured"
                            label="Destaque"
                            color="amber"
                        />
                    </div>
                </td>
                <td class="px-4 py-3 text-muted-foreground">
                    {{ typeLabel[product.type] ?? product.type }}
                </td>
                <td class="px-4 py-3 font-semibold">
                    {{ brl(product.price) }}
                </td>
                <td class="px-4 py-3 text-muted-foreground">
                    {{ cycleLabel[product.billing_cycle] ?? product.billing_cycle }}
                </td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="product.is_active ? 'Ativo' : 'Inativo'"
                        :color="product.is_active ? 'green' : 'red'"
                    />
                </td>
                <td class="px-4 py-3 text-right">
                    <Link
                        :href="`/admin/products/${product.id}/edit`"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-muted-foreground transition-colors hover:bg-accent"
                    >
                        <Pencil :size="15" />
                    </Link>
                </td>
            </tr>
        </DataTable>

        <Pagination :meta="products" />
    </div>
</template>
