<script setup>
import { computed, ref } from 'vue';
import { useIsland, useTranslations, useViewWidth } from '@aaix/laravel-islands/vue';
import { Switch, ToastHost, provideToasts } from '@aaix/laravel-islands/vue/helpers';
import { sendJson } from '@aaix/laravel-islands-datagrid/vue';
import VerticalTabs from './Components/VerticalTabs.vue';

const island = useIsland();
const props = island.props;
const { t } = useTranslations();
const { root, rootStyle } = useViewWidth();
const toast = provideToasts();

const TAB_LABELS = { general: () => t('General') };

const tabs = computed(() => props.tabs.map((key) => ({ key, label: TAB_LABELS[key]() })));
const activeTab = ref(props.initial.tab);

function setTab(key) {
    activeTab.value = key;

    const url = new URL(window.location.href);
    url.searchParams.set('tab', key);
    window.history.replaceState(window.history.state, '', url);
}

const registrationEnabled = ref(props.settings.registrationEnabled);
const saving = ref(false);

async function setRegistration(value) {
    const previous = registrationEnabled.value;
    registrationEnabled.value = value;
    saving.value = true;

    try {
        const { data } = await sendJson(props.updateUrl, 'PUT', { registrationEnabled: value });
        registrationEnabled.value = data.data.registrationEnabled;
        toast.success(value ? t('Registration enabled.') : t('Registration disabled.'));
    } catch {
        registrationEnabled.value = previous;
        toast.danger(t('Could not save the setting.'));
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <div ref="root" class="island-view app-settings mx-auto w-full space-y-4" :style="rootStyle">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ t('App Settings') }}</h1>

        <div class="flex flex-col gap-4 md:flex-row md:items-start">
            <VerticalTabs
                class="md:w-52 md:shrink-0"
                :items="tabs"
                :model-value="activeTab"
                :label="t('Settings sections')"
                @update:model-value="setTab"
            />

            <div class="min-w-0 flex-1">
                <section
                    v-if="activeTab === 'general'"
                    class="rounded-xl bg-white ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-white/10"
                >
                    <h2 class="border-b border-gray-200 px-4 py-3 text-base font-semibold text-gray-900 dark:border-white/10 dark:text-white">
                        {{ t('General') }}
                    </h2>

                    <label class="flex items-center justify-between gap-6 p-4">
                        <span class="flex max-w-prose flex-col gap-0.5">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ t('Public registration') }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ t('Anyone can create an account from the sign-in page. Turn this off once your team is set up.') }}
                            </span>
                        </span>
                        <Switch
                            :model-value="registrationEnabled"
                            :disabled="saving"
                            :aria-label="t('Public registration')"
                            @update:model-value="setRegistration"
                        />
                    </label>
                </section>
            </div>
        </div>

        <ToastHost />
    </div>
</template>
