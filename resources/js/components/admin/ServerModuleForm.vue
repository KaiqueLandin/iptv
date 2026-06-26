<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

interface ModuleModel {
    id?: number;
    name?: string;
    module_key?: string;
    description?: string | null;
    server_hostname?: string | null;
    server_port?: number | null;
    use_ssl?: boolean;
    is_active?: boolean;
}

withDefaults(
    defineProps<{
        action: string;
        method?: 'post' | 'put';
        module?: ModuleModel;
        availableModules?: Record<string, string>;
        submitLabel?: string;
        isEdit?: boolean;
    }>(),
    {
        method: 'post',
        module: () => ({}),
        availableModules: () => ({}),
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
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="name">Nome</Label>
                        <Input
                            id="name"
                            name="name"
                            :default-value="module.name"
                            required
                        />
                        <InputError :message="errors.name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="module_key">Tipo de módulo</Label>
                        <select
                            v-if="!isEdit"
                            id="module_key"
                            name="module_key"
                            class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        >
                            <option
                                v-for="(label, key) in availableModules"
                                :key="key"
                                :value="key"
                            >
                                {{ label }}
                            </option>
                        </select>
                        <Input
                            v-else
                            :default-value="module.module_key"
                            readonly
                        />
                        <InputError :message="errors.module_key" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="description">Descrição</Label>
                    <Input
                        id="description"
                        name="description"
                        :default-value="module.description ?? ''"
                    />
                    <InputError :message="errors.description" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="server_hostname">Hostname</Label>
                        <Input
                            id="server_hostname"
                            name="server_hostname"
                            :default-value="module.server_hostname ?? ''"
                            placeholder="servidor.exemplo.com"
                        />
                        <InputError :message="errors.server_hostname" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="server_port">Porta</Label>
                        <Input
                            id="server_port"
                            name="server_port"
                            type="number"
                            :default-value="module.server_port ?? ''"
                        />
                        <InputError :message="errors.server_port" />
                    </div>
                </div>

                <p class="rounded-md bg-muted p-3 text-xs text-muted-foreground">
                    As credenciais de acesso (usuário, senha, API token) são
                    armazenadas de forma criptografada. Configure-as com cuidado.
                </p>

                <div class="flex flex-col gap-3">
                    <label class="flex items-center gap-3">
                        <input type="hidden" name="use_ssl" value="0" />
                        <input
                            name="use_ssl"
                            type="checkbox"
                            value="1"
                            :checked="module.use_ssl ?? true"
                            class="h-4 w-4 rounded border-input"
                        />
                        <span class="text-sm">Usar SSL</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="hidden" name="is_active" value="0" />
                        <input
                            name="is_active"
                            type="checkbox"
                            value="1"
                            :checked="module.is_active ?? false"
                            class="h-4 w-4 rounded border-input"
                        />
                        <span class="text-sm">Módulo ativo</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2 border-t pt-4">
                    <Button as-child variant="outline" type="button">
                        <Link href="/admin/server-modules">Cancelar</Link>
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
