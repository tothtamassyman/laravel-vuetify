<script setup>
import {ref} from 'vue';
import axios from '@/plugins/axios';
import {useRoute} from 'vue-router';

const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const errors = ref({});
const alertMessage = ref('');
const route = useRoute();

const submit = async () => {
  try {
    const response = await axios.post('/reset-password', {
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
      token: route.query.token,
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
          :error-messages="errors.email"
          label="Email"
      ></v-text-field>
      <v-text-field
          v-model="password"
          :error-messages="errors.password"
          label="New Password"
          type="password"
      ></v-text-field>
      <v-text-field
          v-model="passwordConfirmation"
          :error-messages="errors.password_confirmation"
          label="Confirm Password"
          type="password"
      ></v-text-field>
      <v-btn @click="submit">Reset Password</v-btn>
      <v-alert v-if="alertMessage" dismissible type="success">{{ alertMessage }}</v-alert>
    </v-form>
  </v-container>
</template>

<style scoped>

</style>