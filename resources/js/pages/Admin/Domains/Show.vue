<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Pencil, RefreshCcw } from '@lucide/vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

interface DomainModel {
    id: number;
    domain_name: string;
    registrar: string | null;
    status: string;
    registration_date: string | null;
    expiry_date: string | null;
    auto_renew: boolean;
    privacy_protection: boolean;
    user?: { name: string; email: string } | null;
}

const props = defineProps<{
    domain: DomainModel;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Domínios', href: '/admin/domains' },
            { title: 'Detalhes', href: '#' },
        ],
    }),
});

const statusLabel: Record<string, string> = {
    pending: 'Pendente',
    active: 'Ativo',
    expired: 'Expirado',
    cancelled: 'Cancelado',
    transferred: 'Transferido',
};

const statusColor: Record<string, string> = {
    pending: 'yellow',
    active: 'green',
    expired: 'red',
    cancelled: 'gray',
    transferred: 'blue',
};

const formatDate = (date: string | null) =>
    date ? new Date(date).toLocaleDateString('pt-BR') : '—';
</script>

<template>
    <Head :title="props.domain.domain_name" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            :title="props.domain.domain_name"
            :description="props.domain.user?.name ?? 'Cliente'"
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/domains">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
                <Button as-child>
                    <Link :href="`/admin/domains/${props.domain.id}/edit`">
                        <Pencil :size="16" />
                        Editar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <div class="grid gap-6 lg:grid-cols-3">
            <Card class="lg:col-span-2">
                <CardHeader class="flex-row items-center justify-between">
                    <CardTitle class="text-base">Detalhes do domínio</CardTitle>
                    <StatusBadge
                        :label="statusLabel[props.domain.status] ?? props.domain.status"
                        :color="statusColor[props.domain.status] ?? 'gray'"
                    />
                </CardHeader>
                <CardContent>
                    <dl class="divide-y divide-border text-sm">
                        <div class="flex justify-between py-3 first:pt-0">
                            <dt class="text-muted-foreground">Registrar</dt>
                            <dd>{{ props.domain.registrar ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between py-3">
                            <dt class="text-muted-foreground">Registro</dt>
                            <dd>{{ formatDate(props.domain.registration_date) }}</dd>
                        </div>
                        <div class="flex justify-between py-3">
                            <dt class="text-muted-foreground">Expira em</dt>
                            <dd>{{ formatDate(props.domain.expiry_date) }}</dd>
                        </div>
                        <div class="flex justify-between py-3">
                            <dt class="text-muted-foreground">Renovação automática</dt>
                            <dd>{{ props.domain.auto_renew ? 'Sim' : 'Não' }}</dd>
                        </div>
                        <div class="flex justify-between py-3 last:pb-0">
                            <dt class="text-muted-foreground">Proteção de privacidade</dt>
                            <dd>{{ props.domain.privacy_protection ? 'Sim' : 'Não' }}</dd>
                        </div>
                    </dl>
                </CardContent>
            </Card>

            <Card class="h-fit">
                <CardHeader>
                    <CardTitle class="text-base">Renovar</CardTitle>
                </CardHeader>
                <CardContent>
                    <Form
                        :action="`/admin/domains/${props.domain.id}/renew`"
                        method="post"
                        v-slot="{ processing }"
                        class="flex flex-col gap-3"
                    >
                        <Input
                            name="years"
                            type="number"
                            min="1"
                            max="10"
                            :default-value="1"
                            required
                        />
                        <Button type="submit" :disabled="processing">
                            <RefreshCcw :size="16" />
                            Renovar domínio
                        </Button>
                    </Form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
