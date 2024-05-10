// pinia
import { defineStore } from 'pinia';

// lib
import slug from 'slug';

// utils
import { setSlugCharMap, session } from '@/utils';

// modify slug library (add croatian chars)
setSlugCharMap(slug);

export const useCategoryStore = defineStore('category', {
    state: () => {
        return {
            categoryHistory:
                JSON.parse(sessionStorage.getItem('category-history')) || [],

            store: {
                mainCategories: session.load('category-store')?.mainCategories,
            },
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
        mainCategories: (state) => {
            if (state.store.mainCategories) {
                return state.store.mainCategories
            } else {
                return session.load('category-store')?.mainCategories
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

        addMainCategories(categories) {
            this.store.mainCategories = categories;

            session.save('category-store', this.store);
        },
    },
});
