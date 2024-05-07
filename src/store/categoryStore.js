// pinia
import { defineStore } from 'pinia';

// lib
import slug from 'slug';

// utils
import { setSlugCharMap } from '@/utils';

// modify slug library (add croatian chars)
setSlugCharMap(slug);

export const useCategoryStore = defineStore('category', {
    state: () => {
        return {
            categoryHistory:
                JSON.parse(sessionStorage.getItem('category-history')) || [],

            addMainCategoriesList: null,
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
            const entry = this.categoryHistory.find(
                (entry) => entry.id == category.id,
            );

            if (entry) return;

            this.categoryHistory.push({
                id: category.id,
                name: category.name,
                url: slug(category.name, { lower: true }),
            });

            sessionStorage.setItem(
                'category-history',
                JSON.stringify(this.categoryHistory),
            );
        },

        addMainCategories(categories) {},
    },
});
