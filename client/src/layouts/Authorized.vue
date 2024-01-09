<template>
    <TheHeader />

    <main>
        <div class="wrapper" v-if="!isFetching && authStore.isAuthenticated">
            <router-view v-slot="{ Component }">
                <component :is="Component"></component>
            </router-view>
        </div>

        <div class="loader__wrapper" v-else>
            <div class="loader__circle">
                <div class="loader__border">
                    <div class="loader__core"></div>
                </div>
                <div class="loader__text">Loading</div>
            </div>
        </div>
    </main>
</template>

<script setup>
    import { ref, onBeforeMount } from 'vue';
    import { useAuthStore } from '@/stores/AuthStore';
    import { useDataStore } from '@/stores/DataStore';
    import { useSubscriptionStore } from '@/stores/SubscriptionStore';

    import TheHeader from '@/components/common/TheHeader.vue';

    const dataStore = useDataStore();
    const authStore = useAuthStore();
    const subscriptionStore = useSubscriptionStore();

    let isFetching = ref(false);

    onBeforeMount(async () => {
        isFetching.value = true;
        await authStore.fetchUser();

        await dataStore.fetchData();
        subscriptionStore.fetchSw();
        isFetching.value = false;
    });
</script>

<style lang="scss" scoped>
    @import '@/assets/styles/animations.scss';

    .loader {
        &__wrapper {
            align-items: center;
            display: flex;
            justify-content: center;
            height: 100%;
            position: fixed;
            width: 100%;
            top: 0;
        }

        &__border {
            animation: rotate .8s linear 0s infinite;
            background: linear-gradient(0deg, rgba($--color-primary, 0.2) 33%, rgba($--color-primary, 1) 100%);
            border-radius: 50%;
            height: 150px;
            padding: 3px;
            width: 150px;
        }

        &__core {
            background-color: $--color-background;
            border-radius: 50%;
            height: 100%;
            width: 100%;
        }

        &__text {
            color: $--color-primary;
            font-size: 2.4rem;
            font-weight: bold;
            letter-spacing: 2px;
            margin-top: 15px;
            text-align: center;
        }
    }
</style>