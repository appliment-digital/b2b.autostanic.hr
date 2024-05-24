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
import { useCategoryStore } from '@/store/categoryStore.js';
import { useUIStore } from '@/store/UIStore.js';
import { useUserStore } from '@/store/userStore.js';

// services
import SupplierDetailService from '@/service/SupplierDetailService';

const supplierDetailService = new SupplierDetailService();

// modify slug library (add croatian chars)
setSlugCharMap(slug);

export default {
    props: {
        productCount: {
            type: Number,
        },
        products: {
            type: Array,
        },
        status: {
            type: Array,
        },
        manufacturers: {
            type: Object,
        },
        pageOptions: {
            type: Object,
        },
    },
    components: {
        Header,
        Breadcrumbs,
    },
    data() {
        return {
            first: 1,
            itemsPerPage: this.pageOptions.size,
            isChecked: false,

            filters: {
                status: [],
                manufacturers: {},
            },

            formattedMpcPrices: [],
            // results: {
            //     sort: {
            //         selected: null,
            //         options: [
            //             { label: 'Naziv: A do Z' },
            //             { label: 'Naziv: Z do A' },
            //             { label: 'Cijena: ⬆️' },
            //             { label: 'Cijena: ⬇️' },
            //         ],
            //     },
            // },
        };
    },
    mounted() {
        // load category data from storage
        const selectedCategory = this.categoryStore.selectedCategory;

        this.setFilters();
    },
    updated() {
        // console.log('results updated -> num of products:', {products: this.products});
    },
    watch: {},
    computed: {
        ...mapStores(
            useResultsStore,
            useShoppingCartStore,
            useCategoryStore,
            useUIStore,
            useUserStore,
        ),
        isAnyFilterActive() {
            // Check manufacturers filters
            const manufacturerActive = Object.values(
                this.filters.manufacturers,
            ).some((value) => value);
            // Check status filters
            const statusActive = this.filters.status.some(
                (filter) => filter.value,
            );

            return manufacturerActive || statusActive;
        },
    },
    watch: {
        filters: {
            handler: function (newValue) {
                this.filters.status = newValue.status || [];
                this.filters.manufacturers = newValue.manufacturers || {};

                this.first = 1;
                this.$emit('on-reset-paginator');
            },
            deep: true,
        },
    },
    methods: {
        setFilters() {
            this.filters.status = [];
            this.filters.manufacturers = {};

            const selectedFilters = { statusId: [], manufacturerName: [] };
            this.$emit(
                'on-filter-select',
                selectedFilters,
                this.categoryStore.selectedCategory.id,
            );

            this.manufacturers.forEach((entry) => {
                this.filters.manufacturers[entry] = false;
            });

            this.status.forEach((s) => {
                this.filters.status.push({
                    id: s.id,
                    name: s.name,
                    value: false,
                });
            });
        },
        handleProductClick(product) {
            this.resultsStore.setProduct(product);

            this.$router.push(`/${slug(product.name)}`);
        },

        handlePageChangeClick(event) {
            this.currentPage = event.page + 1;
            this.$emit('on-page-change', event);
        },

        /**
         * Handle user interaction with filter checkboxes (condition, manufacturers)
         */
        handleFilterSelect(event) {
            const { id } = event.target;

            // 2. create selected filters data

            const selectedFilters = { statusId: [], manufacturerName: [] };

            //  add status filters
            for (const status of this.filters.status) {
                if (status.value) {
                    selectedFilters.statusId.push(status.id);
                }
            }

            //  add manufacturer filters
            for (const key in this.filters.manufacturers) {
                if (this.filters.manufacturers[key]) {
                    selectedFilters.manufacturerName.push(`${key}`);
                }
            }

            // 3. emit event to call parent handler ('handleFilterSelect')

            this.$emit(
                'on-filter-select',
                selectedFilters,
                this.categoryStore.selectedCategory.id,
            );
        },

        handleNumOfResultsClick(val) {
            this.$emit('on-num-of-results-change', val);
        },

        formatProductStockQuantity(val) {
            return formatNumber(val);
        },

        formatProductPrice(val) {
            return formatPrice(val);
        },
        discountedPrice(price) {
            if (this.userStore.discount) {
                let discountAmount = (this.userStore.discount / 100) * price;
                return this.formatProductPrice(price - discountAmount);
            }
            return this.formatProductPrice(price);
        },
        getDetailsForProduct(product) {
            return supplierDetailService
                .getDetailsForProduct({
                    supplierId: product.supplierId,
                    categoryId: product.categoryId,
                    price: product.price,
                })
                .then((response) => {
                    return response.data;
                });
        },
    },
};
</script>

