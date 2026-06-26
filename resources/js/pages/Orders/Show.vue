<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Check, KeyRound } from '@lucide/vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

interface TimelineStep {
    key: string;
    label: string;
    done: boolean;
    at: string | null;
}

interface OrderService {
    id: number;
    service_number: string;
    status: string;
    status_label: string;
    status_color: string;
    activation_code: string | null;
    delivered_at: string | null;
}

const props = defineProps<{
    order: {
        id: number;
        order_number: string;
        product_name: string | null;
        status: string;
        status_label: string;
        status_color: string;
        amount: string;
        created_at: string | null;
        activated_at: string | null;
        cancelled_at: string | null;
        cancellation_reason: string | null;
        next_due_date: string | null;
        timeline: TimelineStep[];
        service: OrderService | null;
    };
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Meus pedidos', href: '/orders' },
            { title: 'Rastreamento', href: '#' },
        ],
    }),
});

const brl = (value: string) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(Number(value));

const formatDateTime = (date: string | null) =>
    date
        ? new Date(date).toLocaleString('pt-BR', {
              dateStyle: 'short',
              timeStyle: 'short',
          })
        : null;

const isCancelled = props.order.status === 'cancelled';
</script>

<template>
    <Head :title="`Pedido ${order.order_number}`" />

    <div class="mx-auto flex w-full max-w-3xl flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">
                    {{ order.product_name ?? 'Pedido' }}
                </h1>
                <p class="text-sm text-muted-foreground">
                    {{ order.order_number }}
                </p>
            </div>
            <Button as-child variant="outline">
                <Link href="/orders">
                    <ArrowLeft :size="16" />
                    Voltar
                </Link>
            </Button>
        </div>

        <Card>
            <CardContent class="flex items-center justify-between py-5">
                <div>
                    <p class="text-xs text-muted-foreground">Status atual</p>
                    <div class="mt-1">
                        <StatusBadge
                            :label="order.status_label"
                            :color="order.status_color"
                        />
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs text-muted-foreground">Valor</p>
                    <p class="text-lg font-semibold">{{ brl(order.amount) }}</p>
                </div>
            </CardContent>
        </Card>

        <!-- Pedido cancelado -->
        <Card v-if="isCancelled" class="border-rose-600/30">
            <CardContent class="py-5">
                <p class="text-sm font-medium text-rose-600">
                    Pedido cancelado
                    <span v-if="order.cancelled_at" class="text-muted-foreground">
                        em {{ formatDateTime(order.cancelled_at) }}
                    </span>
                </p>
                <p
                    v-if="order.cancellation_reason"
                    class="mt-1 text-sm text-muted-foreground"
                >
                    {{ order.cancellation_reason }}
                </p>
            </CardContent>
        </Card>

        <!-- Linha do tempo -->
        <Card v-else>
            <CardHeader>
                <CardTitle class="text-base">Rastreamento</CardTitle>
            </CardHeader>
            <CardContent>
                <ol class="relative space-y-6">
                    <li
                        v-for="(step, index) in order.timeline"
                        :key="step.key"
                        class="flex gap-4"
                    >
                        <div class="flex flex-col items-center">
                            <span
                                :class="[
                                    'flex h-8 w-8 items-center justify-center rounded-full border',
                                    step.done
                                        ? 'border-emerald-600 bg-emerald-600 text-white'
                                        : 'border-dashed bg-background text-muted-foreground',
                                ]"
                            >
                                <Check v-if="step.done" :size="16" />
                                <span v-else class="text-xs">{{ index + 1 }}</span>
                            </span>
                            <span
                                v-if="index < order.timeline.length - 1"
                                :class="[
                                    'mt-1 w-px flex-1',
                                    step.done ? 'bg-emerald-600' : 'bg-border',
                                ]"
                            />
                        </div>
                        <div class="pb-2">
                            <p
                                :class="[
                                    'text-sm font-medium',
                                    step.done ? '' : 'text-muted-foreground',
                                ]"
                            >
                                {{ step.label }}
                            </p>
                            <p
                                v-if="formatDateTime(step.at)"
                                class="text-xs text-muted-foreground"
                            >
                                {{ formatDateTime(step.at) }}
                            </p>
                        </div>
                    </li>
                </ol>
            </CardContent>
        </Card>

        <!-- Serviço entregue -->
        <Card v-if="order.service" class="border-emerald-600/30">
            <CardHeader class="flex-row items-center justify-between">
                <CardTitle class="flex items-center gap-2 text-base">
                    <KeyRound :size="18" class="text-emerald-600" />
                    Acesso entregue
                </CardTitle>
                <StatusBadge
                    :label="order.service.status_label"
                    :color="order.service.status_color"
                />
            </CardHeader>
            <CardContent class="space-y-3">
                <div
                    v-if="order.service.activation_code"
                    class="rounded-lg bg-muted p-3"
                >
                    <p class="text-xs text-muted-foreground">Código de ativação</p>
                    <p class="font-mono text-lg font-semibold">
                        {{ order.service.activation_code }}
                    </p>
                </div>
                <Button as-child variant="outline" class="w-full">
                    <Link :href="`/services/${order.service.id}`">
                        Ver detalhes de acesso
                    </Link>
                </Button>
            </CardContent>
        </Card>
    </div>
</template>
