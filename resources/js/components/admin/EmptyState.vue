<script setup lang="ts">
import type { LucideIcon } from '@lucide/vue';
import { Inbox } from '@lucide/vue';

withDefaults(
    defineProps<{
        title?: string;
        description?: string;
        icon?: LucideIcon;
    }>(),
    {
        title: 'Nenhum registro encontrado',
        icon: Inbox,
    },
);
</script>

<template>
    <div
        class="flex flex-col items-center justify-center rounded-lg border border-dashed py-16 text-center"
    >
        <div
            class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-muted text-muted-foreground"
        >
            <component :is="icon" :size="22" />
        </div>
        <p class="text-sm font-medium">{{ title }}</p>
        <p v-if="description" class="mt-1 max-w-sm text-xs text-muted-foreground">
            {{ description }}
        </p>
        <div v-if="$slots.action" class="mt-4">
            <slot name="action" />
        </div>
    </div>
</template>
