<script setup>
import { computed, onMounted, ref } from 'vue';
import { useIsland, useTranslations, useViewWidth } from '@aaix/laravel-islands/vue';
import { Button, ConfirmHost, SelectMenu, ToastHost, provideConfirm, provideToasts } from '@aaix/laravel-islands/vue/helpers';
import { DataTable, SearchInput, SortButton, provideDatagrid, sendJson, useDataTable } from '@aaix/laravel-islands-datagrid/vue';
import UserFormModal from './Components/UserFormModal.vue';
import UserRow from './Components/UserRow.vue';

const island = useIsland();
const props = island.props;
const { t } = useTranslations();
const { root, rootStyle } = useViewWidth();

provideDatagrid({ t, locale: island._island?.locale });
const confirm = provideConfirm();
const toast = provideToasts();

const DEFAULTS = { q: '', status: '', sort: 'created_at', dir: 'asc', page: 1, perPage: props.perPageOptions[0] };

const { state, rows, meta, loading, error, onSearchInput, clearSearch, setFilter, setSort, goToPage, setPerPage, reload, fetchData } =
    useDataTable(props.dataUrl, { defaults: DEFAULTS, initial: props.initial, filterKeys: ['status'] });

const STATUS_OPTIONS = computed(() => [
    { value: 'active', label: t('Active') },
    { value: 'disabled', label: t('Disabled') },
]);

const COLUMNS = computed(() => [
    { field: 'name', label: t('Name') },
    { field: 'email', label: t('Email') },
    { field: null, label: t('Status') },
    { field: 'last_login_at', label: t('Last login') },
    { field: 'created_at', label: t('Joined') },
]);

const editing = ref(null);
const formOpen = ref(false);

function startCreate() {
    editing.value = null;
    formOpen.value = true;
}

function startEdit(user) {
    editing.value = user;
    formOpen.value = true;
}

function onSaved(message) {
    formOpen.value = false;
    toast.success(message);
    reload();
}

async function remove(user) {
    const accepted = await confirm({
        title: t('Delete user'),
        message: t('Delete :name permanently?', { name: user.name }),
        confirmLabel: t('Delete'),
        cancelLabel: t('Cancel'),
        tone: 'danger',
    });

    if (!accepted) {
        return;
    }

    try {
        await sendJson(props.destroyUrl.replace('__ID__', user.id), 'DELETE');
        toast.success(t('User deleted.'));
        reload();
    } catch (exception) {
        toast.danger(exception.payload?.message ? t(exception.payload.message) : t('Could not delete the user.'));
    }
}

function clearFilters() {
    state.q = '';
    state.status = '';
    reload({ resetPage: true });
}

onMounted(() => fetchData());
</script>

<template>
    <div ref="root" class="island-view users mx-auto w-full space-y-4" :style="rootStyle">
        <div class="flex items-center justify-between gap-3">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ t('Users') }}</h1>
            <Button tone="cta" @click="startCreate">{{ t('New user') }}</Button>
        </div>

        <DataTable
            :rows="rows"
            :meta="meta"
            :per-page="state.perPage"
            :per-page-options="props.perPageOptions"
            :col-count="COLUMNS.length + 1"
            :loading="loading"
            :error="error"
            :error-message="t('Could not load users')"
            floating-footer
            @retry="reload()"
            @page-change="goToPage"
            @per-page-change="setPerPage"
        >
            <template #toolbar>
                <SearchInput
                    :model-value="state.q"
                    :placeholder="t('Search by name or email')"
                    @update:model-value="onSearchInput"
                    @clear="clearSearch()"
                />
                <SelectMenu
                    variant="filter"
                    :model-value="state.status"
                    :options="STATUS_OPTIONS"
                    :placeholder="t('Status')"
                    :all-label="t('All')"
                    empty-value=""
                    @update:model-value="setFilter('status', $event)"
                />
            </template>

            <template #head>
                <th
                    v-for="column in COLUMNS"
                    :key="column.label"
                    class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400"
                >
                    <SortButton
                        v-if="column.field"
                        :field="column.field"
                        :label="column.label"
                        :sort="state.sort"
                        :dir="state.dir"
                        @sort="setSort"
                    />
                    <template v-else>{{ column.label }}</template>
                </th>
                <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    <span class="sr-only">{{ t('Actions') }}</span>
                </th>
            </template>

            <UserRow v-for="row in rows" :key="row.id" :row="row" @edit="startEdit" @delete="remove" />

            <template #empty>
                <div class="space-y-2 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    <p>{{ t('No users found') }}</p>
                    <Button v-if="state.q || state.status" tone="secondary" @click="clearFilters">
                        {{ t('Clear filters') }}
                    </Button>
                </div>
            </template>
        </DataTable>

        <UserFormModal
            :open="formOpen"
            :user="editing"
            :store-url="props.storeUrl"
            :update-url="props.updateUrl"
            @cancel="formOpen = false"
            @saved="onSaved"
        />

        <ConfirmHost />
        <ToastHost />
    </div>
</template>
