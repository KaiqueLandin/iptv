<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import GatewayForm from '@/components/admin/GatewayForm.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import { Button } from '@/components/ui/button';

interface PresetField {
    key: string;
    label: string;
    type: string;
    required: boolean;
}

interface Preset {
    name: string;
    description: string;
    fields: PresetField[];
}

defineProps<{
    presets: Record<string, Preset>;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Gateways', href: '/admin/payment-gateways' },
            { title: 'Novo', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head title="Novo gateway" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Novo gateway"
            description="Configure um novo meio de pagamento."
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/payment-gateways">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <GatewayForm
            action="/admin/payment-gateways"
            method="post"
            :presets="presets"
            submit-label="Criar gateway"
        />
    </div>
</template>
