<template>
    <div class="modal-overlay" @click="$emit('close')"></div>
    <div class="modal" ref="modal">
        <div class="modal__header">
            <h2>Add Product</h2>
            <font-awesome-icon icon="close" @click="$emit('close')"/>
        </div>
        <div class="modal__body">
            <form @submit.prevent="submitForm" id="edit-form">
                <div class="form__group">
                    <div class="form__input">
                        <label for="store">Selected Store</label>
                        <select id="store" v-model="formData.store" required>
                            <option value="" selected>Select available store...</option>
                            <option v-for="(store, index) in dataStore.availableStores" :key="index" :value="store">
                                {{ store }}
                            </option>
                        </select>
                    </div>
                </div>

                <div v-if="formData.store">
                    <div class="form__group">
                        <div class="form__input">
                            <label for="url">Product Link</label>
                            <input type="url" id="url" placeholder="Enter the direct link to the product on the selected store." autocomplete="off" required v-model="formData.url"/>
                            <span></span>
                        </div>
                    </div>

                    <div class="form__group">
                        <div class="form__input">
                            <label for="price">Maximum Price</label>
                            <div class="suffix" data-suffix="PLN">
                                <input type="number" step="0.01" id="price" placeholder="Enter the maximum price for the product." required v-model="formData.price"/>
                                <span></span>
                            </div>
                        </div>
                    </div>

                    <div class="form__error" v-if="error">
                        <p>{{ error }}</p>
                    </div>
                </div>

            </form>
        </div>
        <div class="modal__footer">
            <div class="footer__data">
            </div>
            <div class="button__group">
                <button @click="$emit('close')">Discard</button>
                <button class="button-primary" form="edit-form" :disabled="isLoading">
                    <div class="loading" v-if="isLoading">
                        <font-awesome-icon icon="spinner" />
                        <span>Loading...</span>
                    </div>
                    <div v-else>
                        <span>Add</span>
                    </div>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { useDataStore } from '@/stores/DataStore';
    import { onMounted, onUnmounted, reactive, ref } from 'vue'

    onMounted(() => document.body.classList.add('disableScroll'));

    onUnmounted(() => document.body.classList.remove('disableScroll'));

    const dataStore = useDataStore();

    const emit = defineEmits(['close']);

    const formData = reactive({
        store: null,
        url: null,
        price: null
    });

    const isLoading = ref(false);

    const error = ref('');

    const submitForm = async () => {
        error.value = '';
        isLoading.value = true;

        await dataStore.addProduct(formData)
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
</style>