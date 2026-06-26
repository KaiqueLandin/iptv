<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

interface AdminUser {
    id: number;
    name: string;
    email: string;
    role: string;
    is_active: boolean;
}

interface RoleOption {
    value: string;
    label: string;
}

const props = defineProps<{
    user: AdminUser;
    roles: RoleOption[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Clientes', href: '/admin/users' },
            { title: 'Editar', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head :title="`Editar ${props.user.name}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Editar cliente"
            :description="props.user.email"
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/users">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <Card class="max-w-2xl">
            <CardContent>
                <Form
                    :action="`/admin/users/${props.user.id}`"
                    method="put"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-6"
                >
                    <div class="grid gap-2">
                        <Label for="name">Nome</Label>
                        <Input
                            id="name"
                            name="name"
                            :default-value="props.user.name"
                            required
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            name="email"
                            type="email"
                            :default-value="props.user.email"
                            required
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="role">Papel</Label>
                        <select
                            id="role"
                            name="role"
                            class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        >
                            <option
                                v-for="option in props.roles"
                                :key="option.value"
                                :value="option.value"
                                :selected="option.value === props.user.role"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                        <InputError :message="errors.role" />
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="hidden" name="is_active" value="0" />
                        <input
                            id="is_active"
                            name="is_active"
                            type="checkbox"
                            value="1"
                            :checked="props.user.is_active"
                            class="h-4 w-4 rounded border-input"
                        />
                        <Label for="is_active" class="cursor-pointer">
                            Conta ativa
                        </Label>
                        <InputError :message="errors.is_active" />
                    </div>

                    <div class="flex justify-end gap-2 border-t pt-4">
                        <Button as-child variant="outline" type="button">
                            <Link href="/admin/users">Cancelar</Link>
                        </Button>
                        <Button type="submit" :disabled="processing">
                            <Spinner v-if="processing" />
                            Salvar alterações
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>
