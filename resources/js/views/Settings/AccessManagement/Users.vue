<script setup>
import {computed, nextTick, onMounted, onUnmounted, ref, watch} from 'vue';
import {debounce} from 'lodash';
import axios from '@/plugins/axios.js';
import {useI18n} from 'vue-i18n';
import {useAuthStore} from '@/stores/authStore';
import {useThemeStore} from '@/stores/themeStore';
import {useValidationRules} from '@/utils/validationRules';
import ConfirmationDialog from '@/components/ConfirmationDialog.vue';
import AdvancedSearch from '@/components/AdvancedSearch.vue';
import ConfirmCancelButtons from '@/components/ConfirmCancelButtons.vue';
import PasswordPolicyToolTip from '@/components/PasswordPolicyToolTip.vue';

const {t} = useI18n();
const authStore = useAuthStore();
const themeStore = useThemeStore();
const {requiredRules, lengthRules, emailRules, passwordRules} = useValidationRules();

const dialog = ref(false);
const dialogDelete = ref(false);
const loading = ref(false);
const usersForm = ref(null);
const showPassword = ref(false);
const emailPolicy = ref({});
const passwordPolicy = ref({});
const backendErrors = ref({});
const backendErrorTimer = ref(null);
const backendErrorsTimeout = 3000;
const searchMode = ref('simple');
const search = ref('');

const defaultAdvancedSearch = {
    name: '',
    email: '',
    groups: '',
    default_group: '',
    current_group: '',
    roles: '',
    permissions: '',
    condition: 'and',
};
const advancedSearch = ref({...defaultAdvancedSearch});

const headers = computed(() => [
    {title: t('settings.access-management.users.data-table.default.headers.name'), key: 'name'},
    {title: t('settings.access-management.users.data-table.default.headers.email'), key: 'email'},
    {title: t('settings.access-management.users.data-table.default.headers.groups'), key: 'groups', sortable: false},
    {title: t('settings.access-management.users.data-table.default.headers.default_group'), key: 'default_group'},
    {title: t('settings.access-management.users.data-table.default.headers.current_group'), key: 'current_group'},
    {title: t('settings.access-management.users.data-table.default.headers.roles'), key: 'roles', sortable: false},
    {
        title: t('settings.access-management.users.data-table.default.headers.permissions'),
        key: 'permissions',
        sortable: false
    },
    {title: t('settings.access-management.users.data-table.default.headers.actions'), key: 'actions', sortable: false},
]);

const items = ref([]);
const groupsList = ref([]);
const rolesList = ref([]);
const permissionsList = ref([]);
const editedIndex = ref(-1);
const defaultItem = {
    name: '',
    email: '',
    password: '',
    groups: [],
    roles: [],
    permissions: [],
    details: {
        default_group_id: null,
    },
};
const editedItem = ref({...defaultItem});

watch(() => editedItem.value.groups, (newValue) => {
    if (!newValue || newValue.length === 0) {
        editedItem.value.details.default_group_id = null;
        return;
    }

    if (newValue.length === 1) {
        editedItem.value.details.default_group_id = newValue[0];
    } else if (!newValue.includes(editedItem.value.details.default_group_id)) {
        editedItem.value.details.default_group_id = newValue[0];
    }
});

watch(dialog, (val) => {
    if (!val) {
        backendErrors.value = {};
        if (backendErrorTimer.value) clearTimeout(backendErrorTimer.value);
    }
});

const formTitle = computed(() => {
    return editedItem.value?.id
        ? t('settings.access-management.users.data-table.default.dialog.form-title-edit')
        : t('settings.access-management.users.data-table.default.dialog.form-title-new');
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
                    params[key] = key === 'default_group' || key === 'current_group' ? Number(value) : value;
                }
            });
        }

        const response = await axios.get('/users', {params});
        items.value = response.data.data || [];
        pagination.value.default.itemsLength = response.data.pagination?.total || 0;
    } catch (error) {
        console.error('Error fetching users:', error);
    } finally {
        loading.value = false;
    }
}

