<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, CreditCard, Pencil } from '@lucide/vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Gateway {
    id: number;
    name: string;
    gateway_key: string;
    description: string | null;
    is_active: boolean;
    is_sandbox: boolean;
    fee_fixed: string;
    fee_percentage: string;
    sort_order: number;
    created_at: string;
}

const props = defineProps<{
    gateway: Gateway;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Gateways', href: '/admin/payment-gateways' },
            { title: 'Detalhes', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head :title="props.gateway.name" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            :title="props.gateway.name"
            :description="props.gateway.gateway_key"
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/payment-gateways">
                        <ArrowLeft :size="16" /> Voltar
                    </Link>
                </Button>
                <Button as-child>
                    <Link :href="`/admin/payment-gateways/${props.gateway.id}/edit`">
                        <Pencil :size="15" /> Editar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <div class="grid gap-6 md:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-base">
                        <CreditCard :size="17" /> Informações
                    </CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Chave</span>
                        <code class="rounded bg-muted px-1.5 py-0.5 font-mono text-xs">{{ props.gateway.gateway_key }}</code>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Descrição</span>
                        <span>{{ props.gateway.description ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Ambiente</span>
                        <StatusBadge
                            :label="props.gateway.is_sandbox ? 'Sandbox' : 'Produção'"
                            :color="props.gateway.is_sandbox ? 'amber' : 'blue'"
                        />
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Status</span>
                        <StatusBadge
                            :label="props.gateway.is_active ? 'Ativo' : 'Inativo'"
                            :color="props.gateway.is_active ? 'green' : 'red'"
                        />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Taxas</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Taxa fixa</span>
                        <span>R$ {{ props.gateway.fee_fixed }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Taxa percentual</span>
                        <span>{{ props.gateway.fee_percentage }}%</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Ordem de exibição</span>
                        <span>{{ props.gateway.sort_order }}</span>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
