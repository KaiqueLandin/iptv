<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Plus, Trash2 } from '@lucide/vue';
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

interface UserOption {
    id: number;
    name: string;
    email: string;
}

const props = defineProps<{
    users: UserOption[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Faturas', href: '/admin/invoices' },
            { title: 'Nova', href: '#' },
        ],
    }),
});

const form = useForm({
    user_id: props.users[0]?.id ?? '',
    due_date: '',
    tax: 0,
    discount: 0,
    notes: '',
    items: [{ description: '', quantity: 1, unit_price: 0 }],
});

const addItem = () => {
    form.items.push({ description: '', quantity: 1, unit_price: 0 });
};

const removeItem = (index: number) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

const subtotal = computed(() =>
    form.items.reduce(
        (sum, item) => sum + Number(item.quantity) * Number(item.unit_price),
        0,
    ),
);

const total = computed(
    () => subtotal.value + Number(form.tax) - Number(form.discount),
);

const brl = (value: number) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value);

const submit = () => {
    form.post('/admin/invoices');
};
</script>

<template>
    <Head title="Nova fatura" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Nova fatura"
            description="Emita uma fatura manual para um cliente."
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/invoices">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <Card class="max-w-3xl">
            <CardContent>
                <form class="flex flex-col gap-6" @submit.prevent="submit">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="user_id">Cliente</Label>
                            <select
                                id="user_id"
                                v-model="form.user_id"
                                class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                            >
                                <option
                                    v-for="user in users"
                                    :key="user.id"
                                    :value="user.id"
                                >
                                    {{ user.name }} ({{ user.email }})
                                </option>
                            </select>
                            <InputError :message="form.errors.user_id" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="due_date">Vencimento</Label>
                            <Input
                                id="due_date"
                                v-model="form.due_date"
                                type="date"
                                required
                            />
                            <InputError :message="form.errors.due_date" />
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <Label>Itens</Label>
                            <Button
                                type="button"
                                size="sm"
                                variant="outline"
                                @click="addItem"
                            >
                                <Plus :size="14" />
                                Adicionar item
                            </Button>
                        </div>

                        <div
                            v-for="(item, index) in form.items"
                            :key="index"
                            class="flex items-end gap-2"
                        >
                            <div class="flex-1 space-y-1">
                                <Input
                                    v-model="item.description"
                                    placeholder="Descrição"
                                    required
                                />
                            </div>
                            <div class="w-20 space-y-1">
                                <Input
                                    v-model.number="item.quantity"
                                    type="number"
                                    min="1"
                                    placeholder="Qtd"
                                    required
                                />
                            </div>
                            <div class="w-28 space-y-1">
                                <Input
                                    v-model.number="item.unit_price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="Unitário"
                                    required
                                />
                            </div>
                            <Button
                                type="button"
                                size="icon"
                                variant="outline"
                                class="text-rose-600"
                                :disabled="form.items.length === 1"
                                @click="removeItem(index)"
                            >
                                <Trash2 :size="15" />
                            </Button>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="tax">Impostos (R$)</Label>
                            <Input
                                id="tax"
                                v-model.number="form.tax"
                                type="number"
                                step="0.01"
                                min="0"
                            />
                        </div>
                        <div class="grid gap-2">
                            <Label for="discount">Desconto (R$)</Label>
                            <Input
                                id="discount"
                                v-model.number="form.discount"
                                type="number"
                                step="0.01"
                                min="0"
                            />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="notes">Observações</Label>
                        <textarea
                            id="notes"
                            v-model="form.notes"
                            rows="2"
                            class="rounded-md border bg-transparent px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        ></textarea>
                    </div>

                    <dl class="space-y-1 border-t pt-4 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-muted-foreground">Subtotal</dt>
                            <dd>{{ brl(subtotal) }}</dd>
                        </div>
                        <div class="flex justify-between border-t pt-2 text-base font-bold">
                            <dt>Total</dt>
                            <dd>{{ brl(total) }}</dd>
                        </div>
                    </dl>

                    <div class="flex justify-end gap-2 border-t pt-4">
                        <Button as-child variant="outline" type="button">
                            <Link href="/admin/invoices">Cancelar</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            <Spinner v-if="form.processing" />
                            Criar fatura
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
