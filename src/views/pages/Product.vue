<script>
// utils
import { capitalizeFirstLetter, formatPrice } from '@/utils';

// pinia
import { mapStores } from 'pinia';
import { useUserStore } from '@/store/userStore.js';
import { useResultsStore } from '@/store/resultsStore.js';
import { useShoppingCartStore } from '@/store/shoppingCartStore.js';
import { useBreadcrumbsStore } from '@/store/breadcrumbsStore.js';

// components
import Header from '@/components/Header.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';

export default {
    components: {
        Header,
        Breadcrumbs,
    },
    data() {
        return {
            breadcrumbs: {
                home: {
                    icon: 'pi pi-home',
                    route: '/',
                },
                items: [],
            },

            tabMenuItems: [
                { label: 'secondary 1', icon: 'pi pi-home' },
                { label: 'Informacije 2', icon: 'pi pi-chart-line' },
                { label: 'Informacije 3', icon: 'pi pi-list' },
            ],

            itemQuantity: '1',

            inquiry: {
                product: 'Product 1',
                isSending: false, // toggle <Dialog />
                name: 'Hello',
                lastName: 'Test',
                text: '',
            },
        };
    },
    watch: {
        itemQuantity(newVal, oldVal) {
            if (newVal < 1) {
                this.itemQuantity = '1';
            }
        },
    },
    computed: {
        ...mapStores(
            useUserStore,
            useResultsStore,
            useShoppingCartStore,
            useBreadcrumbsStore,
        ),

        productName() {
            return capitalizeFirstLetter(this.resultsStore.product.name);
        },

        productShortDescription() {
            // console.log(this.resultsStore.product.shortDescription);
            return capitalizeFirstLetter(
                this.resultsStore.product.shortDescription,
            );
        },
    },
    mounted() {
        console.log(this.resultsStore.product);

    },
    updated() {
        const [name, lastName] = this.userStore.fullName.split(' ');

        this.inquiry.name = name;
        this.inquiry.lastName = lastName;
    },
    methods: {
        handleSendInquiry() {
            this.inquiry.isSending = true;
        },

        handleAddProdcutToShoppingCart(product) {
            const productDetails = { ...product, quantity: this.itemQuantity };

            this.shoppingCartStore.add(productDetails);
        },

        formatProductPrice(val) {
            return formatPrice(val);
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

    <!-- Prodcut: Image & Description -->
    <div class="grid column-gap-6 justify-content-between">
        <div class="col-12 h-20rem md:col-5 md:h-auto">
            <div class="bg-white p-8 border-round border-1 border-100">
                <img
                    :src="this.resultsStore.product.picture_urls[0]"
                    class="image--product"
                />
            </div>
        </div>

        <div class="col">
            <h2 class="mt-4 md:mt-0">{{ productName }}</h2>
            <span class="font-bold"
                >SKU:
                <span class="font-normal">{{
                    this.resultsStore.product.sku
                }}</span></span
            >

            <hr />

            <p class="mt-3" v-html="productShortDescription" />
            <p class="mt-3" v-html="pro" />

            <!-- Stock -->
            <span class="block mt-5"
                ><span
                    class="font-bold"
                    :class="
                        this.resultsStore.product.stockQuantity == 0
                            ? 'text-red-500'
                            : 'text-green-500'
                    "
                    >{{ this.resultsStore.product.stockQuantity }}</span
                >
                na stanju.</span
            >

            <div
                class="mt-5 flex flex-column row-gap-1 justify-content-between"
            >
                <div>
                    <span class="mb-0 mr-2 mb-2 text-lg">Cijena</span>
                    <span class="font-bold"
                        >{{
                            formatProductPrice(resultsStore.product.price)
                        }}
                        €</span
                    >
                </div>
                <div>
                    <span class="m-0 mr-2 mb-2 text-lg">Cijena s popustom</span>
                    <span class="font-bold"
                        >{{ resultsStore.product.price }} €</span
                    >
                </div>
            </div>

            <!-- Price -->
            <!-- Buttons -->
            <Toolbar class="mt-4 bg-white">
                <template #start>
                    <Button
                        class="button--no-shadow mr-1"
                        icon="pi pi-minus"
                        severity="secondary"
                        outlined
                        @click="itemQuantity--"
                    />
                    <Button
                        class="mr-1"
                        severity="info"
                        outlined
                        :label="itemQuantity"
                        disabled
                    />
                    <Button
                        icon="pi pi-plus"
                        class="button--no-shadow"
                        severity="secondary"
                        outlined
                        @click="itemQuantity++"
                    />
                </template>

                <template #end>
                    <div>
                        <Button
                            class="button--no-shadow mr-2 text-sm"
                            label="Dodaj u košaricu"
                            severity="primary"
                            outlined
                            :disabled="
                                this.resultsStore.product.stockQuantity == 0
                            "
                            @click="
                                handleAddProdcutToShoppingCart(
                                    resultsStore.product,
                                )
                            "
                        />
                        <Button
                            class="button--no-shadow text-sm"
                            label="Pošalji upit"
                            severity="secondary"
                            outlined
                            @click="handleSendInquiry"
                        />
                    </div>
                </template>
            </Toolbar>
        </div>
    </div>

    <!-- Tab Menu -->
    <div class="mt-6 grid column-gap-6">
        <div class="col">
            <TabMenu :model="tabMenuItems" />
        </div>
    </div>

    <Dialog
        modal
        closeOnEscape
        dismissableMask
        :closable="false"
        :visible="inquiry.isSending"
        @update:visible="inquiry.isSending = false"
        class="w-30rem"
    >
        <template #header>
            <h2 class="text-center w-full pt-4">Pošalji upit</h2>
        </template>

        <!-- Dialog Input: Subject -->
        <label>Proizvod</label>
        <InputText
            v-model="inquiry.product"
            class="w-full mt-2 mb-3"
            disabled
        />

        <!-- Dialog Input: Name -->
        <label>Ime</label>
        <InputText v-model="inquiry.name" class="w-full mt-2 mb-3" disabled />

        <!-- Dialog Input: Last Name -->
        <label>Prezime</label>
        <InputText
            v-model="inquiry.lastName"
            class="w-full mt-2 mb-3"
            disabled
        />

        <!-- Dialog Input: Text -->
        <label>Upit</label>
        <Textarea v-model="inquiry.text" rows="7" class="w-full mt-2 mb-6" />

        <div class="flex justify-content-end gap-2">
            <Button
                type="button"
                label="Odustani"
                severity="danger"
                @click="inquiry.isSending = false"
            />
            <Button
                type="button"
                label="Pošalji"
                severity="success"
                @click="inquiry.isSending = false"
            />
        </div>
    </Dialog>
</template>

<style scoped>
.image--product {
    display: block;
    width: 100%;
    height: 300px;
    object-fit: cover;
    object-position: center;
    border-radius: 4px;
}

.button--no-shadow:focus {
    box-shadow: none !important;
}

/* upadate product image on smaller devices */
@media only screen and (max-width: 767px) {
    .image--product {
        height: 100%;
    }
}
</style>
