<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Send } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/tickets';

defineProps<{
    departments: { id: number; name: string }[];
    priorities: { value: string; label: string }[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Suporte', href: '/tickets' },
            { title: 'Abrir ticket', href: '/tickets/create' },
        ],
    }),
});
</script>

<template>
    <Head title="Abrir ticket" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Abrir ticket</h1>
                <p class="text-sm text-muted-foreground">
                    Descreva seu problema e nossa equipe responderá em breve.
                </p>
            </div>
            <Button as-child variant="outline">
                <Link href="/tickets">
                    <ArrowLeft :size="16" /> Voltar
                </Link>
            </Button>
        </div>

        <Card>
            <CardContent class="py-6">
                <Form
                    v-bind="store.form()"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-5"
                >
                    <div class="grid gap-2">
                        <Label for="subject">Assunto</Label>
                        <Input
                            id="subject"
                            name="subject"
                            required
                            autofocus
                            placeholder="Resumo do seu problema"
                        />
                        <InputError :message="errors.subject" />
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="department_id">Departamento</Label>
                            <select
                                id="department_id"
                                name="department_id"
                                class="h-9 rounded-md border bg-transparent px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                            >
                                <option value="">Geral</option>
                                <option
                                    v-for="dept in departments"
                                    :key="dept.id"
                                    :value="dept.id"
                                >
                                    {{ dept.name }}
                                </option>
                            </select>
                            <InputError :message="errors.department_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="priority">Prioridade</Label>
                            <select
                                id="priority"
                                name="priority"
                                class="h-9 rounded-md border bg-transparent px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                            >
                                <option
                                    v-for="p in priorities"
                                    :key="p.value"
                                    :value="p.value"
                                    :selected="p.value === 'medium'"
                                >
                                    {{ p.label }}
                                </option>
                            </select>
                            <InputError :message="errors.priority" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="message">Mensagem</Label>
                        <textarea
                            id="message"
                            name="message"
                            rows="6"
                            required
                            placeholder="Descreva os detalhes do seu problema..."
                            class="rounded-md border bg-transparent px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        ></textarea>
                        <InputError :message="errors.message" />
                    </div>

                    <div class="flex justify-end">
                        <Button type="submit" :disabled="processing">
                            <Spinner v-if="processing" />
                            <Send v-else :size="16" />
                            Enviar ticket
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>
