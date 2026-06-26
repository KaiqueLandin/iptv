<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    AlertTriangle,
    ArrowRight,
    Boxes,
    CheckCircle2,
    CreditCard,
    Package,
    Receipt,
    RefreshCw,
    ShoppingCart,
    TrendingDown,
    TrendingUp,
    Tv,
    Users,
    Wallet,
    Zap,
} from '@lucide/vue';
import { computed } from 'vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import StatCard from '@/components/admin/StatCard.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

interface Stats {
    total_users: number;
    active_users: number;
    total_products: number;
    active_products: number;
    total_orders: number;
    active_orders: number;
    pending_orders: number;
    total_subscriptions: number;
    active_subscriptions: number;
    total_invoices: number;
    unpaid_invoices: number;
    overdue_invoices: number;
    total_revenue: number;
    monthly_revenue: number;
    today_revenue: number;
    avg_ticket: number;
    total_services: number;
    active_services: number;
    failed_services: number;
    pending_services: number;
}

interface SalesAnalytics {
    total_units: number;
    total_sales_value: number;
    units_this_month: number;
    distinct_products_sold: number;
}

interface ProductStat {
    id: number;
    name: string;
    units_sold: number;
    revenue: string | number;
}

interface RevenuePoint {
    date: string;
    total: string | number;
}

interface RecentOrder {
    id: number;
    order_number: string;
    status: string;
    amount: string;
    user?: { name: string } | null;
    product?: { name: string } | null;
}

interface RecentInvoice {
    id: number;
    invoice_number: string;
    status: string;
    total: string;
    user?: { name: string } | null;
}

interface RecentService {
    id: number;
    service_number: string;
    user_name: string | null;
    product_name: string | null;
    status: string;
    status_label: string;
    status_color: string;
    delivered_at: string | null;
}

const props = defineProps<{
    stats: Stats;
    salesAnalytics: SalesAnalytics;
    topProducts: ProductStat[];
    leastProducts: ProductStat[];
    revenueByDay: RevenuePoint[];
    recentServices: RecentService[];
    recent_orders: RecentOrder[];
    recent_invoices: RecentInvoice[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [{ title: 'Administração', href: '/admin/dashboard' }],
    }),
});

// ─── formatters ──────────────────────────────────────────────────────────────
const brl = (value: number | string) =>
    new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(value));

const formatDay = (date: string) =>
    new Date(date).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });

