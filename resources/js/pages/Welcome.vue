<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowRight,
    BadgeCheck,
    Check,
    CheckCircle2,
    ChevronDown,
    CircleDollarSign,
    Clock3,
    CreditCard,
    Headphones,
    Infinity,
    Laptop,
    LockKeyhole,
    Menu,
    MessageCircle,
    MonitorSmartphone,
    Play,
    PlayCircle,
    Radio,
    ShieldCheck,
    ShoppingCart,
    Smartphone,
    Star,
    Tablet,
    Tv,
    WalletCards,
    X,
    Zap,
} from '@lucide/vue';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { dashboard, login, register } from '@/routes';

const page = usePage();
const isMenuOpen = ref(false);
const selectedMediaId = ref('movies');
const isMediaPaused = ref(false);
let mediaRotationTimer: ReturnType<typeof setInterval> | undefined;
let revealObserver: IntersectionObserver | undefined;

const dashboardUrl = computed(() =>
    page.props.currentTeam ? dashboard(page.props.currentTeam.slug).url : '/',
);

const navigation = [
    { label: 'Início', href: '#inicio' },
    { label: 'Como funciona', href: '#como-funciona' },
    { label: 'Planos', href: '#planos' },
    { label: 'Vantagens', href: '#vantagens' },
    { label: 'Depoimentos', href: '#depoimentos' },
    { label: 'FAQ', href: '#faq' },
    { label: 'Contato', href: '#contato' },
];

const heroBenefits = [
    {
        title: 'Entrega instantânea',
        description: 'Créditos liberados após a confirmação.',
        icon: Zap,
    },
    {
        title: 'Pagamento seguro',
        description: 'Ambiente protegido e criptografado.',
        icon: ShieldCheck,
    },
    {
        title: 'Suporte especializado',
        description: 'Atendimento rápido quando precisar.',
        icon: Headphones,
    },
];

const mediaOptions = [
    {
        id: 'movies',
        label: 'Filmes',
        title: 'Cinema para todos os momentos',
        eyebrow: 'Em destaque',
        meta: ['4K UHD', 'Novidades', 'Para todos'],
        image: 'https://images.unsplash.com/photo-1723390944514-9b117fae8f57?auto=format&fit=crop&w=1200&h=680&q=84',
        position: 'center 32%',
    },
    {
        id: 'series',
        label: 'Séries',
        title: 'Histórias que prendem você',
        eyebrow: 'Maratone agora',
        meta: ['Full HD', 'Novos episódios', '16+'],
        image: 'https://images.unsplash.com/photo-1577699089163-ea1d13f110e1?auto=format&fit=crop&w=1200&h=680&q=84',
        position: 'center 35%',
    },
    {
        id: 'sports',
        label: 'Esportes',
        title: 'A emoção acontece ao vivo',
        eyebrow: 'Grandes jogos',
        meta: ['Ao vivo', 'Alta definição', 'Multiângulo'],
        image: 'https://images.unsplash.com/photo-1671631981648-94ccf5623255?auto=format&fit=crop&w=1200&h=680&q=84',
        position: 'center',
    },
    {
        id: 'live',
        label: 'Ao vivo',
        title: 'Seus canais favoritos',
        eyebrow: 'Programação 24/7',
        meta: ['+10 mil canais', 'Estável', 'Sem limites'],
        image: 'https://images.unsplash.com/photo-1617405207340-954e2e19755c?auto=format&fit=crop&w=1200&h=680&q=84',
        position: 'center',
    },
];

const selectedMedia = computed(
    () =>
        mediaOptions.find((item) => item.id === selectedMediaId.value) ??
        mediaOptions[0],
);

const devices = [
    { label: 'Smart TV', icon: Tv },
    { label: 'Android', icon: Smartphone },
    { label: 'iOS', icon: Smartphone },
    { label: 'TV Box', icon: Radio },
    { label: 'Tablet', icon: Tablet },
    { label: 'Notebook', icon: Laptop },
];

const plans = [
    {
        name: 'Básico',
        credits: 10,
        price: '29,90',
        description: 'Para compras pontuais e primeiros acessos.',
    },
    {
        name: 'Popular',
        credits: 25,
        price: '59,90',
        description: 'O melhor equilíbrio entre preço e quantidade.',
        popular: true,
    },
    {
        name: 'Plus',
        credits: 50,
        price: '99,90',
        description: 'Mais créditos com uma economia ainda maior.',
    },
    {
        name: 'Premium',
        credits: 100,
        price: '169,90',
        description: 'Máxima liberdade e o menor custo por crédito.',
    },
];

const planBenefits = [
    'Recarga imediata',
    'Compatível com principais apps',
    'Suporte via WhatsApp',
    'Créditos sem validade',
];

const benefits = [
    {
        title: 'Entrega imediata',
        description:
            'Receba seus créditos poucos instantes após a confirmação.',
        icon: Zap,
        tone: 'green',
    },
    {
        title: 'Segurança total',
        description:
            'Pagamento protegido e tratamento responsável dos seus dados.',
        icon: ShieldCheck,
        tone: 'blue',
    },
    {
        title: 'Compatibilidade',
        description:
            'Use nos principais dispositivos e aplicativos compatíveis.',
        icon: MonitorSmartphone,
        tone: 'green',
    },
    {
        title: 'Sem mensalidade',
        description: 'Você compra somente os créditos que realmente precisa.',
        icon: Infinity,
        tone: 'blue',
    },
    {
        title: 'Suporte rápido',
        description: 'Atendimento humano para orientar sua compra e recarga.',
        icon: MessageCircle,
        tone: 'green',
    },
    {
        title: 'Melhor custo-benefício',
        description: 'Pacotes transparentes e preços melhores em maior volume.',
        icon: CircleDollarSign,
        tone: 'blue',
    },
];

const comparison = [
    { resource: 'Entrega imediata', brand: true, others: 'Pode demorar' },
    { resource: 'Sem mensalidade', brand: true, others: 'Nem sempre' },
    { resource: 'Créditos sem validade', brand: true, others: 'Limitado' },
    { resource: 'Suporte especializado', brand: true, others: 'Básico' },
    { resource: 'Compatibilidade ampla', brand: true, others: 'Variável' },
    { resource: 'Pagamento seguro', brand: true, others: 'Variável' },
    { resource: 'Melhor custo por crédito', brand: true, others: 'Mais caro' },
];

const testimonials = [
    {
        name: 'Mariana Lopes',
        city: 'São Paulo, SP',
        text: 'Compra muito simples e os créditos chegaram logo após o pagamento. O suporte também respondeu rápido.',
        avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=160&h=160&q=82',
    },
    {
        name: 'Rafael Martins',
        city: 'Curitiba, PR',
        text: 'Gostei da transparência dos planos. Escolhi o pacote, paguei por Pix e consegui fazer a recarga sem dificuldade.',
        avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=160&h=160&q=82',
    },
    {
        name: 'Camila Ferreira',
        city: 'Belo Horizonte, MG',
        text: 'Site organizado, pagamento seguro e atendimento atencioso. Foi uma experiência muito tranquila.',
        avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=160&h=160&q=82',
    },
];

