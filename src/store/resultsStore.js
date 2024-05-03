import { defineStore } from 'pinia';

export const useResultsStore = defineStore('results', {
    state: () => {
        return {
            currentResults: [],
            currentProduct: null,
        };
    },
    getters: {
        searchResults: (state) => {
            if (state.currentResults.length) {
                return state.currentResults;
            } else {
                return JSON.parse(sessionStorage.getItem('search-results'));
            }
        },
        product: (state) => {
            if (state.currentProduct) {
                return state.currentProduct;
            } else {
                return JSON.parse(sessionStorage.getItem('current-product'));
            }
        },
    },
    actions: {
        addResults(results) {
            this.currentResults = results;
            console.log('adding results',{results});

            sessionStorage.setItem('search-results', JSON.stringify(results)); 
        },
        addCurrentProduct(product) {
            this.currentProduct = product;

            sessionStorage.setItem('current-product', JSON.stringify(product)); 
        }
    },
});
