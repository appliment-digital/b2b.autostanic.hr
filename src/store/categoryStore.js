// pinia
import { defineStore } from 'pinia';

// utils
import { makeUrl } from '@/utils';

export const useCategoryStore = defineStore('category', {
    state: () => {
        return {
            categoryHistory:
                JSON.parse(sessionStorage.getItem('category-history')) || [],
        };
    },
    getters: {
        history: (state) => {
            if (state.categoryHistory.length) {
                return state.categoryHistory;
            } else {
                return JSON.parse(sessionStorage.getItem('category-history'));
            }
        },
    },
    actions: {
        // not using
        clearHistory() {
            this.categoryHistory = [];

            sessionStorage.setItem(
                'category-history',
                JSON.stringify(this.categoryHistory),
            );
        },
        addHistory(category) {
            this.categoryHistory.push({
                id: category.id,
                name: category.name,
                url: makeUrl(category.name),
            });

            sessionStorage.setItem(
                'category-history',
                JSON.stringify(this.categoryHistory),
            );
        },
    },
});
