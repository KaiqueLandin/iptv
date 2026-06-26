<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import WebhookForm from '@/components/admin/WebhookForm.vue';
import { Button } from '@/components/ui/button';

interface WebhookModel {
    id: number;
    event: string;
    url: string;
    secret: string | null;
    is_active: boolean;
    timeout: number;
    max_retries: number;
}

const props = defineProps<{
    webhook: WebhookModel;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Webhooks', href: '/admin/webhooks' },
            { title: 'Editar', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head title="Editar webhook" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader title="Editar webhook" :description="props.webhook.event">
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/webhooks">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <WebhookForm
            :action="`/admin/webhooks/${props.webhook.id}`"
            method="put"
            :webhook="props.webhook"
            submit-label="Salvar alterações"
        />
    </div>
</template>
