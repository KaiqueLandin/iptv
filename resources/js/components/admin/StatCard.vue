<script setup lang="ts">
import type { LucideIcon } from '@lucide/vue';
import { Card, CardContent } from '@/components/ui/card';

withDefaults(
    defineProps<{
        label: string;
        value: string | number;
        hint?: string;
        icon?: LucideIcon;
        tone?: 'default' | 'emerald' | 'blue' | 'amber' | 'rose' | 'violet';
    }>(),
    {
        tone: 'default',
    },
);

const toneClasses: Record<string, string> = {
    default: 'bg-muted text-foreground',
    emerald:
        'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
    blue: 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
    amber: 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
    rose: 'bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400',
    violet:
        'bg-violet-100 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400',
};
</script>

<template>
    <Card>
        <CardContent class="flex items-center justify-between gap-4">
            <div class="min-w-0 space-y-1">
                <p class="truncate text-sm font-medium text-muted-foreground">
                    {{ label }}
                </p>
                <p class="text-2xl font-bold tracking-tight">{{ value }}</p>
                <p v-if="hint" class="truncate text-xs text-muted-foreground">
                    {{ hint }}
                </p>
            </div>
            <div
                v-if="icon"
                :class="[
                    'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl',
                    toneClasses[tone],
                ]"
            >
                <component :is="icon" :size="20" />
            </div>
        </CardContent>
    </Card>
</template>
