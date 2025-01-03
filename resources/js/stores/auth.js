import { defineStore } from 'pinia';
import axios from '@/plugins/axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token') || null,
    }),

    actions: {
        async login(credentials) {
            try {
                const response = await axios.post('/login', credentials);
                this.user = response.data.user;
                this.token = response.data.token;

                localStorage.setItem('token', this.token);
            } catch (error) {
                console.error('Login failed:', error.message);
                throw error;
            }
        },

        async logout() {
            try {
                await axios.post('/logout');
                this.user = null;
                this.token = null;

                localStorage.removeItem('token');
            } catch (error) {
                console.error('Logout failed:', error.message);
                throw error;
            }
        },

        async fetchUser() {
            try {
                const response = await axios.get('/user');
                this.user = response.data;
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
