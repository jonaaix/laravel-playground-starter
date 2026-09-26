<script setup>
import { computed, ref } from 'vue';
import { useIsland, useTranslations, useViewWidth } from '@aaix/laravel-islands/vue';
import { Button } from '@aaix/laravel-islands/vue/helpers';
import { SearchInput } from '@aaix/laravel-islands-datagrid/vue';
import IconSquares2x2 from '@shared/Icons/IconSquares2x2.vue';
import ModuleGroupCard from './Components/ModuleGroupCard.vue';

const island = useIsland();
const props = island.props;
const { t } = useTranslations();
const { root, rootStyle } = useViewWidth();

const query = ref(props.initial.q);

const total = computed(() => props.groups.reduce((sum, group) => sum + group.entries.length, 0));

const visibleGroups = computed(() => {
    const needle = query.value.trim().toLowerCase();

    if (needle === '') {
        return props.groups;
    }

    return props.groups
        .map((group) => ({
            ...group,
            entries: group.label.toLowerCase().includes(needle)
                ? group.entries
                : group.entries.filter((entry) => entry.label.toLowerCase().includes(needle)),
        }))
        .filter((group) => group.entries.length > 0);
});

function setQuery(value) {
    query.value = value;

    const url = new URL(window.location.href);
    value.trim() === '' ? url.searchParams.delete('q') : url.searchParams.set('q', value);
    window.history.replaceState(window.history.state, '', url);
}
</script>

<template>
    <div ref="root" class="island-view modules mx-auto w-full space-y-4" :style="rootStyle">
        <div class="flex flex-wrap items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400">
                <IconSquares2x2 class="h-5 w-5" />
            </span>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ t('Modules') }}</h1>
            <span class="rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium tabular-nums text-gray-600 dark:bg-white/10 dark:text-gray-300">
                {{ total }}
            </span>
            <SearchInput
                class="w-full sm:ml-auto sm:w-72"
                :model-value="query"
                :placeholder="t('Search modules…')"
                @update:model-value="setQuery"
                @clear="setQuery('')"
            />
        </div>

        <div v-if="visibleGroups.length > 0" class="columns-[18rem] gap-4">
            <ModuleGroupCard v-for="group in visibleGroups" :key="group.key" :group="group" />
        </div>

        <div
            v-else
            class="space-y-3 rounded-xl bg-white p-8 text-center ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-white/10"
        >
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('No modules match your search') }}</p>
            <Button tone="secondary" @click="setQuery('')">{{ t('Clear search') }}</Button>
        </div>
    </div>
</template>
