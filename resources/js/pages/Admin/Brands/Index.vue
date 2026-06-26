<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Search, Tag } from '@lucide/vue';
import { ref, watch } from 'vue';
import DataTable from '@/components/admin/DataTable.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import Pagination from '@/components/admin/Pagination.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { Paginator } from '@/types';

interface AdminBrand {
    id: number;
    name: string;
    slug: string;
    logo_url: string | null;
    is_active: boolean;
    sort_order: number;
    products_count: number;
}

const props = defineProps<{
    brands: Paginator<AdminBrand>;
    filters: { search?: string; status?: string };
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Marcas', href: '/admin/brands' },
        ],
    }),
});

const search = ref(props.filters.search ?? '');

let timer: ReturnType<typeof setTimeout>;
watch(search, () => {
    clearTimeout(timer);
    timer = setTimeout(() => {
        router.get(
            '/admin/brands',
            { search: search.value || undefined },
            { preserveState: true, replace: true, preserveScroll: true },
        );
    }, 350);
});
</script>

<template>
    <Head title="Marcas" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Marcas"
            description="Marcas exibidas no carrossel da loja e associadas aos produtos."
        >
            <template #actions>
                <Button as-child>
                    <Link href="/admin/brands/create">
                        <Plus :size="16" />
                        Nova marca
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
                placeholder="Buscar marcas..."
                class="pl-9"
            />
        </div>

        <EmptyState
            v-if="brands.data.length === 0"
            title="Nenhuma marca cadastrada"
            description="Crie sua primeira marca para exibir no carrossel da loja."
            :icon="Tag"
        >
            <template #action>
                <Button as-child>
                    <Link href="/admin/brands/create">
                        <Plus :size="16" />
                        Nova marca
                    </Link>
                </Button>
            </template>
        </EmptyState>

        <DataTable
            v-else
            :columns="['Marca', 'Produtos', 'Ordem', 'Status', '']"
        >
            <tr
                v-for="brand in brands.data"
                :key="brand.id"
                class="transition-colors hover:bg-muted/40"
            >
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <img
                            v-if="brand.logo_url"
                            :src="brand.logo_url"
                            :alt="brand.name"
                            class="h-8 w-8 rounded object-contain"
                        />
                        <div
                            v-else
                            class="flex h-8 w-8 items-center justify-center rounded bg-muted text-muted-foreground"
                        >
                            <Tag :size="15" />
                        </div>
                        <span class="font-medium">{{ brand.name }}</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-muted-foreground">
                    {{ brand.products_count }}
                </td>
                <td class="px-4 py-3 text-muted-foreground">
                    {{ brand.sort_order }}
                </td>
                <td class="px-4 py-3">
                    <StatusBadge
                        :label="brand.is_active ? 'Ativa' : 'Inativa'"
                        :color="brand.is_active ? 'green' : 'red'"
                    />
                </td>
                <td class="px-4 py-3 text-right">
                    <Link
                        :href="`/admin/brands/${brand.id}/edit`"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-muted-foreground transition-colors hover:bg-accent"
                    >
                        <Pencil :size="15" />
                    </Link>
                </td>
            </tr>
        </DataTable>

        <Pagination :meta="brands" />
    </div>
</template>
