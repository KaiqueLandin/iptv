<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import BrandForm from '@/components/admin/BrandForm.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import { Button } from '@/components/ui/button';

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Marcas', href: '/admin/brands' },
            { title: 'Nova', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head title="Nova marca" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Nova marca"
            description="Cadastre uma nova marca para a loja."
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/brands">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <BrandForm
            action="/admin/brands"
            method="post"
            submit-label="Criar marca"
        />
    </div>
</template>
