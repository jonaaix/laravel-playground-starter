<script setup>
import { ref } from 'vue';
import { useIsland, useTranslations, useViewWidth } from '@aaix/laravel-islands/vue';
import { Switch, ToastHost, provideToasts } from '@aaix/laravel-islands/vue/helpers';
import { sendJson } from '@aaix/laravel-islands-datagrid/vue';

const island = useIsland();
const props = island.props;
const { t } = useTranslations();
const { root, rootStyle } = useViewWidth();
const toast = provideToasts();

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
    <div ref="root" class="island-view workspace mx-auto w-full space-y-4" :style="rootStyle">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ t('Workspace') }}</h1>

        <div class="rounded-xl bg-white p-4 ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-white/10">
            <label class="flex items-center justify-between gap-6">
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
        </div>

        <ToastHost />
    </div>
</template>
