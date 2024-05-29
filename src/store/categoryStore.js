// pinia
import { defineStore } from 'pinia';

// lib
import slug from 'slug';

// utils
import { setSlugCharMap, session, local } from '@/utils';

// modify slug library (add croatian chars)
setSlugCharMap(slug);

export const useCategoryStore = defineStore('category', {
    state: () => {
        return {
            store: {
                mainCategories:
                    session.load('category-store')?.mainCategories || null,
                selectedCategory:
                    session.load('category-store')?.selectedCategory || null,

                local: {
                    history: local.load('category-store')?.history || [],
                },
            },
        };
    },
    getters: {
        history: (state) => {
            return state.store.local.history;
        },
        mainCategories: (state) => {
            return state.store.mainCategories;
        },
        selectedCategory: (state) => {
            return state.store.selectedCategory;
        },
    },
    actions: {
        addHistory(category) {
            const entry = this.store.local.history.find(
                (entry) => entry.id == category.id,
            );

            if (entry) return;

            this.store.local.history.push({
                id: category.id,
                url: slug(category.name),
            });

            local.save('category-store', this.store.local);
        },
        addMainCategories(categories) {
            this.store.mainCategories = categories;

            session.save('category-store', this.store);
        },
        setSelectedCategory(category) {
            this.store.selectedCategory = category;

            session.save('category-store', this.store);
        },
    },
});
