<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import DomainForm from '@/components/admin/DomainForm.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import { Button } from '@/components/ui/button';

interface DomainModel {
    id: number;
    domain_name: string;
    registrar: string | null;
    status: string;
    registration_date: string | null;
    expiry_date: string | null;
    auto_renew: boolean;
    privacy_protection: boolean;
}

const props = defineProps<{
    domain: DomainModel;
    registrars: string[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Domínios', href: '/admin/domains' },
            { title: 'Editar', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head :title="`Editar ${props.domain.domain_name}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader title="Editar domínio" :description="props.domain.domain_name">
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
            :action="`/admin/domains/${props.domain.id}`"
            method="put"
            :domain="props.domain"
            :registrars="registrars"
            is-edit
            submit-label="Salvar alterações"
        />
    </div>
</template>
