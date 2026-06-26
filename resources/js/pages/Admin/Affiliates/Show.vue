<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2 } from '@lucide/vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import StatCard from '@/components/admin/StatCard.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Commission {
    id: number;
    commission_amount: string;
    status: string;
    user?: { name: string } | null;
}

interface Payout {
    id: number;
    amount: string;
    status: string;
    created_at: string;
}

interface Affiliate {
    id: number;
    referral_code: string;
    commission_rate: string;
    total_earned: string;
    pending_balance: string;
    paid_balance: string;
    total_referrals: number;
    is_active: boolean;
    approved_at: string | null;
    user?: { name: string; email: string } | null;
    commissions: Commission[];
    payouts: Payout[];
}

const props = defineProps<{
    affiliate: Affiliate;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Afiliados', href: '/admin/affiliates' },
            { title: 'Detalhes', href: '#' },
        ],
    }),
});

const brl = (value: string) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(Number(value));

const commissionColor: Record<string, string> = {
    pending: 'yellow',
    approved: 'blue',
    paid: 'green',
    cancelled: 'red',
};

const approve = () => {
    router.post(
        `/admin/affiliates/${props.affiliate.id}/approve`,
        {},
        { preserveScroll: true },
    );
};
</script>

<template>
    <Head :title="props.affiliate.user?.name ?? 'Afiliado'" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            :title="props.affiliate.user?.name ?? 'Afiliado'"
            :description="`Código: ${props.affiliate.referral_code}`"
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/affiliates">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
                <Button v-if="!props.affiliate.approved_at" @click="approve">
                    <CheckCircle2 :size="16" />
                    Aprovar
                </Button>
            </template>
        </PageHeader>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <StatCard
                label="Total ganho"
                :value="brl(props.affiliate.total_earned)"
                tone="emerald"
            />
            <StatCard
                label="Saldo pendente"
                :value="brl(props.affiliate.pending_balance)"
                tone="amber"
            />
            <StatCard
                label="Já pago"
                :value="brl(props.affiliate.paid_balance)"
                tone="blue"
            />
            <StatCard
                label="Indicações"
                :value="props.affiliate.total_referrals"
                tone="violet"
            />
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Comissões</CardTitle>
                </CardHeader>
                <CardContent>
                    <ul
                        v-if="props.affiliate.commissions.length"
                        class="divide-y divide-border text-sm"
                    >
                        <li
                            v-for="commission in props.affiliate.commissions"
                            :key="commission.id"
                            class="flex items-center justify-between py-2 first:pt-0 last:pb-0"
                        >
                            <span>{{ commission.user?.name ?? 'Indicado' }}</span>
                            <div class="flex items-center gap-2">
                                <StatusBadge
                                    :label="commission.status"
                                    :color="commissionColor[commission.status] ?? 'gray'"
                                />
                                <span class="font-semibold">
                                    {{ brl(commission.commission_amount) }}
                                </span>
                            </div>
                        </li>
                    </ul>
                    <p v-else class="py-4 text-center text-sm text-muted-foreground">
                        Nenhuma comissão registrada.
                    </p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Pagamentos</CardTitle>
                </CardHeader>
                <CardContent>
                    <ul
                        v-if="props.affiliate.payouts.length"
                        class="divide-y divide-border text-sm"
                    >
                        <li
                            v-for="payout in props.affiliate.payouts"
                            :key="payout.id"
                            class="flex items-center justify-between py-2 first:pt-0 last:pb-0"
                        >
                            <span>{{ payout.status }}</span>
                            <span class="font-semibold">{{ brl(payout.amount) }}</span>
                        </li>
                    </ul>
                    <p v-else class="py-4 text-center text-sm text-muted-foreground">
                        Nenhum pagamento realizado.
                    </p>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
