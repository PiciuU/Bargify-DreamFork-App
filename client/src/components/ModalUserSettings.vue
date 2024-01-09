<template>
    <div class="modal-overlay" @click="$emit('close')"></div>
    <div class="modal" ref="modal">
        <div class="modal__header">
            <h2>Notifications Settings</h2>
            <font-awesome-icon icon="close" @click="$emit('close')"/>
        </div>
        <div class="modal__body">
            <div class="error" v-if="subscriptionStore.error.message">
                <p>
                    {{ subscriptionStore.error.message }}
                </p>
                <a :href="subscriptionStore.error.details" target="_blank" v-if="subscriptionStore.error.details">{{ subscriptionStore.error.details }}</a>
            </div>
            <div v-else>
                <p v-if="subscriptionStore.hasSubscription">
                    You're receiving notifications. If you'd like to stop receiving them, you can disable them here.
                </p>
                <p v-else>
                    Currently, you're not receiving any notifications. If you'd like to receive notifications, you can enable them here.
                </p>
            </div>
        </div>
        <div class="modal__footer" v-if="!subscriptionStore.error.message">
            <div class="footer__data">
            </div>
            <div class="button__group">
                <button @click="$emit('close')">Close</button>
                <button class="button-primary" @click="subscriptionStore.subscribe" v-if="!subscriptionStore.hasSubscription" :disabled="subscriptionStore.isLoading">
                    <div class="loading" v-if="subscriptionStore.isLoading">
                        <font-awesome-icon icon="spinner" />
                        <span>Loading...</span>
                    </div>
                    <div v-else>
                        <span>Enable Notifications</span>
                    </div>
                </button>
                <button class="button-primary" @click="subscriptionStore.unsubscribe" v-else :disabled="subscriptionStore.isLoading">
                    <div class="loading" v-if="subscriptionStore.isLoading">
                        <font-awesome-icon icon="spinner" />
                        <span>Loading...</span>
                    </div>
                    <div v-else>
                        <span>Disable Notifications</span>
                    </div>
                </button>

            </div>
        </div>
    </div>
</template>

<script setup>
    import { onMounted, onUnmounted, } from 'vue'
    import { useSubscriptionStore } from '@/stores/SubscriptionStore';

    onMounted(() => document.body.classList.add('disableScroll'));

    onUnmounted(() => document.body.classList.remove('disableScroll'));

    const subscriptionStore = useSubscriptionStore();

    const emit = defineEmits(['close']);
</script>

<style lang="scss" scoped>
    @import '@/assets/styles/modal.scss';

    .error {
        display: flex;
        flex-direction: column;
        gap: 15px;

        a {
            color: $--color-secondary;
            word-break: break-word;
        }
    }

    .modal__footer {
        padding-top: 20px;
    }
</style>