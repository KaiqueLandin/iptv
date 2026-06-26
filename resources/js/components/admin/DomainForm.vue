<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
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

interface DomainModel {
    id?: number;
    user_id?: number;
    domain_name?: string;
    registrar?: string | null;
    status?: string;
    registration_date?: string | null;
    expiry_date?: string | null;
    auto_renew?: boolean;
    privacy_protection?: boolean;
}

withDefaults(
    defineProps<{
        action: string;
        method?: 'post' | 'put';
        domain?: DomainModel;
        users?: UserOption[];
        registrars: string[];
        submitLabel?: string;
        isEdit?: boolean;
    }>(),
    {
        method: 'post',
        domain: () => ({}),
        users: () => [],
        submitLabel: 'Salvar',
        isEdit: false,
    },
);

const statuses = [
    { value: 'pending', label: 'Pendente' },
    { value: 'active', label: 'Ativo' },
    { value: 'expired', label: 'Expirado' },
    { value: 'cancelled', label: 'Cancelado' },
    { value: 'transferred', label: 'Transferido' },
];
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
                <div v-if="!isEdit" class="grid gap-2">
                    <Label for="user_id">Cliente</Label>
                    <select
                        id="user_id"
                        name="user_id"
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
                    <InputError :message="errors.user_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="domain_name">Domínio</Label>
                    <Input
                        id="domain_name"
                        name="domain_name"
                        :default-value="domain.domain_name"
                        :readonly="isEdit"
                        placeholder="exemplo.com.br"
                        required
                    />
                    <InputError :message="errors.domain_name" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="registrar">Registrar</Label>
                        <select
                            id="registrar"
                            name="registrar"
                            class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        >
                            <option value="">—</option>
                            <option
                                v-for="reg in registrars"
                                :key="reg"
                                :value="reg"
                                :selected="reg === domain.registrar"
                            >
                                {{ reg }}
                            </option>
                        </select>
                        <InputError :message="errors.registrar" />
                    </div>
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
                                :selected="option.value === domain.status"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                        <InputError :message="errors.status" />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="registration_date">Data de registro</Label>
                        <Input
                            id="registration_date"
                            name="registration_date"
                            type="date"
                            :default-value="domain.registration_date ?? ''"
                        />
                        <InputError :message="errors.registration_date" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="expiry_date">Expira em</Label>
                        <Input
                            id="expiry_date"
                            name="expiry_date"
                            type="date"
                            :default-value="domain.expiry_date ?? ''"
                        />
                        <InputError :message="errors.expiry_date" />
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <label class="flex items-center gap-3">
                        <input type="hidden" name="auto_renew" value="0" />
                        <input
                            name="auto_renew"
                            type="checkbox"
                            value="1"
                            :checked="domain.auto_renew ?? true"
                            class="h-4 w-4 rounded border-input"
                        />
                        <span class="text-sm">Renovação automática</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="hidden" name="privacy_protection" value="0" />
                        <input
                            name="privacy_protection"
                            type="checkbox"
                            value="1"
                            :checked="domain.privacy_protection ?? false"
                            class="h-4 w-4 rounded border-input"
                        />
                        <span class="text-sm">Proteção de privacidade</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2 border-t pt-4">
                    <Button as-child variant="outline" type="button">
                        <Link href="/admin/domains">Cancelar</Link>
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
