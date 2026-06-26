<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Check, Copy } from '@lucide/vue';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

interface ServiceDetail {
    id: number;
    service_number: string;
    product_name: string | null;
    status: string;
    status_label: string;
    status_color: string;
    username: string | null;
    password: string | null;
    activation_code: string | null;
    access_url: string | null;
    expires_at: string | null;
    delivered_at: string | null;
    created_at: string | null;
}

const props = defineProps<{
    service: ServiceDetail;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Meus serviços', href: '/services' },
            { title: 'Detalhes', href: '#' },
        ],
    }),
});

const statusClasses: Record<string, string> = {
    green: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
    yellow: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
    orange: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
    red: 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400',
    gray: 'bg-muted text-muted-foreground',
};

const copiedField = ref<string | null>(null);

const copy = async (field: string, value: string | null) => {
    if (!value) return;
    await navigator.clipboard.writeText(value);
    copiedField.value = field;
    setTimeout(() => (copiedField.value = null), 2000);
};

const formatDate = (date: string | null) =>
    date ? new Date(date).toLocaleDateString('pt-BR') : '—';

const fields = [
    { key: 'activation_code', label: 'Código de ativação' },
    { key: 'username', label: 'Usuário' },
    { key: 'password', label: 'Senha' },
    { key: 'access_url', label: 'URL de acesso' },
] as const;
</script>

<template>
    <Head :title="props.service.service_number" />

    <div class="mx-auto flex w-full max-w-2xl flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">
                    {{ props.service.product_name ?? 'Serviço' }}
                </h1>
                <p class="text-sm text-muted-foreground">
                    {{ props.service.service_number }}
                </p>
            </div>
            <Button as-child variant="outline">
                <Link href="/services">
                    <ArrowLeft :size="16" />
                    Voltar
                </Link>
            </Button>
        </div>

        <Card>
            <CardHeader class="flex-row items-center justify-between">
                <CardTitle class="text-base">Dados de acesso</CardTitle>
                <span
                    :class="[
                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                        statusClasses[props.service.status_color] ?? statusClasses.gray,
                    ]"
                >
                    {{ props.service.status_label }}
                </span>
            </CardHeader>
            <CardContent class="space-y-3">
                <div
                    v-for="field in fields"
                    :key="field.key"
                    v-show="props.service[field.key]"
                    class="flex items-center justify-between gap-3 rounded-lg border p-3"
                >
                    <div class="min-w-0">
                        <p class="text-xs text-muted-foreground">{{ field.label }}</p>
                        <p class="truncate font-mono text-sm font-medium">
                            {{ props.service[field.key] }}
                        </p>
                    </div>
                    <Button
                        size="sm"
                        variant="outline"
                        @click="copy(field.key, props.service[field.key])"
                    >
                        <Check v-if="copiedField === field.key" :size="14" />
                        <Copy v-else :size="14" />
                    </Button>
                </div>

                <div class="flex justify-between border-t pt-3 text-sm">
                    <span class="text-muted-foreground">Válido até</span>
                    <span class="font-medium">{{ formatDate(props.service.expires_at) }}</span>
                </div>
            </CardContent>
        </Card>

        <p class="text-center text-xs text-muted-foreground">
            Guarde esses dados com segurança. Em caso de dúvida, fale com o suporte.
        </p>
    </div>
</template>
