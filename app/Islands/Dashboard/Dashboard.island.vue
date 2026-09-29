<script setup>
import { useIsland, useTranslations, useViewWidth } from '@aaix/laravel-islands/vue';

const island = useIsland();
const props = island.props;
const { t } = useTranslations();
const { root, rootStyle } = useViewWidth();
</script>

<template>
    <div ref="root" class="island-view dashboard mx-auto w-full space-y-4" :style="rootStyle">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
            {{ t('Welcome back, :name', { name: props.userName }) }}
        </h1>
    </div>
</template>