async function fetchGroups() {
    try {
        const response = await axios.get('/groups');
        groupsList.value = response.data.data || [];
    } catch (error) {
        console.error('Error fetching groups:', error);
    }
}

async function fetchRoles() {
    try {
        const response = await axios.get('/roles');
        rolesList.value = response.data.data || [];
    } catch (error) {
        console.error('Error fetching roles:', error);
    }
}

async function fetchPermissions() {
    try {
        const response = await axios.get('/permissions');
        permissionsList.value = response.data.data || [];
    } catch (error) {
        console.error('Error fetching permissions:', error);
    }
}

async function fetchPolicies() {
    try {
        const response = await axios.get('/policies');
        passwordPolicy.value = response.data.password || {
            min_length: 8,
            max_length: 255,
            mixed_case: true,
            numbers: true,
            symbols: true,
            letters: false,
            uncompromised: true,
            history_check: true,
            history_limit: 10,
        };
        emailPolicy.value = response.data.email || {
            min_length: 6,
            max_length: 255,
            validation_standards: {
                rfc: true,
                strict: false,
                dns: true,
                spoof: true,
                filter: true,
                filter_unicode: false,
            },
        };
    } catch (error) {
        console.error('Error fetching policies:', error);
        passwordPolicy.value = {
            min_length: 8,
            max_length: 255,
            mixed_case: true,
            numbers: true,
            symbols: true,
            letters: false,
            uncompromised: true,
            history_check: true,
            history_limit: 10,
        };
        emailPolicy.value = {
            min_length: 6,
            max_length: 255,
            validation_standards: {
                rfc: true,
                strict: false,
                dns: true,
                spoof: true,
                filter: true,
                filter_unicode: false,
            },
        };
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
        if (!dialog.value) fetchItems();
    }, 500)
);

const getDetailValue = (details, key) => {
    const detail = details?.find(d => d.key === key);
    return detail ? detail.value : null;
};

const editItem = (item) => {
    editedIndex.value = item?.id ?? -1;
    editedItem.value = {
        ...defaultItem,
        ...item,
        groups: item.groups.map(group => group.id),
        roles: item.roles.map(role => role.id),
        permissions: item.permissions.map(permission => permission.id),
        details: {
            default_group_id: getDetailValue(item.details, 'default_group_id'),
        },
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
        await axios.delete(`/users/${editedItem.value.id}`);
        items.value = items.value.filter(item => item.id !== editedItem.value.id);
        pagination.value.default.itemsLength--;

        if (items.value.length === 0 && pagination.value.default.page > 1) {
            pagination.value.default.page--;
            await fetchItems();
        }
    } catch (error) {
        console.error('Failed to delete user:', error);
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
    user_id: !editedItem.value.id ? authStore.user?.id : undefined,
    groups: editedItem.value.groups,
    roles: editedItem.value.roles,
    permissions: editedItem.value.permissions,
    details: {
        default_group_id: editedItem.value.details.default_group_id,
    },
});

const save = async () => {
    try {
        loading.value = true;
        backendErrors.value = {};

        const result = await usersForm.value.validate();
        if (!result.valid) return;

        const method = editedItem.value.id ? 'put' : 'post';
        const url = editedItem.value.id ? `/users/${editedItem.value.id}` : '/users';
        const response = await axios[method](url, formatPayload());

        if (editedItem.value.id) {
            const index = items.value.findIndex(i => i.id === editedItem.value.id);
            if (index > -1) items.value[index] = response.data.user;
        } else {
            items.value.push(response.data.user);
            pagination.value.default.itemsLength++;
        }
        close();
    } catch (error) {
        console.error('Failed to save user:', error.message);
        Object.assign(backendErrors.value, error.backendErrors || {});

        if (backendErrorTimer.value) clearTimeout(backendErrorTimer.value);
        backendErrorTimer.value = setTimeout(() => {
            backendErrors.value = {};
            usersForm?.value?.resetValidation();
        }, backendErrorsTimeout);
    } finally {
        loading.value = false;
    }
};

