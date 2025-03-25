<script setup>
import {computed, nextTick, onMounted, onUnmounted, ref, watch} from 'vue';
import {debounce} from 'lodash';
import axios from '@/plugins/axios.js';
import {useI18n} from 'vue-i18n';
import {useToast} from 'vue-toastification';
import {useThemeStore} from '@/stores/themeStore';
import {useValidationRules} from '@/utils/validationRules';
import ConfirmationDialog from '@/components/ConfirmationDialog.vue';
import AdvancedSearch from '@/components/AdvancedSearch.vue';
import ConfirmCancelButtons from '@/components/ConfirmCancelButtons.vue';

const {t} = useI18n();
const toast = useToast();
const themeStore = useThemeStore();
const {requiredRules, lengthRules} = useValidationRules();

const dialog = ref(false);
const dialogDelete = ref(false);
const loading = ref(false);
const groupsForm = ref(null);
const backendErrors = ref({});
const backendErrorTimer = ref(null);
const backendErrorsTimeout = 3000;
const maxLength = 255;
const searchMode = ref('simple');
const search = ref('');

const defaultAdvancedSearch = {
    name: '',
    description: '',
    users: '',
    condition: 'and',
};
const advancedSearch = ref({...defaultAdvancedSearch});

const headers = computed(() => [
    {title: t('settings.access-management.groups.data-table.default.headers.name'), key: 'name'},
    {title: t('settings.access-management.groups.data-table.default.headers.description'), key: 'description'},
    {title: t('settings.access-management.groups.data-table.default.headers.users'), key: 'users', sortable: false},
    {title: t('settings.access-management.groups.data-table.default.headers.actions'), key: 'actions', sortable: false},
]);

const items = ref([]);
const usersList = ref([]);
const editedIndex = ref(-1);
const defaultItem = {
    name: '',
    description: '',
    users: [],
};
const editedItem = ref({...defaultItem});

watch(dialog, (val) => {
    if (!val) {
        backendErrors.value = {};
        if (backendErrorTimer.value) clearTimeout(backendErrorTimer.value);
    }
});

const formTitle = computed(() => {
    return editedItem.value?.id
        ? t('settings.access-management.groups.data-table.default.dialog.form-title-edit')
        : t('settings.access-management.groups.data-table.default.dialog.form-title-new');
});

const createPagination = (items = null) => {
    return {
        itemsLength: items ? computed(() => (items?.length ?? 0)) : 0,
        itemsPerPage: 5,
        itemsPerPageOptions: computed(() => {
            const count = items ? (items?.length ?? 0) : pagination.value.default.itemsLength;
            const baseOptions = [5, 10, 25, 50, 100];
            const filteredOptions = baseOptions
                .filter(opt => opt < count)
                .map(opt => ({
                    value: opt,
                    title: opt.toString(),
                }));
            filteredOptions.push({
                value: -1,
                title: t('words.all'),
            });
            return filteredOptions;
        }),
        page: 1,
        sortBy: [],
    };
};

const pagination = ref({
    default: createPagination(),
});

const toggleSearchMode = () => {
    if (searchMode.value === 'simple') {
        resetSearch();
    } else {
        resetAdvancedSearch();
    }
    searchMode.value = searchMode.value === 'simple' ? 'advanced' : 'simple';
};

const resetSearch = () => {
    search.value = '';
};

const resetAdvancedSearch = () => {
    advancedSearch.value = {...defaultAdvancedSearch};
    fetchItems();
};

async function fetchItems() {
    try {
        loading.value = true;
        const params = {
            page: pagination.value.default.page,
            per_page: pagination.value.default.itemsPerPage,
        };

        if (pagination.value.default.sortBy.length > 0) {
            params.sort_by = pagination.value.default.sortBy[0]?.key;
            params.sort_order = pagination.value.default.sortBy[0]?.order;
        }

        if (searchMode.value === 'simple' && search.value.trim()) {
            params.search = search.value;
        } else {
            Object.entries(advancedSearch.value).forEach(([key, value]) => {
                if (value?.trim && value.trim() && !(key === 'condition' && value === defaultAdvancedSearch.condition)) {
                    params[key] = value;
                }
            });
        }

        const response = await axios.get('/groups', {params});
        items.value = response.data.data || [];
        pagination.value.default.itemsLength = response.data.pagination?.total || 0;
    } catch (error) {
        console.error('Error fetching groups:', error);
    } finally {
        loading.value = false;
    }
}

