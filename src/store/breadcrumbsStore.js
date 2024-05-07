import { defineStore } from 'pinia';

export const useBreadcrumbsStore = defineStore('breadcrumbs', {
    state: () => {
        return {
            currentBreadcrumbs:
                JSON.parse(sessionStorage.getItem('current-breadcrumbs')) ||
                null,
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
    },
    actions: {
        set(breadcrumbs) {
            this.currentBreadcrumbs = breadcrumbs;

            sessionStorage.setItem(
                'current-breadcrumbs',
                JSON.stringify(this.currentBreadcrumbs),
            );
        },
    },
});