const formatDate = (date: string | null) =>
    date ? new Date(date).toLocaleString('pt-BR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }) : '—';

// ─── chart ───────────────────────────────────────────────────────────────────
const maxRevenue = computed(() =>
    Math.max(...props.revenueByDay.map((d) => Number(d.total)), 1),
);

const maxTopUnits = computed(() =>
    Math.max(...props.topProducts.map((p) => Number(p.units_sold)), 1),
);

// ─── alerts ──────────────────────────────────────────────────────────────────
const hasAlerts = computed(
    () => props.stats.overdue_invoices > 0 || props.stats.failed_services > 0 || props.stats.pending_orders > 0,
);

// ─── status maps ─────────────────────────────────────────────────────────────
const orderStatusColor: Record<string, string> = {
    pending: 'yellow', active: 'green', suspended: 'orange', cancelled: 'red', completed: 'blue',
};
const orderStatusLabel: Record<string, string> = {
    pending: 'Pendente', active: 'Ativo', suspended: 'Suspenso', cancelled: 'Cancelado', completed: 'Completo',
};
const invoiceStatusColor: Record<string, string> = {
    draft: 'gray', unpaid: 'yellow', paid: 'green', cancelled: 'red', refunded: 'purple', overdue: 'red',
};
const invoiceStatusLabel: Record<string, string> = {
    draft: 'Rascunho', unpaid: 'Não paga', paid: 'Paga', cancelled: 'Cancelada', refunded: 'Reembolsada', overdue: 'Vencida',
};
</script>

<template>
    <Head title="Visão geral" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Visão geral"
            description="Métricas de vendas, faturamento e operações da plataforma."
        >
            <template #actions>
                <Button as-child size="sm" variant="outline">
                    <Link href="/admin/reports">
                        Ver relatórios <ArrowRight :size="15" />
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <!-- ── Alertas ──────────────────────────────────────────────────── -->
        <div v-if="hasAlerts" class="grid gap-3 sm:grid-cols-3">
            <Link
                v-if="stats.overdue_invoices > 0"
                href="/admin/invoices?status=overdue"
                class="flex items-center gap-3 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 transition-colors hover:bg-rose-100 dark:border-rose-800/40 dark:bg-rose-950/30 dark:hover:bg-rose-950/50"
            >
                <AlertTriangle :size="18" class="shrink-0 text-rose-600" />
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-rose-700 dark:text-rose-400">
                        {{ stats.overdue_invoices }} fatura{{ stats.overdue_invoices > 1 ? 's' : '' }} vencida{{ stats.overdue_invoices > 1 ? 's' : '' }}
                    </p>
                    <p class="text-xs text-rose-600/70 dark:text-rose-400/70">Clique para resolver</p>
                </div>
            </Link>

            <Link
                v-if="stats.failed_services > 0"
                href="/admin/orders?status=failed"
                class="flex items-center gap-3 rounded-lg border border-orange-200 bg-orange-50 px-4 py-3 transition-colors hover:bg-orange-100 dark:border-orange-800/40 dark:bg-orange-950/30 dark:hover:bg-orange-950/50"
            >
                <Zap :size="18" class="shrink-0 text-orange-600" />
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-orange-700 dark:text-orange-400">
                        {{ stats.failed_services }} serviço{{ stats.failed_services > 1 ? 's' : '' }} com falha
                    </p>
                    <p class="text-xs text-orange-600/70 dark:text-orange-400/70">Precisa de atenção</p>
                </div>
            </Link>

            <Link
                v-if="stats.pending_orders > 0"
                href="/admin/orders?status=pending"
                class="flex items-center gap-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 transition-colors hover:bg-amber-100 dark:border-amber-800/40 dark:bg-amber-950/30 dark:hover:bg-amber-950/50"
            >
                <ShoppingCart :size="18" class="shrink-0 text-amber-600" />
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-amber-700 dark:text-amber-400">
                        {{ stats.pending_orders }} pedido{{ stats.pending_orders > 1 ? 's' : '' }} pendente{{ stats.pending_orders > 1 ? 's' : '' }}
                    </p>
                    <p class="text-xs text-amber-600/70 dark:text-amber-400/70">Aguardando ação</p>
                </div>
            </Link>
        </div>

        <div
            v-else
            class="flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 dark:border-emerald-800/40 dark:bg-emerald-950/30"
        >
            <CheckCircle2 :size="18" class="shrink-0 text-emerald-600" />
            <p class="text-sm font-medium text-emerald-700 dark:text-emerald-400">
                Tudo em ordem — sem itens pendentes de atenção.
            </p>
        </div>

        <!-- ── Cards de receita ─────────────────────────────────────────── -->
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard
                label="Receita total"
                :value="brl(stats.total_revenue)"
                hint="faturas pagas"
                :icon="Wallet"
                tone="emerald"
            />
            <StatCard
                label="Receita no mês"
                :value="brl(stats.monthly_revenue)"
                hint="mês atual"
                :icon="CreditCard"
                tone="blue"
            />
            <StatCard
                label="Receita hoje"
                :value="brl(stats.today_revenue)"
                hint="pagamentos de hoje"
                :icon="TrendingUp"
                tone="violet"
            />
            <StatCard
                label="Ticket médio"
                :value="brl(stats.avg_ticket)"
                hint="por fatura paga"
                :icon="Receipt"
                tone="amber"
            />
        </div>

        <!-- ── Gráfico de receita + vendas lado a lado ──────────────────── -->
        <div class="grid gap-6 lg:grid-cols-3">
            <Card class="lg:col-span-2">
                <CardHeader>
                    <CardTitle class="text-base">Receita (últimos 14 dias)</CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="revenueByDay.length" class="flex h-52 items-end gap-1.5">
                        <div
                            v-for="point in revenueByDay"
                            :key="point.date"
                            class="group relative flex flex-1 flex-col items-center gap-1.5"
                        >
                            <!-- tooltip -->
                            <div class="absolute -top-8 left-1/2 z-10 hidden -translate-x-1/2 whitespace-nowrap rounded bg-popover px-2 py-1 text-xs shadow-md group-hover:block">
                                {{ brl(point.total) }}
                            </div>
                            <div class="flex w-full flex-1 items-end">
                                <div
                                    class="w-full rounded-t bg-emerald-500 transition-all group-hover:bg-emerald-400"
                                    :style="{ height: `${Math.max((Number(point.total) / maxRevenue) * 100, 2)}%` }"
                                />
                            </div>
                            <span class="text-[9px] text-muted-foreground">{{ formatDay(point.date) }}</span>
                        </div>
                    </div>
                    <EmptyState
                        v-else
                        title="Sem faturamento recente"
                        description="Vendas pagas dos últimos 14 dias aparecem aqui."
                        :icon="TrendingUp"
                    />
                </CardContent>
            </Card>

            <!-- Métricas de vendas -->
            <Card>
                <CardHeader class="flex-row items-center justify-between">
                    <CardTitle class="text-base">Vendas</CardTitle>
                    <Button as-child size="sm" variant="ghost">
                        <Link href="/admin/orders">Ver todos <ArrowRight :size="13" /></Link>
                    </Button>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="rounded-lg bg-muted/50 p-3">
                        <p class="text-xs text-muted-foreground">Total de unidades</p>
                        <p class="text-2xl font-bold">{{ salesAnalytics.total_units }}</p>
                    </div>
                    <div class="rounded-lg bg-muted/50 p-3">
                        <p class="text-xs text-muted-foreground">Valor total vendido</p>
                        <p class="text-lg font-bold">{{ brl(salesAnalytics.total_sales_value) }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-lg bg-muted/50 p-3">
                            <p class="text-xs text-muted-foreground">Neste mês</p>
                            <p class="text-xl font-bold">{{ salesAnalytics.units_this_month }}</p>
                        </div>
                        <div class="rounded-lg bg-muted/50 p-3">
                            <p class="text-xs text-muted-foreground">Produtos</p>
                            <p class="text-xl font-bold">{{ salesAnalytics.distinct_products_sold }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- ── Operações ────────────────────────────────────────────────── -->
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard
                label="Clientes"
                :value="stats.total_users"
                :hint="`${stats.active_users} ativos`"
                :icon="Users"
                tone="violet"
            />
            <StatCard
                label="Assinaturas ativas"
                :value="stats.active_subscriptions"
                :hint="`${stats.total_subscriptions} no total`"
                :icon="RefreshCw"
                tone="emerald"
            />
            <StatCard
                label="Faturas não pagas"
                :value="stats.unpaid_invoices"
                :hint="`${stats.total_invoices} no total`"
                :icon="Receipt"
                tone="amber"
            />
            <StatCard
                label="Serviços ativos"
                :value="stats.active_services"
                :hint="`${stats.total_services} entregues`"
                :icon="Tv"
                tone="emerald"
            />
        </div>

        <!-- ── Mais e menos vendidos ────────────────────────────────────── -->
        <div class="grid gap-6 lg:grid-cols-2">
            <Card>
                <CardHeader class="flex-row items-center justify-between">
                    <CardTitle class="flex items-center gap-2 text-base">
                        <TrendingUp :size="17" class="text-emerald-600" />
                        Mais vendidos
                    </CardTitle>
                    <Button as-child size="sm" variant="ghost">
                        <Link href="/admin/reports/products">
                            Relatório <ArrowRight :size="13" />
                        </Link>
                    </Button>
                </CardHeader>
                <CardContent>
                    <EmptyState v-if="topProducts.length === 0" title="Nenhuma venda registrada" :icon="Package" />
                    <ul v-else class="space-y-3">
                        <li v-for="product in topProducts" :key="product.id" class="space-y-1.5">
                            <div class="flex items-center justify-between text-sm">
                                <span class="truncate font-medium">{{ product.name }}</span>
                                <span class="ml-3 shrink-0 text-xs text-muted-foreground">
                                    {{ product.units_sold }} un · {{ brl(product.revenue) }}
                                </span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-muted">
                                <div
                                    class="h-full rounded-full bg-emerald-500 transition-all"
                                    :style="{ width: `${(Number(product.units_sold) / maxTopUnits) * 100}%` }"
                                />
                            </div>
                        </li>
                    </ul>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex-row items-center justify-between">
                    <CardTitle class="flex items-center gap-2 text-base">
                        <TrendingDown :size="17" class="text-rose-600" />
                        Menos vendidos
                    </CardTitle>
                    <Button as-child size="sm" variant="ghost">
                        <Link href="/admin/products">
                            Ver produtos <ArrowRight :size="13" />
                        </Link>
                    </Button>
                </CardHeader>
                <CardContent>
                    <EmptyState v-if="leastProducts.length === 0" title="Nenhum produto ativo" :icon="Package" />
                    <ul v-else class="divide-y divide-border">
                        <li
                            v-for="product in leastProducts"
                            :key="product.id"
                            class="flex items-center justify-between py-2.5 text-sm first:pt-0 last:pb-0"
                        >
                            <span class="truncate font-medium">{{ product.name }}</span>
                            <div class="ml-3 flex shrink-0 items-center gap-2">
                                <StatusBadge
                                    :label="`${product.units_sold} vendas`"
                                    :color="Number(product.units_sold) === 0 ? 'red' : 'gray'"
                                />
                                <span class="text-xs text-muted-foreground">{{ brl(product.revenue) }}</span>
                            </div>
                        </li>
                    </ul>
                </CardContent>
            </Card>
        </div>

        <!-- ── Serviços recentes ─────────────────────────────────────────── -->
        <Card>
            <CardHeader class="flex-row items-center justify-between">
                <CardTitle class="flex items-center gap-2 text-base">
                    <Tv :size="17" class="text-emerald-600" />
                    Serviços recentes
                    <StatusBadge
                        v-if="stats.failed_services > 0"
                        :label="`${stats.failed_services} com falha`"
                        color="red"
                    />
                </CardTitle>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-muted-foreground">
                        {{ stats.pending_services }} aguardando
                    </span>
                    <Button as-child size="sm" variant="ghost">
                        <Link href="/admin/orders">Ver todos <ArrowRight :size="13" /></Link>
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <EmptyState v-if="recentServices.length === 0" title="Nenhum serviço provisionado" :icon="Tv" />
                <ul v-else class="divide-y divide-border">
                    <li
                        v-for="service in recentServices"
                        :key="service.id"
                        class="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0"
                    >
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-muted">
                                <Tv :size="14" class="text-muted-foreground" />
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium">{{ service.user_name ?? 'Cliente' }}</p>
                                <p class="truncate text-xs text-muted-foreground">
                                    {{ service.service_number }} · {{ service.product_name ?? '—' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex shrink-0 items-center gap-3">
                            <StatusBadge :label="service.status_label" :color="service.status_color" />
                            <span v-if="service.delivered_at" class="text-xs text-muted-foreground">
                                {{ formatDate(service.delivered_at) }}
                            </span>
                        </div>
                    </li>
                </ul>
            </CardContent>
        </Card>

        <!-- ── Pedidos e Faturas recentes ───────────────────────────────── -->
        <div class="grid gap-6 lg:grid-cols-2">
            <Card>
                <CardHeader class="flex-row items-center justify-between">
                    <CardTitle class="text-base">Pedidos recentes</CardTitle>
                    <Button as-child size="sm" variant="ghost">
                        <Link href="/admin/orders">Ver todos <ArrowRight :size="13" /></Link>
                    </Button>
                </CardHeader>
                <CardContent>
                    <EmptyState v-if="recent_orders.length === 0" title="Nenhum pedido ainda" :icon="ShoppingCart" />
                    <ul v-else class="divide-y divide-border">
                        <li
                            v-for="order in recent_orders"
                            :key="order.id"
                            class="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0"
                        >
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium">{{ order.user?.name ?? 'Cliente' }}</p>
                                <p class="truncate text-xs text-muted-foreground">
                                    {{ order.order_number }} · {{ order.product?.name ?? '—' }}
                                </p>
                            </div>
                            <div class="flex shrink-0 items-center gap-2">
                                <StatusBadge
                                    :label="orderStatusLabel[order.status] ?? order.status"
                                    :color="orderStatusColor[order.status] ?? 'gray'"
                                />
                                <span class="text-sm font-semibold">{{ brl(order.amount) }}</span>
                            </div>
                        </li>
                    </ul>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex-row items-center justify-between">
                    <CardTitle class="text-base">Faturas recentes</CardTitle>
                    <Button as-child size="sm" variant="ghost">
                        <Link href="/admin/invoices">Ver todas <ArrowRight :size="13" /></Link>
                    </Button>
                </CardHeader>
                <CardContent>
                    <EmptyState v-if="recent_invoices.length === 0" title="Nenhuma fatura ainda" :icon="Receipt" />
                    <ul v-else class="divide-y divide-border">
                        <li
                            v-for="invoice in recent_invoices"
                            :key="invoice.id"
                            class="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0"
                        >
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium">{{ invoice.user?.name ?? 'Cliente' }}</p>
                                <p class="truncate text-xs text-muted-foreground">{{ invoice.invoice_number }}</p>
                            </div>
                            <div class="flex shrink-0 items-center gap-2">
                                <StatusBadge
                                    :label="invoiceStatusLabel[invoice.status] ?? invoice.status"
                                    :color="invoiceStatusColor[invoice.status] ?? 'gray'"
                                />
                                <span class="text-sm font-semibold">{{ brl(invoice.total) }}</span>
                            </div>
                        </li>
                    </ul>
                </CardContent>
            </Card>
        </div>

        <!-- ── Atalhos rápidos ──────────────────────────────────────────── -->
        <Card>
            <CardHeader>
                <CardTitle class="text-base">Atalhos rápidos</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-4">
                    <Link
                        href="/admin/users/create"
                        class="flex items-center gap-3 rounded-lg border p-3 text-sm font-medium transition-colors hover:bg-accent"
                    >
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-100 text-violet-600 dark:bg-violet-900/30">
                            <Users :size="15" />
                        </div>
                        Novo cliente
                    </Link>
                    <Link
                        href="/admin/invoices/create"
                        class="flex items-center gap-3 rounded-lg border p-3 text-sm font-medium transition-colors hover:bg-accent"
                    >
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900/30">
                            <Receipt :size="15" />
                        </div>
                        Nova fatura
                    </Link>
                    <Link
                        href="/admin/products/create"
                        class="flex items-center gap-3 rounded-lg border p-3 text-sm font-medium transition-colors hover:bg-accent"
                    >
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/30">
                            <Package :size="15" />
                        </div>
                        Novo produto
                    </Link>
                    <Link
                        href="/admin/payment-gateways/create"
                        class="flex items-center gap-3 rounded-lg border p-3 text-sm font-medium transition-colors hover:bg-accent"
                    >
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30">
                            <CreditCard :size="15" />
                        </div>
                        Configurar gateway
                    </Link>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