const faqs = [
    {
        question: 'Como recebo meus créditos?',
        answer: 'Após a confirmação do pagamento, os créditos são processados e disponibilizados na sua conta. Você também recebe as instruções pelo canal informado na compra.',
    },
    {
        question: 'Os créditos têm validade?',
        answer: 'Não. Os créditos adquiridos ficam disponíveis para uso, sem prazo curto de expiração.',
    },
    {
        question: 'Quais formas de pagamento são aceitas?',
        answer: 'Você pode pagar por Pix, cartão, boleto ou carteira digital, conforme as opções disponíveis no checkout.',
    },
    {
        question: 'Posso usar em qualquer aplicativo?',
        answer: 'Os créditos funcionam em listas e aplicativos IPTV compatíveis. Se tiver dúvida sobre o seu app, consulte nosso suporte antes da compra.',
    },
    {
        question: 'Precisa de ajuda com sua compra?',
        answer: 'Nosso suporte atende pelo WhatsApp e pode orientar sobre planos, pagamento, compatibilidade e recarga.',
    },
];

const selectMedia = (mediaId: string) => {
    selectedMediaId.value = mediaId;
    restartMediaRotation();
};

const rotateMedia = () => {
    if (isMediaPaused.value || document.hidden) {
        return;
    }

    const currentIndex = mediaOptions.findIndex(
        (item) => item.id === selectedMediaId.value,
    );
    selectedMediaId.value =
        mediaOptions[(currentIndex + 1) % mediaOptions.length].id;
};

const startMediaRotation = () => {
    mediaRotationTimer = setInterval(rotateMedia, 5200);
};

const restartMediaRotation = () => {
    if (mediaRotationTimer) {
        clearInterval(mediaRotationTimer);
    }

    startMediaRotation();
};

const closeMenu = () => {
    isMenuOpen.value = false;
};

onMounted(() => {
    startMediaRotation();

    revealObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealObserver?.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.12 },
    );

    document
        .querySelectorAll<HTMLElement>('[data-reveal]')
        .forEach((element) => revealObserver?.observe(element));
});

onBeforeUnmount(() => {
    if (mediaRotationTimer) {
        clearInterval(mediaRotationTimer);
    }

    revealObserver?.disconnect();
});
</script>

