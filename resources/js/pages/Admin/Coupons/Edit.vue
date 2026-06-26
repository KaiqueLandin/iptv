<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import CouponForm from '@/components/admin/CouponForm.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import { Button } from '@/components/ui/button';

interface CouponModel {
    id: number;
    code: string;
    description: string | null;
    type: string;
    value: string;
    minimum_amount: string | null;
    max_uses: number | null;
    uses_per_customer: number;
    valid_from: string | null;
    valid_until: string | null;
    applies_to_renewals: boolean;
    is_active: boolean;
}

const props = defineProps<{
    coupon: CouponModel;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Cupons', href: '/admin/coupons' },
            { title: 'Editar', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head :title="`Editar ${props.coupon.code}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader title="Editar cupom" :description="props.coupon.code">
            <template #actions>
                <Button as-child variant="outline">
                    <Link href="/admin/coupons">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <CouponForm
            :action="`/admin/coupons/${props.coupon.id}`"
            method="put"
            :coupon="props.coupon"
            is-edit
            submit-label="Salvar alterações"
        />
    </div>
</template>
