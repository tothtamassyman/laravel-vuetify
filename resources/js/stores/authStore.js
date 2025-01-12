import {defineStore} from 'pinia';
import axios from '@/plugins/axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token') || null,
        // permissions: [
        //     'view dashboard link',
        //     'view settings link',
        //     'view own-profile link',
        //     'view access-management link',
        //     'view groups link',
        //     'view users link',
        //     'view roles link',
        //     'view permissions link',
        // ],
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
    },

    actions: {
        setToken(token) {
            this.token = token;
            localStorage.setItem('token', token);
        },

        setUser(user) {
            this.user = user;
        },

        clearAuth() {
            this.setToken(null);
            this.setUser(null);
            localStorage.removeItem('token');
        },

        async login(credentials) {
            try {
                const response = await axios.post('/login', credentials);
                this.setToken(response.data.token);
                this.setUser(response.data.user);

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

        // hasPermission(permission) {
        //     return this.permissions.includes(permission);
        // },
    },
});
