<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Heart, ShoppingCart, Trash2 } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

interface WishlistProduct {
    id: number;
    name: string;
    slug: string;
    price: string;
    description: string | null;
    brand_name: string | null;
    is_active: boolean;
    checkoutUrl: string;
}

defineProps<{
    products: WishlistProduct[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [{ title: 'Favoritos', href: '/wishlist' }],
    }),
});

const brl = (value: string) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(Number(value));

const remove = (product: WishlistProduct) => {
    router.delete(`/wishlist/${product.id}`, { preserveScroll: true });
};
</script>

<template>
    <Head title="Favoritos" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Favoritos</h1>
            <p class="text-sm text-muted-foreground">
                Produtos que você salvou para comprar depois.
            </p>
        </div>

        <div
            v-if="products.length === 0"
            class="flex flex-col items-center justify-center rounded-lg border border-dashed py-16 text-center"
        >
            <div
                class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-muted text-muted-foreground"
            >
                <Heart :size="22" />
            </div>
            <p class="text-sm font-medium">Sua lista está vazia</p>
            <p class="mt-1 text-xs text-muted-foreground">
                Adicione produtos aos favoritos para encontrá-los aqui.
            </p>
            <Button as="a" href="/" class="mt-4">Ver planos</Button>
        </div>

        <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="product in products" :key="product.id">
                <CardHeader>
                    <CardTitle class="flex items-start justify-between gap-2 text-base">
                        <span>{{ product.name }}</span>
                        <button
                            type="button"
                            class="text-muted-foreground transition-colors hover:text-rose-600"
                            title="Remover dos favoritos"
                            @click="remove(product)"
                        >
                            <Trash2 :size="16" />
                        </button>
                    </CardTitle>
                    <p
                        v-if="product.brand_name"
                        class="text-xs text-muted-foreground"
                    >
                        {{ product.brand_name }}
                    </p>
                </CardHeader>
                <CardContent class="space-y-3">
                    <p
                        v-if="product.description"
                        class="line-clamp-2 text-sm text-muted-foreground"
                    >
                        {{ product.description }}
                    </p>
                    <p class="text-lg font-semibold">{{ brl(product.price) }}</p>
                    <Button
                        v-if="product.is_active"
                        as-child
                        class="w-full"
                    >
                        <Link :href="product.checkoutUrl" method="post" as="button">
                            <ShoppingCart :size="16" />
                            Comprar
                        </Link>
                    </Button>
                    <p
                        v-else
                        class="rounded-md border border-dashed py-2 text-center text-xs text-muted-foreground"
                    >
                        Indisponível no momento
                    </p>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
