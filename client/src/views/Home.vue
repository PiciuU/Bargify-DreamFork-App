<template>
    <div class="container" :class=" { 'table-empty': dataStore.getProducts.length == 0 }">
        <div class="card">
            <div class="card__header">
                <h1>Observed Products</h1>
                <p>
                    Stay ahead of the game by adding your desired products to the watchlist. Be the first to know when they become available or go on sale. Gain an advantage over other shoppers and never miss out on your dream purchases!
                </p>
                <button @click="modals.isAddModalVisible = true">
                    Add Product
                </button>
            </div>
            <div class="card__body" v-if="dataStore.getProducts.length != 0">
                <table>
                    <thead>
                        <tr>
                            <th>
                                Product Name
                            </th>
                            <th>
                                Availability
                            </th>
                            <th>
                                Last Price
                            </th>
                            <th>
                                Notifications
                            </th>
                            <th>
                                Operations
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in dataStore.getProducts" :key="product.id">
                            <td data-heading="Product Name:">
                                <a :href="product.url" target="_blank">
                                    {{ product.name }}
                                </a>
                            </td>
                            <td data-heading="Availability:">
                                <span :class="{'active': product.is_available, 'inactive': !product.is_available}">
                                    {{ product.is_available ? 'Yes' : 'No' }}
                                </span>
                                <span v-if="product.last_available_at">
                                    ({{ product.last_available_at }})
                                </span>
                            </td>
                            <td data-heading="Last Price:">
                                {{ product.last_known_price ?? 0 }} PLN
                            </td>
                            <td data-heading="Notifications:">
                                <span :class="{'active': product.enable_notifications && subscriptionStore.hasSubscription, 'inactive': !product.enable_notifications || !subscriptionStore.hasSubscription}">
                                    {{ product.enable_notifications && subscriptionStore.hasSubscription ? 'Enabled' : 'Disabled' }}
                                </span>
                            </td>
                            <td>
                                <font-awesome-icon icon="pen-to-square" @click="enableModalProductEdit(product)"/>
                                <font-awesome-icon icon="trash-can" @click="handleProductDelete(product)"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Teleport to="#app">
            <ModalProductEdit v-if="modals.isEditModalVisible" :product="selectedProduct" @close="modals.isEditModalVisible = false"/>
            <ModalProductAdd v-if="modals.isAddModalVisible" @close="modals.isAddModalVisible = false"/>
        </Teleport>
    </div>
</template>

<script setup>
    import { ref, reactive } from 'vue'
    import { useDataStore } from '@/stores/DataStore';
    import { useSubscriptionStore } from '@/stores/SubscriptionStore';
    import ModalProductEdit from '@/components/ModalProductEdit.vue'
    import ModalProductAdd from '@/components/ModalProductAdd.vue'

    const dataStore = useDataStore();
    const subscriptionStore = useSubscriptionStore();

    const modals = reactive({
        isEditModalVisible: false,
        isAddModalVisible: false,
    })

    const selectedProduct = ref(null);

    function enableModalProductEdit(product) {
        selectedProduct.value = product;
        modals.isEditModalVisible = true;
    }

    function handleProductDelete(product) {
        if (!confirm("Are you sure you want to remove the selected product from your watchlist?")) return;

        dataStore.deleteProduct(product.id)
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
    }
</script>


<style lang="scss" scoped>
    .container {
        align-items: center;
        display: flex;
        flex-direction: column;
        gap: 50px;
        height: 100%;
        padding: 20px 0px;
        width: 100%;
        margin: 0 auto;
        max-width: 1440px;
    }

    .container.table-empty {
        height: calc(100% - $--header-height);
        justify-content: center;
    }

    .container.table-empty .card {
        margin-top: 0px;
    }

    .card {
        display: flex;
        flex-direction: column;
        width: 100%;
        border-radius: 25px;
        padding: 15px 20px;
        margin-top: 50px;

        &__header {
            display: flex;
            flex-direction: column;
            align-items: center;

            h1 {
                font-size: 3.2rem;
                font-weight: bold;
                margin-bottom: 15px;
                color: $--color-primary-variant;
                text-align: center;
            }

            p {
                text-align: center;
                max-width: 700px;
                margin-bottom: 10px;
            }

            button {
                display: block;
                height: 50px;
                border-radius: 15px;
                outline: none;
                border: none;
                background: $--color-primary;
                background-size: 200%;
                font-size: 1.6rem;
                font-weight: bold;
                color: #fff;
                font-family: 'Poppins', sans-serif;
                text-transform: uppercase;
                margin: 10px 0;
                padding: 10px 30px;
                cursor: pointer;
                transition: .5s;

                &:hover {
                    background: $--color-primary-variant;
                    transform: scale(1.1);
                }
            }
        }

        &__body {
            margin-top: 50px;

            table {
                width: 100%;
                border-collapse: collapse;
                border-spacing: 0;

                thead {
                    border-bottom: 2px solid $--color-text-muted-1;

                    th {
                        padding: 10px;
                        font-weight: bold;
                    }

                    th:first-child {
                        text-align: left;
                    }
                }

                tbody {
                    tr {
                        transition: .25s;

                        &:hover {
                            background: $--color-overlay;
                        }
                    }

                    td {
                        padding: 10px;
                        text-align: center;


                        &:first-child {
                            text-align: left;
                            max-width: 600px;
                        }

                        &:last-child svg {
                            cursor: pointer;
                            transition: .25s;

                            &:hover {
                                color: $--color-secondary;
                            }

                            &:first-child {
                                margin-right: 20px;
                            }
                        }

                        a {
                            color: $--color-secondary;
                        }

                        span.active {
                            color: $--color-success;
                        }

                        span.inactive {
                            color: $--color-error;
                        }
                    }
                }
            }
        }
    }

    @media screen and (max-width: ($--breakpoint-small-devices - 1px)) {
        .card {
            margin-top: 0px;
        }

        .card__body {
            table {
                margin-bottom: 20px;
                thead {
                    display: none;
                }

                tbody {
                    display: grid;
                    gap: 20px;

                    tr {
                        display: flex;
                        flex-direction: column;
                        widows: 100%;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                        border-radius: 15px;
                        padding: 10px;
                    }

                    td {
                        text-align: left;
                        padding: 5px 10px;

                        &:before {
                            content: attr(data-heading);
                            display: block;
                            font-weight: bold;
                            color: $--color-text;
                        }

                        &:last-child {
                            position:absolute;
                            top: 10px;
                            right: 10px;
                        }
                    }
                }
            }
        }
    }

</style>