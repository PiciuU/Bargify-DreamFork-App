<template>
    <div class="modal-overlay" @click="$emit('close')"></div>
    <div class="modal" ref="modal">
        <div class="modal__header">
            <h2>Edit Product</h2>
            <font-awesome-icon icon="close" @click="$emit('close')"/>
        </div>
        <div class="modal__body">
            <form @submit.prevent="submitForm" id="edit-form">
                <div class="form__group">
                    <div class="form__input">
                        <label for="url">Link</label>
                        <input type="text" id="url" placeholder="" required v-model="formData.url" disabled/>
                    </div>
                </div>

                <div class="form__group">
                    <div class="form__input">
                        <label for="name">Product Name</label>
                        <input type="text" id="name" placeholder="" required v-model="formData.name" disabled/>
                    </div>
                </div>

                <div class="form__group">
                    <div class="form__input">
                        <label for="price">Maximum Price</label>
                        <div class="suffix" data-suffix="PLN">
                            <input type="number" step="0.01" id="price" placeholder="Enter the maximum price for the product." required v-model="formData.max_price"/>
                            <span></span>
                        </div>
                    </div>
                </div>

                <div class="form__group">
                    <div class="form__input form__input--checkbox">
                        <label>Receive Notifications</label>
                        <div v-if="subscriptionStore.hasSubscription">
                            <label class="switch" for="notification">
                                <input type="checkbox" name="slider" class="slider-checkbox" id="notification" v-model="formData.enable_notifications" :true-value="1" :false-value="0">
                                <span></span>
                            </label>
                        </div>
                        <div v-else>
                            <p class="hint">To receive notifications, please enable them in notifications settings.</p>
                        </div>
                    </div>
                </div>

                <div class="form__error" v-if="error">
                    <p>{{ error }}</p>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <div class="footer__data">
                <p>
                    Last checked at:
                </p>
                <p>
                    {{props.product.updated_at }}
                </p>
            </div>
            <div class="button__group">
                <button @click="$emit('close')">Discard</button>
                <button class="button-primary" form="edit-form" :disabled="isLoading">
                    <div class="loading" v-if="isLoading">
                        <font-awesome-icon icon="spinner" />
                        <span>Loading...</span>
                    </div>
                    <div v-else>
                        <span>Save</span>
                    </div>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { useDataStore } from '@/stores/DataStore';
    import { useSubscriptionStore } from '@/stores/SubscriptionStore';

    import { onMounted, onUnmounted, reactive, ref } from 'vue'

    onMounted(() => {
        document.body.classList.add('disableScroll');

        Object.assign(formData, {
            name: props.product?.name ?? '',
            url: props.product?.url ?? '',
            max_price: props.product?.max_price ?? 0,
            last_known_price: props.product?.last_known_price ?? 0,
            is_available: props.product?.is_available ?? 0,
            last_available_at: props.product?.last_available_at ?? '',
            enable_notifications: props.product?.enable_notifications ?? 0
        })
    });

    onUnmounted(() => document.body.classList.remove('disableScroll'));

    const dataStore = useDataStore();
    const subscriptionStore = useSubscriptionStore();

    const emit = defineEmits(['close']);

    const props = defineProps({
        product: { type: Object, required: true, default: {} }
    });

    const formData = reactive({});

    const isLoading = ref(false);

    const error = ref('');

    const submitForm = async () => {
        error.value = '';
        isLoading.value = true;

        await dataStore.updateProduct(props.product.id, formData)
            .catch((e) => {
                if (e.errors) {
                    error.value = e.errors[Object.keys(e.errors)[0]][0];
                } else if (e.message) {
                    error.value = e.message;
                }
                else {
                    error.value = "An unexpected error occurred. Please try again later or contact support if the issue persists.";
                }
            })
            .finally(() => {
                isLoading.value = false;
            })

        if (error.value) return;

        emit('close');

    };

</script>

<style lang="scss" scoped>
    @import '@/assets/styles/modal.scss';

    .hint {
        font-size: 1.2rem;
        color: $--color-error;
    }
</style>