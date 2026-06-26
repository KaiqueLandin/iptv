<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Pencil, TicketPercent, Trash2 } from '@lucide/vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Coupon {
    id: number;
    code: string;
    description: string | null;
    type: string;
    value: string;
    minimum_amount: string | null;
    max_uses: number | null;
    uses_per_customer: number;
    valid_from: string | null;
    valid_until: string | null;
    applies_to_renewals: boolean;
    is_active: boolean;
    usages_count?: number;
    created_at: string;
}

const props = defineProps<{
    coupon: Coupon;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Cupons', href: '/admin/coupons' },
            { title: 'Detalhes', href: '#' },
        ],
    }),
});

const formatDate = (d: string | null) =>
    d ? new Date(d).toLocaleDateString('pt-BR') : '—';
</script>

<template>
    <Head :title="props.coupon.code" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader :title="props.coupon.code" description="Detalhes do cupom de desconto">
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/coupons">
                        <ArrowLeft :size="16" /> Voltar
                    </Link>
                </Button>
                <Button as-child>
                    <Link :href="`/admin/coupons/${props.coupon.id}/edit`">
                        <Pencil :size="15" /> Editar
                    </Link>
                </Button>
                <Form :action="`/admin/coupons/${props.coupon.id}`" method="delete">
                    <Button type="submit" variant="destructive" size="sm">
                        <Trash2 :size="15" /> Deletar
                    </Button>
                </Form>
            </template>
        </PageHeader>

        <div class="grid gap-6 md:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-base">
                        <TicketPercent :size="17" /> Configuração
                    </CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Código</span>
                        <code class="rounded bg-muted px-1.5 py-0.5 font-mono text-xs">{{ props.coupon.code }}</code>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Tipo</span>
                        <span>{{ props.coupon.type === 'percentage' ? 'Porcentagem' : 'Valor fixo' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Desconto</span>
                        <span>{{ props.coupon.type === 'percentage' ? `${props.coupon.value}%` : `R$ ${props.coupon.value}` }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Valor mínimo</span>
                        <span>{{ props.coupon.minimum_amount ? `R$ ${props.coupon.minimum_amount}` : '—' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Status</span>
                        <StatusBadge
                            :label="props.coupon.is_active ? 'Ativo' : 'Inativo'"
                            :color="props.coupon.is_active ? 'green' : 'red'"
                        />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Uso e Validade</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Usos realizados</span>
                        <span>{{ props.coupon.usages_count ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Limite total</span>
                        <span>{{ props.coupon.max_uses ?? 'Ilimitado' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Por cliente</span>
                        <span>{{ props.coupon.uses_per_customer }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Válido de</span>
                        <span>{{ formatDate(props.coupon.valid_from) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Válido até</span>
                        <span>{{ formatDate(props.coupon.valid_until) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Aplica em renovações</span>
                        <StatusBadge
                            :label="props.coupon.applies_to_renewals ? 'Sim' : 'Não'"
                            :color="props.coupon.applies_to_renewals ? 'green' : 'gray'"
                        />
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