const skeletonRows = computed(() => {
    return `table-row@${pagination.value.default.itemsPerPage > 0 ? pagination.value.default.itemsPerPage : 10}`;
});

onMounted(async () => {
    await fetchGroups();
    await fetchRoles();
    await fetchPermissions();
    await fetchPolicies();
    await fetchItems();
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
            :items-per-page-text="t('settings.access-management.users.data-table.default.perPageText')"
    >
        <template v-slot:top>
            <v-toolbar flat>
                <v-toolbar-title class="text-primary">
                    {{ t('settings.access-management.users.toolbar-title') }}
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
                        :label="t('settings.access-management.users.searchTextFieldLabel')"
                        density="compact"
                        hide-details
                        outlined
                />

                <v-spacer/>

                <!-- Switch between simple and advanced search -->
                <v-btn
                        @click="toggleSearchMode"
                        :text="searchMode === 'simple' ? t('settings.access-management.users.advancedSearchBtnText') : t('settings.access-management.users.simpleSearchBtnText')"
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

                <v-dialog v-model="dialog" max-width="1000px" persistent>
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
                            id="usersForm"
                            ref="usersForm"
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
                                                    :rules="lengthRules(3, 255, t('settings.access-management.users.data-table.default.dialog.nameTextFieldLabel'))"
                                                    :error-messages="backendErrors.name || []"
                                                    :label="t('settings.access-management.users.data-table.default.dialog.nameTextFieldLabel')"
                                                    outlined
                                                    density="compact"
                                                    clearable
                                                    color="primary"
                                                    base-color="primary"
                                            />
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="5">
                                            <v-text-field
                                                    v-model="editedItem.email"
                                                    v-on:keyup.enter="save"
                                                    :rules="emailRules(emailPolicy, t('settings.access-management.users.data-table.default.dialog.emailTextFieldLabel'))"
                                                    :error-messages="backendErrors.email || []"
                                                    :label="t('settings.access-management.users.data-table.default.dialog.emailTextFieldLabel')"
                                                    outlined
                                                    density="compact"
                                                    clearable
                                                    color="primary"
                                                    base-color="primary"
                                            />
                                        </v-col>
                                        <v-col cols="7">
                                            <v-text-field
                                                    v-model="editedItem.password"
                                                    v-on:keyup.enter="save"
                                                    :type="showPassword ? 'text' : 'password'"
                                                    :rules="passwordRules(passwordPolicy, t('settings.access-management.users.data-table.default.dialog.passwordTextFieldLabel'), editedIndex === -1)"
                                                    :error-messages="backendErrors.password || []"
                                                    :label="t('settings.access-management.users.data-table.default.dialog.passwordTextFieldLabel')"
                                                    outlined
                                                    density="compact"
                                                    clearable
                                                    color="primary"
                                                    base-color="primary"
                                            >
                                                <template v-slot:prepend>
                                                    <PasswordPolicyToolTip
                                                            :title="t('passwordPolicyToolTip.title')"
                                                            :policy="passwordPolicy"
                                                    />
                                                </template>
                                                <template v-slot:append>
                                                    <v-icon
                                                            @click="showPassword = !showPassword"
                                                            :color="showPassword ? 'error' : 'primary'"
                                                    >{{ showPassword ? 'mdi-eye' : 'mdi-eye-off' }}
                                                    </v-icon>
                                                </template>
                                            </v-text-field>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="12">
                                            <v-autocomplete
                                                    v-model="editedItem.groups"
                                                    v-on:keyup.enter="save"
                                                    :items="groupsList"
                                                    item-title="name"
                                                    item-value="id"
                                                    :rules="requiredRules(t('settings.access-management.users.data-table.default.dialog.groupsAutocompleteLabel'))"
                                                    :error-messages="backendErrors.groups || []"
                                                    :label="t('settings.access-management.users.data-table.default.dialog.groupsAutocompleteLabel')"
                                                    :no-data-text="t('settings.access-management.users.data-table.default.noDataMessage')"
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
                                    <v-row>
                                        <v-col cols="12">
                                            <v-autocomplete
                                                    v-if="editedItem.groups.length > 1 && groupsList && groupsList.length"
                                                    v-model="editedItem.details.default_group_id"
                                                    v-on:keyup.enter="save"
                                                    :items="groupsList.filter(group => editedItem.groups.includes(group.id))"
                                                    item-title="name"
                                                    item-value="id"
                                                    :rules="requiredRules(t('settings.access-management.users.data-table.default.dialog.defaultGroupAutocompleteLabel'))"
                                                    :error-messages="backendErrors['details.default_group_id'] || []"
                                                    :label="t('settings.access-management.users.data-table.default.dialog.defaultGroupAutocompleteLabel')"
                                                    :no-data-text="t('settings.access-management.users.data-table.default.noDataMessage')"
                                                    density="comfortable"
                                                    clearable
                                                    color="primary"
                                                    base-color="primary"
                                            />
                                            <v-text-field
                                                    v-else
                                                    v-show="false"
                                                    v-model="editedItem.details.default_group_id"
                                            />
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="12">
                                            <v-autocomplete
                                                    v-model="editedItem.roles"
                                                    v-on:keyup.enter="save"
                                                    :items="rolesList"
                                                    item-title="name"
                                                    item-value="id"
                                                    :rules="requiredRules(t('settings.access-management.users.data-table.default.dialog.rolesAutocompleteLabel'))"
                                                    :error-messages="backendErrors.roles || []"
                                                    :label="t('settings.access-management.users.data-table.default.dialog.rolesAutocompleteLabel')"
                                                    :no-data-text="t('settings.access-management.users.data-table.default.noDataMessage')"
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
                                    <v-row>
                                        <v-col cols="12">
                                            <v-autocomplete
                                                    v-model="editedItem.permissions"
                                                    v-on:keyup.enter="save"
                                                    :items="permissionsList"
                                                    item-title="name"
                                                    item-value="id"
                                                    :rules="requiredRules(t('settings.access-management.users.data-table.default.dialog.permissionsAutocompleteLabel'))"
                                                    :error-messages="backendErrors.permissions || []"
                                                    :label="t('settings.access-management.users.data-table.default.dialog.permissionsAutocompleteLabel')"
                                                    :no-data-text="t('settings.access-management.users.data-table.default.noDataMessage')"
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
                                        :confirmButton="{ text: t('buttons.save') }"
                                        :cancelButton="{ text: t('buttons.cancel') }"
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
                        :confirm-button="{ text: t('buttons.delete') }"
                        :cancel-button="{ text: t('buttons.cancel') }"
                />
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
                                :label="t('settings.access-management.users.advancedSearch.nameTextFieldLabel')"
                                density="compact"
                                clearable
                                color="primary"
                                base-color="primary"
                        />
                    </v-col>
                    <v-col cols="6">
                        <v-text-field
                                v-model="advancedSearch.email"
                                v-on:keyup.enter="fetchItems"
                                :label="t('settings.access-management.users.advancedSearch.emailTextFieldLabel')"
                                density="compact"
                                clearable
                                color="primary"
                                base-color="primary"
                        />
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="4">
                        <v-text-field
                                v-model="advancedSearch.groups"
                                v-on:keyup.enter="fetchItems"
                                :label="t('settings.access-management.users.advancedSearch.groupsTextFieldLabel')"
                                density="compact"
                                clearable
                                color="primary"
                                base-color="primary"
                        />
                    </v-col>
                    <v-col cols="4">
                        <v-text-field
                                v-model="advancedSearch.default_group"
                                v-on:keyup.enter="fetchItems"
                                :label="t('settings.access-management.users.advancedSearch.defaultGroupTextFieldLabel')"
                                density="compact"
                                clearable
                                color="primary"
                                base-color="primary"
                        />
                    </v-col>
                    <v-col cols="4">
                        <v-text-field
                                v-model="advancedSearch.current_group"
                                v-on:keyup.enter="fetchItems"
                                :label="t('settings.access-management.users.advancedSearch.currentGroupTextFieldLabel')"
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
                                v-model="advancedSearch.roles"
                                v-on:keyup.enter="fetchItems"
                                :label="t('settings.access-management.users.advancedSearch.rolesTextFieldLabel')"
                                density="compact"
                                clearable
                                color="primary"
                                base-color="primary"
                        />
                    </v-col>
                    <v-col cols="6">
                        <v-text-field
                                v-model="advancedSearch.permissions"
                                v-on:keyup.enter="fetchItems"
                                :label="t('settings.access-management.users.advancedSearch.permissionsTextFieldLabel')"
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

        <template v-slot:item.email="{ item }">
            <template v-if="item.email && item.email.length > 25">
                <v-tooltip location="top">
                    <template v-slot:activator="{ props }">
                        <span v-bind="props">
                            {{ item.email.substring(0, 20) }}...
                        </span>
                    </template>
                    <span>{{ item.email }}</span>
                </v-tooltip>
            </template>
            <template v-else>
                {{ item.email }}
            </template>
        </template>

        <template v-slot:item.groups="{ item }">
            <template v-if="Array.isArray(item.groups) && item.groups.length > 0">
                <v-chip
                        v-for="(group, index) in item.groups.slice(0, 2)"
                        :key="index"
                        color="primary"
                        class="mr-2"
                        variant="elevated"
                >
                    {{ group.name }}
                </v-chip>

                <v-tooltip v-if="item.groups.length > 2">
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
                    <p v-for="(group, index) in item.groups.slice(2)" :key="index">
                        {{ group.name }}
                    </p>
                </v-tooltip>
            </template>
        </template>

        <template v-slot:item.default_group="{ item }">
            {{ item.default_group?.name }}
        </template>

        <template v-slot:item.current_group="{ item }">
            {{ item.current_group?.name }}
        </template>

        <template v-slot:item.roles="{ item }">
            <template v-if="Array.isArray(item.roles) && item.roles.length > 0">
                <v-chip
                        v-for="(role, index) in item.roles.slice(0, 2)"
                        :key="index"
                        color="primary"
                        class="mr-2"
                        variant="elevated"
                >
                    {{ role.name }}
                </v-chip>

                <v-tooltip v-if="item.roles.length > 2">
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
                    <p v-for="(role, index) in item.roles.slice(2)" :key="index">
                        {{ role.name }}
                    </p>
                </v-tooltip>
            </template>
        </template>

        <template v-slot:item.permissions="{ item }">
            <template v-if="Array.isArray(item.permissions) && item.permissions.length > 0">
                <v-chip
                        v-for="(permission, index) in item.permissions.slice(0, 2)"
                        :key="index"
                        color="primary"
                        class="mr-2"
                        variant="elevated"
                >
                    {{ permission.name }}
                </v-chip>

                <v-tooltip v-if="item.permissions.length > 2">
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
                    <p v-for="(permission, index) in item.permissions.slice(2)" :key="index">
                        {{ permission.name }}
                    </p>
                </v-tooltip>
            </template>
        </template>

        <template v-slot:loading>
            <v-skeleton-loader :type="skeletonRows"/>
        </template>

        <template v-slot:no-data>
            <div class="text-center">
                <p>{{ t('settings.access-management.users.data-table.default.noDataMessage') }}</p>
                <v-btn
                        @click="searchMode === 'simple' ? resetSearch() : resetAdvancedSearch()"
                        append-icon="mdi-refresh"
                        color="primary"
                >
                    {{ t('settings.access-management.users.data-table.default.reset') }}
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
