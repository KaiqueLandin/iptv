<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import DomainForm from '@/components/admin/DomainForm.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import { Button } from '@/components/ui/button';

interface UserOption {
    id: number;
    name: string;
    email: string;
}

defineProps<{
    users: UserOption[];
    registrars: string[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Domínios', href: '/admin/domains' },
            { title: 'Novo', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head title="Novo domínio" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Novo domínio"
            description="Cadastre um domínio para um cliente."
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/domains">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <DomainForm
            action="/admin/domains"
            method="post"
            :users="users"
            :registrars="registrars"
            submit-label="Criar domínio"
        />
    </div>
</template>
