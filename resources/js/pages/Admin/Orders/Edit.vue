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

interface OrderModel {
    id: number;
    order_number: string;
    status: string;
    amount: string;
    next_due_date: string | null;
}

const props = defineProps<{
    order: OrderModel;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Pedidos', href: '/admin/orders' },
            { title: 'Editar', href: '#' },
        ],
    }),
});

const statuses = [
    { value: 'pending', label: 'Pendente' },
    { value: 'active', label: 'Ativo' },
    { value: 'suspended', label: 'Suspenso' },
    { value: 'cancelled', label: 'Cancelado' },
    { value: 'completed', label: 'Completo' },
];
</script>

<template>
    <Head :title="`Editar ${props.order.order_number}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader title="Editar pedido" :description="props.order.order_number">
            <template #actions>
                <Button as-child variant="outline">
                    <Link :href="`/admin/orders/${props.order.id}`">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <Card class="max-w-2xl">
            <CardContent>
                <Form
                    :action="`/admin/orders/${props.order.id}`"
                    method="put"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-6"
                >
                    <div class="grid gap-2">
                        <Label for="status">Status</Label>
                        <select
                            id="status"
                            name="status"
                            class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        >
                            <option
                                v-for="option in statuses"
                                :key="option.value"
                                :value="option.value"
                                :selected="option.value === props.order.status"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                        <InputError :message="errors.status" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="amount">Valor (R$)</Label>
                        <Input
                            id="amount"
                            name="amount"
                            type="number"
                            step="0.01"
                            min="0"
                            :default-value="props.order.amount"
                            required
                        />
                        <InputError :message="errors.amount" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="next_due_date">Próximo vencimento</Label>
                        <Input
                            id="next_due_date"
                            name="next_due_date"
                            type="date"
                            :default-value="props.order.next_due_date ?? ''"
                        />
                        <InputError :message="errors.next_due_date" />
                    </div>

                    <div class="flex justify-end gap-2 border-t pt-4">
                        <Button as-child variant="outline" type="button">
                            <Link :href="`/admin/orders/${props.order.id}`">
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
