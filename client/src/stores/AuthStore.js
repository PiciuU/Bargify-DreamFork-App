import { defineStore } from 'pinia';
import { router } from '@/router/index';

import ApiService from '@/services/api.service';
import { setCookie, getCookie, deleteCookie } from '@/services/storage.service';

export const useAuthStore = defineStore('authStore', {
    state: () => ({
        token: getCookie('token') || null,
        user: {},
        loading: false,
    }),
    getters: {
        isLoading: (state) => state.loading,
        isLogged: (state) => !!state.token,
        isAuthenticated: (state) => !!state.token && Object.keys(state.user).length != 0 && state.user.constructor === Object,
    },
    actions: {
        async register(payload) {
            try {
                this.loading = true;
                const response = await ApiService.post('/auth/register', payload);
                this.setAuthorization(response.data.token, response.data.user);
                return Promise.resolve();
            }
            catch (error) {
                return Promise.reject(error.data);
            }
            finally {
                this.loading = false;
            }
        },
        async login(payload) {
            try {
                this.loading = true;
                const response = await ApiService.post('/auth/login', payload);
                this.setAuthorization(response.data.token, response.data.user);
                return Promise.resolve();
            }
            catch (error) {
                return Promise.reject(error.data);
            }
            finally {
                this.loading = false;
            }
        },
        async logout() {
            try {
                this.loading = true;
                await ApiService.get('/auth/logout');
                return Promise.resolve();
            }
            catch (error) {
                return Promise.reject(error.data);
            }
            finally {
                this.loading = false;
                this.clearAuthorization();
            }
        },
        async fetchUser() {
            try {
                this.loading = true;
                const response = await ApiService.get('auth/user');
                this.user = response.data;
                return Promise.resolve();
            }
            catch (error) {
               this.clearAuthorization();
            }
            finally {
                this.loading = false;
            }
        },
        setAuthorization(token, user) {
            this.user = user;
            this.token = token;
            setCookie('token', token);
            router.push({ name: 'Home' });
        },
        clearAuthorization() {
            this.user = {};
            this.token = null;
            deleteCookie('token');
            router.push({ name: 'Login' });
        },
    }
});