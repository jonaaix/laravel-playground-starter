<script setup>
import { useIsland, useTranslations, useViewWidth } from '@aaix/laravel-islands/vue';
import QuickAccessCard from './Components/QuickAccessCard.vue';

const island = useIsland();
const props = island.props;
const { t } = useTranslations();
const { root, rootStyle } = useViewWidth();
</script>

<template>
    <div ref="root" class="island-view dashboard mx-auto w-full space-y-6" :style="rootStyle">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
            {{ t('Welcome back, :name', { name: props.userName }) }}
        </h1>

        <div class="grid gap-6 lg:grid-cols-3">
            <QuickAccessCard :entries="props.quickAccess" :modules-url="props.modulesUrl" />
        </div>
    </div>
</template>
