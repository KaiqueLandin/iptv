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

defineProps<{
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
            { title: 'Novo', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head title="Novo produto" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Novo produto"
            description="Cadastre um novo produto no catálogo."
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
            action="/admin/products"
            method="post"
            :types="types"
            :billing-cycles="billing_cycles"
            :brands="brands"
            :cross-sell-options="crossSellOptions"
            submit-label="Criar produto"
        />
    </div>
</template>
