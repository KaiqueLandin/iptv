<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import CouponForm from '@/components/admin/CouponForm.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import { Button } from '@/components/ui/button';

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Administração', href: '/admin/dashboard' },
            { title: 'Cupons', href: '/admin/coupons' },
            { title: 'Novo', href: '#' },
        ],
    }),
});
</script>

<template>
    <Head title="Novo cupom" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader title="Novo cupom" description="Crie um cupom de desconto.">
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
            action="/admin/coupons"
            method="post"
            submit-label="Criar cupom"
        />
    </div>
</template>
