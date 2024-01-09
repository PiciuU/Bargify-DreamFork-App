<template>
    <header>
        <nav>
            <router-link to="/">
                <img class="logomark" src="@/assets/images/bargify-logomark.svg">
                <img class="logotype" src="@/assets/images/bargify-logotype.svg">
            </router-link>
            <ul>
                <li>
                    <font-awesome-icon icon="bell" @click="modals.isSettingsModalVisible = true"/>
                </li>
                <li>
                    <font-awesome-icon icon="right-from-bracket" @click="handleLogout"/>
                </li>
            </ul>
        </nav>

        <Teleport to="#app" v-if="authStore.isAuthenticated">
            <ModalUserSettings v-if="modals.isSettingsModalVisible" @close="modals.isSettingsModalVisible = false"/>
        </Teleport>
    </header>
</template>

<script setup>
    import { reactive } from 'vue';
    import { useAuthStore } from '@/stores/AuthStore';
    import ModalUserSettings from '@/components/ModalUserSettings.vue'

    const authStore = useAuthStore();

    const modals = reactive({
        isSettingsModalVisible: false,
    })

    const handleLogout = () => {
        authStore.logout();
    };
</script>

<style lang="scss" scoped>
    header {
        background: $--color-background;
        height: $--header-height;
        z-index: 1000;

        nav {
            align-items: center;
            display: flex;
            margin: 0 auto;
            max-width: 1440px;
            padding: 25px 20px;
            justify-content: space-between;

            a {
                display: inline-flex;
                justify-content: center;
                align-items: center;

                .logomark {
                    height: 50px;
                }

                .logotype {
                    height: 40px;
                    margin-left: 15px;
                    margin-top: 5px;
                }
            }

            ul {
                list-style-type: none;
                display: flex;
                gap: 25px;

                svg {
                    font-size: 2.2rem;
                    color: $--color-primary;
                    cursor: pointer;
                    transition: .4s;

                    &:hover {
                        color: $--color-secondary;
                    }
                }
            }

        }
    }

    @media screen and (max-width: $--breakpoint-mobile) {
        header {
            height: calc($--header-height - 10px);
        }

        header nav a .logomark {
            height: 40px;
        }

        header nav a .logotype {
            height: 30px;
        }
    }

    @media screen and (max-width: ($--breakpoint-small-mobile - 1px)) {
        .logotype {
            display: none;
        }
    }
</style>