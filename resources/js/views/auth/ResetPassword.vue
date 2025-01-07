<script setup>
import {ref} from 'vue';
import axios from '@/plugins/axios';
import {useRoute} from 'vue-router';

const route = useRoute();
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
                    label="Email"
                    :error-messages="errors.email"
            ></v-text-field>
            <v-text-field
                    v-model="password"
                    label="New Password"
                    type="password"
                    :error-messages="errors.password"
            ></v-text-field>
            <v-text-field
                    v-model="passwordConfirmation"
                    label="Confirm Password"
                    type="password"
                    :error-messages="errors.password_confirmation"
            ></v-text-field>
            <v-btn @click="submit">Reset Password</v-btn>
            <v-alert v-if="alertMessage" type="success" dismissible>{{ alertMessage }}</v-alert>
        </v-form>
    </v-container>
</template>

<style scoped>

</style>