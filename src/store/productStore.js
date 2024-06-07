import { defineStore } from 'pinia';

// storage
import { session, local } from '@/utils/browser-storage';

export const useProductStore = defineStore('product', {
    state: () => {
        return {
            store: {
                session: {
                    products: session.load('product-store')?.products || {},
                },
            },
        };
    },
    getters: {
        products(state) {
            return state.store.session.products;
        },
    },
    actions: {
        add({ product, details, breadcrumbs }) {
            this.store.session.products[product.id] = {
                ...product,
                details,
                breadcrumbs
            };

            session.save('product-store', this.store.session);
        },
    },
});
