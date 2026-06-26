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

interface InvoiceModel {
    id: number;
    invoice_number: string;
    due_date: string;
    tax: string;
    discount: string;
    notes: string | null;
}

const props = defineProps<{
    invoice: InvoiceModel;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Faturas', href: '/admin/invoices' },
            { title: 'Editar', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head :title="`Editar ${props.invoice.invoice_number}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Editar fatura"
            :description="props.invoice.invoice_number"
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link :href="`/admin/invoices/${props.invoice.id}`">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <Card class="max-w-2xl">
            <CardContent>
                <Form
                    :action="`/admin/invoices/${props.invoice.id}`"
                    method="put"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-6"
                >
                    <div class="grid gap-2">
                        <Label for="due_date">Vencimento</Label>
                        <Input
                            id="due_date"
                            name="due_date"
                            type="date"
                            :default-value="props.invoice.due_date"
                            required
                        />
                        <InputError :message="errors.due_date" />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="tax">Impostos (R$)</Label>
                            <Input
                                id="tax"
                                name="tax"
                                type="number"
                                step="0.01"
                                min="0"
                                :default-value="props.invoice.tax"
                            />
                            <InputError :message="errors.tax" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="discount">Desconto (R$)</Label>
                            <Input
                                id="discount"
                                name="discount"
                                type="number"
                                step="0.01"
                                min="0"
                                :default-value="props.invoice.discount"
                            />
                            <InputError :message="errors.discount" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="notes">Observações</Label>
                        <textarea
                            id="notes"
                            name="notes"
                            rows="3"
                            class="rounded-md border bg-transparent px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                            :value="props.invoice.notes ?? ''"
                        ></textarea>
                        <InputError :message="errors.notes" />
                    </div>

                    <div class="flex justify-end gap-2 border-t pt-4">
                        <Button as-child variant="outline" type="button">
                            <Link :href="`/admin/invoices/${props.invoice.id}`">
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
