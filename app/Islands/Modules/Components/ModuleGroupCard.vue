<script setup>
import { accentStyle } from '@shared/accent.js';
import MarkupIcon from '@shared/Icons/MarkupIcon.vue';

defineProps({
    group: { type: Object, required: true },
});
</script>

<template>
    <section
        class="mb-4 break-inside-avoid rounded-xl bg-white ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-white/10"
        :style="accentStyle(group.accent)"
    >
        <header class="flex items-center gap-2.5 border-b border-gray-200 px-4 py-3 dark:border-white/10">
            <span class="flex h-7 w-7 items-center justify-center rounded-md bg-(--accent)/10 text-(--accent)">
                <MarkupIcon :svg="group.icon" class="h-4 w-4" />
            </span>
            <h2 class="text-sm font-semibold text-(--accent)">{{ group.label }}</h2>
            <span class="ml-auto rounded px-1.5 py-0.5 text-[10px] font-medium tabular-nums bg-(--accent)/10 text-(--accent)">
                {{ group.entries.length }}
            </span>
        </header>

        <ul class="py-1.5">
            <li v-for="entry in group.entries" :key="entry.url">
                <a
                    :href="entry.url"
                    class="flex h-9 items-center gap-2.5 px-4 text-sm text-gray-700 transition-colors hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white"
                >
                    <MarkupIcon :svg="entry.icon" class="h-4 w-4 text-gray-400 dark:text-gray-500" />
                    {{ entry.label }}
                </a>
            </li>
        </ul>
    </section>
</template>
