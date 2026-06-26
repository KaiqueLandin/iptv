<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import EmailTemplateForm from '@/components/admin/EmailTemplateForm.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import { Button } from '@/components/ui/button';

interface TemplateModel {
    id: number;
    name: string;
    subject: string;
    body_html: string;
    body_text: string | null;
    type: string;
    is_active: boolean;
}

const props = defineProps<{
    template: TemplateModel;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Templates de email', href: '/admin/email-templates' },
            { title: 'Editar', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head :title="`Editar ${props.template.name}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader title="Editar template" :description="props.template.name">
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
            :action="`/admin/email-templates/${props.template.id}`"
            method="put"
            :template="props.template"
            submit-label="Salvar alterações"
        />
    </div>
</template>
