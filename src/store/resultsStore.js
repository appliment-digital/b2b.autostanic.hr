// lib
import { defineStore } from 'pinia';
import slug from 'slug';

// utils
import { setSlugCharMap, session } from '@/utils';

setSlugCharMap(slug);

export const useResultsStore = defineStore('results', {
    state: () => {
        return {
            store: {
                product: session.load('results-store')?.product || null,
                productHistory:
                    session.load('results-store')?.productHistory || {},
            },
        };
    },
    getters: {
        product: (state) => {
            if (state.store.product) {
                return state.store.product;
            } else {
                return session.load('results-store')?.product || null;
            }
        },
    },
    actions: {
        addResults(results) {
            this.currentResults = results;

            sessionStorage.setItem('search-results', JSON.stringify(results));
        },
        setProduct(product) {
            this.store.product = product;

            session.save('results-store', this.store);
        },
    },
});
