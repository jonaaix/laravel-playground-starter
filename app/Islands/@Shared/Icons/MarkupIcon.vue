<script setup>
defineProps({
    svg: { type: String, required: true },
});
</script>

<!-- Renders a server-generated Heroicon, so pages keep one icon declaration for Filament and islands alike. -->
<template>
    <span class="inline-flex shrink-0 items-center justify-center [&>svg]:h-full [&>svg]:w-full" aria-hidden="true" v-html="svg" />
</template>
