<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Mail, Pencil } from '@lucide/vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface AdminUser {
    id: number;
    name: string;
    email: string;
    role: string;
    is_active: boolean;
    created_at: string;
    email_verified_at: string | null;
}

const props = defineProps<{
    user: AdminUser;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Clientes', href: '/admin/users' },
            { title: 'Detalhes', href: '#' },
        ],
    }),
});

const roleLabel: Record<string, string> = {
    user: 'Usuário',
    admin: 'Administrador',
    superadmin: 'Super Admin',
};

const roleColor: Record<string, string> = {
    user: 'gray',
    admin: 'blue',
    superadmin: 'purple',
};

const formatDate = (date: string | null) =>
    date ? new Date(date).toLocaleString('pt-BR') : '—';
</script>

<template>
    <Head :title="props.user.name" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader :title="props.user.name" :description="props.user.email">
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/users">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
                <Button as-child>
                    <Link :href="`/admin/users/${props.user.id}/edit`">
                        <Pencil :size="16" />
                        Editar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <Card class="max-w-2xl">
            <CardHeader>
                <CardTitle class="flex items-center gap-2 text-base">
                    <Mail :size="18" class="text-muted-foreground" />
                    Informações da conta
                </CardTitle>
            </CardHeader>
            <CardContent>
                <dl class="divide-y divide-border text-sm">
                    <div class="flex items-center justify-between py-3 first:pt-0">
                        <dt class="text-muted-foreground">Papel</dt>
                        <dd>
                            <StatusBadge
                                :label="roleLabel[props.user.role] ?? props.user.role"
                                :color="roleColor[props.user.role] ?? 'gray'"
                            />
                        </dd>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <dt class="text-muted-foreground">Status</dt>
                        <dd>
                            <StatusBadge
                                :label="props.user.is_active ? 'Ativo' : 'Inativo'"
                                :color="props.user.is_active ? 'green' : 'red'"
                            />
                        </dd>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <dt class="text-muted-foreground">Email verificado</dt>
                        <dd>{{ formatDate(props.user.email_verified_at) }}</dd>
                    </div>
                    <div class="flex items-center justify-between py-3 last:pb-0">
                        <dt class="text-muted-foreground">Cadastrado em</dt>
                        <dd>{{ formatDate(props.user.created_at) }}</dd>
                    </div>
                </dl>
            </CardContent>
        </Card>
    </div>
</template>
