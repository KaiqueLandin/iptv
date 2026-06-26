<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Ban, PlayCircle, PauseCircle } from '@lucide/vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface AdminSubscription {
    id: number;
    status: string;
    amount: string;
    billing_cycle: string;
    started_at: string | null;
    next_billing_date: string | null;
    expires_at: string | null;
    user?: { id: number; name: string; email: string } | null;
    product?: { id: number; name: string } | null;
}

const props = defineProps<{
    subscription: AdminSubscription;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Assinaturas', href: '/admin/subscriptions' },
            { title: 'Detalhes', href: '#' },
        ],
    }),
});

const statusLabel: Record<string, string> = {
    active: 'Ativa',
    paused: 'Pausada',
    cancelled: 'Cancelada',
    expired: 'Expirada',
};

const statusColor: Record<string, string> = {
    active: 'green',
    paused: 'yellow',
    cancelled: 'red',
    expired: 'gray',
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
        `/admin/subscriptions/${props.subscription.id}/${verb}`,
        {},
        { preserveScroll: true },
    );
};
</script>

<template>
    <Head title="Assinatura" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            :title="props.subscription.user?.name ?? 'Assinatura'"
            :description="props.subscription.product?.name ?? ''"
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/subscriptions">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <div class="grid gap-6 lg:grid-cols-3">
            <Card class="lg:col-span-2">
                <CardHeader>
                    <CardTitle class="text-base">Detalhes da assinatura</CardTitle>
                </CardHeader>
                <CardContent>
                    <dl class="divide-y divide-border text-sm">
                        <div class="flex justify-between py-3 first:pt-0">
                            <dt class="text-muted-foreground">Valor</dt>
                            <dd class="font-semibold">{{ brl(props.subscription.amount) }}</dd>
                        </div>
                        <div class="flex justify-between py-3">
                            <dt class="text-muted-foreground">Início</dt>
                            <dd>{{ formatDate(props.subscription.started_at) }}</dd>
                        </div>
                        <div class="flex justify-between py-3">
                            <dt class="text-muted-foreground">Próxima cobrança</dt>
                            <dd>{{ formatDate(props.subscription.next_billing_date) }}</dd>
                        </div>
                        <div class="flex justify-between py-3 last:pb-0">
                            <dt class="text-muted-foreground">Expira em</dt>
                            <dd>{{ formatDate(props.subscription.expires_at) }}</dd>
                        </div>
                    </dl>
                </CardContent>
            </Card>

            <Card class="h-fit">
                <CardHeader>
                    <CardTitle class="flex items-center justify-between text-base">
                        Status
                        <StatusBadge
                            :label="statusLabel[props.subscription.status] ?? props.subscription.status"
                            :color="statusColor[props.subscription.status] ?? 'gray'"
                        />
                    </CardTitle>
                </CardHeader>
                <CardContent class="flex flex-col gap-2">
                    <Button
                        variant="outline"
                        class="justify-start"
                        @click="action('resume')"
                    >
                        <PlayCircle :size="16" />
                        Retomar
                    </Button>
                    <Button
                        variant="outline"
                        class="justify-start"
                        @click="action('pause')"
                    >
                        <PauseCircle :size="16" />
                        Pausar
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
