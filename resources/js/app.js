import './bootstrap';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import vuetify from '@/plugins/vuetify';
import axios from '@/plugins/axios';
import router from "@/router/index";

const app = createApp(App);

app.config.globalProperties.$axios = axios;

app.use(vuetify)
app.use(router)
app.use(createPinia())
app.mount('#app');
