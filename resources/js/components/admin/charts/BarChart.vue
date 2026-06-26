<script setup lang="ts" generic="T extends object">
import {
    VisAxis,
    VisStackedBar,
    VisStackedBarSelectors,
    VisTooltip,
    VisXYContainer,
} from '@unovis/vue';
import { computed } from 'vue';

/**
 * Lightweight bar chart wrapper around @unovis/vue, styled with the shadcn
 * chart CSS variables. Generic over the row type so callers keep type safety.
 */
const props = withDefaults(
    defineProps<{
        data: T[];
        /** Key holding the numeric value plotted on the Y axis. */
        yKey: keyof T;
        /** Key holding the category label plotted on the X axis. */
        xKey: keyof T;
        /** Chart color (any CSS color). Defaults to chart-1 token. */
        color?: string;
        /** Container height in pixels. */
        height?: number;
        /** Formats the tooltip/Y values. */
        valueFormatter?: (value: number) => string;
        /** Formats the X axis tick labels. */
        xFormatter?: (value: string) => string;
    }>(),
    {
        color: 'var(--color-chart-1)',
        height: 256,
        valueFormatter: (value: number) => String(value),
        xFormatter: (value: string) => value,
    },
);

const yValue = (d: T) => Number(d[props.yKey] ?? 0);

const escapeHtml = (value: string) =>
    value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');

const xTickFormat = (_tick: number, i: number) => {
    const row = props.data[i];
    return row ? props.xFormatter(String(row[props.xKey] ?? '')) : '';
};

const tooltipTemplate = (d: T) =>
    `<div class="text-xs">
        <div class="font-medium">${escapeHtml(props.xFormatter(String(d[props.xKey] ?? '')))}</div>
        <div>${escapeHtml(props.valueFormatter(yValue(d)))}</div>
    </div>`;

const triggers = computed(() => ({
    [VisStackedBarSelectors.bar]: tooltipTemplate,
}));
</script>

<template>
    <VisXYContainer
        :data="data"
        :height="height"
        :style="{ '--vis-color': color }"
    >
        <VisStackedBar
            :x="(_d: T, i: number) => i"
            :y="yValue"
            :color="color"
            :rounded-corners="4"
            :bar-padding="0.25"
        />
        <VisAxis
            type="x"
            :tick-format="xTickFormat"
            :grid-line="false"
            :tick-line="false"
            :domain-line="false"
        />
        <VisAxis
            type="y"
            :tick-format="(v: number) => valueFormatter(v)"
            :tick-line="false"
            :domain-line="false"
        />
        <VisTooltip :triggers="triggers" />
    </VisXYContainer>
</template>

<style scoped>
:deep(.unovis-xy-container) {
    --vis-axis-grid-color: var(--color-border);
    --vis-axis-tick-color: var(--color-border);
    --vis-axis-tick-label-color: var(--color-muted-foreground);
    --vis-axis-tick-label-font-size: 10px;
    --vis-tooltip-background-color: var(--color-popover);
    --vis-tooltip-border-color: var(--color-border);
    --vis-tooltip-text-color: var(--color-popover-foreground);
}
</style>
