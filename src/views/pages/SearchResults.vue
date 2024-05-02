<script>
// lib
import slugify from 'slugify';

// components
import Header from '@/components/Header.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';

// pinia
import { mapStores } from 'pinia';
import { useResultsStore } from '@/store/resultsStore.js';
import { useShoppingCartStore } from '@/store/shoppingCartStore.js';

// generate random 12-digit SKU
function generateRandomDigits() {
    let sku = '';
    for (let i = 0; i < 12; i++) {
        sku += Math.floor(Math.random() * 10);
    }
    return sku;
}

export default {
    components: {
        Header,
        Breadcrumbs,
    },
    data() {
        return {
            searchResults: null,

            // paginate results
            currentPage: 1,
            itemsPerPage: 9,
            totalItems: 10,

            filters: {},

            // display `add to cart` button on mouse over card
            mouseOverCard: {}, // e.g ([id]: true)
        };
    },
    mounted() {
        console.log('results store', this.resultsStore.searchResults);
        console.log({ paginatedData: this.paginatedData });

        this.filters.brand = new Set(
            this.resultsStore.searchResults.map(
                (product) => product.manufacturerName,
            ),
        );
    },

    computed: {
        paginatedData() {
            const startIndex = (this.currentPage - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            return this.resultsStore.searchResults.slice(startIndex, endIndex);
        },

        ...mapStores(useResultsStore, useShoppingCartStore),
    },
    methods: {
        onPageChange(event) {
            this.currentPage = event.page + 1;
        },

        handleProductClick(product) {
            console.log('clicking product', { product });

            const productSlug = slugify(product.name, {
                lower: false,
            });

            this.resultsStore.addCurrentProduct(product);
            this.$router.push(`/${productSlug}`);
        },

        handleMouseEntersCard(product) {
            this.mouseOverCard[product.id] = true
        },

        handleMouseLeavesCard(product) {
            delete this.mouseOverCard[product.id];
        },

        handleAddProdcutToShoppingCart(product) {
            this.shoppingCartStore.addProduct(product)
        }
    },
};
</script>

<template>
    <Header />

    <Breadcrumbs />

    <div class="mt-4 grid column-gap-4">
        <!-- Filters -->
        <div class="col-2">
            <div class="h-30rem flex flex-column">
                <span class="mb-2">Stanje</span>

                <div class="flex align-items-center mb-1">
                    <Checkbox
                        :v-model="false"
                        inputId="ingredient2"
                        name="pizza"
                        value="Mushroom"
                    />
                    <label for="ingredient2" class="ml-2"> Novo </label>
                </div>
                <div class="flex align-items-center mb-1">
                    <Checkbox
                        :v-model="false"
                        inputId="ingredient2"
                        name="pizza"
                        value="Mushroom"
                    />
                    <label for="ingredient2" class="ml-2"> Rabljeno </label>
                </div>

                <span class="mt-3 mb-2">Brend</span>
                <div
                    v-for="brand in filters.brand"
                    class="flex align-items-center mb-1"
                >
                    <!-- Checkbox -->
                    <Checkbox
                        :v-model="false"
                        inputId="ingredient2"
                        name="pizza"
                        value="Mushroom"
                    />
                    <label for="ingredient2" class="ml-2">
                        {{ brand }}
                    </label>
                </div>
            </div>
        </div>

        <!-- Results -->
        <div id="search-results" class="col grid">
            <div v-for="product in paginatedData" class="col-4">
                <!-- Product -->
                <Card
                    style="overflow: hidden"
                    class="cursor-pointer shadow-1 hover:shadow-5"
                    @click="handleProductClick(product)"
                    @mouseenter="handleMouseEntersCard(product)" 
                    @mouseleave="handleMouseLeavesCard(product)" 
                >
                    <template #header>
                        <img
                            v-if="product.pictureUrls[0]"
                            :src="product.pictureUrls[0]"
                            class="product-image border-200"
                        />
                    </template>
                    <template #title>
                        <span class="text-sm">{{
                            product.manufacturerName
                        }}</span>
                    </template>
                    <template #subtitle>
                        <span class="block text-sm h-4rem">
                            {{ product.name }}
                            <span class="block mt-1 text-sm"
                                >SKU: {{ product.sku }}</span
                            >
                        </span>
                    </template>
                    <template #content>
                        <div class="flex align-items-center justify-content-between h-3rem">
                            <span>{{ product.price.slice(0, 5) }} €</span>
                            <Button
                                v-if="mouseOverCard[product.id] "
                                icon="pi pi-cart-plus"
                                severity="secondary"
                                label="Dodaj u košaricu"
                                class="text-xs"
                                raised
                                @click.stop="handleAddProdcutToShoppingCart(product)"
                            />
                        </div>
                    </template>
                </Card>
            </div>
        </div>
    </div>

    <!-- Paginator -->
    <Paginator
        :rows="itemsPerPage"
        :totalRecords="totalItems"
        v-model="currentPage"
        @page="onPageChange"
        class="mt-4"
        style="background: none"
    />
</template>

<style scoped>
.product-image {
    width: 100%;
    height: 160px;
    object-fit: cover;
}
</style>
