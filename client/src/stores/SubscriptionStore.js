import { defineStore } from 'pinia';

import ApiService from '@/services/api.service';
import { setCookie, getCookie, deleteCookie } from '@/services/storage.service';

export const useSubscriptionStore = defineStore('subscriptionStore', {
    state: () => ({
        sw: null,
        loading: false,
        vapidPublicKey: import.meta.env.VITE_APP_VAPID_PUBLIC_KEY,
        subscription: null,
        subscribed: false,
        error: {
            message: null,
            details: null
        }
    }),
    getters: {
        hasSubscription: (state) => state.subscription !== null && state.subscribed,
        isLoading: (state) => state.loading
    },
    actions: {
        async sendSubscriptionToServer(method = 'create', oldEndpoint = null) {
            this.loading = true;
            try {
                const public_key = btoa(String.fromCharCode.apply(null, new Uint8Array(this.subscription.getKey('p256dh'))));
                const auth_token = btoa(String.fromCharCode.apply(null, new Uint8Array(this.subscription.getKey('auth'))));

                await ApiService.post(`/subscription/${method}`, { 'old_endpoint': oldEndpoint, 'endpoint': this.subscription.endpoint, 'public_key': public_key, 'auth_token': auth_token});

                return Promise.resolve();
            }
            catch (error) {
                return Promise.reject(error.data);
            }
            finally {
                this.loading = false;
            }
        },
        async fetchSw() {
            if (!'serviceWorker' in navigator || !navigator.serviceWorker.controller || !'PushManager' in window || !'showNotification' in ServiceWorkerRegistration.prototype) {
                this.createError("We couldn't initiate all the necessary components for receiving notifications. Please try refreshing the page and attempting again. If the issue persists, ensure that your browser is updated to the latest version and supports service workers and push notifications.");
                return;
            }

            this.sw = await navigator.serviceWorker.ready;

            this.checkNotificationPermissions();
            this.checkSubscriptionStatus();
        },
        async checkSubscriptionStatus() {
            this.subscription = await this.sw.pushManager.getSubscription();

            if (!this.subscription) return;

            this.subscribed = true;

            if(!getCookie('subscription-valid')) this.resubscribe();
        },
        async resubscribe() {
            const oldEndpoint = this.subscription.endpoint;

            await this.subscribeToPushManager();

            if (oldEndpoint == this.subscription.endpoint) {
                this.subscribed = true;
                setCookie('subscription-valid', true, 1);
                return;
            }

            this.sendSubscriptionToServer('update', oldEndpoint)
                .then(() => {
                    this.subscribed = true;
                    setCookie('subscription-valid', true, 1);
                })
                .catch((e) => {
                    this.createError("An error occurred while handling notification settings. Please refresh the page and try again. If the issue persists, contact the administration.");
                    this.subscribed = false;
                    this.subscription.unsubscribe();
                    this.subscription = null;
                })
        },
        async subscribe() {
            if (!(await this.checkNotificationPermissions(true))) return;

            await this.subscribeToPushManager();

            this.sendSubscriptionToServer('create')
                .then(() => {
                    this.subscribed = true;
                    setCookie('subscription-valid', true, 1);
                })
                .catch((e) => {
                    this.createError("An error occurred while handling notification settings. Please refresh the page and try again. If the issue persists, contact the administration.");
                    this.subscribed = false;
                    this.subscription.unsubscribe();
                    this.subscription = null;
                })
        },
        async unsubscribe() {
            this.sendSubscriptionToServer('delete')
                .finally(() => {
                    deleteCookie('subscription-valid');
                    this.subscribed = false;
                    this.subscription.unsubscribe();
                    this.subscription = null;
                })
        },
        async subscribeToPushManager(){
            this.loading = true;

            this.subscription = await this.sw.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(this.vapidPublicKey)
            });

            this.loading = false;
        },
        checkNotificationPermissions(requestPermission = false) {

            if (Notification.permission === 'denied') {
                this.createError("Notifications are blocked. Please update your browser settings to allow notifications. For assistance, you can find help here: ", "https://support.google.com/chrome/answer/3220216");
                return false;
            } else if (Notification.permission === 'granted') {
                return true;
            } else if (Notification.permission === 'default' && requestPermission) {
                return Notification.requestPermission().then((result) => {
                    if (result === 'granted') {
                        return true;
                    } else if (result === 'denied') {
                        this.createError("Notifications are blocked. Please update your browser settings to allow notifications. For assistance, you can find help here: ", "https://support.google.com/chrome/answer/3220216");
                    }
                    return false;
                });
            }
        },
        createError(message, details = "") {
            this.error.message = message;
            this.error.details = details
        },
        urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');

            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);

            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
    }
});