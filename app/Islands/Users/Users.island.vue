<script setup>
import { computed, onMounted, ref } from 'vue';
import { useIsland, useTranslations, useViewWidth } from '@aaix/laravel-islands/vue';
import { Button, ConfirmHost, ToastHost, provideConfirm, provideToasts } from '@aaix/laravel-islands/vue/helpers';
import {
    ColumnPicker,
    Combobox,
    DataTable,
    SearchInput,
    SortButton,
    SortMenu,
    ViewProfileMenu,
    provideDatagrid,
    sendJson,
    useColumnPicker,
    useDataTable,
    useViewProfiles,
} from '@aaix/laravel-islands-datagrid/vue';
import UserFormModal from './Components/UserFormModal.vue';
import UserRow from './Components/UserRow.vue';
import { userColumns } from './Support/Columns.js';

const island = useIsland();
const props = island.props;
const { t } = useTranslations();
const { root, rootStyle } = useViewWidth();

provideDatagrid({ t, locale: island._island?.locale });
const confirm = provideConfirm();
const toast = provideToasts();

const COLUMN_REGISTRY = userColumns(t);

const columns = useColumnPicker(COLUMN_REGISTRY, {
    remembered: props.preferences.columns,
    remember: (value) => sendJson(props.preferencesUrl, 'PATCH', { key: 'columns', value }),
});

const DEFAULTS = {
    q: '',
    status: '',
    sort: 'created_at',
    dir: 'asc',
    page: 1,
    perPage: props.perPageOptions[0],
    cols: columns.defaultValue,
    view: '',
};

const table = useDataTable(props.dataUrl, {
    defaults: DEFAULTS,
    initial: { ...props.initial, cols: columns.startValue },
    filterKeys: ['status'],
    clientOnly: ['cols', 'view'],
});

const { state, rows, meta, loading, error, onSearchInput, clearSearch, setFilter, setSort, goToPage, setPerPage, reload, fetchData } = table;

columns.connect(table);

const views = useViewProfiles({
    state,
    defaults: DEFAULTS,
    keys: ['q', 'status', 'sort', 'dir', 'perPage', 'cols'],
    storeUrl: props.profilesUrl,
    profileUrl: props.profileUrl,
    initial: props.profiles,
    shared: props.activeProfile,
    plain: { cols: columns.startValue },
    apply: (payload, options = {}) => reload({ resetPage: true, ...options }),
    onError: (message) => toast.danger(message || t('Could not save this view')),
});

const SORT_OPTIONS = COLUMN_REGISTRY.filter((column) => column.sort).map((column) => ({ field: column.sort, label: column.label }));

const STATUS_OPTIONS = computed(() => [
    { value: 'active', label: t('Active') },
    { value: 'disabled', label: t('Disabled') },
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

onMounted(() => {
    if (views.openDefault()) {
        return;
    }

    fetchData();
});
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
            :col-count="columns.span.value"
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
                <Combobox
                    variant="filter"
                    :model-value="state.status"
                    :options="STATUS_OPTIONS"
                    :placeholder="t('Status')"
                    :clear-option="false"
                    empty-value=""
                    @update:model-value="setFilter('status', $event)"
                />

                <ViewProfileMenu
                    class="ml-auto"
                    :profiles="views.profiles.value"
                    :active="views.active.value"
                    :changed="views.changed.value"
                    :dirty="views.dirty.value"
                    :busy="views.busy.value"
                    @apply="views.open"
                    @reset="views.reset"
                    @save="views.save"
                    @update="views.replace"
                    @rename="views.rename"
                    @remove="views.remove"
                    @set-default="views.setDefault(true)"
                    @unset-default="views.setDefault(false)"
                />
                <SortMenu :options="SORT_OPTIONS" :sort="state.sort" :dir="state.dir" :label="t('Sort by')" @sort="setSort" />
                <ColumnPicker
                    :columns="COLUMN_REGISTRY"
                    :visible="columns.keys.value"
                    :changed="columns.changed.value"
                    :label="t('Columns')"
                    :reset-label="t('Reset columns')"
                    @update="columns.set"
                    @reset="columns.reset"
                />
            </template>

            <template #head>
                <th
                    v-for="column in columns.visible.value"
                    :key="column.key"
                    class="px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400"
                    :class="column.key === 'actions' ? 'text-right' : 'text-left'"
                >
                    <span v-if="column.key === 'actions'" class="sr-only">{{ column.label }}</span>
                    <SortButton
                        v-else-if="column.sort"
                        :field="column.sort"
                        :label="column.label"
                        :sort="state.sort"
                        :dir="state.dir"
                        @sort="setSort"
                    />
                    <template v-else>{{ column.label }}</template>
                </th>
            </template>

            <UserRow v-for="row in rows" :key="row.id" :row="row" :columns="columns.keys.value" @edit="startEdit" @delete="remove" />

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
