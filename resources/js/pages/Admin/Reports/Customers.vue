<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import { ref, watch } from 'vue';
import LineChart from '@/components/admin/charts/LineChart.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import StatCard from '@/components/admin/StatCard.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

interface Point {
    date: string;
    count: number;
}

interface Stats {
    total_customers: number;
    active_customers: number;
    new_this_month: number;
    new_today: number;
}

const props = defineProps<{
    data: Point[];
    stats: Stats;
    period: string;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Relatórios', href: '/admin/reports' },
            { title: 'Clientes', href: '#' },
        ],
    }),
});

const period = ref(props.period);

watch(period, () => {
    router.get(
        '/admin/reports/customers',
        { period: period.value },
        { preserveState: true, replace: true, preserveScroll: true },
    );
});

const formatDate = (date: string) =>
    new Date(date).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: 'short',
    });
</script>

<template>
    <Head title="Relatório de clientes" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Clientes"
            description="Crescimento da base de clientes."
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/reports">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <StatCard label="Total" :value="props.stats.total_customers" tone="violet" />
            <StatCard label="Ativos" :value="props.stats.active_customers" tone="emerald" />
            <StatCard label="Novos no mês" :value="props.stats.new_this_month" tone="blue" />
            <StatCard label="Novos hoje" :value="props.stats.new_today" tone="amber" />
        </div>

        <Card>
            <CardHeader class="flex-row items-center justify-between">
                <CardTitle class="text-base">Novos cadastros</CardTitle>
                <select
                    v-model="period"
                    class="h-8 rounded-md border bg-transparent px-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                >
                    <option value="day">Por dia</option>
                    <option value="month">Por mês</option>
                    <option value="year">Por ano</option>
                </select>
            </CardHeader>
            <CardContent>
                <LineChart
                    v-if="data.length"
                    :data="data"
                    y-key="count"
                    x-key="date"
                    color="var(--color-chart-1)"
                    :value-formatter="(v) => String(v)"
                    :x-formatter="formatDate"
                />
                <p v-else class="py-12 text-center text-sm text-muted-foreground">
                    Sem dados para o período selecionado.
                </p>
            </CardContent>
        </Card>
    </div>
</template>
