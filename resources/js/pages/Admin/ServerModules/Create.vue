<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import ServerModuleForm from '@/components/admin/ServerModuleForm.vue';
import { Button } from '@/components/ui/button';

defineProps<{
    availableModules: Record<string, string>;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Servidores', href: '/admin/server-modules' },
            { title: 'Novo', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head title="Novo módulo de servidor" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Novo módulo de servidor"
            description="Configure a integração com um servidor."
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/server-modules">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <ServerModuleForm
            action="/admin/server-modules"
            method="post"
            :available-modules="availableModules"
            submit-label="Criar módulo"
        />
    </div>
</template>
