<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { useTranslations } from '@aaix/laravel-islands/vue';
import { FieldCaption, FormModal, Switch, TextField } from '@aaix/laravel-islands/vue/helpers';
import { sendJson } from '@aaix/laravel-islands-datagrid/vue';

const props = defineProps({
    open: { type: Boolean, default: false },
    user: { type: Object, default: null },
    storeUrl: { type: String, required: true },
    updateUrl: { type: String, required: true },
});

const emit = defineEmits(['cancel', 'saved']);

const { t } = useTranslations();

const form = reactive({ name: '', email: '', password: '', emailVerified: true, isDisabled: false });
const errors = ref({});
const busy = ref(false);

const isEditing = computed(() => props.user !== null);

watch(
    () => props.open,
    (open) => {
        if (!open) {
            return;
        }

        Object.assign(form, {
            name: props.user?.name ?? '',
            email: props.user?.email ?? '',
            password: '',
            emailVerified: props.user?.emailVerified ?? true,
            isDisabled: props.user?.isDisabled ?? false,
        });
        errors.value = {};
    },
);

async function submit() {
    busy.value = true;
    errors.value = {};

    try {
        if (isEditing.value) {
            await sendJson(props.updateUrl.replace('__ID__', props.user.id), 'PUT', form);
            emit('saved', t('User updated.'));
        } else {
            await sendJson(props.storeUrl, 'POST', form);
            emit('saved', t('User created.'));
        }
    } catch (exception) {
        errors.value = exception.payload?.errors ?? { name: [t('Could not save the user.')] };
    } finally {
        busy.value = false;
    }
}
</script>

<template>
    <FormModal
        :open="open"
        :title="isEditing ? t('Edit user') : t('New user')"
        :cancel-label="t('Cancel')"
        :submit-label="isEditing ? t('Save') : t('Create')"
        :busy="busy"
        @cancel="emit('cancel')"
        @submit="submit"
    >
        <label class="flex flex-col gap-1">
            <FieldCaption>{{ t('Name') }}</FieldCaption>
            <TextField v-model="form.name" required />
            <span v-if="errors.name" class="text-xs text-red-600 dark:text-red-400">{{ errors.name[0] }}</span>
        </label>

        <label class="flex flex-col gap-1">
            <FieldCaption>{{ t('Email') }}</FieldCaption>
            <TextField v-model="form.email" type="email" required />
            <span v-if="errors.email" class="text-xs text-red-600 dark:text-red-400">{{ errors.email[0] }}</span>
        </label>

        <label class="flex flex-col gap-1">
            <FieldCaption>{{ t('Password') }}</FieldCaption>
            <TextField
                v-model="form.password"
                type="password"
                :required="!isEditing"
                :placeholder="isEditing ? t('Leave empty to keep the current password') : ''"
            />
            <span v-if="errors.password" class="text-xs text-red-600 dark:text-red-400">{{ errors.password[0] }}</span>
        </label>

        <label class="flex items-center justify-between gap-4">
            <span class="text-sm text-gray-700 dark:text-gray-300">{{ t('Email verified') }}</span>
            <Switch v-model="form.emailVerified" />
        </label>

        <label v-if="!user?.isSelf" class="flex items-center justify-between gap-4">
            <span class="flex flex-col">
                <span class="text-sm text-gray-700 dark:text-gray-300">{{ t('Disabled') }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ t('Disabled accounts cannot sign in.') }}</span>
            </span>
            <Switch v-model="form.isDisabled" tone="danger" />
        </label>
    </FormModal>
</template>
