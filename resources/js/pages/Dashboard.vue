<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    ArrowUpRight,
    Headphones,
    KeyRound,
    Plus,
    Receipt,
    RefreshCcw,
    ShoppingBag,
    Sparkles,
    Tv,
    TrendingDown,
    TrendingUp,
    Wallet,
} from '@lucide/vue';
import { computed } from 'vue';
import PendingInvitationsModal from '@/components/PendingInvitationsModal.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { dashboard } from '@/routes';
import type { Auth, DashboardInvitation, Team } from '@/types';

interface Product {
    id: number;
    name: string;
    price: string;
    checkoutUrl: string;
    description?: string | null;
}

interface ServiceItem {
    id: number;
    service_number: string;
    product_name: string | null;
    status: string;
    status_label: string;
    status_color: string;
    activation_code: string | null;
    expires_at: string | null;
}

interface Transaction {
    id: number;
    type: string;
    amount: string;
    balance_after: string;
    description: string;
    created_at: string;
}

interface WalletData {
    balance: number;
    formattedBalance: string;
}

const props = defineProps<{
    pendingInvitations?: DashboardInvitation[];
    wallet: WalletData;
    recentTransactions: Transaction[];
    products: Product[];
    services: ServiceItem[];
}>();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Painel',
                href: props.currentTeam
                    ? dashboard(props.currentTeam.slug)
                    : '/',
            },
        ],
    }),
});

const page = usePage<{ auth: Auth }>();

const firstName = computed(
    () => page.props.auth.user.name?.split(' ')[0] ?? 'cliente',
);

const formatDate = (date: string) =>
    new Date(date).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
    });

const isCredit = (type: string) => type === 'credit' || type === 'refund';

const transactionIcon = (type: string) => {
    switch (type) {
        case 'credit':
            return TrendingUp;
        case 'refund':
            return RefreshCcw;
        case 'debit':
            return TrendingDown;
        default:
            return Receipt;
    }
};

const buyProduct = (product: Product) => {
    router.post(`/checkout/product/${product.id}`);
};

const serviceStatusClasses: Record<string, string> = {
    green: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
    yellow: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
    orange: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
    red: 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400',
    gray: 'bg-muted text-muted-foreground',
};
</script>

