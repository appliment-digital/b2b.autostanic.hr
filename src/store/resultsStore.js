// lib
import { defineStore } from 'pinia';

// storage
import { session, local } from '@/utils/browser-storage';

export const useResultsStore = defineStore('results', {
    state: () => ({
        store: {
            session: {
                searchcodes: session.load('results-store')?.searchcodes || {},
            },
        },
    }),
    getters: {
        searchcodes: (state) => {
            return state.store.session.searchcodes;
        },
    },
    actions: {
        setSearchCodesResult(data) {
            this.store.session.searchcodes = data;

            session.save('results-store', this.store.session);
        },
    },
});
