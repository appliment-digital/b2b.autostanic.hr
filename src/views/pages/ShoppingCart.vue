<script>
// components
import Header from '@/components/Header.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';

// pinia
import { mapStores } from 'pinia';
import { useShoppingCartStore } from '@/store/shoppingCartStore.js';
import { useBreadcrumbsStore } from '@/store/breadcrumbsStore.js';

export default {
    components: {
        Header,
        Breadcrumbs,
    },
    data() {
        return {
            tableTotal: {},
        };
    },
    computed: {
        ...mapStores(useShoppingCartStore, useBreadcrumbsStore),

        shoppingCartProducts() {
            if (this.shoppingCartStore.cart.length) {
                // console.log(this.shoppingCartStore.cart);
                return this.shoppingCartStore.cart;
            } else {
                return [];
            }
        },

        tableTotal() {
            return [
                {
                    currentTotal: this.shoppingCartStore.total,
                    delivery: 10,
                    tax: 25,
                    afterTotalsl: 123123,
                },
            ];
        },
    },

    methods: {
        calcPrice(price, quantity) {
            return Number(quantity * price).toFixed(2);
        },

        handleNewProductQuantity(product) {
            if (product.quantity < 1) return;

            this.shoppingCartStore.updateQuantity(product);
        },

        handleDeleteProduct(product) {
            this.shoppingCartStore.delete(product);
        },

        handleFinishOrderClick() {
            this.$router.push('/hvala')
        },
    },
};
</script>

<template>
    <Header />

    <div class="mt-3 flex justify-content-between align-items-center">
        <Breadcrumbs :crumbs="this.breadcrumbsStore.current" />
        <div v-if="isDataLoading" class="flex align-items-center column-gap-2">
            <ProgressSpinner class="w-2rem h-3rem text-400" strokeWidth="3" />
            učitavanje podataka...
        </div>
    </div>

    <div class="grid grid-nogutter justify-content-between column-gap-3">
        <div class="col-12">
            <div class="card mb-0">
                <DataTable
                    v-if="shoppingCartProducts.length"
                    :value="shoppingCartProducts"
                    tableStyle="min-width: 50rem"
                >
                    <Column field="image" header="Slika">
                        <template #body="{ data }">
                            <img
                                :src="data.picture_urls[0]"
                                class="table-image border-round"
                            />
                        </template>
                    </Column>

                    <Column field="name" header="Proizvod"> </Column>

                    <Column field="price" header="Cijena">
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
                                icon="pi pi-times"
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

        <div v-if="shoppingCartProducts.length" class="col mt-3">
            <div class="card">
                <DataTable :value="tableTotal">
                    <Column field="currentTotal" header="Ukupno">
                        <template #body="{ data }">
                            <span>{{ data.currentTotal }} €</span>
                        </template>
                    </Column>
                    <Column header="Dostava"> 
                        <template #body="{ data }">
                            <span>{{ data.delivery }} €</span>
                        </template>
                    </Column>
                    <Column header="Porez"> 
                        <template #body="{ data }">
                            <span>{{ data.tax }} €</span>
                        </template>
                    </Column>
                    <Column header="Sveukupno"> 
                        <template #body="{ data }">
                            <span>{{ data.currentTotal }} €</span>
                        </template>
                    </Column>
                    <Column header="Akcije" style="width: 200px;"> 
                        <template #body>
                            <Button
                                class="button--no-shadow w-full"
                                label="Završi narudžbu"
                                outlined
                                severity="info"
                                @click="handleFinishOrderClick"
                            />

                        </template>
                    </Column>
                </DataTable>
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
