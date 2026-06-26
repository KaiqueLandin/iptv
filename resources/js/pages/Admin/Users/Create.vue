<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

interface RoleOption {
    value: string;
    label: string;
}

defineProps<{
    roles: RoleOption[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Clientes', href: '/admin/users' },
            { title: 'Novo', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head title="Novo usuário" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader title="Novo usuário" description="Crie uma conta manualmente.">
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
            <CardContent class="py-6">
                <Form
                    action="/admin/users"
                    method="post"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-5"
                >
                    <div class="grid gap-2">
                        <Label for="name">Nome</Label>
                        <Input id="name" name="name" required autofocus placeholder="Nome completo" />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input id="email" name="email" type="email" required placeholder="email@exemplo.com" />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="phone">Telefone</Label>
                        <Input id="phone" name="phone" type="tel" placeholder="(11) 99999-9999" />
                        <InputError :message="errors.phone" />
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="password">Senha</Label>
                            <PasswordInput id="password" name="password" required placeholder="Mínimo 8 caracteres" />
                            <InputError :message="errors.password" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="password_confirmation">Confirmar senha</Label>
                            <PasswordInput id="password_confirmation" name="password_confirmation" required placeholder="Repita a senha" />
                            <InputError :message="errors.password_confirmation" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="role">Papel</Label>
                        <select
                            id="role"
                            name="role"
                            class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        >
                            <option v-for="option in roles" :key="option.value" :value="option.value">
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
                            checked
                            class="h-4 w-4 rounded border-input"
                        />
                        <Label for="is_active" class="cursor-pointer">Conta ativa</Label>
                    </div>

                    <div class="flex justify-end gap-2 border-t pt-4">
                        <Button as-child variant="outline" type="button">
                            <Link href="/admin/users">Cancelar</Link>
                        </Button>
                        <Button type="submit" :disabled="processing">
                            <Spinner v-if="processing" />
                            Criar usuário
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>
