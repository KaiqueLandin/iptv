<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

interface PresetField {
    key: string;
    label: string;
    type: string;
    required: boolean;
}

interface Preset {
    name: string;
    description: string;
    fields: PresetField[];
}

interface GatewayModel {
    id?: number;
    name?: string;
    gateway_key?: string;
    description?: string | null;
    is_active?: boolean;
    is_sandbox?: boolean;
    fee_fixed?: string | number;
    fee_percentage?: string | number;
    sort_order?: number;
}

const props = withDefaults(
    defineProps<{
        action: string;
        method?: 'post' | 'put';
        gateway?: GatewayModel;
        presets: Record<string, Preset>;
        configuredKeys?: string[];
        submitLabel?: string;
        isEdit?: boolean;
    }>(),
    {
        method: 'post',
        gateway: () => ({}),
        configuredKeys: () => [],
        submitLabel: 'Salvar',
        isEdit: false,
    },
);

const presetKeys = Object.keys(props.presets);

const selectedKey = ref(
    props.gateway.gateway_key ?? presetKeys[0] ?? '',
);

const activePreset = computed<Preset | undefined>(
    () => props.presets[selectedKey.value],
);

const credentialFields = computed<PresetField[]>(
    () => activePreset.value?.fields ?? [],
);

const buildCredentials = () => {
    const creds: Record<string, string> = {};
    credentialFields.value.forEach((field) => {
        creds[field.key] = '';
    });
    return creds;
};

const form = useForm({
    name: props.gateway.name ?? activePreset.value?.name ?? '',
    gateway_key: selectedKey.value,
    description: props.gateway.description ?? activePreset.value?.description ?? '',
    is_active: props.gateway.is_active ?? false,
    is_sandbox: props.gateway.is_sandbox ?? true,
    fee_fixed: props.gateway.fee_fixed ?? 0,
    fee_percentage: props.gateway.fee_percentage ?? 0,
    sort_order: props.gateway.sort_order ?? 0,
    credentials: buildCredentials() as Record<string, string>,
});

const onPresetChange = () => {
    form.gateway_key = selectedKey.value;
    if (!props.gateway.name) {
        form.name = activePreset.value?.name ?? '';
    }
    if (!props.gateway.description) {
        form.description = activePreset.value?.description ?? '';
    }
    form.credentials = buildCredentials();
};

const isConfigured = (key: string) => props.configuredKeys.includes(key);

const submit = () => {
    form.submit(props.method, props.action, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Card class="max-w-2xl">
        <CardContent>
            <form class="flex flex-col gap-6" @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="gateway_key">Provedor</Label>
                    <select
                        id="gateway_key"
                        v-model="selectedKey"
                        :disabled="isEdit"
                        class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring disabled:opacity-60"
                        @change="onPresetChange"
                    >
                        <option
                            v-for="key in presetKeys"
                            :key="key"
                            :value="key"
                        >
                            {{ presets[key].name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.gateway_key" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="name">Nome de exibição</Label>
                        <Input id="name" v-model="form.name" required />
                        <InputError :message="form.errors.name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="sort_order">Ordem</Label>
                        <Input
                            id="sort_order"
                            v-model.number="form.sort_order"
                            type="number"
                            min="0"
                        />
                        <InputError :message="form.errors.sort_order" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="description">Descrição</Label>
                    <Input id="description" v-model="form.description" />
                    <InputError :message="form.errors.description" />
                </div>

                <!-- Credenciais dinâmicas por provedor -->
                <div class="space-y-4 rounded-lg border bg-muted/30 p-4">
                    <div>
                        <h3 class="text-sm font-semibold">Credenciais da API</h3>
                        <p class="text-xs text-muted-foreground">
                            Armazenadas de forma criptografada.
                            <span v-if="isEdit">
                                Deixe em branco para manter o valor atual.
                            </span>
                        </p>
                    </div>

                    <div
                        v-for="field in credentialFields"
                        :key="field.key"
                        class="grid gap-2"
                    >
                        <Label :for="`cred_${field.key}`">
                            {{ field.label }}
                            <span v-if="field.required" class="text-rose-500">*</span>
                            <span
                                v-if="isEdit && isConfigured(field.key)"
                                class="ml-1 text-xs font-normal text-emerald-600"
                            >
                                (configurado)
                            </span>
                        </Label>
                        <Input
                            :id="`cred_${field.key}`"
                            v-model="form.credentials[field.key]"
                            :type="field.type === 'password' ? 'password' : 'text'"
                            :placeholder="
                                isEdit && isConfigured(field.key)
                                    ? '••••••••'
                                    : ''
                            "
                            autocomplete="off"
                        />
                    </div>

                    <p
                        v-if="credentialFields.length === 0"
                        class="text-xs text-muted-foreground"
                    >
                        Este provedor não requer credenciais adicionais.
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="fee_fixed">Taxa fixa (R$)</Label>
                        <Input
                            id="fee_fixed"
                            v-model.number="form.fee_fixed"
                            type="number"
                            step="0.01"
                            min="0"
                        />
                        <InputError :message="form.errors.fee_fixed" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="fee_percentage">Taxa (%)</Label>
                        <Input
                            id="fee_percentage"
                            v-model.number="form.fee_percentage"
                            type="number"
                            step="0.01"
                            min="0"
                            max="100"
                        />
                        <InputError :message="form.errors.fee_percentage" />
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <label class="flex items-center gap-3">
                        <input
                            v-model="form.is_active"
                            type="checkbox"
                            class="h-4 w-4 rounded border-input"
                        />
                        <span class="text-sm">Gateway ativo</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input
                            v-model="form.is_sandbox"
                            type="checkbox"
                            class="h-4 w-4 rounded border-input"
                        />
                        <span class="text-sm">Modo sandbox (testes)</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2 border-t pt-4">
                    <Button as-child variant="outline" type="button">
                        <Link href="/admin/payment-gateways">Cancelar</Link>
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        <Spinner v-if="form.processing" />
                        {{ submitLabel }}
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>
</template>
