<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

interface CouponModel {
    id?: number;
    code?: string;
    description?: string | null;
    type?: string;
    value?: string | number;
    minimum_amount?: string | number | null;
    max_uses?: number | null;
    uses_per_customer?: number;
    valid_from?: string | null;
    valid_until?: string | null;
    applies_to_renewals?: boolean;
    is_active?: boolean;
}

withDefaults(
    defineProps<{
        action: string;
        method?: 'post' | 'put';
        coupon?: CouponModel;
        submitLabel?: string;
        isEdit?: boolean;
    }>(),
    {
        method: 'post',
        coupon: () => ({}),
        submitLabel: 'Salvar',
        isEdit: false,
    },
);
</script>

<template>
    <Card class="max-w-2xl">
        <CardContent>
            <Form
                :action="action"
                :method="method"
                v-slot="{ errors, processing }"
                class="flex flex-col gap-6"
            >
                <div class="grid gap-2">
                    <Label for="code">Código</Label>
                    <Input
                        id="code"
                        name="code"
                        :default-value="coupon.code"
                        :readonly="isEdit"
                        placeholder="EX: BEMVINDO10"
                        required
                    />
                    <InputError :message="errors.code" />
                </div>

                <div class="grid gap-2">
                    <Label for="description">Descrição</Label>
                    <Input
                        id="description"
                        name="description"
                        :default-value="coupon.description ?? ''"
                    />
                    <InputError :message="errors.description" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="type">Tipo</Label>
                        <select
                            id="type"
                            name="type"
                            class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        >
                            <option
                                value="percentage"
                                :selected="coupon.type === 'percentage'"
                            >
                                Porcentagem
                            </option>
                            <option
                                value="fixed"
                                :selected="coupon.type === 'fixed'"
                            >
                                Valor fixo
                            </option>
                        </select>
                        <InputError :message="errors.type" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="value">Valor</Label>
                        <Input
                            id="value"
                            name="value"
                            type="number"
                            step="0.01"
                            min="0"
                            :default-value="coupon.value ?? '0'"
                            required
                        />
                        <InputError :message="errors.value" />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="minimum_amount">Valor mínimo (R$)</Label>
                        <Input
                            id="minimum_amount"
                            name="minimum_amount"
                            type="number"
                            step="0.01"
                            min="0"
                            :default-value="coupon.minimum_amount ?? ''"
                        />
                        <InputError :message="errors.minimum_amount" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="uses_per_customer">Usos por cliente</Label>
                        <Input
                            id="uses_per_customer"
                            name="uses_per_customer"
                            type="number"
                            min="1"
                            :default-value="coupon.uses_per_customer ?? 1"
                            required
                        />
                        <InputError :message="errors.uses_per_customer" />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="max_uses">Máximo de usos (vazio = ilimitado)</Label>
                        <Input
                            id="max_uses"
                            name="max_uses"
                            type="number"
                            min="1"
                            :default-value="coupon.max_uses ?? ''"
                        />
                        <InputError :message="errors.max_uses" />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="valid_from">Válido de</Label>
                        <Input
                            id="valid_from"
                            name="valid_from"
                            type="date"
                            :default-value="coupon.valid_from ?? ''"
                        />
                        <InputError :message="errors.valid_from" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="valid_until">Válido até</Label>
                        <Input
                            id="valid_until"
                            name="valid_until"
                            type="date"
                            :default-value="coupon.valid_until ?? ''"
                        />
                        <InputError :message="errors.valid_until" />
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <label class="flex items-center gap-3">
                        <input type="hidden" name="applies_to_renewals" value="0" />
                        <input
                            name="applies_to_renewals"
                            type="checkbox"
                            value="1"
                            :checked="coupon.applies_to_renewals ?? false"
                            class="h-4 w-4 rounded border-input"
                        />
                        <span class="text-sm">Aplicar em renovações</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="hidden" name="is_active" value="0" />
                        <input
                            name="is_active"
                            type="checkbox"
                            value="1"
                            :checked="coupon.is_active ?? true"
                            class="h-4 w-4 rounded border-input"
                        />
                        <span class="text-sm">Cupom ativo</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2 border-t pt-4">
                    <Button as-child variant="outline" type="button">
                        <Link href="/admin/coupons">Cancelar</Link>
                    </Button>
                    <Button type="submit" :disabled="processing">
                        <Spinner v-if="processing" />
                        {{ submitLabel }}
                    </Button>
                </div>
            </Form>
        </CardContent>
    </Card>
</template>
