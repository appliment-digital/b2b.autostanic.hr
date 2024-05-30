// lib
import slug from 'slug';

// pinia
import { defineStore } from 'pinia';

// utils
import { setSlugCharMap } from '@/utils';

// storage
import { session, local } from '@/utils/browser-storage';

// modify slug library (add croatian chars)
setSlugCharMap(slug);

export const useCategoryStore = defineStore('category', {
    state: () => {
        return {
            store: {
                session: {
                    mainCategories:
                        session.load('category-store')?.mainCategories || null,
                    selectedCategory:
                        session.load('category-store')?.selectedCategory ||
                        null,
                },
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
            return state.store.session.mainCategories;
        },
        selectedCategory: (state) => {
            return state.store.session.selectedCategory;
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
            this.store.session.mainCategories = categories.map((category) => ({
                id: category.id,
                url: slug(category.name),
                name: category.name,
            }));

            session.save('category-store', this.store.session);
        },
        setSelectedCategory(category) {
            this.store.session.selectedCategory = category;

            session.save('category-store', this.store.session);
        },
    },
});
