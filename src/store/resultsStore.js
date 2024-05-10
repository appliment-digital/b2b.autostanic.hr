// lib
import { defineStore } from 'pinia';
import slug from 'slug';

// utils
import { setSlugCharMap, session } from '@/utils';

setSlugCharMap(slug);

export const useResultsStore = defineStore('results', {
    state: () => {
        return {
            currentResults: [],

            store: {
                product: session.load('results-store')?.product || null,
                productHistory:
                    session.load('results-store')?.productHistory || {},
            },
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
            if (state.store.product) {
                return state.store.product;
            } else {
                return session.load('results-store')?.product || null;
            }
        },
        getProductFromHistory: (state) => (productSlug) => () => {
            if (state.store.productHistory[productSlug]) {
                return state.store.productHistory[productSlug];
            }
        },
    },
    actions: {
        addResults(results) {
            this.currentResults = results;
            // console.log('adding results',{results});

            sessionStorage.setItem('search-results', JSON.stringify(results));
        },
        setProduct(product) {
            console.log('adding current product to the store', { product });
            this.store.product = product;
            this.store.productHistory[slug(product.name)] = product;

            session.save('results-store', this.store);
        },
    },
});
