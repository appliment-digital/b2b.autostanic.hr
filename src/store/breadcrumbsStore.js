// lib
import { defineStore } from 'pinia';

// storage
import { session, local } from '@/utils/browser-storage';

export const useBreadcrumbsStore = defineStore('breadcrumbs', {
    state: () => {
        return {
            store: {
                local: {
                    products: local.load('breadcrumbs-store')?.products || {},
                    current: local.load('breadcrumbs-store')?.current || [],
                },
            },
        };
    },
    getters: {
        current: (state) => {
            return state.store.local.current;
        },

        products: (state) => {
            return state.store.local.products;
        },
    },
    actions: {
        set(breadcrumbs) {
            this.store.local.current = breadcrumbs;
            local.save('breadcrumbs-store', this.store.local);
        },

        addProductCrumbs(id, breadcrumbs) {
            this.store.local.products[id] = breadcrumbs;
            local.save('breadcrumbs-store', this.store.local);
        },
    },
});
