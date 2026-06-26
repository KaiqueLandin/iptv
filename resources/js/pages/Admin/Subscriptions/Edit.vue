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

interface SubscriptionModel {
    id: number;
    amount: string;
    next_billing_date: string | null;
    expires_at: string | null;
    user?: { name: string } | null;
    product?: { name: string } | null;
}

const props = defineProps<{
    subscription: SubscriptionModel;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Assinaturas', href: '/admin/subscriptions' },
            { title: 'Editar', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head title="Editar assinatura" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Editar assinatura"
            :description="props.subscription.user?.name ?? ''"
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link :href="`/admin/subscriptions/${props.subscription.id}`">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <Card class="max-w-2xl">
            <CardContent>
                <Form
                    :action="`/admin/subscriptions/${props.subscription.id}`"
                    method="put"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-6"
                >
                    <div class="grid gap-2">
                        <Label for="amount">Valor (R$)</Label>
                        <Input
                            id="amount"
                            name="amount"
                            type="number"
                            step="0.01"
                            min="0"
                            :default-value="props.subscription.amount"
                            required
                        />
                        <InputError :message="errors.amount" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="next_billing_date">Próxima cobrança</Label>
                        <Input
                            id="next_billing_date"
                            name="next_billing_date"
                            type="date"
                            :default-value="props.subscription.next_billing_date ?? ''"
                        />
                        <InputError :message="errors.next_billing_date" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="expires_at">Expira em</Label>
                        <Input
                            id="expires_at"
                            name="expires_at"
                            type="date"
                            :default-value="props.subscription.expires_at ?? ''"
                        />
                        <InputError :message="errors.expires_at" />
                    </div>

                    <div class="flex justify-end gap-2 border-t pt-4">
                        <Button as-child variant="outline" type="button">
                            <Link :href="`/admin/subscriptions/${props.subscription.id}`">
                                Cancelar
                            </Link>
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
