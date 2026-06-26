<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import EmailTemplateForm from '@/components/admin/EmailTemplateForm.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import { Button } from '@/components/ui/button';

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Templates de email', href: '/admin/email-templates' },
            { title: 'Novo', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head title="Novo template" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Novo template"
            description="Crie um modelo de email automático."
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/email-templates">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <EmailTemplateForm
            action="/admin/email-templates"
            method="post"
            submit-label="Criar template"
        />
    </div>
</template>