<template>
    <Head title="Painel" />

    <PendingInvitationsModal
        v-if="pendingInvitations && pendingInvitations.length > 0"
        :invitations="pendingInvitations"
    />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <!-- Hero: saldo + ação principal -->
        <div
            class="relative overflow-hidden rounded-2xl border border-emerald-600/20 bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-700 p-6 text-white shadow-lg md:p-8"
        >
            <div
                class="absolute -right-10 -top-10 h-48 w-48 rounded-full bg-white/10"
            ></div>
            <div
                class="absolute -bottom-16 right-24 h-40 w-40 rounded-full bg-white/5"
            ></div>

            <div
                class="relative z-10 flex flex-col gap-6 md:flex-row md:items-end md:justify-between"
            >
                <div>
                    <p class="text-sm font-medium text-emerald-50/90">
                        Olá, {{ firstName }} 👋
                    </p>
                    <div
                        class="mt-4 flex items-center gap-2 text-sm font-medium text-emerald-50/80"
                    >
                        <Wallet :size="16" />
                        Saldo disponível
                    </div>
                    <div class="mt-1 flex items-baseline gap-2">
                        <span class="text-4xl font-bold tracking-tight md:text-5xl">
                            {{ wallet.formattedBalance }}
                        </span>
                        <span class="text-lg font-medium text-emerald-50/80"
                            >créditos</span
                        >
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <Button
                        as="a"
                        href="#comprar"
                        class="bg-white text-emerald-700 shadow-sm hover:bg-emerald-50"
                    >
                        <Plus :size="16" />
                        Comprar créditos
                    </Button>
                </div>
            </div>
        </div>

        <!-- Meus serviços (códigos de ativação) -->
        <Card>
            <CardHeader class="flex-row items-center justify-between">
                <CardTitle class="flex items-center gap-2 text-base">
                    <Tv :size="18" class="text-emerald-600" />
                    Meus serviços
                </CardTitle>
                <Link
                    href="/services"
                    class="text-xs font-medium text-emerald-600 hover:underline"
                >
                    Ver todos
                </Link>
            </CardHeader>
            <CardContent>
                <div
                    v-if="services.length === 0"
                    class="flex flex-col items-center justify-center rounded-lg border border-dashed py-10 text-center"
                >
                    <KeyRound :size="22" class="mb-2 text-muted-foreground/60" />
                    <p class="text-sm text-muted-foreground">
                        Seus acessos aparecerão aqui após a compra.
                    </p>
                </div>
                <div v-else class="grid gap-3 sm:grid-cols-2">
                    <Link
                        v-for="service in services"
                        :key="service.id"
                        :href="`/services/${service.id}`"
                        class="flex items-center justify-between gap-3 rounded-lg border p-3 transition-colors hover:border-emerald-500 hover:bg-accent"
                    >
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold">
                                {{ service.product_name ?? 'Serviço' }}
                            </p>
                            <p
                                v-if="service.activation_code"
                                class="truncate font-mono text-xs text-muted-foreground"
                            >
                                {{ service.activation_code }}
                            </p>
                            <p v-else class="text-xs text-muted-foreground">
                                {{ service.service_number }}
                            </p>
                        </div>
                        <span
                            :class="[
                                'shrink-0 rounded-full px-2.5 py-0.5 text-xs font-medium',
                                serviceStatusClasses[service.status_color] ?? serviceStatusClasses.gray,
                            ]"
                        >
                            {{ service.status_label }}
                        </span>
                    </Link>
                </div>
            </CardContent>
        </Card>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Histórico de créditos -->
            <Card class="lg:col-span-2">
                <CardHeader class="flex-row items-center justify-between">
                    <CardTitle class="flex items-center gap-2 text-base">
                        <Receipt :size="18" class="text-muted-foreground" />
                        Histórico de créditos
                    </CardTitle>
                    <span class="text-xs text-muted-foreground">
                        últimas {{ recentTransactions.length }} movimentações
                    </span>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="recentTransactions.length === 0"
                        class="flex flex-col items-center justify-center rounded-lg border border-dashed py-14 text-center"
                    >
                        <div
                            class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-muted text-muted-foreground"
                        >
                            <Receipt :size="22" />
                        </div>
                        <p class="text-sm font-medium">
                            Nenhuma movimentação ainda
                        </p>
                        <p class="mt-1 text-xs text-muted-foreground">
                            Suas compras e recargas aparecem aqui.
                        </p>
                    </div>

                    <ul v-else class="divide-y divide-border">
                        <li
                            v-for="transaction in recentTransactions"
                            :key="transaction.id"
                            class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0"
                        >
                            <div class="flex min-w-0 items-center gap-3">
                                <div
                                    :class="[
                                        'flex h-9 w-9 shrink-0 items-center justify-center rounded-full',
                                        isCredit(transaction.type)
                                            ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400'
                                            : 'bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400',
                                    ]"
                                >
                                    <component
                                        :is="transactionIcon(transaction.type)"
                                        :size="17"
                                    />
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium">
                                        {{ transaction.description }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ formatDate(transaction.created_at) }}
                                    </p>
                                </div>
                            </div>
                            <div class="shrink-0 text-right">
                                <p
                                    :class="[
                                        'text-sm font-semibold',
                                        isCredit(transaction.type)
                                            ? 'text-emerald-600 dark:text-emerald-400'
                                            : 'text-rose-600 dark:text-rose-400',
                                    ]"
                                >
                                    {{ transaction.type === 'debit' ? '−' : '+' }}{{ transaction.amount }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    saldo {{ transaction.balance_after }}
                                </p>
                            </div>
                        </li>
                    </ul>
                </CardContent>
            </Card>

            <!-- Coluna lateral: comprar + suporte -->
            <div class="flex flex-col gap-6">
                <Card id="comprar">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-base">
                            <ShoppingBag
                                :size="18"
                                class="text-muted-foreground"
                            />
                            Comprar créditos
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-2.5">
                        <button
                            v-for="product in products"
                            :key="product.id"
                            type="button"
                            class="group flex w-full items-center justify-between rounded-lg border p-3 text-left transition-colors hover:border-emerald-500 hover:bg-accent"
                            @click="buyProduct(product)"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400"
                                >
                                    <Sparkles :size="17" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold">
                                        {{ product.name }}
                                    </p>
                                    <p
                                        v-if="product.description"
                                        class="text-xs text-muted-foreground"
                                    >
                                        {{ product.description }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 text-right">
                                <div>
                                    <p class="text-sm font-bold">
                                        R$ {{ product.price }}
                                    </p>
                                </div>
                                <ArrowUpRight
                                    :size="16"
                                    class="text-muted-foreground transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                                />
                            </div>
                        </button>

                        <div
                            v-if="products.length === 0"
                            class="flex flex-col items-center justify-center rounded-lg border border-dashed py-10 text-center"
                        >
                            <ShoppingBag
                                :size="22"
                                class="mb-2 text-muted-foreground/60"
                            />
                            <p class="text-sm text-muted-foreground">
                                Nenhum pacote disponível no momento.
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card
                    class="border-emerald-600/20 bg-emerald-50/50 dark:bg-emerald-950/20"
                >
                    <CardContent class="flex items-start gap-3 pt-0">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-white"
                        >
                            <Headphones :size="19" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold">Precisa de ajuda?</p>
                            <p class="mt-0.5 text-xs text-muted-foreground">
                                Fale com nosso suporte pelo WhatsApp para dúvidas
                                sobre compra e recarga.
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
