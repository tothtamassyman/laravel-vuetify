import {defineStore} from 'pinia';
import axios from '@/plugins/axios';
import ability from '@/plugins/abilities.js';

export const useAuthStore = defineStore('auth', {
    /**
     * State
     *
     * @returns {Object}
     * @property {Object} user - User data
     * @property {String|null} token - User token
     * @property {Array} abilities - User abilities
     */
    state: () => ({
        user: null,
        token: localStorage.getItem('token') || null,
        abilities: [],
    }),

    /**
     * Getters
     *
     * @returns {Object}
     * @property {Boolean} isAuthenticated - Is user authenticated
     */
    getters: {
        isAuthenticated: (state) => !!state.token,
    },

    actions: {
        /**
         * Set token
         *
         * @param token
         * @returns {void}
         */
        setToken(token) {
            this.token = token;
            localStorage.setItem('token', token);
        },

        /**
         * Set user
         *
         * @param {Object} user - User data
         * @returns {void}
         */
        setUser(user) {
            this.user = user;
        },

        /**
         * Set abilities
         *
         * @param {Array} userAbilities - User abilities
         * @returns {void}
         */
        setAbilities(userAbilities = []) {
            this.abilities = userAbilities;
            ability.update(userAbilities);
        },

        /**
         * Clear auth data
         *
         * @returns {void}
         */
        clearAuth() {
            this.setToken(null);
            this.setUser(null);
            this.setAbilities([]);
            localStorage.removeItem('token');
        },

        /**
         * Login
         *
         * @param {Object} credentials -
         * @returns {Promise}
         * @throws {Error}
         */
        async login(credentials) {
            try {
                const response = await axios.post('/login', credentials);
                const { user, token, abilities } = response.data;
                this.setToken(token);
                this.setUser(user);
                this.setAbilities(abilities);

                return response;
            } catch (error) {
                console.error('Login failed:', error.message);
                throw error;
            }
        },

        /**
         * Logout
         *
         * @returns {Promise}
         * @throws {Error}
         */
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

        /**
         * Fetch user
         *
         * @returns {Promise}
         * @throws {Error}
         */
        async fetchUser() {
            try {
                const response = await axios.get('/user');
                const user = response.data;
                this.setUser(user);

                await this.fetchAbilities();
            } catch (error) {
                console.error('Failed to fetch user:', error.response?.data);
                throw error;
            }
        },

        /**
         * Fetch abilities
         *
         * @returns {Promise}
         * @throws {Error}
         */
        async fetchAbilities() {
            try {
                const response = await axios.get('/abilities');
                const userAbilities = response.data.abilities || [];
                this.setAbilities(userAbilities);
            } catch (error) {
                console.error('Failed to fetch abilities:', error);
                throw error;
            }
        },

        /**
         * Check if user has ability
         *
         * @param {String} action -
         * @param {String} subject -
         * @returns {Boolean}
         */
        hasAbility(action, subject) {
            return ability.can(action, subject);
        },
    },
});
