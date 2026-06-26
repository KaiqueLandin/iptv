<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import ProductForm from '@/components/admin/ProductForm.vue';
import { Button } from '@/components/ui/button';

interface Option {
    value: string;
    label: string;
}

interface IdOption {
    value: number;
    label: string;
}

interface ProductModel {
    id: number;
    name: string;
    description: string | null;
    brand_id: number | null;
    price: string;
    setup_fee: string;
    billing_cycle: string;
    type: string;
    is_active: boolean;
    is_featured: boolean;
    stock: number | null;
    cross_sell_ids: number[];
}

const props = defineProps<{
    product: ProductModel;
    types: Option[];
    billing_cycles: Option[];
    brands: IdOption[];
    crossSellOptions: IdOption[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Produtos', href: '/admin/products' },
            { title: 'Editar', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head :title="`Editar ${props.product.name}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Editar produto"
            :description="props.product.name"
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/products">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <ProductForm
            :action="`/admin/products/${props.product.id}`"
            method="put"
            :product="props.product"
            :types="types"
            :billing-cycles="billing_cycles"
            :brands="brands"
            :cross-sell-options="crossSellOptions"
            submit-label="Salvar alterações"
        />
    </div>
</template>
