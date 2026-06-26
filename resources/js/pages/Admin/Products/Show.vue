<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Pencil } from '@lucide/vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface ProductModel {
    id: number;
    name: string;
    description: string | null;
    price: string;
    setup_fee: string;
    billing_cycle: string;
    type: string;
    is_active: boolean;
    is_featured: boolean;
    stock: number | null;
}

const props = defineProps<{
    product: ProductModel;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Produtos', href: '/admin/products' },
            { title: 'Detalhes', href: '#' },
        ],
    }),
});

const brl = (value: string) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(Number(value));
</script>

<template>
    <Head :title="props.product.name" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            :title="props.product.name"
            :description="props.product.description ?? 'Sem descrição'"
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/products">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
                <Button as-child>
                    <Link :href="`/admin/products/${props.product.id}/edit`">
                        <Pencil :size="16" />
                        Editar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <Card class="max-w-2xl">
            <CardHeader>
                <CardTitle class="text-base">Detalhes do produto</CardTitle>
            </CardHeader>
            <CardContent>
                <dl class="divide-y divide-border text-sm">
                    <div class="flex items-center justify-between py-3 first:pt-0">
                        <dt class="text-muted-foreground">Preço</dt>
                        <dd class="font-semibold">{{ brl(props.product.price) }}</dd>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <dt class="text-muted-foreground">Taxa de setup</dt>
                        <dd>{{ brl(props.product.setup_fee) }}</dd>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <dt class="text-muted-foreground">Estoque</dt>
                        <dd>{{ props.product.stock ?? 'Ilimitado' }}</dd>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <dt class="text-muted-foreground">Status</dt>
                        <dd class="flex gap-2">
                            <StatusBadge
                                :label="props.product.is_active ? 'Ativo' : 'Inativo'"
                                :color="props.product.is_active ? 'green' : 'red'"
                            />
                            <StatusBadge
                                v-if="props.product.is_featured"
                                label="Destaque"
                                color="amber"
                            />
                        </dd>
                    </div>
                </dl>
            </CardContent>
        </Card>
    </div>
</template>
