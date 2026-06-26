<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from '@lucide/vue';

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Paginator {
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
    prev_page_url: string | null;
    next_page_url: string | null;
}

defineProps<{
    meta: Paginator;
}>();

const isNav = (label: string) =>
    label.includes('Previous') ||
    label.includes('Next') ||
    label.includes('&laquo;') ||
    label.includes('&raquo;');
</script>

<template>
    <div
        v-if="meta.total > 0"
        class="flex flex-col items-center justify-between gap-3 pt-2 sm:flex-row"
    >
        <p class="text-xs text-muted-foreground">
            Mostrando {{ meta.from ?? 0 }}–{{ meta.to ?? 0 }} de
            {{ meta.total }} registros
        </p>

        <div class="flex items-center gap-1">
            <Link
                v-if="meta.prev_page_url"
                :href="meta.prev_page_url"
                preserve-scroll
                class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-muted-foreground transition-colors hover:bg-accent"
            >
                <ChevronLeft :size="16" />
            </Link>
            <span
                v-else
                class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-muted-foreground/40"
            >
                <ChevronLeft :size="16" />
            </span>

            <template v-for="(link, index) in meta.links" :key="index">
                <Link
                    v-if="link.url && !isNav(link.label)"
                    :href="link.url"
                    preserve-scroll
                    :class="[
                        'inline-flex h-8 min-w-8 items-center justify-center rounded-md border px-2 text-sm transition-colors',
                        link.active
                            ? 'border-emerald-600 bg-emerald-600 text-white'
                            : 'hover:bg-accent',
                    ]"
                    v-html="link.label"
                />
                <span
                    v-else-if="!isNav(link.label)"
                    class="inline-flex h-8 min-w-8 items-center justify-center rounded-md px-2 text-sm text-muted-foreground/50"
                    v-html="link.label"
                />
            </template>

            <Link
                v-if="meta.next_page_url"
                :href="meta.next_page_url"
                preserve-scroll
                class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-muted-foreground transition-colors hover:bg-accent"
            >
                <ChevronRight :size="16" />
            </Link>
            <span
                v-else
                class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-muted-foreground/40"
            >
                <ChevronRight :size="16" />
            </span>
        </div>
    </div>
</template>
