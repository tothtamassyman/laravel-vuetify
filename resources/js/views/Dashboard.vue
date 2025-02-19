<script setup>
/** @name Dashboard */

import { ref, onMounted } from 'vue';
import axios from "@/plugins/axios.js";
import {useI18n} from "vue-i18n";
import {useThemeStore} from '@/stores/themeStore';

const {t} = useI18n();
const themeStore = useThemeStore();
const stats = ref({});
const loading = ref(true);

const fetchStats = async () => {
    try {
        const response = await axios.get('/group-stats');
        stats.value = response.data.data;
    } catch (error) {
        console.error('Error fetching group stats:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchStats);
</script>

<template>
    <v-container>
        <h1>{{ t('dashboard.welcome_message') }}</h1>
        <v-card max-width="400">
            <v-card-title>{{ t('dashboard.group_statistics.v_card_title') }}</v-card-title>
            <v-card-text>
                <v-progress-circular v-if="loading" indeterminate color="primary" />
                <v-table v-else>
                    <thead>
                    <tr>
                        <th>{{ t('dashboard.group_statistics.table.thead.group_name') }}</th>
                        <th>{{ t('dashboard.group_statistics.table.thead.logged_in_users') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(count, groupName) in stats" :key="groupName">
                        <td>{{ groupName }}</td>
                        <td>{{ count }}</td>
                    </tr>
                    </tbody>
                </v-table>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<style scoped>
.v-container {
    color: v-bind(themeStore.secondaryColor);
}
</style>
