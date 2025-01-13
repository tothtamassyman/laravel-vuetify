<script setup>
/** @name ResetPassword */

import {ref} from 'vue';
import axios from '@/plugins/axios';
import {useRoute} from 'vue-router';
import {useI18n} from "vue-i18n";

const route = useRoute();
const {t} = useI18n();
const email = ref(route.query.email || '');
const password = ref('');
const passwordConfirmation = ref('');
const errors = ref({});
const alertMessage = ref('');

const submit = async () => {
    try {
        const response = await axios.post('/reset-password', {
            email: email.value,
            password: password.value,
            password_confirmation: passwordConfirmation.value,
            token: route.params.token,
        });
        alertMessage.value = response.data.message;
        errors.value = {};
    } catch (error) {
        if (error.response && error.response.data.errors) {
            errors.value = error.response.data.errors;
        } else {
            alertMessage.value = 'An unexpected error occurred.';
        }
    }
};
</script>

<template>
    <v-container>
        <v-form ref="form">
            <v-text-field
                    v-model="email"
                    :label="t('default.reset-password.email')"
                    :error-messages="errors.email"
            ></v-text-field>
            <v-text-field
                    v-model="password"
                    :label="t('default.reset-password.new_password')"
                    type="password"
                    :error-messages="errors.password"
            ></v-text-field>
            <v-text-field
                    v-model="passwordConfirmation"
                    :label="t('default.reset-password.password_confirmation')"
                    type="password"
                    :error-messages="errors.password_confirmation"
            ></v-text-field>
            <v-btn @click="submit">{{ t('default.reset-password.submit_button')}}</v-btn>
            <v-alert v-if="alertMessage" type="error" dismissible>{{ alertMessage }}</v-alert>
        </v-form>
    </v-container>
</template>

<style scoped>

</style>