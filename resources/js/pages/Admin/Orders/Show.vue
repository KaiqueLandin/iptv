<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Ban, CheckCircle2, PauseCircle } from '@lucide/vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface AdminOrder {
    id: number;
    order_number: string;
    status: string;
    amount: string;
    billing_cycle: string;
    next_due_date: string | null;
    created_at: string;
    user?: { id: number; name: string; email: string } | null;
    product?: { id: number; name: string } | null;
}

const props = defineProps<{
    order: AdminOrder;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Pedidos', href: '/admin/orders' },
            { title: 'Detalhes', href: '#' },
        ],
    }),
});

const statusLabel: Record<string, string> = {
    pending: 'Pendente',
    active: 'Ativo',
    suspended: 'Suspenso',
    cancelled: 'Cancelado',
    completed: 'Completo',
};

const statusColor: Record<string, string> = {
    pending: 'yellow',
    active: 'green',
    suspended: 'orange',
    cancelled: 'red',
    completed: 'blue',
};

const brl = (value: string) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(Number(value));

const formatDate = (date: string | null) =>
    date ? new Date(date).toLocaleDateString('pt-BR') : '—';

const action = (verb: string) => {
    router.post(
        `/admin/orders/${props.order.id}/${verb}`,
        {},
        { preserveScroll: true },
    );
};
</script>

<template>
    <Head :title="props.order.order_number" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            :title="props.order.order_number"
            :description="props.order.user?.name ?? 'Cliente'"
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/orders">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <div class="grid gap-6 lg:grid-cols-3">
            <Card class="lg:col-span-2">
                <CardHeader>
                    <CardTitle class="text-base">Detalhes do pedido</CardTitle>
                </CardHeader>
                <CardContent>
                    <dl class="divide-y divide-border text-sm">
                        <div class="flex justify-between py-3 first:pt-0">
                            <dt class="text-muted-foreground">Cliente</dt>
                            <dd class="text-right">
                                {{ props.order.user?.name ?? '—' }}
                                <div class="text-xs text-muted-foreground">
                                    {{ props.order.user?.email }}
                                </div>
                            </dd>
                        </div>
                        <div class="flex justify-between py-3">
                            <dt class="text-muted-foreground">Produto</dt>
                            <dd>{{ props.order.product?.name ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between py-3">
                            <dt class="text-muted-foreground">Valor</dt>
                            <dd class="font-semibold">{{ brl(props.order.amount) }}</dd>
                        </div>
                        <div class="flex justify-between py-3">
                            <dt class="text-muted-foreground">Próx. vencimento</dt>
                            <dd>{{ formatDate(props.order.next_due_date) }}</dd>
                        </div>
                        <div class="flex justify-between py-3 last:pb-0">
                            <dt class="text-muted-foreground">Criado em</dt>
                            <dd>{{ formatDate(props.order.created_at) }}</dd>
                        </div>
                    </dl>
                </CardContent>
            </Card>

            <Card class="h-fit">
                <CardHeader>
                    <CardTitle class="flex items-center justify-between text-base">
                        Status
                        <StatusBadge
                            :label="statusLabel[props.order.status] ?? props.order.status"
                            :color="statusColor[props.order.status] ?? 'gray'"
                        />
                    </CardTitle>
                </CardHeader>
                <CardContent class="flex flex-col gap-2">
                    <Button
                        variant="outline"
                        class="justify-start"
                        @click="action('activate')"
                    >
                        <CheckCircle2 :size="16" />
                        Ativar pedido
                    </Button>
                    <Button
                        variant="outline"
                        class="justify-start"
                        @click="action('suspend')"
                    >
                        <PauseCircle :size="16" />
                        Suspender
                    </Button>
                    <Button
                        variant="outline"
                        class="justify-start text-rose-600 hover:text-rose-600"
                        @click="action('cancel')"
                    >
                        <Ban :size="16" />
                        Cancelar
                    </Button>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
