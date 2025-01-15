import {defineStore} from 'pinia';
import axios from '@/plugins/axios';
import ability from '@/plugins/abilities.js';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token') || null,
        abilities: [],
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
            const testAbilities = [
                {action: 'view', subject: 'settings button'},
                {action: 'view', subject: 'dashboard link'},
                {action: 'view', subject: 'settings link'},
                {action: 'view', subject: 'own-profile link'},
                {action: 'view', subject: 'access-management link'},
                {action: 'view', subject: 'groups link'},
                {action: 'view', subject: 'users link'},
                {action: 'view', subject: 'roles link'},
                {action: 'view', subject: 'permissions link'},
            ];
            this.syncAbilities(testAbilities);
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

        syncAbilities(userAbilities) {
            this.abilities = userAbilities;
            ability.update(userAbilities);
        },

        hasAbility(action, subject) {
            return ability.can(action, subject);
        },
    },
});