<template>
    <Head title="Nexo Play — Créditos IPTV">
        <meta
            name="description"
            content="Compre créditos IPTV com recarga imediata, pagamento seguro e suporte especializado."
        />
    </Head>

    <div class="landing-page">
        <header class="site-header">
            <div class="header-inner container">
                <a href="#inicio" class="brand" aria-label="Nexo Play - Início">
                    <span class="brand-mark"
                        ><Play :size="17" fill="currentColor"
                    /></span>
                    <span>NEXO<span>PLAY</span></span>
                </a>

                <nav class="desktop-nav" aria-label="Navegação principal">
                    <a
                        v-for="item in navigation"
                        :key="item.href"
                        :href="item.href"
                    >
                        {{ item.label }}
                    </a>
                </nav>

                <div class="header-actions">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboardUrl"
                        class="button button-outline button-small"
                    >
                        Painel
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="button button-outline button-small"
                        >
                            Entrar
                        </Link>
                        <a
                            href="#planos"
                            class="button button-primary button-small"
                        >
                            <Zap :size="16" /> Comprar créditos
                        </a>
                    </template>
                </div>

                <button
                    class="menu-button"
                    type="button"
                    :aria-expanded="isMenuOpen"
                    aria-label="Abrir menu"
                    @click="isMenuOpen = !isMenuOpen"
                >
                    <X v-if="isMenuOpen" :size="22" />
                    <Menu v-else :size="22" />
                </button>
            </div>

            <nav
                v-if="isMenuOpen"
                class="mobile-nav"
                aria-label="Navegação mobile"
            >
                <a
                    v-for="item in navigation"
                    :key="item.href"
                    :href="item.href"
                    @click="closeMenu"
                >
                    {{ item.label }}
                </a>
                <div class="mobile-actions">
                    <Link :href="login()" class="button button-outline"
                        >Entrar</Link
                    >
                    <a
                        href="#planos"
                        class="button button-primary"
                        @click="closeMenu"
                    >
                        Comprar créditos
                    </a>
                </div>
            </nav>
        </header>

        <main>
            <section id="inicio" class="hero">
                <div class="hero-shape hero-shape-green"></div>
                <div class="hero-shape hero-shape-blue"></div>
                <div class="hero-grid container">
                    <div class="hero-copy" data-reveal>
                        <div class="eyebrow eyebrow-green">
                            <ShieldCheck :size="15" />
                            Segurança e entrega imediata
                        </div>
                        <h1>
                            Créditos IPTV para
                            <span class="gradient-text">recarga imediata</span>
                        </h1>
                        <p class="hero-description">
                            Recarregue de forma rápida, segura e 100% online.
                            Compatível com os principais dispositivos e com
                            suporte especializado durante toda a compra.
                        </p>

                        <div class="hero-actions">
                            <a
                                href="#planos"
                                class="button button-primary button-large"
                            >
                                Ver planos e preços <ArrowRight :size="18" />
                            </a>
                            <a
                                href="#como-funciona"
                                class="button button-outline button-large"
                            >
                                <PlayCircle :size="18" /> Como funciona
                            </a>
                        </div>

                        <div class="hero-benefits">
                            <article
                                v-for="item in heroBenefits"
                                :key="item.title"
                            >
                                <span class="mini-icon"
                                    ><component :is="item.icon" :size="18"
                                /></span>
                                <div>
                                    <strong>{{ item.title }}</strong>
                                    <p>{{ item.description }}</p>
                                </div>
                            </article>
                        </div>
                    </div>

                    <div
                        class="media-stage"
                        data-reveal
                        aria-label="Demonstração em múltiplos dispositivos"
                        @mouseenter="isMediaPaused = true"
                        @mouseleave="isMediaPaused = false"
                        @focusin="isMediaPaused = true"
                        @focusout="isMediaPaused = false"
                    >
                        <div class="media-halo"></div>
                        <div class="status-card">
                            <span><CheckCircle2 :size="17" /></span>
                            <div>
                                <strong>Recarga concluída</strong>
                                <small>Créditos liberados com sucesso</small>
                            </div>
                        </div>

                        <div class="tv-device">
                            <div class="tv-screen">
                                <Transition name="media-fade" mode="out-in">
                                    <img
                                        :key="selectedMedia.id"
                                        class="tv-backdrop"
                                        :src="selectedMedia.image"
                                        :alt="`Destaque da categoria ${selectedMedia.label}`"
                                        :style="{
                                            objectPosition:
                                                selectedMedia.position,
                                        }"
                                    />
                                </Transition>
                                <div class="screen-overlay"></div>
                                <div class="screen-topbar">
                                    <div class="mini-brand">
                                        <span
                                            ><Play
                                                :size="9"
                                                fill="currentColor"
                                        /></span>
                                        NEXO PLAY
                                    </div>
                                    <div class="live-pill">
                                        <span></span> AO VIVO
                                    </div>
                                </div>
                                <div class="featured-content">
                                    <div class="content-label">
                                        {{ selectedMedia.eyebrow }}
                                    </div>
                                    <h3>{{ selectedMedia.title }}</h3>
                                    <div class="content-meta">
                                        <template
                                            v-for="(
                                                meta, index
                                            ) in selectedMedia.meta"
                                            :key="meta"
                                        >
                                            <span v-if="index">•</span
                                            >{{ meta }}
                                        </template>
                                    </div>
                                    <button type="button">
                                        <Play :size="10" fill="currentColor" />
                                        Assistir
                                    </button>
                                </div>
                                <div
                                    class="content-shelves"
                                    role="tablist"
                                    aria-label="Categorias em destaque"
                                >
                                    <button
                                        v-for="(media, index) in mediaOptions"
                                        :key="media.id"
                                        type="button"
                                        class="poster"
                                        :class="[
                                            `poster-${index + 1}`,
                                            {
                                                'is-active':
                                                    media.id ===
                                                    selectedMedia.id,
                                            },
                                        ]"
                                        role="tab"
                                        :aria-selected="
                                            media.id === selectedMedia.id
                                        "
                                        @click="selectMedia(media.id)"
                                    >
                                        <span>{{ media.label }}</span>
                                    </button>
                                </div>
                            </div>
                            <div class="tv-stand"></div>
                        </div>

                        <div class="tablet-device">
                            <div class="tablet-screen">
                                <span class="device-live">LIVE</span>
                                <Tv :size="27" />
                                <strong>Canais ao vivo</strong>
                                <small>Conteúdo em alta definição</small>
                                <div class="player-line"><span></span></div>
                            </div>
                        </div>

                        <div class="phone-device">
                            <div class="phone-notch"></div>
                            <div class="phone-screen">
                                <div class="phone-icon">
                                    <Radio :size="17" />
                                </div>
                                <small>TRANSMISSÃO</small>
                                <strong>ESTÁVEL</strong>
                                <div class="phone-play">
                                    <Play :size="10" fill="currentColor" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="compatibility">
                <div class="compatibility-inner container" data-reveal>
                    <p>Compatível com seus dispositivos favoritos</p>
                    <div class="device-list">
                        <span v-for="device in devices" :key="device.label">
                            <component :is="device.icon" :size="19" />
                            {{ device.label }}
                        </span>
                    </div>
                </div>
            </section>

            <section id="como-funciona" class="section soft-section">
                <div class="container">
                    <div class="section-heading" data-reveal>
                        <div class="eyebrow eyebrow-blue">
                            Compra simples e rápida
                        </div>
                        <h2>Da escolha à recarga em poucos minutos</h2>
                        <p>
                            Um processo direto, transparente e sem burocracia.
                        </p>
                    </div>
                    <div class="steps-grid">
                        <article data-reveal>
                            <span class="step-number">01</span>
                            <div class="feature-icon green">
                                <ShoppingCart :size="23" />
                            </div>
                            <h3>Escolha seu plano</h3>
                            <p>
                                Compare os pacotes e selecione a quantidade
                                ideal.
                            </p>
                        </article>
                        <article data-reveal>
                            <span class="step-number">02</span>
                            <div class="feature-icon blue">
                                <CreditCard :size="23" />
                            </div>
                            <h3>Pague com segurança</h3>
                            <p>
                                Finalize pelo método de pagamento que preferir.
                            </p>
                        </article>
                        <article data-reveal>
                            <span class="step-number">03</span>
                            <div class="feature-icon green">
                                <Zap :size="23" />
                            </div>
                            <h3>Receba os créditos</h3>
                            <p>A liberação acontece logo após a confirmação.</p>
                        </article>
                        <article data-reveal>
                            <span class="step-number">04</span>
                            <div class="feature-icon blue">
                                <PlayCircle :size="23" />
                            </div>
                            <h3>Recarregue e aproveite</h3>
                            <p>
                                Use seus créditos na lista ou aplicativo
                                compatível.
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <section id="planos" class="section">
                <div class="container">
                    <div class="section-heading" data-reveal>
                        <div class="eyebrow eyebrow-green">
                            Planos transparentes
                        </div>
                        <h2>Escolha o plano ideal para você</h2>
                        <p>
                            Créditos válidos para recarga de listas IPTV
                            compatíveis.
                        </p>
                    </div>

                    <div class="plans-grid">
                        <article
                            v-for="plan in plans"
                            :key="plan.name"
                            class="plan-card"
                            :class="{ popular: plan.popular }"
                            data-reveal
                        >
                            <div v-if="plan.popular" class="popular-badge">
                                <BadgeCheck :size="15" /> Mais escolhido
                            </div>
                            <div class="plan-topline">
                                <div>
                                    <span class="plan-name">{{
                                        plan.name
                                    }}</span>
                                    <h3>{{ plan.credits }} créditos</h3>
                                </div>
                                <span class="plan-icon"
                                    ><Zap :size="20"
                                /></span>
                            </div>
                            <p class="plan-description">
                                {{ plan.description }}
                            </p>
                            <div class="plan-price">
                                <span>R$</span><strong>{{ plan.price }}</strong>
                                <small>pagamento único</small>
                            </div>
                            <ul>
                                <li
                                    v-for="benefit in planBenefits"
                                    :key="benefit"
                                >
                                    <Check :size="17" /> {{ benefit }}
                                </li>
                            </ul>
                            <Link
                                :href="register()"
                                class="button plan-button"
                                :class="
                                    plan.popular
                                        ? 'button-blue'
                                        : 'button-primary'
                                "
                            >
                                Comprar agora <ArrowRight :size="17" />
                            </Link>
                        </article>
                    </div>
                </div>
            </section>

            <section class="payment-section">
                <div class="payment-card container" data-reveal>
                    <div class="payment-security">
                        <span><LockKeyhole :size="25" /></span>
                        <div>
                            <strong
                                >Ambiente 100% seguro e criptografado</strong
                            >
                            <p>
                                Seus dados e seu pagamento são processados com
                                proteção.
                            </p>
                        </div>
                    </div>
                    <div class="payment-methods">
                        <span><Zap :size="17" /> PIX</span>
                        <span><CreditCard :size="17" /> Cartão</span>
                        <span><WalletCards :size="17" /> Boleto</span>
                        <span><Smartphone :size="17" /> Carteira digital</span>
                    </div>
                </div>
            </section>

            <section id="vantagens" class="section soft-section">
                <div class="container">
                    <div class="section-heading" data-reveal>
                        <div class="eyebrow eyebrow-blue">
                            Mais tranquilidade
                        </div>
                        <h2>Por que escolher a Nexo Play?</h2>
                        <p>
                            Tudo foi pensado para uma compra rápida, clara e
                            confiável.
                        </p>
                    </div>
                    <div class="benefits-grid">
                        <article
                            v-for="item in benefits"
                            :key="item.title"
                            data-reveal
                        >
                            <div class="feature-icon" :class="item.tone">
                                <component :is="item.icon" :size="24" />
                            </div>
                            <h3>{{ item.title }}</h3>
                            <p>{{ item.description }}</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="section comparison-section">
                <div class="comparison-layout container">
                    <div class="comparison-copy" data-reveal>
                        <div class="eyebrow eyebrow-green">
                            Escolha inteligente
                        </div>
                        <h2>Compare e veja a diferença</h2>
                        <p>
                            Mais créditos, mais liberdade e uma experiência de
                            compra melhor.
                        </p>
                        <a href="#planos" class="button button-primary">
                            Ver planos <ArrowRight :size="17" />
                        </a>
                    </div>
                    <div class="comparison-table-wrap" data-reveal>
                        <table class="comparison-table">
                            <thead>
                                <tr>
                                    <th>Recursos</th>
                                    <th>
                                        <span class="table-brand-mark">N</span>
                                        Nexo Play
                                    </th>
                                    <th>Outros serviços</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="row in comparison"
                                    :key="row.resource"
                                >
                                    <td>{{ row.resource }}</td>
                                    <td>
                                        <CheckCircle2 :size="18" /> Incluído
                                    </td>
                                    <td>{{ row.others }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section id="depoimentos" class="section testimonials-section">
                <div class="container">
                    <div class="section-heading" data-reveal>
                        <div class="eyebrow eyebrow-blue">
                            Clientes satisfeitos
                        </div>
                        <h2>O que nossos clientes dizem</h2>
                        <p>A satisfação de quem já comprou e recomenda.</p>
                    </div>
                    <div class="testimonials-grid">
                        <article
                            v-for="testimonial in testimonials"
                            :key="testimonial.name"
                            data-reveal
                        >
                            <div class="stars" aria-label="5 estrelas">
                                <Star
                                    v-for="index in 5"
                                    :key="index"
                                    :size="16"
                                    fill="currentColor"
                                />
                            </div>
                            <p>“{{ testimonial.text }}”</p>
                            <div class="testimonial-author">
                                <img
                                    :src="testimonial.avatar"
                                    :alt="testimonial.name"
                                    loading="lazy"
                                />
                                <div>
                                    <strong>{{ testimonial.name }}</strong>
                                    <span>{{ testimonial.city }}</span>
                                </div>
                                <BadgeCheck :size="19" />
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section id="faq" class="section soft-section">
                <div class="container">
                    <div class="section-heading" data-reveal>
                        <div class="eyebrow eyebrow-green">Ajuda e suporte</div>
                        <h2>Dúvidas frequentes</h2>
                        <p>
                            Confira as respostas para as perguntas mais comuns.
                        </p>
                    </div>
                    <div class="faq-layout">
                        <div class="faq-list" data-reveal>
                            <details v-for="faq in faqs" :key="faq.question">
                                <summary>
                                    {{ faq.question }}
                                    <span><ChevronDown :size="18" /></span>
                                </summary>
                                <p>{{ faq.answer }}</p>
                            </details>
                        </div>
                        <aside id="contato" class="support-card" data-reveal>
                            <div class="support-icon">
                                <MessageCircle :size="29" />
                            </div>
                            <span>ATENDIMENTO HUMANIZADO</span>
                            <h3>Fale com nosso suporte pelo WhatsApp</h3>
                            <p>
                                Tire dúvidas sobre planos, compatibilidade ou
                                pagamento antes de finalizar sua compra.
                            </p>
                            <a
                                href="https://wa.me/5500000000000"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="button button-primary"
                            >
                                <MessageCircle :size="18" /> Abrir WhatsApp
                            </a>
                            <small
                                ><Clock3 :size="14" /> Atendimento todos os
                                dias</small
                            >
                        </aside>
                    </div>
                </div>
            </section>
        </main>

        <footer class="site-footer">
            <div class="footer-grid container">
                <div class="footer-brand">
                    <a href="#inicio" class="brand">
                        <span class="brand-mark"
                            ><Play :size="17" fill="currentColor"
                        /></span>
                        <span>NEXO<span>PLAY</span></span>
                    </a>
                    <p>
                        Créditos IPTV com compra simples, entrega rápida e
                        suporte confiável.
                    </p>
                    <div class="safe-site">
                        <ShieldCheck :size="17" /> Site seguro
                    </div>
                </div>
                <div>
                    <h3>Navegação</h3>
                    <a href="#inicio">Início</a>
                    <a href="#como-funciona">Como funciona</a>
                    <a href="#planos">Planos</a>
                    <a href="#vantagens">Vantagens</a>
                </div>
                <div>
                    <h3>Informações</h3>
                    <a href="#faq">Perguntas frequentes</a>
                    <a href="#">Termos de uso</a>
                    <a href="#">Política de privacidade</a>
                    <a href="#contato">Contato</a>
                </div>
                <div>
                    <h3>Suporte</h3>
                    <a href="https://wa.me/5500000000000">WhatsApp</a>
                    <a href="mailto:suporte@nexoplay.com"
                        >suporte@nexoplay.com</a
                    >
                    <p>Todos os dias, das 8h às 22h</p>
                </div>
                <div>
                    <h3>Pagamentos</h3>
                    <div class="footer-payments">
                        <span>PIX</span><span>VISA</span><span>MASTER</span>
                    </div>
                    <p>Pagamento protegido e criptografado.</p>
                </div>
            </div>
            <div class="footer-bottom container">
                <span>© 2026 Nexo Play. Todos os direitos reservados.</span>
                <a
                    href="https://unsplash.com"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Imagens por Unsplash
                </a>
            </div>
        </footer>
    </div>
</template>

<style scoped>
:global(html) {
    scroll-behavior: smooth;
    scroll-padding-top: 84px;
}

:global(body) {
    margin: 0;
    background: #f8fafc;
}

.landing-page {
    min-height: 100vh;
    overflow: hidden;
    background: #f8fafc;
    color: #0f172a;
    font-family: Inter, 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
}

.container {
    width: min(1280px, calc(100% - 48px));
    margin-inline: auto;
}

.site-header {
    position: fixed;
    z-index: 50;
    top: 0;
    right: 0;
    left: 0;
    border-bottom: 1px solid rgba(226, 232, 240, 0.82);
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(16px);
}

.header-inner {
    display: flex;
    height: 74px;
    align-items: center;
    justify-content: space-between;
}

.brand {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    color: #0f172a;
    font-size: 17px;
    font-weight: 800;
    letter-spacing: -0.035em;
}

.brand > span:last-child > span {
    color: #10b981;
}

.brand-mark {
    display: inline-grid;
    width: 35px;
    height: 35px;
    place-items: center;
    border-radius: 11px;
    background: linear-gradient(135deg, #10b981, #2563eb);
    box-shadow: 0 8px 20px rgba(37, 99, 235, 0.18);
    color: white;
}

.desktop-nav {
    display: flex;
    align-items: center;
    gap: 26px;
}

.desktop-nav a {
    color: #475569;
    font-size: 13px;
    font-weight: 500;
    transition: color 0.2s ease;
}

.desktop-nav a:hover {
    color: #0f172a;
}

.header-actions,
.hero-actions,
.mobile-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.button {
    display: inline-flex;
    min-height: 44px;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border: 1px solid transparent;
    border-radius: 13px;
    padding: 0 19px;
    font-size: 14px;
    font-weight: 700;
    transition:
        transform 0.22s ease,
        box-shadow 0.22s ease,
        background 0.22s ease,
        border-color 0.22s ease;
}

.button:hover {
    transform: translateY(-2px);
}

.button:focus-visible,
a:focus-visible,
button:focus-visible,
summary:focus-visible {
    outline: 3px solid rgba(37, 99, 235, 0.22);
    outline-offset: 3px;
}

.button-primary {
    background: #10b981;
    box-shadow: 0 10px 24px rgba(16, 185, 129, 0.2);
    color: white;
}

.button-primary:hover {
    background: #059669;
    box-shadow: 0 14px 30px rgba(16, 185, 129, 0.28);
}

.button-blue {
    background: #2563eb;
    box-shadow: 0 10px 24px rgba(37, 99, 235, 0.22);
    color: white;
}

.button-blue:hover {
    background: #1d4ed8;
}

.button-outline {
    border-color: #d7dee8;
    background: white;
    color: #0f172a;
}

.button-outline:hover {
    border-color: #94a3b8;
    background: #f8fafc;
}

.button-small {
    min-height: 39px;
    padding-inline: 15px;
    font-size: 13px;
}

.button-large {
    min-height: 54px;
    padding-inline: 23px;
    font-size: 15px;
}

.menu-button,
.mobile-nav {
    display: none;
}

.hero {
    position: relative;
    padding: 142px 0 82px;
    background:
        radial-gradient(
            circle at 84% 20%,
            rgba(37, 99, 235, 0.07),
            transparent 28rem
        ),
        radial-gradient(
            circle at 10% 35%,
            rgba(16, 185, 129, 0.06),
            transparent 25rem
        ),
        #f8fafc;
}

.hero-grid {
    position: relative;
    z-index: 2;
    display: grid;
    min-height: 590px;
    grid-template-columns: 0.9fr 1.1fr;
    align-items: center;
    gap: 64px;
}

.hero-shape {
    position: absolute;
    border-radius: 50%;
    filter: blur(5px);
    pointer-events: none;
}

.hero-shape-green {
    top: 110px;
    right: 8%;
    width: 270px;
    height: 270px;
    background: rgba(16, 185, 129, 0.05);
}

.hero-shape-blue {
    right: -80px;
    bottom: 20px;
    width: 320px;
    height: 320px;
    background: rgba(37, 99, 235, 0.05);
}

.eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    border-radius: 999px;
    padding: 8px 12px;
    font-size: 11px;
    font-weight: 750;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.eyebrow-green {
    border: 1px solid #bbf7d0;
    background: #ecfdf5;
    color: #059669;
}

.eyebrow-blue {
    border: 1px solid #bfdbfe;
    background: #eff6ff;
    color: #2563eb;
}

.hero-copy h1 {
    max-width: 660px;
    margin: 22px 0 20px;
    font-size: clamp(3rem, 5vw, 3.7rem);
    font-weight: 800;
    letter-spacing: -0.055em;
    line-height: 1.05;
}

.gradient-text {
    display: block;
    background: linear-gradient(135deg, #10b981 0%, #2563eb 100%);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.hero-description {
    max-width: 620px;
    color: #475569;
    font-size: 17px;
    line-height: 1.7;
}

.hero-actions {
    flex-wrap: wrap;
    margin-top: 30px;
}

.hero-benefits {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-top: 34px;
}

.hero-benefits article {
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.mini-icon {
    display: grid;
    width: 34px;
    height: 34px;
    flex: 0 0 auto;
    place-items: center;
    border: 1px solid #d1fae5;
    border-radius: 10px;
    background: white;
    color: #10b981;
    box-shadow: 0 5px 16px rgba(15, 23, 42, 0.05);
}

.hero-benefits strong {
    display: block;
    margin-top: 1px;
    font-size: 11px;
}

.hero-benefits p {
    margin-top: 3px;
    color: #64748b;
    font-size: 9px;
    line-height: 1.45;
}

.media-stage {
    position: relative;
    min-height: 530px;
    perspective: 1000px;
}

.media-halo {
    position: absolute;
    top: 5%;
    right: 2%;
    width: 92%;
    height: 84%;
    border-radius: 45%;
    background: radial-gradient(
        circle,
        rgba(16, 185, 129, 0.15),
        rgba(37, 99, 235, 0.08) 45%,
        transparent 70%
    );
    filter: blur(15px);
}

.tv-device {
    position: absolute;
    z-index: 3;
    top: 70px;
    right: 0;
    width: 94%;
}

.tv-screen {
    position: relative;
    aspect-ratio: 16 / 9;
    overflow: hidden;
    border: 7px solid #172033;
    border-radius: 17px;
    background: #071026;
    box-shadow:
        0 32px 70px rgba(15, 23, 42, 0.24),
        inset 0 0 0 1px rgba(255, 255, 255, 0.12);
}

.tv-backdrop {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.screen-overlay {
    position: absolute;
    z-index: 1;
    inset: 0;
    background:
        linear-gradient(
            90deg,
            rgba(2, 6, 23, 0.97),
            rgba(2, 6, 23, 0.66) 48%,
            rgba(2, 6, 23, 0.08)
        ),
        linear-gradient(180deg, transparent 50%, rgba(2, 6, 23, 0.62));
    pointer-events: none;
}

.screen-topbar {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 18px;
}

.mini-brand {
    display: flex;
    align-items: center;
    gap: 6px;
    color: white;
    font-size: 8px;
    font-weight: 800;
}

.mini-brand > span {
    display: grid;
    width: 18px;
    height: 18px;
    place-items: center;
    border-radius: 6px;
    background: linear-gradient(135deg, #10b981, #2563eb);
}

.live-pill {
    display: flex;
    align-items: center;
    gap: 5px;
    border-radius: 999px;
    background: rgba(7, 14, 31, 0.72);
    padding: 5px 8px;
    color: #dce7f7;
    font-size: 6px;
    font-weight: 800;
}

.live-pill span {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #fb456c;
    box-shadow: 0 0 8px #fb456c;
}

.featured-content {
    position: relative;
    z-index: 2;
    width: 52%;
    padding: 20px 25px 15px;
    color: white;
}

.content-label {
    color: #6ee7b7;
    font-size: 7px;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.featured-content h3 {
    min-height: 2.05em;
    margin: 7px 0;
    font-size: 22px;
    letter-spacing: -0.045em;
    line-height: 1.04;
}

.content-meta {
    color: #a8b7ce;
    font-size: 7px;
}

.content-meta span {
    margin-inline: 4px;
}

.featured-content button {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-top: 12px;
    border: 0;
    border-radius: 6px;
    background: white;
    padding: 7px 10px;
    color: #0f172a;
    font-size: 7px;
    font-weight: 800;
}

.content-shelves {
    position: absolute;
    z-index: 3;
    right: 16px;
    bottom: 14px;
    left: 18px;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
}

.poster {
    position: relative;
    height: 56px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    padding: 0;
    background-color: #172033;
    color: white;
    cursor: pointer;
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.25);
    transition:
        transform 0.25s ease,
        border-color 0.25s ease,
        box-shadow 0.25s ease;
}

.poster::before {
    position: absolute;
    inset: 0;
    background: linear-gradient(transparent, rgba(2, 6, 23, 0.9));
    content: '';
}

.poster:hover {
    transform: translateY(-2px);
}

.poster.is-active {
    border-color: #6ee7b7;
    box-shadow:
        0 0 0 1px rgba(110, 231, 183, 0.35),
        0 0 18px rgba(16, 185, 129, 0.35);
    transform: translateY(-3px);
}

.poster span {
    position: absolute;
    z-index: 1;
    right: 6px;
    bottom: 5px;
    left: 6px;
    font-size: 6px;
    font-weight: 800;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

.poster-1 {
    background: url('https://images.unsplash.com/photo-1723390944514-9b117fae8f57?auto=format&fit=crop&w=360&h=220&q=78')
        center 32% / cover;
}

.poster-2 {
    background: url('https://images.unsplash.com/photo-1577699089163-ea1d13f110e1?auto=format&fit=crop&w=360&h=220&q=78')
        center 35% / cover;
}

.poster-3 {
    background: url('https://images.unsplash.com/photo-1671631981648-94ccf5623255?auto=format&fit=crop&w=360&h=220&q=78')
        center / cover;
}

.poster-4 {
    background: url('https://images.unsplash.com/photo-1617405207340-954e2e19755c?auto=format&fit=crop&w=360&h=220&q=78')
        center / cover;
}

.tv-stand {
    position: relative;
    width: 33%;
    height: 21px;
    margin: 0 auto;
    border-bottom: 5px solid #263247;
}

.tv-stand::before {
    position: absolute;
    top: 0;
    left: calc(50% - 18px);
    width: 36px;
    height: 20px;
    background: linear-gradient(#263247, #111827);
    content: '';
    clip-path: polygon(35% 0, 65% 0, 100% 100%, 0 100%);
}

.tablet-device {
    position: absolute;
    z-index: 5;
    bottom: 7px;
    left: 0;
    width: 205px;
    height: 142px;
    border: 6px solid #1e293b;
    border-radius: 16px;
    background: #071022;
    box-shadow: 0 22px 50px rgba(15, 23, 42, 0.26);
    transform: rotate(-4deg);
}

.tablet-screen {
    position: relative;
    display: flex;
    height: 100%;
    flex-direction: column;
    justify-content: flex-end;
    overflow: hidden;
    border-radius: 9px;
    background:
        linear-gradient(90deg, rgba(5, 13, 31, 0.82), rgba(24, 18, 65, 0.15)),
        url('https://images.unsplash.com/photo-1671631981648-94ccf5623255?auto=format&fit=crop&w=640&h=440&q=80')
            center / cover;
    padding: 16px;
    color: white;
}

.tablet-screen svg {
    margin-bottom: 6px;
    color: #6ee7b7;
}

.tablet-screen strong {
    font-size: 13px;
}

.tablet-screen small {
    color: #b7c4d7;
    font-size: 8px;
}

.device-live {
    position: absolute;
    top: 10px;
    left: 10px;
    border-radius: 999px;
    background: #ef4444;
    padding: 3px 6px;
    font-size: 5px;
    font-weight: 900;
}

.player-line {
    height: 3px;
    margin-top: 9px;
    border-radius: 5px;
    background: rgba(255, 255, 255, 0.22);
}

.player-line span {
    display: block;
    width: 68%;
    height: 100%;
    border-radius: inherit;
    background: #34d399;
}

.phone-device {
    position: absolute;
    z-index: 7;
    right: 12px;
    bottom: -8px;
    width: 98px;
    height: 184px;
    border: 6px solid #1e293b;
    border-radius: 22px;
    background: #071022;
    box-shadow: 0 25px 55px rgba(15, 23, 42, 0.3);
    transform: rotate(4deg);
}

.phone-notch {
    position: absolute;
    z-index: 2;
    top: 5px;
    left: 50%;
    width: 24px;
    height: 4px;
    border-radius: 5px;
    background: #020617;
    transform: translateX(-50%);
}

.phone-screen {
    display: flex;
    height: 100%;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 15px;
    background:
        linear-gradient(180deg, rgba(7, 17, 38, 0.2), rgba(7, 13, 35, 0.9)),
        url('https://images.unsplash.com/photo-1617405207340-954e2e19755c?auto=format&fit=crop&w=300&h=560&q=78')
            center / cover;
    color: white;
}

.phone-icon {
    display: grid;
    width: 34px;
    height: 34px;
    margin-bottom: 9px;
    place-items: center;
    border: 1px solid rgba(110, 231, 183, 0.52);
    border-radius: 50%;
    background: rgba(16, 185, 129, 0.18);
    color: #6ee7b7;
}

.phone-screen small {
    color: #a7f3d0;
    font-size: 5px;
    font-weight: 800;
}

.phone-screen strong {
    margin-top: 3px;
    font-size: 10px;
}

.phone-play {
    display: grid;
    width: 27px;
    height: 27px;
    margin-top: 13px;
    place-items: center;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981, #2563eb);
}

.status-card {
    position: absolute;
    z-index: 10;
    top: 15px;
    right: -7px;
    display: flex;
    align-items: center;
    gap: 10px;
    border: 1px solid #d1fae5;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.94);
    padding: 12px 14px;
    box-shadow: 0 14px 35px rgba(15, 23, 42, 0.11);
    backdrop-filter: blur(12px);
}

.status-card > span {
    display: grid;
    width: 32px;
    height: 32px;
    place-items: center;
    border-radius: 10px;
    background: #ecfdf5;
    color: #10b981;
}

.status-card div {
    display: flex;
    flex-direction: column;
}

.status-card strong {
    font-size: 10px;
}

.status-card small {
    margin-top: 2px;
    color: #64748b;
    font-size: 7px;
}

.media-fade-enter-active,
.media-fade-leave-active {
    transition:
        opacity 0.32s ease,
        transform 0.48s cubic-bezier(0.22, 1, 0.36, 1),
        filter 0.32s ease;
}

.media-fade-enter-from {
    opacity: 0;
    filter: blur(5px);
    transform: scale(1.04);
}

.media-fade-leave-to {
    opacity: 0;
    transform: scale(1.02);
}

.compatibility {
    border-block: 1px solid #e2e8f0;
    background: white;
}

.compatibility-inner {
    display: flex;
    min-height: 90px;
    align-items: center;
    justify-content: space-between;
    gap: 30px;
}

.compatibility-inner > p {
    color: #475569;
    font-size: 13px;
    font-weight: 600;
}

.device-list {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
}

.device-list span {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
    padding: 9px 11px;
    color: #475569;
    font-size: 11px;
    font-weight: 600;
}

.device-list svg {
    color: #2563eb;
}

.section {
    padding: 96px 0;
}

.soft-section {
    border-block: 1px solid #e9eef5;
    background: #f1f5f9;
}

.section-heading {
    max-width: 680px;
    margin: 0 auto 50px;
    text-align: center;
}

.section-heading h2,
.comparison-copy h2 {
    margin: 18px 0 12px;
    font-size: clamp(2rem, 3.5vw, 2.5rem);
    font-weight: 800;
    letter-spacing: -0.045em;
    line-height: 1.15;
}

.section-heading p,
.comparison-copy > p {
    color: #64748b;
    font-size: 15px;
    line-height: 1.65;
}

.steps-grid,
.plans-grid,
.benefits-grid,
.testimonials-grid {
    display: grid;
    gap: 18px;
}

.steps-grid {
    grid-template-columns: repeat(4, 1fr);
}

.steps-grid article,
.benefits-grid article {
    position: relative;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    background: white;
    padding: 27px;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    transition:
        transform 0.25s ease,
        box-shadow 0.25s ease,
        border-color 0.25s ease;
}

.steps-grid article:hover,
.benefits-grid article:hover {
    border-color: #cbd5e1;
    box-shadow: 0 16px 38px rgba(15, 23, 42, 0.09);
    transform: translateY(-4px);
}

.step-number {
    position: absolute;
    top: 23px;
    right: 25px;
    color: #cbd5e1;
    font-size: 22px;
    font-weight: 800;
}

.feature-icon {
    display: grid;
    width: 48px;
    height: 48px;
    place-items: center;
    border-radius: 14px;
}

.feature-icon.green {
    background: #ecfdf5;
    color: #10b981;
}

.feature-icon.blue {
    background: #eff6ff;
    color: #2563eb;
}

.steps-grid h3,
.benefits-grid h3 {
    margin: 20px 0 8px;
    font-size: 16px;
    font-weight: 750;
}

.steps-grid p,
.benefits-grid p {
    color: #64748b;
    font-size: 13px;
    line-height: 1.65;
}

.plans-grid {
    grid-template-columns: repeat(4, 1fr);
    align-items: stretch;
}

.plan-card {
    position: relative;
    display: flex;
    min-height: 510px;
    flex-direction: column;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    background: white;
    padding: 28px;
    box-shadow: 0 6px 24px rgba(15, 23, 42, 0.05);
    transition:
        transform 0.25s ease,
        box-shadow 0.25s ease;
}

.plan-card:hover {
    box-shadow: 0 18px 42px rgba(15, 23, 42, 0.1);
    transform: translateY(-5px);
}

.plan-card.popular {
    border: 2px solid #2563eb;
    box-shadow:
        0 18px 45px rgba(37, 99, 235, 0.12),
        0 0 0 6px rgba(37, 99, 235, 0.035);
    transform: translateY(-10px);
}

.plan-card.popular:hover {
    transform: translateY(-15px);
}

.popular-badge {
    position: absolute;
    top: -14px;
    left: 50%;
    display: flex;
    align-items: center;
    gap: 6px;
    border-radius: 999px;
    background: #2563eb;
    padding: 7px 13px;
    color: white;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    transform: translateX(-50%);
}

.plan-topline {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 15px;
}

.plan-name {
    color: #64748b;
    font-size: 12px;
    font-weight: 700;
}

.plan-topline h3 {
    margin-top: 6px;
    font-size: 23px;
    letter-spacing: -0.04em;
}

.plan-icon {
    display: grid;
    width: 42px;
    height: 42px;
    place-items: center;
    border-radius: 13px;
    background: #ecfdf5;
    color: #10b981;
}

.popular .plan-icon {
    background: #eff6ff;
    color: #2563eb;
}

.plan-description {
    min-height: 44px;
    margin-top: 15px;
    color: #64748b;
    font-size: 12px;
    line-height: 1.6;
}

.plan-price {
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    margin: 24px 0;
    padding-bottom: 22px;
    border-bottom: 1px solid #e2e8f0;
}

.plan-price > span {
    margin-right: 5px;
    color: #64748b;
    font-size: 13px;
}

.plan-price strong {
    font-size: 37px;
    font-weight: 800;
    letter-spacing: -0.06em;
}

.plan-price small {
    width: 100%;
    margin-top: 4px;
    color: #94a3b8;
    font-size: 10px;
}

.plan-card ul {
    display: grid;
    gap: 13px;
    margin: 0 0 25px;
    padding: 0;
    color: #475569;
    font-size: 12px;
    list-style: none;
}

.plan-card li {
    display: flex;
    align-items: center;
    gap: 9px;
}

.plan-card li svg {
    flex: 0 0 auto;
    color: #10b981;
}

.plan-button {
    width: 100%;
    margin-top: auto;
}

.payment-section {
    padding-bottom: 96px;
}

.payment-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 40px;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    background: white;
    padding: 27px 32px;
    box-shadow: 0 8px 28px rgba(15, 23, 42, 0.05);
}

.payment-security {
    display: flex;
    align-items: center;
    gap: 14px;
}

.payment-security > span {
    display: grid;
    width: 48px;
    height: 48px;
    flex: 0 0 auto;
    place-items: center;
    border-radius: 14px;
    background: #ecfdf5;
    color: #10b981;
}

.payment-security strong {
    display: block;
    font-size: 14px;
}

.payment-security p {
    margin-top: 4px;
    color: #64748b;
    font-size: 11px;
}

.payment-methods {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 9px;
}

.payment-methods span {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
    padding: 10px 12px;
    color: #475569;
    font-size: 10px;
    font-weight: 700;
}

.payment-methods svg {
    color: #2563eb;
}

.benefits-grid {
    grid-template-columns: repeat(3, 1fr);
}

.comparison-section {
    background: white;
}

.comparison-layout {
    display: grid;
    grid-template-columns: 0.7fr 1.3fr;
    align-items: center;
    gap: 70px;
}

.comparison-copy > p {
    margin-bottom: 25px;
}

.comparison-table-wrap {
    overflow: hidden;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    box-shadow: 0 12px 36px rgba(15, 23, 42, 0.07);
}

.comparison-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    font-size: 12px;
}

.comparison-table th,
.comparison-table td {
    padding: 16px 20px;
    border-bottom: 1px solid #e9eef5;
    text-align: left;
}

.comparison-table th {
    background: #f8fafc;
    color: #334155;
    font-size: 11px;
}

.comparison-table th:nth-child(2) {
    background: #eff6ff;
    color: #1d4ed8;
}

.comparison-table td {
    color: #64748b;
}

.comparison-table td:first-child {
    color: #0f172a;
    font-weight: 600;
}

.comparison-table td:nth-child(2) {
    background: rgba(239, 246, 255, 0.5);
    color: #059669;
    font-weight: 700;
}

.comparison-table td:nth-child(2) svg {
    display: inline;
    margin-right: 7px;
    vertical-align: middle;
}

.comparison-table tr:last-child td {
    border-bottom: 0;
}

.table-brand-mark {
    display: inline-grid;
    width: 22px;
    height: 22px;
    margin-right: 6px;
    place-items: center;
    border-radius: 7px;
    background: linear-gradient(135deg, #10b981, #2563eb);
    color: white;
    font-size: 10px;
}

.testimonials-section {
    border-block: 1px solid #e9eef5;
    background: #f8fafc;
}

.testimonials-grid {
    grid-template-columns: repeat(3, 1fr);
}

.testimonials-grid article {
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    background: white;
    padding: 28px;
    box-shadow: 0 5px 22px rgba(15, 23, 42, 0.05);
    transition:
        transform 0.25s ease,
        box-shadow 0.25s ease;
}

.testimonials-grid article:hover {
    box-shadow: 0 16px 36px rgba(15, 23, 42, 0.09);
    transform: translateY(-4px);
}

.stars {
    display: flex;
    gap: 3px;
    color: #f59e0b;
}

.testimonials-grid article > p {
    min-height: 92px;
    margin: 17px 0 23px;
    color: #475569;
    font-size: 13px;
    line-height: 1.75;
}

.testimonial-author {
    display: flex;
    align-items: center;
    gap: 11px;
    padding-top: 18px;
    border-top: 1px solid #e2e8f0;
}

.testimonial-author img {
    width: 43px;
    height: 43px;
    border-radius: 50%;
    object-fit: cover;
}

.testimonial-author div {
    display: flex;
    flex: 1;
    flex-direction: column;
}

.testimonial-author strong {
    font-size: 12px;
}

.testimonial-author span {
    margin-top: 3px;
    color: #94a3b8;
    font-size: 10px;
}

.testimonial-author > svg {
    color: #2563eb;
}

.faq-layout {
    display: grid;
    grid-template-columns: 1.35fr 0.65fr;
    gap: 28px;
}

.faq-list {
    display: grid;
    gap: 11px;
}

.faq-list details {
    border: 1px solid #e2e8f0;
    border-radius: 15px;
    background: white;
    transition: border-color 0.2s ease;
}

.faq-list details[open] {
    border-color: #93c5fd;
}

.faq-list summary {
    display: flex;
    cursor: pointer;
    align-items: center;
    justify-content: space-between;
    padding: 19px 20px;
    color: #0f172a;
    font-size: 13px;
    font-weight: 700;
    list-style: none;
}

.faq-list summary::-webkit-details-marker {
    display: none;
}

.faq-list summary span {
    display: grid;
    width: 31px;
    height: 31px;
    place-items: center;
    border-radius: 9px;
    background: #eff6ff;
    color: #2563eb;
}

.faq-list details[open] summary svg {
    transform: rotate(180deg);
}

.faq-list details p {
    margin: -3px 20px 20px;
    padding-right: 50px;
    color: #64748b;
    font-size: 12px;
    line-height: 1.7;
}

.support-card {
    display: flex;
    align-items: flex-start;
    flex-direction: column;
    border: 1px solid #a7f3d0;
    border-radius: 20px;
    background:
        radial-gradient(
            circle at 90% 10%,
            rgba(37, 99, 235, 0.08),
            transparent 11rem
        ),
        #ecfdf5;
    padding: 30px;
}

.support-icon {
    display: grid;
    width: 55px;
    height: 55px;
    place-items: center;
    border-radius: 17px;
    background: #10b981;
    box-shadow: 0 12px 25px rgba(16, 185, 129, 0.22);
    color: white;
}

.support-card > span {
    margin-top: 22px;
    color: #059669;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 0.08em;
}

.support-card h3 {
    margin: 8px 0 10px;
    font-size: 22px;
    letter-spacing: -0.04em;
    line-height: 1.18;
}

.support-card > p {
    margin-bottom: 22px;
    color: #475569;
    font-size: 12px;
    line-height: 1.7;
}

.support-card .button {
    width: 100%;
}

.support-card small {
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 17px auto 0;
    color: #64748b;
    font-size: 9px;
}

.site-footer {
    border-top: 1px solid #e2e8f0;
    background: white;
    padding-top: 64px;
}

.footer-grid {
    display: grid;
    grid-template-columns: 1.5fr repeat(4, 1fr);
    gap: 44px;
    padding-bottom: 48px;
}

.footer-brand > p {
    max-width: 270px;
    margin-top: 18px;
    color: #64748b;
    font-size: 11px;
    line-height: 1.7;
}

.safe-site {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    margin-top: 18px;
    border-radius: 9px;
    background: #ecfdf5;
    padding: 8px 10px;
    color: #059669;
    font-size: 9px;
    font-weight: 700;
}

.footer-grid h3 {
    margin-bottom: 17px;
    color: #334155;
    font-size: 11px;
    font-weight: 800;
}

.footer-grid > div:not(.footer-brand) > a {
    display: block;
    width: fit-content;
    margin-bottom: 10px;
    color: #64748b;
    font-size: 10px;
}

.footer-grid a:hover {
    color: #059669;
}

.footer-grid > div:not(.footer-brand) > p {
    color: #94a3b8;
    font-size: 9px;
    line-height: 1.6;
}

.footer-payments {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 12px;
}

.footer-payments span {
    border: 1px solid #e2e8f0;
    border-radius: 7px;
    background: #f8fafc;
    padding: 7px 8px;
    color: #475569;
    font-size: 7px;
    font-weight: 800;
}

.footer-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top: 1px solid #e2e8f0;
    padding-block: 20px;
    color: #94a3b8;
    font-size: 9px;
}

.footer-bottom a {
    color: #94a3b8;
}

[data-reveal] {
    opacity: 0;
    transform: translateY(10px);
    transition:
        opacity 0.45s ease-out,
        transform 0.45s ease-out;
}

[data-reveal].is-visible {
    opacity: 1;
    transform: translateY(0);
}

@media (max-width: 1120px) {
    .desktop-nav {
        gap: 15px;
    }

    .desktop-nav a {
        font-size: 11px;
    }

    .hero-grid {
        gap: 25px;
    }

    .plans-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .plan-card.popular,
    .plan-card.popular:hover {
        transform: none;
    }

    .footer-grid {
        grid-template-columns: 1.4fr repeat(2, 1fr);
    }
}

@media (max-width: 860px) {
    .container {
        width: min(100% - 40px, 1280px);
    }

    .desktop-nav,
    .header-actions {
        display: none;
    }

    .menu-button {
        display: grid;
        width: 42px;
        height: 42px;
        place-items: center;
        border: 1px solid #d7dee8;
        border-radius: 12px;
        background: white;
        color: #0f172a;
    }

    .mobile-nav {
        display: grid;
        border-top: 1px solid #e2e8f0;
        background: white;
        padding: 10px 20px 20px;
    }

    .mobile-nav > a {
        border-bottom: 1px solid #edf1f6;
        padding: 13px 2px;
        color: #475569;
        font-size: 13px;
    }

    .mobile-actions {
        padding-top: 16px;
    }

    .mobile-actions .button {
        flex: 1;
    }

    .hero {
        padding-top: 115px;
    }

    .hero-grid {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .hero-copy h1,
    .hero-description {
        margin-inline: auto;
    }

    .hero-actions {
        justify-content: center;
    }

    .hero-benefits {
        width: min(650px, 100%);
        margin-inline: auto;
        text-align: left;
    }

    .media-stage {
        width: min(650px, 100%);
        margin: 15px auto 0;
    }

    .compatibility-inner {
        align-items: flex-start;
        flex-direction: column;
        padding-block: 24px;
    }

    .device-list {
        justify-content: flex-start;
    }

    .steps-grid,
    .benefits-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .comparison-layout,
    .faq-layout {
        grid-template-columns: 1fr;
    }

    .comparison-copy {
        max-width: 620px;
        text-align: center;
        margin-inline: auto;
    }

    .testimonials-grid {
        grid-template-columns: 1fr;
    }

    .testimonials-grid article > p {
        min-height: auto;
    }

    .payment-card {
        align-items: flex-start;
        flex-direction: column;
    }

    .payment-methods {
        justify-content: flex-start;
    }
}

@media (max-width: 560px) {
    .container {
        width: min(100% - 28px, 1280px);
    }

    .header-inner {
        height: 68px;
    }

    .hero {
        padding: 104px 0 58px;
    }

    .hero-grid {
        min-height: auto;
    }

    .hero-copy h1 {
        font-size: 2.45rem;
    }

    .hero-description {
        font-size: 15px;
    }

    .hero-actions {
        align-items: stretch;
        flex-direction: column;
    }

    .hero-actions .button {
        width: 100%;
    }

    .hero-benefits {
        grid-template-columns: 1fr;
    }

    .media-stage {
        min-height: 370px;
    }

    .tv-device {
        top: 62px;
        width: 100%;
    }

    .screen-topbar {
        padding: 10px;
    }

    .featured-content {
        padding: 10px 14px;
    }

    .featured-content h3 {
        font-size: 15px;
    }

    .content-shelves {
        right: 8px;
        bottom: 8px;
        left: 8px;
        gap: 4px;
    }

    .poster {
        height: 36px;
    }

    .tablet-device {
        bottom: 5px;
        left: -3px;
        width: 145px;
        height: 100px;
        border-width: 4px;
    }

    .tablet-screen {
        padding: 10px;
    }

    .tablet-screen svg {
        width: 19px;
        height: 19px;
    }

    .tablet-screen strong {
        font-size: 9px;
    }

    .phone-device {
        right: 2px;
        bottom: -3px;
        width: 73px;
        height: 137px;
        border-width: 4px;
        border-radius: 17px;
    }

    .phone-screen {
        border-radius: 13px;
    }

    .status-card {
        top: 5px;
        right: 0;
        padding: 9px 10px;
    }

    .status-card > span {
        width: 27px;
        height: 27px;
    }

    .section {
        padding: 66px 0;
    }

    .section-heading {
        margin-bottom: 36px;
    }

    .steps-grid,
    .plans-grid,
    .benefits-grid {
        grid-template-columns: 1fr;
    }

    .plan-card {
        min-height: 490px;
    }

    .payment-section {
        padding-bottom: 66px;
    }

    .payment-card {
        padding: 24px 20px;
    }

    .comparison-table-wrap {
        overflow-x: auto;
    }

    .comparison-table {
        min-width: 620px;
    }

    .faq-list details p {
        padding-right: 10px;
    }

    .footer-grid {
        grid-template-columns: 1fr;
        gap: 29px;
    }

    .footer-bottom {
        align-items: flex-start;
        flex-direction: column;
        gap: 9px;
    }
}

@media (prefers-reduced-motion: reduce) {
    :global(html) {
        scroll-behavior: auto;
    }

    *,
    *::before,
    *::after {
        transition-duration: 0.01ms !important;
    }

    [data-reveal] {
        opacity: 1;
        transform: none;
    }
}
</style>
