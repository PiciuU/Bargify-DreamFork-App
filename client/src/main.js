import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'

/* Styles */
import '@/assets/styles/main.scss'

/* Font Awesome */
import { fontAwesome } from '@/plugins/font-awesome'

/* Axios */
import ApiService from '@/services/api.service';
ApiService.init();

/* App */
const app = createApp(App);

app.use(createPinia()).use(router).use(fontAwesome).mount('#app');

/* Inject Service Worker */
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register(`${import.meta.env.BASE_URL}sw.js`, { scope: `${import.meta.env.BASE_URL}` });
    });
}