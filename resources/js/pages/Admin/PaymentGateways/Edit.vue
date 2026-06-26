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

interface GatewayModel {
    id: number;
    name: string;
    gateway_key: string;
    description: string | null;
    is_active: boolean;
    is_sandbox: boolean;
    fee_fixed: string;
    fee_percentage: string;
    sort_order: number;
}

const props = defineProps<{
    gateway: GatewayModel;
    presets: Record<string, Preset>;
    configuredKeys: string[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Gateways', href: '/admin/payment-gateways' },
            { title: 'Editar', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head :title="`Editar ${props.gateway.name}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader title="Editar gateway" :description="props.gateway.name">
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
            :action="`/admin/payment-gateways/${props.gateway.id}`"
            method="put"
            :gateway="props.gateway"
            :presets="presets"
            :configured-keys="configuredKeys"
            is-edit
            submit-label="Salvar alterações"
        />
    </div>
</template>
