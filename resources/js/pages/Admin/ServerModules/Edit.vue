<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import ServerModuleForm from '@/components/admin/ServerModuleForm.vue';
import { Button } from '@/components/ui/button';

interface ModuleModel {
    id: number;
    name: string;
    module_key: string;
    description: string | null;
    server_hostname: string | null;
    server_port: number | null;
    use_ssl: boolean;
    is_active: boolean;
}

const props = defineProps<{
    module: ModuleModel;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Servidores', href: '/admin/server-modules' },
            { title: 'Editar', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head :title="`Editar ${props.module.name}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader title="Editar módulo" :description="props.module.name">
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
            :action="`/admin/server-modules/${props.module.id}`"
            method="put"
            :module="props.module"
            is-edit
            submit-label="Salvar alterações"
        />
    </div>
</template>
