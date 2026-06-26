<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, BarChart3 } from '@lucide/vue';
import BarChart from '@/components/admin/charts/BarChart.vue';
import DataTable from '@/components/admin/DataTable.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface TopProduct {
    name: string;
    orders_count: number;
    revenue: string;
}

defineProps<{
    topProducts: TopProduct[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Relatórios', href: '/admin/reports' },
            { title: 'Produtos', href: '#' },
        ],
    }),
});

const brl = (value: string) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(Number(value));
</script>

<template>
    <Head title="Relatório de produtos" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Produtos mais vendidos"
            description="Ranking de produtos por número de pedidos."
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

        <EmptyState
            v-if="topProducts.length === 0"
            title="Sem dados de vendas"
            :icon="BarChart3"
        />

        <template v-else>
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Pedidos por produto</CardTitle>
                </CardHeader>
                <CardContent>
                    <BarChart
                        :data="topProducts"
                        y-key="orders_count"
                        x-key="name"
                        color="var(--color-chart-4)"
                        :value-formatter="(v) => String(v)"
                        :x-formatter="(name) => name.length > 12 ? name.slice(0, 12) + '…' : name"
                    />
                </CardContent>
            </Card>

            <DataTable :columns="['#', 'Produto', 'Pedidos', 'Receita']">
                <tr
                    v-for="(product, index) in topProducts"
                    :key="product.name"
                    class="transition-colors hover:bg-muted/40"
                >
                    <td class="px-4 py-3 font-semibold text-muted-foreground">
                        {{ index + 1 }}
                    </td>
                    <td class="px-4 py-3 font-medium">{{ product.name }}</td>
                    <td class="px-4 py-3">{{ product.orders_count }}</td>
                    <td class="px-4 py-3 font-semibold">{{ brl(product.revenue) }}</td>
                </tr>
            </DataTable>
        </template>
    </div>
</template>