async function fetchUsers() {
    try {
        const response = await axios.get('/users');
        usersList.value = response.data.data || [];
    } catch (error) {
        console.error('Error fetching users:', error);
    }
}

watch(
    [
        () => search.value,
        () => pagination.value.default.page,
        () => pagination.value.default.itemsPerPage,
        () => pagination.value.default.sortBy,
    ],
    debounce(() => {
        if (dialog.value) return;
        fetchItems();
    }, 500),
    {deep: true}
);

const editItem = (item) => {
    editedIndex.value = item?.id ?? -1;
    editedItem.value = {
        ...defaultItem,
        ...item,
        users: item.users.map(user => user.id),
    };
    dialog.value = true;
};

const deleteItem = (item) => {
    editedIndex.value = item?.id ?? -1;
    editedItem.value = {
        ...defaultItem,
        ...item,
    };
    dialogDelete.value = true;
};

const deleteItemConfirm = async () => {
    try {
        await axios.delete(`/groups/${editedItem.value.id}`);
        items.value = items.value.filter(item => item.id !== editedItem.value.id);
        pagination.value.default.itemsLength--;

        if (items.value.length === 0 && pagination.value.default.page > 1) {
            pagination.value.default.page--;
            await fetchItems();
        }
    } catch (error) {
        console.error('Failed to delete group:', error);
    } finally {
        closeDelete();
    }
};

const close = () => {
    dialog.value = false;
    nextTick(() => {
        editedItem.value = {...defaultItem};
        editedIndex.value = -1;
    });
};

const closeDelete = () => {
    dialogDelete.value = false;
    nextTick(() => {
        editedItem.value = {...defaultItem};
        editedIndex.value = -1;
    });
};

const formatPayload = () => ({
    ...editedItem.value,
    users: editedItem.value.users.map(user => ({id: user})),
});

const save = async () => {
    try {
        loading.value = true;
        backendErrors.value = {};

        const result = await groupsForm.value.validate();
        if (!result.valid) return;

        const method = editedItem.value.id ? 'put' : 'post';
        const url = editedItem.value.id ? `/groups/${editedItem.value.id}` : '/groups';
        const response = await axios[method](url, formatPayload());

        if (editedItem.value.id) {
            const index = items.value.findIndex(i => i.id === editedItem.value.id);
            if (index > -1) items.value[index] = response.data.group;
        } else {
            items.value.push(response.data.group);
            pagination.value.default.itemsLength++;
        }
        close();
    } catch (error) {
        console.error('Failed to save group:', error.message);
        Object.assign(backendErrors.value, error.backendErrors || {});

        if (backendErrorTimer.value) clearTimeout(backendErrorTimer.value);
        backendErrorTimer.value = setTimeout(() => {
            backendErrors.value = {};
            groupsForm?.value?.resetValidation();
        }, backendErrorsTimeout);
    } finally {
        loading.value = false;
    }
};

const skeletonRows = computed(() => {
    return `table-row@${pagination.value.default.itemsPerPage > 0 ? pagination.value.default.itemsPerPage : 10}`;
});

onMounted(() => {
    fetchItems();
    fetchUsers();
});

onUnmounted(() => {
    if (backendErrorTimer.value) clearTimeout(backendErrorTimer.value);
    backendErrors.value = {};
});
</script>

