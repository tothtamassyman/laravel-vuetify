import './bootstrap';

import { createApp } from 'vue';
import App from './App.vue';
import vuetify from '@/plugins/vuetify';
import axios from '@/plugins/axios';

const app = createApp(App);

app.config.globalProperties.$axios = axios;

app.use(vuetify);
app.mount('#app');
