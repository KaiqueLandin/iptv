<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2, Copy, CreditCard, Plus } from '@lucide/vue';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

interface InvoiceItem {
    description: string;
    quantity: number;
    amount: string;
}

interface Gateway {
    id: number;
    name: string;
    gateway_key: string;
    description: string | null;
}

interface CrossSell {
    id: number;
    name: string;
    slug: string;
    price: string;
    description: string | null;
    checkoutUrl: string;
}

interface Charge {
    method?: string;
    pix_qr_code?: string | null;
    pix_copy_paste?: string | null;
    boleto_url?: string | null;
    redirect_url?: string | null;
    instructions?: string | null;
}

const props = defineProps<{
    invoice: {
        id: number;
        invoice_number: string;
        status: string;
        total: string;
        items: InvoiceItem[];
    };
    gateways: Gateway[];
    crossSells?: CrossSell[];
    charge?: Charge | null;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [{ title: 'Pagamento', href: '#' }],
    }),
});

const charge = computed<Charge | null>(() => props.charge ?? null);

const crossSells = computed<CrossSell[]>(() => props.crossSells ?? []);

const selectedGateway = ref<number | null>(props.gateways[0]?.id ?? null);
const copied = ref(false);

const form = useForm({ gateway_id: selectedGateway.value });

const brl = (value: string) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(Number(value));

const isPaid = computed(() => props.invoice.status === 'paid');

const pay = () => {
    if (!selectedGateway.value) {
        return;
    }
    form.gateway_id = selectedGateway.value;
    form.post(`/checkout/${props.invoice.id}/pay`, { preserveScroll: true });
};

const copyPix = async () => {
    if (charge.value?.pix_copy_paste) {
        await navigator.clipboard.writeText(charge.value.pix_copy_paste);
        copied.value = true;
        setTimeout(() => (copied.value = false), 2000);
    }
};

const goToServices = () => router.get('/services');
</script>

<template>
    <Head title="Pagamento" />

    <div class="mx-auto flex w-full max-w-3xl flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight">Pagamento</h1>
            <Button as="a" href="/" variant="outline">
                <ArrowLeft :size="16" />
                Voltar
            </Button>
        </div>

        <!-- Pago -->
        <Card v-if="isPaid" class="border-emerald-600/30">
            <CardContent class="flex flex-col items-center gap-3 py-10 text-center">
                <div
                    class="flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30"
                >
                    <CheckCircle2 :size="28" />
                </div>
                <h2 class="text-lg font-semibold">Pagamento confirmado!</h2>
                <p class="text-sm text-muted-foreground">
                    Seu acesso foi gerado e enviado por e-mail. Você também pode
                    consultá-lo em "Meus serviços".
                </p>
                <Button class="mt-2" @click="goToServices">
                    Ver meus serviços
                </Button>
            </CardContent>
        </Card>

        <template v-else>
            <!-- Resumo da fatura -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">
                        Resumo · {{ invoice.invoice_number }}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <ul class="divide-y divide-border text-sm">
                        <li
                            v-for="(item, i) in invoice.items"
                            :key="i"
                            class="flex justify-between py-2 first:pt-0"
                        >
                            <span>{{ item.quantity }}× {{ item.description }}</span>
                            <span class="font-medium">{{ brl(item.amount) }}</span>
                        </li>
                    </ul>
                    <div class="mt-3 flex justify-between border-t pt-3 text-base font-bold">
                        <span>Total</span>
                        <span>{{ brl(invoice.total) }}</span>
                    </div>
                </CardContent>
            </Card>

            <!-- Cobrança gerada -->
            <Card v-if="charge" class="border-emerald-600/30">
                <CardHeader>
                    <CardTitle class="text-base">Pague para liberar o acesso</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <p v-if="charge.instructions" class="text-sm text-muted-foreground">
                        {{ charge.instructions }}
                    </p>

                    <img
                        v-if="charge.pix_qr_code"
                        :src="charge.pix_qr_code"
                        alt="QR Code Pix"
                        class="mx-auto h-48 w-48 rounded-lg border"
                    />

                    <div v-if="charge.pix_copy_paste" class="space-y-2">
                        <label class="text-xs font-medium text-muted-foreground">
                            Pix copia e cola
                        </label>
                        <div class="flex gap-2">
                            <input
                                :value="charge.pix_copy_paste"
                                readonly
                                class="flex-1 truncate rounded-md border bg-muted px-3 py-2 text-sm"
                            />
                            <Button variant="outline" @click="copyPix">
                                <Copy :size="15" />
                                {{ copied ? 'Copiado!' : 'Copiar' }}
                            </Button>
                        </div>
                    </div>

                    <Button
                        v-if="charge.boleto_url"
                        as="a"
                        :href="charge.boleto_url"
                        target="_blank"
                        variant="outline"
                        class="w-full"
                    >
                        Abrir boleto
                    </Button>

                    <Button
                        v-if="charge.redirect_url"
                        as="a"
                        :href="charge.redirect_url"
                        class="w-full"
                    >
                        Ir para o pagamento
                    </Button>

                    <p class="text-center text-xs text-muted-foreground">
                        Assim que o pagamento for confirmado, seu acesso será
                        liberado automaticamente.
                    </p>
                </CardContent>
            </Card>

            <!-- Escolha do gateway -->
            <Card v-else>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-base">
                        <CreditCard :size="18" class="text-muted-foreground" />
                        Forma de pagamento
                    </CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <p
                        v-if="gateways.length === 0"
                        class="rounded-lg border border-dashed py-8 text-center text-sm text-muted-foreground"
                    >
                        Nenhuma forma de pagamento disponível no momento.
                    </p>

                    <label
                        v-for="gateway in gateways"
                        :key="gateway.id"
                        :class="[
                            'flex cursor-pointer items-center gap-3 rounded-lg border p-4 transition-colors',
                            selectedGateway === gateway.id
                                ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20'
                                : 'hover:bg-accent',
                        ]"
                    >
                        <input
                            type="radio"
                            name="gateway"
                            :value="gateway.id"
                            v-model="selectedGateway"
                            class="h-4 w-4"
                        />
                        <div>
                            <p class="text-sm font-medium">{{ gateway.name }}</p>
                            <p
                                v-if="gateway.description"
                                class="text-xs text-muted-foreground"
                            >
                                {{ gateway.description }}
                            </p>
                        </div>
                    </label>

                    <Button
                        class="w-full"
                        :disabled="!selectedGateway || form.processing"
                        @click="pay"
                    >
                        Pagar {{ brl(invoice.total) }}
                    </Button>
                </CardContent>
            </Card>

            <!-- Cross-sell: produtos relacionados -->
            <Card v-if="crossSells.length > 0">
                <CardHeader>
                    <CardTitle class="text-base">
                        Você também pode gostar
                    </CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div
                        v-for="item in crossSells"
                        :key="item.id"
                        class="flex items-center justify-between gap-4 rounded-lg border p-4"
                    >
                        <div class="min-w-0">
                            <p class="text-sm font-medium">{{ item.name }}</p>
                            <p
                                v-if="item.description"
                                class="truncate text-xs text-muted-foreground"
                            >
                                {{ item.description }}
                            </p>
                            <p class="mt-1 text-sm font-semibold">
                                {{ brl(item.price) }}
                            </p>
                        </div>
                        <Button as-child variant="outline" class="shrink-0">
                            <Link :href="item.checkoutUrl" method="post" as="button">
                                <Plus :size="15" />
                                Adicionar
                            </Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </template>
    </div>
</template>