<template>
    <v-data-table-server
            :headers="headers"
            :items="items"
            :items-length="pagination.default.itemsLength"
            :items-per-page="pagination.default.itemsPerPage"
            :page="pagination.default.page"
            :sort-by="pagination.default.sortBy"
            @update:page="pagination.default.page = $event"
            @update:items-per-page="pagination.default.itemsPerPage = $event"
            @update:sort-by="pagination.default.sortBy = $event"
            :loading="loading"
            :page-text="'{0} - {1} / {2}'"
            :items-per-page-options="pagination.default.itemsPerPageOptions"
            :items-per-page-text="t('settings.access-management.permissions.data-table.default.perPageText')"
    >
        <template v-slot:top>
            <v-toolbar flat>
                <v-toolbar-title class="text-primary">
                    {{ t('settings.access-management.groups.toolbar-title') }}
                </v-toolbar-title>
                <v-spacer/>

                <!-- Simple Search -->
                <v-text-field
                        v-if="searchMode === 'simple'"
                        v-model="search"
                        @keyup.enter="fetchItems"
                        :append-inner-icon="search.length ? 'mdi-magnify' : ''"
                        @click:append-inner="fetchItems"
                        @click:clear="resetSearch"
                        clear-icon="mdi-close-circle"
                        clearable
                        color="primary"
                        base-color="primary"
                        :label="t('settings.access-management.groups.searchTextFieldLabel')"
                        density="compact"
                        hide-details
                        outlined
                />

                <v-spacer/>

                <!-- Switch between simple and advanced search -->
                <v-btn
                        @click="toggleSearchMode"
                        :text="searchMode === 'simple' ? t('settings.access-management.groups.advancedSearchBtnText') : t('settings.access-management.groups.simpleSearchBtnText')"
                        :append-icon="searchMode === 'simple' ? 'mdi-chevron-down' : 'mdi-chevron-up'"
                        color="primary"
                />

                <!-- Reload Button -->
                <v-btn
                        @click="fetchItems"
                        :text="t('buttons.reload')"
                        append-icon="mdi-refresh"
                        color="primary"
                />

                <v-dialog v-model="dialog" max-width="950px" persistent>
                    <template v-slot:activator="{ props }">
                        <v-btn
                                v-bind="props"
                                append-icon="mdi-plus"
                                color="primary"
                        >
                            {{ t('buttons.add') }}
                        </v-btn>
                    </template>

                    <v-form
                            id="groupsForm"
                            ref="groupsForm"
                            validate-on="blur lazy"
                            @submit.prevent="save"
                    >
                        <v-card>
                            <v-card-title>
                                <span class="text-h5">{{ formTitle }}</span>
                            </v-card-title>

                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="12">
                                            <v-text-field
                                                    v-model="editedItem.name"
                                                    v-on:keyup.enter="save"
                                                    :rules="lengthRules(3, 255, t('settings.access-management.groups.data-table.default.dialog.nameTextFieldLabel'))"
                                                    :error-messages="backendErrors.name || []"
                                                    :label="t('settings.access-management.groups.data-table.default.dialog.nameTextFieldLabel')"
                                                    outlined
                                                    density="compact"
                                                    clearable
                                                    color="primary"
                                                    base-color="primary"
                                            />
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="12">
                                            <v-textarea
                                                    v-model="editedItem.description"
                                                    :rules="lengthRules(3, 255, t('settings.access-management.groups.data-table.default.dialog.descriptionTextareaLabel'), false)"
                                                    :error-messages="backendErrors.description || []"
                                                    :label="t('settings.access-management.groups.data-table.default.dialog.descriptionTextareaLabel')"
                                                    no-resize
                                                    :maxlength="maxLength"
                                                    counter
                                                    density="comfortable"
                                                    clearable
                                            >
                                                <template v-slot:counter>
                                                    <v-icon class="mx-2">mdi-text</v-icon>
                                                    {{ maxLength }} / {{
                                                        (editedItem?.description?.length > 0) ? editedItem?.description?.length : 0
                                                    }}
                                                </template>
                                            </v-textarea>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="12">
                                            <v-autocomplete
                                                    v-model="editedItem.users"
                                                    v-on:keyup.enter="save"
                                                    :items="usersList"
                                                    item-title="name"
                                                    item-value="id"
                                                    :error-messages="backendErrors.users || []"
                                                    :label="t('settings.access-management.groups.data-table.default.dialog.usersAutocompleteLabel')"
                                                    :no-data-text="t('settings.access-management.groups.data-table.default.noDataMessage')"
                                                    chips
                                                    closable-chips
                                                    multiple
                                                    auto-select-first
                                                    density="comfortable"
                                                    clearable
                                                    color="primary"
                                                    base-color="primary"
                                            >
                                                <template v-slot:chip="{ props, item }">
                                                    <v-chip
                                                            v-bind="props"
                                                            :text="item?.raw?.name"
                                                            variant="elevated"
                                                            color="primary"
                                                            class="mb-2"
                                                    ></v-chip>
                                                </template>
                                            </v-autocomplete>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>

                            <v-card-actions>
                                <v-spacer/>
                                <ConfirmCancelButtons
                                        @confirm="save"
                                        @cancel="close"
                                        :confirmButton="{text: t('buttons.save')}"
                                        :cancelButton="{text: t('buttons.cancel')}"
                                />
                            </v-card-actions>
                        </v-card>
                    </v-form>
                </v-dialog>

                <confirmation-dialog
                        v-model="dialogDelete"
                        max-width="500px"
                        @confirm="deleteItemConfirm"
                        @cancel="closeDelete"
                        :title="t('dialogDelete.title')"
                        :text="t('dialogDelete.text')"
                        :subtitle="editedItem.name"
                        :confirm-button="{text: t('buttons.delete')}"
                        :cancel-button="{text: t('buttons.cancel')}"
                ></confirmation-dialog>
            </v-toolbar>

            <!-- Advanced Search Fields -->
            <AdvancedSearch
                    v-if="searchMode === 'advanced'"
                    v-model="advancedSearch"
                    @apply-filters="fetchItems"
                    @reset-filters="resetAdvancedSearch"
            >
                <v-row>
                    <v-col cols="6">
                        <v-text-field
                                v-model="advancedSearch.name"
                                v-on:keyup.enter="fetchItems"
                                :label="t('settings.access-management.groups.advancedSearch.nameTextFieldLabel')"
                                density="compact"
                                clearable
                                color="primary"
                                base-color="primary"
                        />
                    </v-col>
                    <v-col cols="6">
                        <v-text-field
                                v-model="advancedSearch.description"
                                v-on:keyup.enter="fetchItems"
                                :label="t('settings.access-management.groups.advancedSearch.descriptionTextFieldLabel')"
                                density="compact"
                                clearable
                                color="primary"
                                base-color="primary"
                        />
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="6">
                        <v-text-field
                                v-model="advancedSearch.users"
                                v-on:keyup.enter="fetchItems"
                                :label="t('settings.access-management.groups.advancedSearch.usersTextFieldLabel')"
                                density="compact"
                                clearable
                                color="primary"
                                base-color="primary"
                        />
                    </v-col>
                </v-row>
            </AdvancedSearch>
        </template>

        <template v-slot:item.actions="{ item }">
            <v-tooltip :text="t('words.edit')">
                <template v-slot:activator="{ props }">
                    <v-icon v-bind="props" class="ms-1" color="primary" @click="editItem(item)">mdi-pencil</v-icon>
                </template>
            </v-tooltip>
            <v-tooltip :text="t('words.delete')">
                <template v-slot:activator="{ props }">
                    <v-icon v-bind="props" class="ms-1" color="error" @click="deleteItem(item)">mdi-delete</v-icon>
                </template>
            </v-tooltip>
        </template>

        <template v-slot:item.description="{ item }">
            <template v-if="item.description && item.description.length > 50">
                <v-tooltip location="top">
                    <template v-slot:activator="{ props }">
                <span v-bind="props">
                    {{ item.description.substring(0, 20) }}...
                </span>
                    </template>
                    <span>{{ item.description }}</span>
                </v-tooltip>
            </template>
            <template v-else>
                {{ item.description }}
            </template>
        </template>

        <template #item.users="{ item }">
            <template v-if="Array.isArray(item.users) && item.users.length > 0">
                <v-chip
                        v-for="(user, index) in item.users.slice(0, 2)"
                        :key="index"
                        color="primary"
                        class="mr-2"
                        variant="elevated"
                >
                    {{ user.name }}
                </v-chip>

                <v-tooltip v-if="item.users.length > 2">
                    <template #activator="{ props }">
                        <v-chip
                                v-bind="props"
                                color="primary"
                                class="mr-2"
                                variant="elevated"
                        >
                            ...
                        </v-chip>
                    </template>
                    <p v-for="(user, index) in item.users.slice(2)" :key="index">
                        {{ user.name }}
                    </p>
                </v-tooltip>
            </template>
        </template>

        <template v-slot:loading>
            <v-skeleton-loader
                    :type="skeletonRows"
            />
        </template>

        <template v-slot:no-data>
            <div class="text-center">
                <p>{{ t('settings.access-management.groups.data-table.default.noDataMessage') }}</p>
                <v-btn
                        @click="searchMode === 'simple' ? resetSearch() : resetAdvancedSearch()"
                        append-icon="mdi-refresh"
                        color="primary"
                >
                    {{ t('settings.access-management.groups.data-table.default.reset') }}
                </v-btn>
            </div>
        </template>
    </v-data-table-server>
</template>

<style scoped>
.v-container {
    color: var(--v-primary-base);
}
</style>
