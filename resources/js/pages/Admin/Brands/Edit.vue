<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Trash2 } from '@lucide/vue';
import BrandForm from '@/components/admin/BrandForm.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import { Button } from '@/components/ui/button';

interface BrandModel {
    id: number;
    name: string;
    description: string | null;
    logo_url: string | null;
    website_url: string | null;
    is_active: boolean;
    sort_order: number;
}

const props = defineProps<{
    brand: BrandModel;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Marcas', href: '/admin/brands' },
            { title: 'Editar', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head :title="`Editar ${props.brand.name}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Editar marca"
            :description="props.brand.name"
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
            :action="`/admin/brands/${props.brand.id}`"
            method="put"
            :brand="props.brand"
            submit-label="Salvar alterações"
        />

        <Form
            :action="`/admin/brands/${props.brand.id}`"
            method="delete"
            v-slot="{ processing }"
            class="max-w-3xl"
        >
            <Button type="submit" variant="destructive" :disabled="processing">
                <Trash2 :size="16" />
                Deletar marca
            </Button>
        </Form>
    </div>
</template>
