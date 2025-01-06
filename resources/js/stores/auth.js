import {defineStore} from 'pinia';
import axios from '@/plugins/axios';
import router from '@/router';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token') || null,
    }),

    actions: {
        setToken(token) {
            this.token = token;
            localStorage.setItem('token', token);
            // axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        },

        setUser(user) {
            this.user = user;
        },

        clearAuth() {
            this.setToken(null);
            this.setUser(null);
            localStorage.removeItem('token');
            // delete axios.defaults.headers.common['Authorization'];
        },

        async login(credentials) {
            try {
                const response = await axios.post('/login', credentials);
                this.setToken(response.data.token);
                this.setUser(response.data.user);

                await router.push({name: 'Dashboard'});

                return response;
            } catch (error) {
                console.error('Login failed:', error.message);
                throw error;
            }
        },

        async logout() {
            try {
                const response = await axios.post('/logout');
                this.clearAuth();

                await router.push({name: 'Welcome'});

                return response;
            } catch (error) {
                console.error('Logout failed:', error.message);
                throw error;
            }
        },

        async fetchUser() {
            try {
                const response = await axios.get('/user');
                this.setUser(response.data);
            } catch (error) {
                console.error('Failed to fetch user:', error.response?.data);
                throw error;
            }
        },

        async isAuthenticated() {
            if (!this.token) {
                return false;
            }

            try {
                await this.fetchUser();
                return true;
            } catch (error) {
                console.error('Authentication check failed:', error.message);
                return false;
            }
        },
    },
});
