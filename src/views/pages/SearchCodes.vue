<script>
// pinia
import { mapStores } from 'pinia';
import { useResultsStore } from '@/store/resultsStore';
import Results from '@/components/Results.vue';

import ProductService from '@/service/ProductService.js';
export default {
    components: {
        Results,
    },
    data() {
        return {
            page: {
                current: 1,
                size: 24,
            },
            searchCodeType: [
                { name: 'Broj artikla', key: 'sku' },
                { name: 'Broj dijela', key: 'oem' },
                { name: 'Šifra motora', key: 'EngineCode' },
                { name: 'Šifra mjenjača', key: 'GearboxCode' },
                { name: 'Šifra proizvođača', key: 'CrossReference' },
            ],
            code: null,
            value: null,
            products: [],
            productCount: null,
            status: [],
            manufacturers: [],
            isSearchDone: false,
        };
    },
    computed: {
        ...mapStores(useResultsStore),
        searchCode() {
            return this.searchCodeType.find(
                (c) => c.key == this.$route.query.code,
            ).name;
        },
    },
    mounted() {
        this.code = this.$route.query.code;
        this.value = this.$route.query.value;
        this.getSearchResults();
        this.handleDefaultProductImage(this.products);
    },
    watch: {
        '$route.query.code'(newValue, oldValue) {
            if (newValue !== oldValue) {
                this.getSearchResults();
            }
        },
        '$route.query.value'(newValue, oldValue) {
            if (newValue !== oldValue) {
                this.getSearchResults();
            }
        },
    },
    methods: {
        getSearchResults() {
            ProductService.getProductsByCodeAndTerm(
                this.page.current,
                this.page.size,
                this.code,
                this.value,
                {},
            )
                .then((response) => {
                    var data = response.data;
                    this.products = data.products;
                    this.productCount = data.productCount;
                    this.status = data.status;
                    this.manufacturers = data.manufacturers;
                    this.isSearchDone = true;
                })
                .catch((err) => console.error(err));
        },
        handleDefaultProductImage(products) {
            products.forEach((entry) => {
                if (
                    entry.pictureUrl &&
                    entry.pictureUrl ===
                        'https://www.autostanic.hr/content/images/thumbs/default-image_280.png'
                ) {
                    entry.hasDefaultImage = 'true';
                    entry.pictureUrl = '/images/as_logo_single.png';
                }
            });
        },
    },
};
</script>

<template>
    <p class="text-xl">
        <span class="font-bold mr-1">Rezultati pretrage:</span>
        <span class="bg-blue-100 p-1 border-round-md"
            >{{ this.code }} = "{{ this.value }}"</span
        >
    </p>
    <Results
        v-if="isSearchDone"
        :productCount="productCount"
        :products="products"
        :status="status"
        :manufacturers="manufacturers"
        :pageOptions="page"
    />
</template>
