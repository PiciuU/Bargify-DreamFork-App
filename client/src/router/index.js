import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/AuthStore'

export const router = createRouter({
	history: createWebHistory(import.meta.env.BASE_URL),
	routes: [
		{
			path: '/',
            meta: { requiresAuth: true },
            component: () => import(/* webpackChunkName: "group-authorized" */ '@/layouts/Authorized.vue'),
            children: [
                {
                    name: 'Home',
                    path: '',
                    component: () => import(/* webpackChunkName: "group-authorized" */ '@/views/Home.vue'),
                    meta: { breadcrumb: '' }
                },
			]
		},
		{
            path: '/',
            meta: { requiresAuth: false },
            component: () => import(/* webpackChunkName: "group-default" */ '@/layouts/Default.vue'),
			children: [
                {
                    name: 'Login',
                    path: 'login',
                    component: () => import(/* webpackChunkName: "group-default" */ '@/views/auth/Login.vue')
                },
				{
                    name: 'Register',
                    path: 'register',
                    component: () => import(/* webpackChunkName: "group-default" */ '@/views/auth/Register.vue')
                },
            ]
        },
	]
})

router.beforeEach((to, from, next) => {
	const authStore = useAuthStore();

    if (to.meta.requiresAuth && !authStore.isLogged) next('/login'); // User not logged in, redirect to the login page
    else if (!to.meta.requiresAuth && authStore.isLogged) next('/'); // User logged in, redirect to the Home view
    else next(); // Redirect to the intended view
});

export default router
