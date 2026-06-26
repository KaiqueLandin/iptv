<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

interface WebhookModel {
    id?: number;
    event?: string;
    url?: string;
    secret?: string | null;
    is_active?: boolean;
    timeout?: number;
    max_retries?: number;
}

const props = withDefaults(
    defineProps<{
        action: string;
        method?: 'post' | 'put';
        webhook?: WebhookModel;
        events?: string[];
        submitLabel?: string;
    }>(),
    {
        method: 'post',
        webhook: () => ({}),
        events: () => [],
        submitLabel: 'Salvar',
    },
);

const defaultEvents = [
    'invoice.created',
    'invoice.paid',
    'invoice.overdue',
    'order.created',
    'order.activated',
    'order.suspended',
    'order.cancelled',
    'payment.completed',
    'payment.failed',
    'ticket.created',
    'ticket.replied',
    'ticket.closed',
    'user.registered',
];

const eventOptions = props.events.length ? props.events : defaultEvents;
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
                    <Label for="event">Evento</Label>
                    <select
                        id="event"
                        name="event"
                        class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                    >
                        <option
                            v-for="event in eventOptions"
                            :key="event"
                            :value="event"
                            :selected="event === webhook.event"
                        >
                            {{ event }}
                        </option>
                    </select>
                    <InputError :message="errors.event" />
                </div>

                <div class="grid gap-2">
                    <Label for="url">URL de destino</Label>
                    <Input
                        id="url"
                        name="url"
                        type="url"
                        :default-value="webhook.url"
                        placeholder="https://..."
                        required
                    />
                    <InputError :message="errors.url" />
                </div>

                <div class="grid gap-2">
                    <Label for="secret">Secret (assinatura)</Label>
                    <Input
                        id="secret"
                        name="secret"
                        :default-value="webhook.secret ?? ''"
                    />
                    <InputError :message="errors.secret" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="timeout">Timeout (segundos)</Label>
                        <Input
                            id="timeout"
                            name="timeout"
                            type="number"
                            min="1"
                            max="120"
                            :default-value="webhook.timeout ?? 30"
                        />
                        <InputError :message="errors.timeout" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="max_retries">Tentativas máximas</Label>
                        <Input
                            id="max_retries"
                            name="max_retries"
                            type="number"
                            min="0"
                            max="10"
                            :default-value="webhook.max_retries ?? 3"
                        />
                        <InputError :message="errors.max_retries" />
                    </div>
                </div>

                <label class="flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0" />
                    <input
                        name="is_active"
                        type="checkbox"
                        value="1"
                        :checked="webhook.is_active ?? true"
                        class="h-4 w-4 rounded border-input"
                    />
                    <span class="text-sm">Webhook ativo</span>
                </label>

                <div class="flex justify-end gap-2 border-t pt-4">
                    <Button as-child variant="outline" type="button">
                        <Link href="/admin/webhooks">Cancelar</Link>
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
