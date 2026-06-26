<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import WebhookForm from '@/components/admin/WebhookForm.vue';
import { Button } from '@/components/ui/button';

defineProps<{
    events: string[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Webhooks', href: '/admin/webhooks' },
            { title: 'Novo', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head title="Novo webhook" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Novo webhook"
            description="Configure um endpoint para receber eventos."
        >
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
            action="/admin/webhooks"
            method="post"
            :events="events"
            submit-label="Criar webhook"
        />
    </div>
</template>
