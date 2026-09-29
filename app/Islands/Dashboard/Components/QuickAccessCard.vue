<script setup>
import { useTranslations } from '@aaix/laravel-islands/vue';
import { Button } from '@aaix/laravel-islands/vue/helpers';
import MarkupIcon from '@shared/Icons/MarkupIcon.vue';

defineProps({
    entries: { type: Array, required: true },
    modulesUrl: { type: String, required: true },
});

const { t } = useTranslations();
</script>

<template>
    <section class="rounded-xl bg-white ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-white/10">
        <h2 class="border-b border-gray-200 px-4 py-3 text-base font-semibold text-gray-900 dark:border-white/10 dark:text-white">
            {{ t('Quick access') }}
        </h2>

        <ul v-if="entries.length > 0" class="py-1.5">
            <li v-for="entry in entries" :key="entry.url">
                <a
                    :href="entry.url"
                    class="flex h-9 items-center gap-2.5 px-4 text-sm text-gray-700 transition-colors hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white"
                >
                    <MarkupIcon :svg="entry.icon" class="h-4 w-4 text-gray-400 dark:text-gray-500" />
                    <span class="min-w-0 flex-1">{{ entry.label }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ entry.group }}</span>
                </a>
            </li>
        </ul>

        <div v-else class="space-y-3 p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('Modules you open show up here.') }}</p>
            <Button tone="secondary" :href="modulesUrl">{{ t('Browse modules') }}</Button>
        </div>
    </section>
</template>
