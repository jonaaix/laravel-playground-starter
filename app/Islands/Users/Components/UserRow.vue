<script setup>
import { useTranslations } from '@aaix/laravel-islands/vue';
import { Badge, Button } from '@aaix/laravel-islands/vue/helpers';
import { formatRelative } from '@shared/format.js';

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
                <Button tone="ghost" size="sm" @click="emit('edit', row)">{{ t('Edit') }}</Button>
                <Button v-if="!row.isSelf" tone="ghost" size="sm" @click="emit('delete', row)">{{ t('Delete') }}</Button>
            </div>
        </td>
    </tr>
</template>
