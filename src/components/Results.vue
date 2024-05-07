<script>
// lib
import slug from 'slug';

// utils
import { setSlugCharMap, formatNumber, formatPrice } from '@/utils';

// components
import Header from '@/components/Header.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';

// pinia
import { mapStores } from 'pinia';
import { useResultsStore } from '@/store/resultsStore.js';
import { useShoppingCartStore } from '@/store/shoppingCartStore.js';

// modify slug library (add croatian chars)
setSlugCharMap(slug);

export default {
    props: ['results', 'resultsFilter', 'productCount'],
    components: {
        Header,
        Breadcrumbs,
    },
    data() {
        return {
            itemsPerPage: 9,
            totalItems: Number(this.productCount),
            isChecked: false,

            checkboxesStates: {},

            filters: {},
        };
    },
    mounted() {
        console.log('results component props: ', {
            totalItems: this.totalItems,
            resultsFilter: this.resultsFilter,
        });

        console.log(this.resultsFilter);
        // create checkboxes states
        this.resultsFilter.manufacturers.forEach((filter) => {
            this.checkboxesStates[filter] = false;
        });

        console.log({ checkboxesStates: this.checkboxesStates });
    },
    computed: {
        ...mapStores(useResultsStore, useShoppingCartStore),
    },
    methods: {
        handleProductClick(product) {
            const productSlug = slug(product.name, { lower: false });

            this.resultsStore.addCurrentProduct(product);
            this.$router.push(`/${productSlug}`);
        },

        handlePageChangeClick(event) {
            this.$emit('on-page-change', event);
        },

        handleFilterSelect() {
            this.$emit('on-filter-select', this.checkboxesStates);
        },

        formatProductStockQuantity(val) {
            return formatNumber(val);
        },

        formatProductPrice(val) {
            return formatPrice(val);
        },
    },
};
</script>

<template>
    <div class="grid column-gap-4">
        <!-- Filters -->
        <div class="col-2">
            <div class="flex flex-column">
                <span class="mb-2">Stanje</span>

                <div class="flex align-items-center mb-1">
                    <Checkbox
                        inputId="ingredient2"
                        :v-model="false"
                        :binary="true"
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

                <!-- manufacturer -->
                <div class="mt-3 max-h-30rem overflow-y-scroll">
                    <span class="block mt-3 mb-2">Proizvođači</span>
                    <div
                        v-for="manufacturer in resultsFilter.manufacturers"
                        class="flex align-items-center mb-1 px-1"
                    >
                        <!-- Checkbox -->
                        <Checkbox
                            v-if="manufacturer"
                            v-model="checkboxesStates[manufacturer]"
                            @change="handleFilterSelect"
                            :binary="true"
                            :name="manufacturer"
                        />
                        <label
                            v-if="manufacturer"
                            for="ingredient2"
                            class="ml-2"
                        >
                            {{ manufacturer }}
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results -->
        <div class="col">
            <div id="search-results" class="grid">
                <div v-for="product in results" class="col-4">
                    <!-- Product -->
                    <Card
                        :pt="{
                            header: {
                                style: 'padding: 1.25rem',
                                id: 'myPanelHeader',
                                class: [
                                    {
                                        '.card-header--fixed-height':
                                            product.hasDefaultImage,
                                    },
                                ],
                            },
                        }"
                        style="overflow: hidden"
                        class="cursor-pointer shadow-1 hover:shadow-5"
                        @click="handleProductClick(product)"
                    >
                        <template #header>
                            <div
                                v-if="product.hasDefaultImage"
                                class="card-header--fixed-height flex justify-content-center align-items-center"
                                src="/images/as_logo_single.png"
                            >
                                <img
                                    v-if="product.picture_urls[0]"
                                    :src="product.picture_urls[0]"
                                    class="product-image--default border-200"
                                />
                            </div>
                            <div v-else>
                                <img
                                    v-if="product.picture_urls[0]"
                                    :src="product.picture_urls[0]"
                                    class="product-image border-200"
                                />
                            </div>
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
                            <div
                                class="flex align-items-center justify-content-between h-3rem"
                            >
                                <span
                                    >{{
                                        formatProductPrice(product.price)
                                    }}
                                    €</span
                                >
                                <span class="block mt-1 text-sm"
                                    >Stanje:
                                    <span
                                        :class="
                                            product.stockQuantity == 0
                                                ? 'text-red-500'
                                                : 'text-green-500'
                                        "
                                        >{{
                                            formatProductStockQuantity(
                                                product.stockQuantity,
                                            )
                                        }}</span
                                    ></span
                                >
                                <!--
                                <Button
                                    icon="pi pi-cart-plus"
                                    severity="secondary"
                                    class="text-xs border-100"
                                    outlined
                                    rounded
                                    @click.stop="
                                        handleAddProdcutToShoppingCart(product)
                                    "
                                />
                                -->
                            </div>
                        </template>
                    </Card>
                </div>
            </div>

            <Paginator
                :rows="itemsPerPage"
                :totalRecords="totalItems"
                v-model="currentPage"
                @page="handlePageChangeClick"
                class="mt-4"
            />
        </div>
    </div>
</template>

<style scoped>
.product-image {
    display: block;
    width: 100%;
    height: 120px;
    object-fit: cover;
    object-position: center;
    border-radius: 5px;
}

.product-image--default {
    width: 48px;
    height: 48px;
    object-fit: cover;
    border-radius: 5px;
    opacity: 0.1;
}

.card-header--fixed-height {
    height: 120px;
}
</style>
