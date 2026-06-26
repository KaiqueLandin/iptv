<script setup lang="ts">
import { Card } from '@/components/ui/card';

defineProps<{
    columns: string[];
}>();
</script>

<template>
    <Card class="overflow-hidden py-0">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b bg-muted/40 text-left">
                        <th
                            v-for="column in columns"
                            :key="column"
                            class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground"
                        >
                            {{ column }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <slot />
                </tbody>
            </table>
        </div>
    </Card>
</template>
