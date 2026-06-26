<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    CreditCard,
    Heart,
    LayoutGrid,
    LayoutDashboard,
    LifeBuoy,
    Package,
    Receipt,
    RefreshCw,
    ShieldCheck,
    ShoppingCart,
    Tag,
    Ticket,
    Tv,
    Users,
} from '@lucide/vue';import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import TeamSwitcher from '@/components/TeamSwitcher.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { Auth, NavItem } from '@/types';

const page = usePage<{ auth: Auth }>();

const dashboardUrl = computed(() =>
    page.props.currentTeam ? dashboard(page.props.currentTeam.slug).url : '/',
);

const isAdmin = computed(() => Boolean(page.props.auth?.isAdmin));

const mainNavItems = computed<NavItem[]>(() => [
    {
        title: 'Painel',
        href: dashboardUrl.value,
        icon: LayoutGrid,
    },
    {
        title: 'Meus serviços',
        href: '/services',
        icon: Tv,
    },
    {
        title: 'Meus pedidos',
        href: '/orders',
        icon: Receipt,
    },
    {
        title: 'Favoritos',
        href: '/wishlist',
        icon: Heart,
    },
    {
        title: 'Suporte',
        href: '/tickets',
        icon: LifeBuoy,
    },
]);

const adminNavItems = computed<NavItem[]>(() => [
    {
        title: 'Visão geral',
        href: '/admin/dashboard',
        icon: LayoutDashboard,
    },
    {
        title: 'Clientes',
        href: '/admin/users',
        icon: Users,
    },
    {
        title: 'Produtos',
        href: '/admin/products',
        icon: Package,
    },
    {
        title: 'Marcas',
        href: '/admin/brands',
        icon: Tag,
    },
    {
        title: 'Pedidos',
        href: '/admin/orders',
        icon: ShoppingCart,
    },
    {
        title: 'Assinaturas',
        href: '/admin/subscriptions',
        icon: RefreshCw,
    },
    {
        title: 'Faturas',
        href: '/admin/invoices',
        icon: Receipt,
    },
    {
        title: 'Tickets',
        href: '/admin/tickets',
        icon: Ticket,
    },
    {
        title: 'Cupons',
        href: '/admin/coupons',
        icon: Tag,
    },
    {
        title: 'Pagamentos',
        href: '/admin/payment-gateways',
        icon: CreditCard,
    },
]);

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboardUrl">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
            <SidebarMenu>
                <SidebarMenuItem>
                    <TeamSwitcher />
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
            <NavMain
                v-if="isAdmin"
                :items="adminNavItems"
                label="Administração"
                :icon="ShieldCheck"
            />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
