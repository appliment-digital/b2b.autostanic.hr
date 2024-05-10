// lib
import { defineStore } from 'pinia';

// utild
import { session } from '@/utils';

export const useBreadcrumbsStore = defineStore('breadcrumbs', {
    state: () => {
        return {
            currentBreadcrumbs:
                JSON.parse(sessionStorage.getItem('current-breadcrumbs')) ||
                null,
            isProductLastSeen: null,

            store: {
                productBreadcrumbsHistory:
                    session.load('breadcrumbs-store')
                        ?.productBreadcrumbsHistory || {},
            },
        };
    },
    getters: {
        current: (state) => {
            if (state.currentBreadcrumbs) {
                return state.currentBreadcrumbs;
            } else {
                return JSON.parse(
                    sessionStorage.getItem('current-breadcrumbs'),
                );
            }
        },
        isProductLast: (state) => {
            if (state.isProductLastSeen === null) {
                return JSON.parse(
                    sessionStorage.getItem('isProduct-last-seen'),
                );
            } else {
                return state.isProductLastSeen;
            }
        },
        productBreadcrumbsHistory: (state) => {
            if (state.store.productBreadcrumbsHistory) {
                return state.store.productBreadcrumbsHistory;
            } else {
                return session.load('breadcrumbs-store')
                    ?.productBreadcrumbsHistory;
            }
        },
    },
    actions: {
        haveFun() {
            console.log('having fun');
        },

        set(breadcrumbs) {
            console.log('setting breadcrumbs', { breadcrumbs });
            this.currentBreadcrumbs = breadcrumbs;

            sessionStorage.setItem(
                'current-breadcrumbs',
                JSON.stringify(this.currentBreadcrumbs),
            );
        },
        setIsProductLastSeen(val) {
            this.isProductLastSeen = Boolean(val);

            sessionStorage.setItem(
                'isProduct-last-seen',
                JSON.stringify(this.isProductLastSeen),
            );
        },
        addProductBreadcrumbsHistory(product, breadcrumbs) {
            this.store.productBreadcrumbsHistory[product] = breadcrumbs;

            console.log('adding product breadcrumbs history', {
                product,
                breadcrumbs,
                store: this.store.productBreadcrumbsHistory,
            });

            session.save('breadcrumbs-store', this.store);
        },
    },
});
