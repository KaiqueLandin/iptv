<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Send } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

interface Department { id: number; name: string }
interface Priority { value: string; label: string }
interface UserOption { id: number; name: string; email: string }
interface StaffOption { id: number; name: string }

defineProps<{
    departments: Department[];
    priorities: Priority[];
    users: UserOption[];
    staff: StaffOption[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Tickets', href: '/admin/tickets' },
            { title: 'Novo', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head title="Novo ticket" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader title="Novo ticket" description="Abra um ticket em nome de um cliente.">
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/tickets">
                        <ArrowLeft :size="16" /> Voltar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <Card class="max-w-2xl">
            <CardContent class="py-6">
                <Form
                    action="/admin/tickets"
                    method="post"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-5"
                >
                    <div class="grid gap-2">
                        <Label for="user_id">Cliente</Label>
                        <select
                            id="user_id"
                            name="user_id"
                            required
                            class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        >
                            <option value="">Selecione o cliente</option>
                            <option v-for="u in users" :key="u.id" :value="u.id">
                                {{ u.name }} — {{ u.email }}
                            </option>
                        </select>
                        <InputError :message="errors.user_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="subject">Assunto</Label>
                        <Input id="subject" name="subject" required autofocus placeholder="Resumo do problema" />
                        <InputError :message="errors.subject" />
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="department_id">Departamento</Label>
                            <select
                                id="department_id"
                                name="department_id"
                                class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                            >
                                <option value="">Geral</option>
                                <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                            </select>
                        </div>

                        <div class="grid gap-2">
                            <Label for="priority">Prioridade</Label>
                            <select
                                id="priority"
                                name="priority"
                                class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                            >
                                <option v-for="p in priorities" :key="p.value" :value="p.value" :selected="p.value === 'medium'">
                                    {{ p.label }}
                                </option>
                            </select>
                            <InputError :message="errors.priority" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="assigned_to">Atribuir para</Label>
                        <select
                            id="assigned_to"
                            name="assigned_to"
                            class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        >
                            <option value="">Não atribuído</option>
                            <option v-for="s in staff" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <Label for="message">Mensagem inicial</Label>
                        <textarea
                            id="message"
                            name="message"
                            rows="5"
                            required
                            placeholder="Descreva o problema em detalhes..."
                            class="rounded-md border bg-transparent px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        ></textarea>
                        <InputError :message="errors.message" />
                    </div>

                    <div class="flex justify-end gap-2 border-t pt-4">
                        <Button as-child variant="outline" type="button">
                            <Link href="/admin/tickets">Cancelar</Link>
                        </Button>
                        <Button type="submit" :disabled="processing">
                            <Spinner v-if="processing" />
                            <Send v-else :size="15" />
                            Criar ticket
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>
