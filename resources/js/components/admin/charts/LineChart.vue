<script setup lang="ts" generic="T extends object">
import {
    VisArea,
    VisAxis,
    VisCrosshair,
    VisLine,
    VisTooltip,
    VisXYContainer,
} from '@unovis/vue';

/**
 * Time-series line+area chart wrapper around @unovis/vue, styled with the
 * shadcn chart CSS variables. Generic over the row type for type safety.
 */
const props = withDefaults(
    defineProps<{
        data: T[];
        /** Key holding the numeric value plotted on the Y axis. */
        yKey: keyof T;
        /** Key holding the label plotted on the X axis (e.g. a date string). */
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

const crosshairTemplate = (d: T) =>
    `<div class="text-xs">
        <div class="font-medium">${escapeHtml(props.xFormatter(String(d[props.xKey] ?? '')))}</div>
        <div>${escapeHtml(props.valueFormatter(yValue(d)))}</div>
    </div>`;
</script>

<template>
    <VisXYContainer
        :data="data"
        :height="height"
        :style="{ '--vis-color': color }"
    >
        <VisArea
            :x="(_d: T, i: number) => i"
            :y="yValue"
            :color="color"
            :opacity="0.15"
        />
        <VisLine
            :x="(_d: T, i: number) => i"
            :y="yValue"
            :color="color"
            :line-width="2"
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
        <VisCrosshair
            :x="(_d: T, i: number) => i"
            :y="yValue"
            :color="color"
            :template="crosshairTemplate"
        />
        <VisTooltip />
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
