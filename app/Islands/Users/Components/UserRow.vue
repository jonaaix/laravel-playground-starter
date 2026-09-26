<script setup>
import { useTranslations } from '@aaix/laravel-islands/vue';
import { Badge, IconButton } from '@aaix/laravel-islands/vue/helpers';
import { formatRelative } from '@shared/format.js';
import IconPencilSquare from '@shared/Icons/IconPencilSquare.vue';
import IconTrash from '@shared/Icons/IconTrash.vue';

defineProps({
    row: { type: Object, required: true },
});

const emit = defineEmits(['edit', 'delete']);

const { t } = useTranslations();
</script>

<template>
    <tr class="text-sm text-gray-700 dark:text-gray-300">
        <td class="px-3 py-2.5">
            <div class="flex items-center gap-2 font-medium text-gray-900 dark:text-white">
                {{ row.name }}
                <Badge v-if="row.isSelf" tone="gray">{{ t('You') }}</Badge>
            </div>
        </td>
        <td class="px-3 py-2.5">
            <div class="flex items-center gap-2 whitespace-nowrap">
                {{ row.email }}
                <Badge v-if="!row.emailVerified" tone="amber">{{ t('Unverified') }}</Badge>
            </div>
        </td>
        <td class="px-3 py-2.5">
            <Badge :tone="row.isDisabled ? 'red' : 'emerald'">
                {{ row.isDisabled ? t('Disabled') : t('Active') }}
            </Badge>
        </td>
        <td class="whitespace-nowrap px-3 py-2.5 tabular-nums text-gray-500 dark:text-gray-400">
            {{ row.lastLoginAt ? formatRelative(row.lastLoginAt) : '—' }}
        </td>
        <td class="whitespace-nowrap px-3 py-2.5 tabular-nums text-gray-500 dark:text-gray-400">
            {{ formatRelative(row.createdAt) }}
        </td>
        <td class="px-3 py-2.5">
            <div class="flex items-center justify-end gap-1">
                <IconButton :label="t('Edit')" @click="emit('edit', row)">
                    <IconPencilSquare />
                </IconButton>
                <IconButton
                    v-if="!row.isSelf"
                    :label="t('Delete')"
                    tone="plain"
                    class="text-red-600 hover:bg-red-50 active:bg-red-100 dark:text-red-400 dark:hover:bg-red-500/15 dark:active:bg-red-500/25"
                    @click="emit('delete', row)"
                >
                    <IconTrash />
                </IconButton>
            </div>
        </td>
    </tr>
</template>
