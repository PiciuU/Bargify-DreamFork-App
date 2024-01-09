import { defineStore } from 'pinia';

import ApiService from '@/services/api.service';

import { useAuthStore as authStore } from '@/stores/AuthStore';

export const useDataStore = defineStore('dataStore', {
    state: () => ({
        products: [],
        settings: [],
        loading: false,
    }),
    getters: {
        getProducts: (state) => state.products,
        getSettings: (state) => state.settings,
        isLoading: (state) => state.loading,
        availableStores: (state) => state.settings?.stores
    },
    actions: {
        async fetchData() {
            try {
                this.loading = true;

                const [productsResponse, settingsResponse] = await Promise.all([
                    ApiService.get('/products'),
                    ApiService.get('/settings'),
                ]);

                this.products = productsResponse.data.products;
                this.settings = settingsResponse.data;

                const currentDate = new Date();

                const targetDate = new Date(this.settings.schedule);

                const timeToWait = targetDate.getTime() - currentDate.getTime();

                setTimeout(() => {
                    if (authStore().isAuthenticated) this.fetchData();
                }, timeToWait);

                return Promise.resolve();
            }
            catch (error) {
                return Promise.reject(error.data);
            }
            finally {
                this.loading = false;
            }
        },
        async updateProduct(productId, { max_price, enable_notifications }) {
            try {
                this.loading = true;
                await new Promise(resolve => setTimeout(resolve, 1000));
                const response = await ApiService.post('/product/update', { 'product_id': productId, 'max_price': max_price, 'enable_notifications': enable_notifications });
                const product = this.products.find(p => p.id === productId);
                Object.assign(product, response.data.product);
                return Promise.resolve();
            }
            catch (error) {
                return Promise.reject(error.data);
            }
            finally {
                this.loading = false;
            }
        },
        async addProduct(payload) {
            try {
                this.loading = true;
                await new Promise(resolve => setTimeout(resolve, 1000));
                const response = await ApiService.post('/product', payload);
                this.products.push(response.data.product);
                return Promise.resolve();
            }
            catch (error) {
                return Promise.reject(error.data);
            }
            finally {
                this.loading = false;
            }
        },
        async deleteProduct(productId) {
            try {
                this.loading = true;
                await ApiService.delete(`/product/${productId}`);
                const index = this.products.findIndex(p => p.id === productId);
                this.products.splice(index, 1);
                return Promise.resolve();
            }
            catch (error) {
                return Promise.reject(error.data);
            }
            finally {
                this.loading = false;
            }
        },
    }
});