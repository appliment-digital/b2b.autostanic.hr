<script>
// lib
import slug from 'slug';

// utils
import { setSlugCharMap } from '@/utils';

// components
import Header from '@/components/Header.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';

// pinia
import { mapStores } from 'pinia';
import { useShoppingCartStore } from '@/store/shoppingCartStore.js';
import { useBreadcrumbsStore } from '@/store/breadcrumbsStore.js';
import { useResultsStore } from '@/store/resultsStore.js';
import { useUIStore } from '@/store/UIStore.js';

// modify slug library (add croatian chars)
setSlugCharMap(slug);

export default {
    components: {
        Header,
        Breadcrumbs,
    },
    data() {
        return {
            selectedProduct: null,
        };
    },
    computed: {
        ...mapStores(
            useShoppingCartStore,
            useBreadcrumbsStore,
            useResultsStore,
            useUIStore,
        ),

        order() {
            return [
                {
                    name: 'Ukupno',
                    value: this.shoppingCartStore.totalWithoutFees,
                },
                {
                    name: 'Rabat',
                    value: this.shoppingCartStore.fees.delivery,
                },
                {
                    name: 'Porez',
                    value: this.shoppingCartStore.fees.tax,
                },
                {
                    name: 'Sveukupno',
                    value: this.shoppingCartStore.totalWithFees,
                },
            ];
        },
    },

    mounted() {
        console.log('shopping cart store', this.shoppingCartStore.cart);

    },

    methods: {
        calcPrice(price, quantity) {
            return `${Number(quantity * price).toFixed(2)}`;
        },

        handleNewProductQuantity(product) {
            if (product.quantity < 1) return;

            this.shoppingCartStore.updateQuantity(product);
        },

        handleDeleteProduct(product) {
            this.shoppingCartStore.delete(product);
        },

        handleFinishOrderClick() {
            this.$router.push('/hvala');
        },

        handleProductTableItemClick(product) {
            const productSlug = slug(product.name, { lower: true });

            // this.UIStore.setIsProductViewedFromCart(true);
            this.resultsStore.setProduct(product);
            this.$router.push(`/${productSlug}`);
        },
    },
};
</script>

<template>
    <div class="grid justify-content-between">
        <div class="col">
            <div class="card p-5 mb-0">
                <DataTable
                    v-if="shoppingCartStore.cart && shoppingCartStore.cart.length"
                    :value="shoppingCartStore.cart"
                >
                    <Column field="image" header="Slika">
                        <template #body="{ data }">
                            <img
                                :src="data.pictureUrl"
                                class="table-image border-round cursor-pointer"
                                @click="handleProductTableItemClick(data)"
                            />
                        </template>
                    </Column>

                    <Column field="name" header="Proizvod">
                        <template #body="{ data }">
                            <span
                                class="cursor-pointer"
                                @click="handleProductTableItemClick(data)"
                                >{{ data.name }}</span
                            >
                        </template>
                    </Column>

                    <Column
                        field="price"
                        header="Cijena"
                        style="min-width: 110px"
                    >
                        <template #body="{ data }">
                            <span
                                >{{
                                    calcPrice(data.price, data.quantity)
                                }}
                                €</span
                            >
                        </template>
                    </Column>

                    <Column header="Količina">
                        <template #body="{ data }">
                            <InputNumber
                                inputId="locale-german"
                                locale="de-DE"
                                v-model="data.quantity"
                                inputStyle="width: 60px; text-align: center;"
                                @update:modelValue="
                                    handleNewProductQuantity(data)
                                "
                            />
                        </template>
                    </Column>

                    <Column header="Obriši">
                        <template #body="{ data }">
                            <Button
                                class="button--no-shadow"
                                icon="pi pi-trash"
                                outlined
                                severity="info"
                                @click="handleDeleteProduct(data)"
                            />
                        </template>
                    </Column>
                </DataTable>

                <div v-else>Nema proizvoda u košarici...</div>
            </div>
        </div>

        <div v-if="shoppingCartStore.cart && shoppingCartStore.cart.length" class="col-12 lg:col-3">
            <div class="card p-5">
                <div class="max-w-24rem mx-auto">
                    <DataTable :value="order">
                        <Column header="Ukupna narudžba">
                            <template #body="{ data }">
                                <div class="flex justify-content-between">
                                    <span>{{ data.name }}</span>
                                    <span class="text-right"
                                        >{{ data.value }} €</span
                                    >
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                    <Button
                        class="button--no-shadow w-full mt-3"
                        label="Završi narudžbu"
                        outlined
                        severity="info"
                        @click="handleFinishOrderClick"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.table-image {
    width: 100px;
    object-fit: cover;
    height: 60px;
}

.button--no-shadow:focus {
    box-shadow: none !important;
}

.button--small .p-button-icon {
    font-size: 8px;
}
</style>
