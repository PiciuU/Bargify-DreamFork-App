<template>
    <div class="background-wave">
        <img src="@/assets/images/background-wave.svg" alt="Image of a single color wave serving as the background of the page.">
	</div>
    <div class="container">
        <div class="image">
            <img src="@/assets/images/receipt.svg" alt="Image of a vector illustration.">
        </div>
        <div class="content">
            <form class="content__form" @submit.prevent="submitForm">
                <img src="@/assets/images/bargify-logo.svg">
                <div class="form__group">
                    <div class="form__input">
                        <input type="text" id="login" placeholder="" autocomplete="off" required v-model="credentials.login"/>
                        <font-awesome-icon icon="user"/>
                        <label for="login">Username</label>
                        <span></span>
                    </div>
                </div>

                <div class="form__group">
                    <div class="form__input">
                        <input type="password" id="password" placeholder="" autocomplete="off" required v-model="credentials.password"/>
                        <font-awesome-icon icon="lock"/>
                        <label for="login">Password</label>
                        <span></span>
                    </div>
                </div>

                <div class="form__hint">
                    <router-link to="/register">Sign up here.</router-link>
                </div>

                <button :disabled='authStore.isLoading'>
                    <div class="loading" v-if="authStore.isLoading">
                        <font-awesome-icon icon="spinner" />
                        <span>Loading...</span>
                    </div>
                    <div v-else>
                        <span>Login</span>
                    </div>
                </button>
                <div v-if="error" class="form__error"><p>{{ error }}</p></div>
            </form>

        </div>
    </div>
</template>

<script setup>
    import { ref, reactive } from 'vue';
    import { useAuthStore } from '@/stores/AuthStore';

    const authStore = useAuthStore();

    const credentials = reactive({
        login: '',
        password: ''
    });

    const error = ref();

    const submitForm = () => {
        error.value = '';

        if (!credentials.login || !credentials.password) error.value = 'Please enter your username and password.';

        if (error.value) return;

        authStore.login(credentials)
            .catch((e) => {
                if (e.message) {
                    error.value = e.message;
                }
                else {
                    error.value = "An unexpected error occurred. Please try again later or contact support if the issue persists.";
                }
            })
    };
</script>

<style lang="scss" scoped>
    @import '@/assets/styles/auth.scss';
</style>