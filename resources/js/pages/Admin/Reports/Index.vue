<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BarChart3, TrendingUp, Users } from '@lucide/vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Relatórios', href: '/admin/reports' },
        ],
    }),
});

const reports = [
    {
        title: 'Receita',
        description: 'Evolução do faturamento ao longo do tempo.',
        href: '/admin/reports/revenue',
        icon: TrendingUp,
        tone: 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
    },
    {
        title: 'Clientes',
        description: 'Crescimento da base de clientes.',
        href: '/admin/reports/customers',
        icon: Users,
        tone: 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
    },
    {
        title: 'Produtos',
        description: 'Produtos mais vendidos e receita gerada.',
        href: '/admin/reports/products',
        icon: BarChart3,
        tone: 'bg-violet-100 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400',
    },
];
</script>

<template>
    <Head title="Relatórios" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Relatórios"
            description="Análises de desempenho da plataforma."
        />

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="report in reports"
                :key="report.href"
                :href="report.href"
            >
                <Card class="transition-colors hover:border-emerald-500">
                    <CardHeader>
                        <div
                            :class="[
                                'flex h-11 w-11 items-center justify-center rounded-xl',
                                report.tone,
                            ]"
                        >
                            <component :is="report.icon" :size="20" />
                        </div>
                        <CardTitle class="mt-3 text-base">
                            {{ report.title }}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground">
                            {{ report.description }}
                        </p>
                    </CardContent>
                </Card>
            </Link>
        </div>
    </div>
</template>
