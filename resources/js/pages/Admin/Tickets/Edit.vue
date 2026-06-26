<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

interface Department { id: number; name: string }
interface Priority { value: string; label: string }
interface Status { value: string; label: string }
interface StaffOption { id: number; name: string }

interface AdminTicket {
    id: number;
    ticket_number: string;
    subject: string;
    status: string;
    priority: string;
    department_id: number | null;
    assigned_to: number | null;
}

const props = defineProps<{
    ticket: AdminTicket;
    departments: Department[];
    priorities: Priority[];
    statuses: Status[];
    staff: StaffOption[];
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Tickets', href: '/admin/tickets' },
            { title: 'Editar', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head :title="`Editar ${props.ticket.ticket_number}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            :title="props.ticket.subject"
            :description="props.ticket.ticket_number"
        >
            <template #actions>
                <Button as-child variant="outline">
                    <Link :href="`/admin/tickets/${props.ticket.id}`">
                        <ArrowLeft :size="16" /> Voltar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <Card class="max-w-2xl">
            <CardContent class="py-6">
                <Form
                    :action="`/admin/tickets/${props.ticket.id}`"
                    method="put"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-5"
                >
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="status">Status</Label>
                            <select
                                id="status"
                                name="status"
                                class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                            >
                                <option
                                    v-for="s in statuses"
                                    :key="s.value"
                                    :value="s.value"
                                    :selected="s.value === props.ticket.status"
                                >{{ s.label }}</option>
                            </select>
                            <InputError :message="errors.status" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="priority">Prioridade</Label>
                            <select
                                id="priority"
                                name="priority"
                                class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                            >
                                <option
                                    v-for="p in priorities"
                                    :key="p.value"
                                    :value="p.value"
                                    :selected="p.value === props.ticket.priority"
                                >{{ p.label }}</option>
                            </select>
                            <InputError :message="errors.priority" />
                        </div>
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
                                <option
                                    v-for="d in departments"
                                    :key="d.id"
                                    :value="d.id"
                                    :selected="d.id === props.ticket.department_id"
                                >{{ d.name }}</option>
                            </select>
                        </div>

                        <div class="grid gap-2">
                            <Label for="assigned_to">Atribuído a</Label>
                            <select
                                id="assigned_to"
                                name="assigned_to"
                                class="h-9 rounded-md border bg-transparent px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"
                            >
                                <option value="">Não atribuído</option>
                                <option
                                    v-for="s in staff"
                                    :key="s.id"
                                    :value="s.id"
                                    :selected="s.id === props.ticket.assigned_to"
                                >{{ s.name }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 border-t pt-4">
                        <Button as-child variant="outline" type="button">
                            <Link :href="`/admin/tickets/${props.ticket.id}`">Cancelar</Link>
                        </Button>
                        <Button type="submit" :disabled="processing">
                            <Spinner v-if="processing" />
                            Salvar alterações
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>
