<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    BadgeCheck,
    Headphones,
    Play,
    ShieldCheck,
    Zap,
} from '@lucide/vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();

const highlights = [
    {
        title: 'Entrega instantânea',
        description: 'Créditos liberados logo após a confirmação.',
        icon: Zap,
    },
    {
        title: 'Pagamento seguro',
        description: 'Ambiente protegido e criptografado.',
        icon: ShieldCheck,
    },
    {
        title: 'Suporte especializado',
        description: 'Atendimento humano sempre que precisar.',
        icon: Headphones,
    },
];
</script>

<template>
    <div class="auth-shell">
        <!-- Brand / showcase side -->
        <aside class="auth-aside">
            <div class="auth-aside-glow auth-aside-glow-green"></div>
            <div class="auth-aside-glow auth-aside-glow-blue"></div>

            <div class="auth-aside-inner">
                <Link :href="home()" class="auth-brand">
                    <span class="auth-brand-mark">
                        <Play :size="18" fill="currentColor" />
                    </span>
                    <span>CLICK<span>TV</span></span>
                </Link>

                <div class="auth-aside-copy">
                    <span class="auth-eyebrow">
                        <ShieldCheck :size="14" />
                        Plataforma de créditos IPTV
                    </span>
                    <h2>Recarga rápida, segura e 100% online.</h2>
                    <p>
                        Gerencie seus créditos, faturas e pedidos em um só
                        lugar. Acesse sua conta para continuar.
                    </p>
                </div>

                <ul class="auth-highlights">
                    <li v-for="item in highlights" :key="item.title">
                        <span class="auth-highlight-icon">
                            <component :is="item.icon" :size="18" />
                        </span>
                        <div>
                            <strong>{{ item.title }}</strong>
                            <small>{{ item.description }}</small>
                        </div>
                    </li>
                </ul>

                <div class="auth-aside-foot">
                    <BadgeCheck :size="16" />
                    Ambiente verificado e protegido
                </div>
            </div>
        </aside>

        <!-- Form side -->
        <main class="auth-main">
            <div class="auth-card">
                <Link :href="home()" class="auth-brand auth-brand-mobile">
                    <span class="auth-brand-mark">
                        <Play :size="16" fill="currentColor" />
                    </span>
                    <span>CLICK<span>TV</span></span>
                </Link>

                <div class="auth-head" v-if="title || description">
                    <h1 v-if="title">{{ title }}</h1>
                    <p v-if="description">{{ description }}</p>
                </div>

                <slot />
            </div>
        </main>
    </div>
</template>

<style scoped>
.auth-shell {
    display: grid;
    min-height: 100svh;
    grid-template-columns: 1fr;
    background: #f8fafc;
    color: #0f172a;
    font-family: Inter, 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
}

@media (min-width: 1024px) {
    .auth-shell {
        grid-template-columns: 1.05fr 1fr;
    }
}

/* ---------- Brand side ---------- */
.auth-aside {
    position: relative;
    display: none;
    overflow: hidden;
    padding: 48px;
    background:
        radial-gradient(circle at 18% 16%, rgba(16, 185, 129, 0.22), transparent 42%),
        radial-gradient(circle at 86% 12%, rgba(37, 99, 235, 0.26), transparent 40%),
        linear-gradient(160deg, #04101f 0%, #071c3a 52%, #052e2b 100%);
    color: white;
}

@media (min-width: 1024px) {
    .auth-aside {
        display: flex;
    }
}

.auth-aside-glow {
    position: absolute;
    border-radius: 50%;
    filter: blur(10px);
    pointer-events: none;
}

.auth-aside-glow-green {
    top: -60px;
    left: -40px;
    width: 280px;
    height: 280px;
    background: rgba(16, 185, 129, 0.18);
}

.auth-aside-glow-blue {
    right: -70px;
    bottom: -50px;
    width: 320px;
    height: 320px;
    background: rgba(37, 99, 235, 0.2);
}

.auth-aside-inner {
    position: relative;
    z-index: 1;
    display: flex;
    width: 100%;
    flex-direction: column;
    justify-content: space-between;
    gap: 40px;
}

.auth-aside-copy h2 {
    margin: 18px 0 14px;
    max-width: 460px;
    font-size: clamp(1.9rem, 2.6vw, 2.6rem);
    font-weight: 800;
    letter-spacing: -0.04em;
    line-height: 1.1;
}

.auth-aside-copy p {
    max-width: 420px;
    color: rgba(226, 232, 240, 0.82);
    font-size: 15px;
    line-height: 1.7;
}

.auth-highlights {
    display: grid;
    gap: 16px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.auth-highlights li {
    display: flex;
    align-items: flex-start;
    gap: 14px;
}

.auth-highlight-icon {
    display: grid;
    width: 40px;
    height: 40px;
    flex: 0 0 auto;
    place-items: center;
    border: 1px solid rgba(110, 231, 183, 0.4);
    border-radius: 12px;
    background: rgba(16, 185, 129, 0.16);
    color: #6ee7b7;
}

.auth-highlights strong {
    display: block;
    font-size: 14px;
    font-weight: 700;
}

.auth-highlights small {
    color: rgba(203, 213, 225, 0.78);
    font-size: 12px;
}

.auth-aside-foot {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: rgba(167, 243, 208, 0.92);
    font-size: 12px;
    font-weight: 600;
}

/* ---------- Brand mark ---------- */
.auth-brand {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    color: inherit;
    font-size: 18px;
    font-weight: 800;
    letter-spacing: -0.035em;
}

.auth-brand > span:last-child > span {
    color: #10b981;
}

.auth-brand-mark {
    display: inline-grid;
    width: 36px;
    height: 36px;
    place-items: center;
    border-radius: 11px;
    background: linear-gradient(135deg, #10b981, #2563eb);
    box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25);
    color: white;
}

/* ---------- Form side ---------- */
.auth-main {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px 20px;
}

@media (min-width: 768px) {
    .auth-main {
        padding: 48px;
    }
}

.auth-card {
    width: 100%;
    max-width: 420px;
    display: flex;
    flex-direction: column;
    gap: 28px;
}

.auth-brand-mobile {
    align-self: center;
    color: #0f172a;
}

@media (min-width: 1024px) {
    .auth-brand-mobile {
        display: none;
    }
}

.auth-head {
    display: flex;
    flex-direction: column;
    gap: 8px;
    text-align: center;
}

@media (min-width: 1024px) {
    .auth-head {
        text-align: left;
    }
}

.auth-head h1 {
    font-size: 1.6rem;
    font-weight: 800;
    letter-spacing: -0.03em;
    color: #0f172a;
}

.auth-head p {
    color: #64748b;
    font-size: 14px;
    line-height: 1.6;
}

.auth-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    border-radius: 999px;
    border: 1px solid rgba(110, 231, 183, 0.35);
    background: rgba(16, 185, 129, 0.12);
    padding: 7px 12px;
    color: #6ee7b7;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

:global(html.dark) .auth-shell {
    background: #020617;
    color: #e2e8f0;
}

:global(html.dark) .auth-head h1 {
    color: #f8fafc;
}

:global(html.dark) .auth-brand-mobile {
    color: #f8fafc;
}
</style>
