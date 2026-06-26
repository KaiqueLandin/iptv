<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

interface Option {
    value: string;
    label: string;
}

interface CrossSellOption {
    value: number;
    label: string;
}

interface BrandOption {
    value: number;
    label: string;
}

interface ProductModel {
    id?: number;
    name?: string;
    description?: string | null;
    brand_id?: number | null;
    price?: string | number;
    setup_fee?: string | number;
    billing_cycle?: string;
    type?: string;
    is_active?: boolean;
    is_featured?: boolean;
    stock?: number | null;
    cross_sell_ids?: number[];
}

withDefaults(
    defineProps<{
        action: string;
        method?: 'post' | 'put';
        product?: ProductModel;
        types: Option[];
        billingCycles: Option[];
        brands?: BrandOption[];
        crossSellOptions?: CrossSellOption[];
        submitLabel?: string;
    }>(),
    {
        method: 'post',
        product: () => ({}),
        brands: () => [],
        crossSellOptions: () => [],
        submitLabel: 'Salvar',
    },
);
</script>

<template>
    <Card class="max-w-3xl">
        <CardContent>
            <Form
                :action="action"
                :method="method"
                v-slot="{ errors, processing }"
                class="flex flex-col gap-6"
            >
                <div class="grid gap-2">
                    <Label for="name">Nome</Label>
                    <Input
                        id="name"
                        name="name"
                        :default-value="product.name"
                        required
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="description">Descrição</Label>
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        class="rounded-md border bg-transparent px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        :value="product.description ?? ''"
                    ></textarea>
                    <InputError :message="errors.description" />
                </div>

                <div class="grid gap-2 sm:max-w-sm">
                    <Label for="brand_id">Marca</Label>
                    <select
                        id="brand_id"
                        name="brand_id"
                        class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                    >
                        <option
                            value=""
                            :selected="
                                product.brand_id === null ||
                                product.brand_id === undefined
                            "
                        >
                            Sem marca
                        </option>
                        <option
                            v-for="option in brands"
                            :key="option.value"
                            :value="option.value"
                            :selected="option.value === product.brand_id"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                    <InputError :message="errors.brand_id" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="price">Preço (R$)</Label>
                        <Input
                            id="price"
                            name="price"
                            type="number"
                            step="0.01"
                            min="0"
                            :default-value="product.price ?? '0'"
                            required
                        />
                        <InputError :message="errors.price" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="setup_fee">Taxa de setup (R$)</Label>
                        <Input
                            id="setup_fee"
                            name="setup_fee"
                            type="number"
                            step="0.01"
                            min="0"
                            :default-value="product.setup_fee ?? '0'"
                        />
                        <InputError :message="errors.setup_fee" />
                    </div>
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
                                v-for="option in types"
                                :key="option.value"
                                :value="option.value"
                                :selected="option.value === product.type"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                        <InputError :message="errors.type" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="billing_cycle">Ciclo de cobrança</Label>
                        <select
                            id="billing_cycle"
                            name="billing_cycle"
                            class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        >
                            <option
                                v-for="option in billingCycles"
                                :key="option.value"
                                :value="option.value"
                                :selected="option.value === product.billing_cycle"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                        <InputError :message="errors.billing_cycle" />
                    </div>
                </div>

                <div class="grid gap-2 sm:max-w-xs">
                    <Label for="stock">Estoque (vazio = ilimitado)</Label>
                    <Input
                        id="stock"
                        name="stock"
                        type="number"
                        min="0"
                        :default-value="product.stock ?? ''"
                    />
                    <InputError :message="errors.stock" />
                </div>

                <div v-if="crossSellOptions.length > 0" class="grid gap-2">
                    <Label>Produtos relacionados (cross-sell)</Label>
                    <p class="text-xs text-muted-foreground">
                        Sugeridos ao cliente no checkout deste produto.
                    </p>
                    <div
                        class="grid max-h-48 gap-2 overflow-y-auto rounded-md border p-3 sm:grid-cols-2"
                    >
                        <label
                            v-for="option in crossSellOptions"
                            :key="option.value"
                            class="flex items-center gap-2 text-sm"
                        >
                            <input
                                type="checkbox"
                                name="cross_sell_ids[]"
                                :value="option.value"
                                :checked="
                                    product.cross_sell_ids?.includes(option.value)
                                "
                                class="h-4 w-4 rounded border-input"
                            />
                            <span>{{ option.label }}</span>
                        </label>
                    </div>
                    <InputError :message="errors.cross_sell_ids" />
                </div>

                <div class="flex flex-col gap-3">
                    <label class="flex items-center gap-3">
                        <input type="hidden" name="is_active" value="0" />
                        <input
                            name="is_active"
                            type="checkbox"
                            value="1"
                            :checked="product.is_active ?? true"
                            class="h-4 w-4 rounded border-input"
                        />
                        <span class="text-sm">Produto ativo</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="hidden" name="is_featured" value="0" />
                        <input
                            name="is_featured"
                            type="checkbox"
                            value="1"
                            :checked="product.is_featured ?? false"
                            class="h-4 w-4 rounded border-input"
                        />
                        <span class="text-sm">Destacar na home</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2 border-t pt-4">
                    <Button as-child variant="outline" type="button">
                        <Link href="/admin/products">Cancelar</Link>
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
