<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Package, Receipt } from '@lucide/vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
} from '@/components/ui/card';

interface OrderItem {
    id: number;
    order_number: string;
    product_name: string | null;
    status: string;
    status_label: string;
    status_color: string;
    amount: string;
    created_at: string | null;
    next_due_date: string | null;
}

defineProps<{
    orders: OrderItem[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [{ title: 'Meus pedidos', href: '/orders' }],
    }),
});

const brl = (value: string) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(Number(value));

const formatDate = (date: string | null) =>
    date ? new Date(date).toLocaleDateString('pt-BR') : '—';
</script>

<template>
    <Head title="Meus pedidos" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Meus pedidos</h1>
            <p class="text-sm text-muted-foreground">
                Acompanhe o status e o rastreamento dos seus pedidos.
            </p>
        </div>

        <div
            v-if="orders.length === 0"
            class="flex flex-col items-center justify-center rounded-lg border border-dashed py-16 text-center"
        >
            <div
                class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-muted text-muted-foreground"
            >
                <Receipt :size="22" />
            </div>
            <p class="text-sm font-medium">Nenhum pedido ainda</p>
            <p class="mt-1 text-xs text-muted-foreground">
                Quando você comprar um plano, ele aparecerá aqui.
            </p>
            <Button as="a" href="/" class="mt-4">Ver planos</Button>
        </div>

        <div v-else class="grid gap-4">
            <Card v-for="order in orders" :key="order.id">
                <CardContent class="flex flex-col gap-4 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-muted text-muted-foreground"
                        >
                            <Package :size="18" />
                        </div>
                        <div>
                            <p class="font-medium">
                                {{ order.product_name ?? 'Pedido' }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ order.order_number }} ·
                                {{ formatDate(order.created_at) }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <StatusBadge
                            :label="order.status_label"
                            :color="order.status_color"
                        />
                        <span class="font-semibold">{{ brl(order.amount) }}</span>
                        <Button as-child variant="outline" size="sm">
                            <Link :href="`/orders/${order.id}`">
                                Rastrear
                            </Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
