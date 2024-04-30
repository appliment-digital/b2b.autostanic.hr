import { defineStore } from 'pinia';

export const useResultsStore = defineStore('results', {
    state: () => {
        return {
            currentResults: [],
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
    },
    actions: {
        addResults(results) {
            this.currentResults = results;

            sessionStorage.setItem('search-results', JSON.stringify(results));
        },
    },
});
