<script setup>
import { onBeforeUnmount, onMounted, ref, toRaw, watch } from 'vue';
import { Chart } from 'chart.js';
import { CHART_HEIGHT, buildChartConfig } from '@shared/chart.js';

const props = defineProps({
    type: { type: String, required: true },
    data: { type: Object, required: true },
    options: { type: Object, default: () => ({}) },
});

const canvas = ref(null);
let chart = null;
let themeObserver = null;

function render() {
    chart?.destroy();
    chart = new Chart(canvas.value, buildChartConfig(canvas.value, {
        type: props.type,
        data: toRaw(props.data),
        options: toRaw(props.options),
    }));
}

onMounted(() => {
    render();
    themeObserver = new MutationObserver(render);
    themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});

watch(() => [props.type, props.data, props.options], render, { deep: true });

onBeforeUnmount(() => {
    themeObserver?.disconnect();
    chart?.destroy();
});
</script>

<template>
    <div class="relative" :style="{ height: `${CHART_HEIGHT}px` }">
        <canvas ref="canvas" />
    </div>
</template>