<template>
    <div class="grid column-gap-1">
        <!-- Filters -->
        <div class="col-3">
            <div class="flex flex-column">
                <Button
                    v-if="isAnyFilterActive"
                    class="button--no-shadow text-blue-500 bg-blue-100 border-blue-200 text-600 hover:bg-white-alpha-10 mb-2"
                    label="Poništi filtere"
                    severity="secondary"
                    icon="pi pi-filter-slash"
                    @click="setFilters()"
                />
                <Accordion :activeIndex="0">
                    <AccordionTab
                        header="Stanje"
                        :pt="{
                            headerAction: {
                                style: 'background-color: white;',
                            },
                            content: {
                                class: 'bg-white-alpha-60',
                            },
                        }"
                    >
                        <div class="mt-0">
                            <div
                                v-for="status in filters.status"
                                class="flex align-items-center mb-1 px-1"
                            >
                                <!-- Checkbox -->
                                <template v-if="status.id != 3">
                                    <Checkbox
                                        v-model="status.value"
                                        @change="handleFilterSelect"
                                        :binary="true"
                                        :pt="{
                                            box: {
                                                style: 'border-width: 1px',
                                            },
                                        }"
                                    />
                                    <label
                                        for="ingredient2"
                                        class="text-sm ml-2"
                                    >
                                        {{ status.name }}
                                    </label>
                                </template>
                            </div>
                        </div>
                    </AccordionTab>
                </Accordion>

                <Accordion :activeIndex="0">
                    <AccordionTab
                        class="border-0"
                        style="border: none"
                        header="Proizvođači"
                        :pt="{
                            headerAction: {
                                style: 'background-color: white;',
                            },
                            content: {
                                style: 'background-color: white;',
                                class: 'bg-white-alpha-60',
                            },
                        }"
                    >
                        <!-- manufacturer -->
                        <div class="mt-0 max-h-30rem overflow-y-scroll">
                            <div
                                v-for="manufacturer in manufacturers"
                                class="flex align-items-center mb-1 px-1"
                            >
                                <!-- Checkbox -->
                                <Checkbox
                                    v-if="manufacturer"
                                    v-model="
                                        filters.manufacturers[manufacturer]
                                    "
                                    @change="handleFilterSelect"
                                    :binary="true"
                                    :pt="{
                                        box: {
                                            style: 'border-width: 1px',
                                        },
                                    }"
                                />
                                <label
                                    v-if="manufacturer"
                                    for="ingredient2"
                                    class="text-sm ml-2"
                                >
                                    {{ manufacturer }}
                                </label>
                            </div>
                        </div>
                    </AccordionTab>
                </Accordion>
            </div>
        </div>

        <!-- Results -->
        <div class="col">
            <div class="border-1 border-100 bg-white-alpha-60 border-round p-3">
                <div
                    v-if="products.length > 0"
                    class="flex justify-content-center text-red-400"
                >
                    Cijene su iskazane bez PDV-a
                </div>
                <span v-if="!products.length" class="block h-2rem"
                    >Nema rezultata pretrage...</span
                >
                <div
                    v-else
                    class="ml-2 mb-3 flex align-items-center justify-content-between"
                >
                    <div class="flex align-items-center">
                        <span class="block mr-4"
                            >Proizvodi
                            <span class="text-red-400 font-bold">{{
                                productCount
                            }}</span></span
                        >
                    </div>
                    <div class="flex align-items-center">
                        <!-- <label for="sort-dropdown" class="mr-2"
                            >Poredaj po</label
                        >
                        <Dropdown
                            inputId="sort-dropdown"
                            v-model="results.sort.selected"
                            :options="results.sort.options"
                            optionLabel="label"
                            placeholder="Pozicija"
                            class="w-full md:w-12rem"
                        /> -->
                        <span v-if="productCount > 24" class="ml-3 mr-2"
                            >Broj rezultata</span
                        >
                        <Button
                            v-if="productCount > 24"
                            class="button--no-shadow mr-1 text-xs p-0 border-100"
                            :class="
                                pageOptions.size === 24 &&
                                'bg-blue-100 border-blue-200'
                            "
                            style="width: 32px; height: 32px"
                            label="24"
                            outlined
                            severity="primary"
                            @click="handleNumOfResultsClick(24)"
                        />
                        <Button
                            v-if="productCount > 36"
                            class="button--no-shadow mr-1 text-xs p-0 border-100"
                            :class="
                                pageOptions.size === 36 &&
                                'bg-blue-100 border-blue-200'
                            "
                            style="width: 32px; height: 32px"
                            label="36"
                            outlined
                            severity="primary"
                            @click="handleNumOfResultsClick(36)"
                        />
                        <Button
                            v-if="productCount > 48"
                            class="button--no-shadow text-xs p-0 border-100"
                            :class="
                                pageOptions.size === 48 &&
                                'bg-blue-100 border-blue-200'
                            "
                            style="width: 32px; height: 32px"
                            label="48"
                            outlined
                            severity="primary"
                            @click="handleNumOfResultsClick(48)"
                        />
                    </div>
                </div>

                <div id="search-results" class="grid">
                    <div
                        v-for="(product, index) in products"
                        :key="product.id"
                        class="col-12 md:col-4"
                    >
                        <!-- Product -->
                        <Card
                            :pt="{
                                header: {
                                    style: 'padding: 1.25rem; padding-bottom: 0',
                                    id: 'myPanelHeader',
                                    class: [
                                        {
                                            '.card-header--fixed-height':
                                                product.hasDefaultImage,
                                        },
                                    ],
                                },
                                content: {
                                    class: ['p-0'],
                                },
                            }"
                            style="overflow: hidden"
                            class="relative cursor-pointer shadow-1 hover:shadow-5 border-1 border-100"
                            @click="handleProductClick(product)"
                        >
                            <template #header>
                                <!-- prettier-ignore -->
                                <div
                                    v-if="product.hasDefaultImage"
                                    class="card-header--fixed-height flex 
                                    justify-content-center align-items-center"
                                    src="/images/as_logo_single.png"
                                >
                                    <img
                                        v-if="product.pictureUrl"
                                        :src="product.pictureUrl"
                                        class="product-image--default border-200"
                                    />
                                </div>
                                <div v-else>
                                    <img
                                        v-if="product.pictureUrl"
                                        :src="product.pictureUrl"
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
                                    <!-- prettier-ignore -->
                                    <span v-if="Boolean(Number(product.isNewPart)) || Boolean(Number(product.isUsedPart))"
                                        class="absolute top-0 right-0 inline-block
                                        p-2 text-center opacity-90
                                        text-white
                                        "
                                        style="margin-top: 1.25rem; margin-right: 1.25rem; border-radius: 4px; min-width: 74px; background-color: #123649;"
                                    >
                                    <span 
                                        class="text-xs font-bold"
                                        style="letter-spacing: 1px"
                                        v-if="
                                            Boolean(Number(product.isNewPart))
                                        "
                                        >NOVO</span
                                    >
                                    <span 
                                    class="text-xs font-bold"
                                    style="letter-spacing: 1px"
                                        v-if="
                                            Boolean(Number(product.isUsedPart))
                                        "
                                        >RABLJENO</span
                                    >
                                </span>
                                </span>
                            </template>
                            <template #content>
                                <div
                                    class="flex align-items-center justify-content-between"
                                >
                                    <div class="flex flex-column">
                                        <span class="text-blue-500 mr-2"
                                            >VPC
                                            {{
                                                formatProductPrice(
                                                    product.price,
                                                )
                                            }}
                                            €</span
                                        >

                                        <span class="text-green-500"
                                            >MPC
                                            {{ discountedPrice(product.price) }}
                                            €</span
                                        >
                                    </div>
                                    <span
                                        class="block mt-1 text-sm border-1 border-solid border-round-sm px-2 py-1"
                                    >
                                        <span
                                            v-if="product.stockQuantity > 0"
                                            class=""
                                            >Dostupno:
                                            <span class="font-bold">{{
                                                formatProductStockQuantity(
                                                    product.stockQuantity,
                                                )
                                            }}</span>
                                        </span>
                                        <span v-else> Nedostupno </span>
                                    </span>
                                </div>
                            </template>
                        </Card>
                    </div>
                </div>
                <Paginator
                    v-if="productCount > 24"
                    :rows="itemsPerPage"
                    :totalRecords="productCount"
                    v-model:first="first"
                    v-model:page="currentPage"
                    @page="handlePageChangeClick"
                    class="p-0 mt-4"
                    currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
                >
                </Paginator>
            </div>
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
