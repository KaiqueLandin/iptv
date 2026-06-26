<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { KeyRound, Tv } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

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

defineProps<{
    services: ServiceItem[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [{ title: 'Meus serviços', href: '/services' }],
    }),
});

const statusClasses: Record<string, string> = {
    green: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
    yellow: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
    orange: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
    red: 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400',
    gray: 'bg-muted text-muted-foreground',
};

const formatDate = (date: string | null) =>
    date ? new Date(date).toLocaleDateString('pt-BR') : '—';
</script>

<template>
    <Head title="Meus serviços" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Meus serviços</h1>
            <p class="text-sm text-muted-foreground">
                Seus acessos e códigos de ativação.
            </p>
        </div>

        <div
            v-if="services.length === 0"
            class="flex flex-col items-center justify-center rounded-lg border border-dashed py-16 text-center"
        >
            <div
                class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-muted text-muted-foreground"
            >
                <Tv :size="22" />
            </div>
            <p class="text-sm font-medium">Nenhum serviço ainda</p>
            <p class="mt-1 text-xs text-muted-foreground">
                Compre um plano para receber seu acesso.
            </p>
            <Button as="a" href="/" class="mt-4">Ver planos</Button>
        </div>

        <div v-else class="grid gap-4 md:grid-cols-2">
            <Card v-for="service in services" :key="service.id">
                <CardHeader class="flex-row items-center justify-between">
                    <CardTitle class="flex items-center gap-2 text-base">
                        <KeyRound :size="18" class="text-emerald-600" />
                        {{ service.product_name ?? 'Serviço' }}
                    </CardTitle>
                    <span
                        :class="[
                            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                            statusClasses[service.status_color] ?? statusClasses.gray,
                        ]"
                    >
                        {{ service.status_label }}
                    </span>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div
                        v-if="service.activation_code"
                        class="rounded-lg bg-muted p-3"
                    >
                        <p class="text-xs text-muted-foreground">Código de ativação</p>
                        <p class="font-mono text-lg font-semibold">
                            {{ service.activation_code }}
                        </p>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-muted-foreground">
                            {{ service.service_number }}
                        </span>
                        <span class="text-muted-foreground">
                            Válido até {{ formatDate(service.expires_at) }}
                        </span>
                    </div>
                    <Button as-child variant="outline" class="w-full">
                        <Link :href="`/services/${service.id}`">
                            Ver detalhes de acesso
                        </Link>
                    </Button>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
