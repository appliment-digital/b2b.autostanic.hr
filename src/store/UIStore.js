// lib
import { defineStore } from 'pinia';

// storage
import { session, local } from '@/utils/browser-storage';

export const useUIStore = defineStore('UI', {
    state: () => ({
        store: {
            session: {
                isDataLoading: session.load('ui-store')?.isDataLoading,
            }
        },
    }),
    getters: {
        isDataLoading: (state) => {
            if (state.store.session.isDataLoading !== undefined) {
                return state.store.session.isDataLoading;
            } else {
                return session.load('ui-store')?.isDataLoading;
            }
        },
    },
    actions: {
        setIsDataLoading(val) {
            this.store.session.isDataLoading = val;
            session.save('ui-store', this.store.session);
        },
    },
});
