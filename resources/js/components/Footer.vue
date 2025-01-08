<script setup>
/** @name Footer */

import {useThemeStore} from '@/stores/themeStore';

const themeStore = useThemeStore();

const appName = import.meta.env.VITE_APP_NAME;
const version = import.meta.env.VITE_APP_VERSION;

</script>

<template>
    <v-footer app
              :elevation="6"
    >
        <v-container fluid class="text-center">
            <v-row justify="center" class="text-center">
                <v-col
                        cols="12"
                        class="py-0"
                >
                    <div :style="{ color: themeStore.primaryColor }">
                        Copyright &copy; {{ new Date().getFullYear() }} &dash; <strong>{{ appName }}</strong> &dash;
                        Version: {{ version }}
                    </div>
                </v-col>
            </v-row>
        </v-container>
    </v-footer>
</template>

<style scoped>

</style>
