// lib
import { defineStore } from 'pinia';

// utils
import { session } from '@/utils';

export const useUIStore = defineStore('UI', {
    state: () => ({
        store: {
            isDataLoading: session.load('ui-store')?.isDataLoading,
        },
    }),
    getters: {
        isDataLoading: (state) => {
            if (state.store.isDataLoading !== undefined) {
                return state.store.isDataLoading;
            } else {
                return session.load('ui-store')?.isDataLoading;
            }
        },
    },
    actions: {
        setIsDataLoading(val) {
            this.store.isDataLoading = val;

            session.save('ui-store', this.store);
        },
    },
});
