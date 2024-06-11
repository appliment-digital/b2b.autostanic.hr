<script>
import ProductService from '@/service/ProductService.js';

import Results from '@/components/Results.vue';

export default {
    components: {
        Results,
    },
    mounted() {
        this.term = this.$route.query.q;
        this.getHeaderSearchResults();
    },
    data() {
        return {
            term: null,
            page: {
                current: 1,
                size: 24,
            },
            products: [],
            productCount: null,
            status: [],
            manufacturers: [],
            isSearchDone: false,
        };
    },
    watch: {
        '$route.query.q'(newValue, oldValue) {
            if (newValue !== oldValue) {
                this.term = newValue;
                this.getHeaderSearchResults();
            }
        },
    },
    methods: {
        getHeaderSearchResults() {
            ProductService.searchProductsByTerm(
                this.page.current,
                this.page.size,
                this.term,
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
    },
};
</script>

<template>
    <p class="text-xl">
        <span class="font-bold">Rezultati pretrage:</span>
        <span class="bg-blue-100 p-1 border-round-md">{{ this.term }}</span>
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
