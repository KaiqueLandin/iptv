<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

interface TemplateModel {
    id?: number;
    name?: string;
    subject?: string;
    body_html?: string;
    body_text?: string | null;
    type?: string;
    is_active?: boolean;
}

withDefaults(
    defineProps<{
        action: string;
        method?: 'post' | 'put';
        template?: TemplateModel;
        submitLabel?: string;
    }>(),
    {
        method: 'post',
        template: () => ({}),
        submitLabel: 'Salvar',
    },
);

const types = [
    { value: 'general', label: 'Geral' },
    { value: 'invoice', label: 'Fatura' },
    { value: 'order', label: 'Pedido' },
    { value: 'ticket', label: 'Ticket' },
    { value: 'notification', label: 'Notificação' },
];

const variableExample = '{{nome}}';
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
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="name">Nome</Label>
                        <Input
                            id="name"
                            name="name"
                            :default-value="template.name"
                            required
                        />
                        <InputError :message="errors.name" />
                    </div>
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
                                :selected="option.value === template.type"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                        <InputError :message="errors.type" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="subject">Assunto</Label>
                    <Input
                        id="subject"
                        name="subject"
                        :default-value="template.subject"
                        required
                    />
                    <InputError :message="errors.subject" />
                </div>

                <div class="grid gap-2">
                    <Label for="body_html">Conteúdo (HTML)</Label>
                    <textarea
                        id="body_html"
                        name="body_html"
                        rows="8"
                        required
                        class="rounded-md border bg-transparent px-3 py-2 font-mono text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        :value="template.body_html ?? ''"
                    ></textarea>
                    <p class="text-xs text-muted-foreground">
                        Use variáveis no formato
                        <code class="rounded bg-muted px-1">{{ variableExample }}</code>.
                    </p>
                    <InputError :message="errors.body_html" />
                </div>

                <div class="grid gap-2">
                    <Label for="body_text">Versão texto (opcional)</Label>
                    <textarea
                        id="body_text"
                        name="body_text"
                        rows="4"
                        class="rounded-md border bg-transparent px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        :value="template.body_text ?? ''"
                    ></textarea>
                    <InputError :message="errors.body_text" />
                </div>

                <label class="flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0" />
                    <input
                        name="is_active"
                        type="checkbox"
                        value="1"
                        :checked="template.is_active ?? true"
                        class="h-4 w-4 rounded border-input"
                    />
                    <span class="text-sm">Template ativo</span>
                </label>

                <div class="flex justify-end gap-2 border-t pt-4">
                    <Button as-child variant="outline" type="button">
                        <Link href="/admin/email-templates">Cancelar</Link>
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
