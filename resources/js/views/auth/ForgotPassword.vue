<script setup>
import {ref} from 'vue';
import axios from '@/plugins/axios';

const email = ref('');
const errors = ref({});
const alertMessage = ref('');

const submit = async () => {
  try {
    const response = await axios.post('/forgot-password', {email: email.value});
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
      <v-btn @click="submit">Send Reset Link</v-btn>
      <v-alert v-if="alertMessage" dismissible type="success">{{ alertMessage }}</v-alert>
    </v-form>
  </v-container>
</template>

<style scoped>

</style>