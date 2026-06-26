<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

interface BrandModel {
    id?: number;
    name?: string;
    description?: string | null;
    logo_url?: string | null;
    website_url?: string | null;
    is_active?: boolean;
    sort_order?: number | null;
}

withDefaults(
    defineProps<{
        action: string;
        method?: 'post' | 'put';
        brand?: BrandModel;
        submitLabel?: string;
    }>(),
    {
        method: 'post',
        brand: () => ({}),
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
                        :default-value="brand.name"
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
                        :value="brand.description ?? ''"
                    ></textarea>
                    <InputError :message="errors.description" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="logo_url">URL do logo</Label>
                        <Input
                            id="logo_url"
                            name="logo_url"
                            type="url"
                            placeholder="https://..."
                            :default-value="brand.logo_url ?? ''"
                        />
                        <InputError :message="errors.logo_url" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="website_url">Site</Label>
                        <Input
                            id="website_url"
                            name="website_url"
                            type="url"
                            placeholder="https://..."
                            :default-value="brand.website_url ?? ''"
                        />
                        <InputError :message="errors.website_url" />
                    </div>
                </div>

                <div class="grid gap-2 sm:max-w-xs">
                    <Label for="sort_order">Ordem de exibição</Label>
                    <Input
                        id="sort_order"
                        name="sort_order"
                        type="number"
                        min="0"
                        :default-value="brand.sort_order ?? 0"
                    />
                    <InputError :message="errors.sort_order" />
                </div>

                <div class="flex flex-col gap-3">
                    <label class="flex items-center gap-3">
                        <input type="hidden" name="is_active" value="0" />
                        <input
                            name="is_active"
                            type="checkbox"
                            value="1"
                            :checked="brand.is_active ?? true"
                            class="h-4 w-4 rounded border-input"
                        />
                        <span class="text-sm">Marca ativa</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2 border-t pt-4">
                    <Button as-child variant="outline" type="button">
                        <Link href="/admin/brands">Cancelar</Link>
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
