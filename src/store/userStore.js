import { defineStore } from 'pinia';

// storage
import { session, local } from '@/utils/browser-storage';

export const useUserStore = defineStore('user', {
    state: () => {
        return {
            store: {
                local: {
                    user: local.load('user-store')?.user || {},
                },
            },
        };
    },
    getters: {
        initials: (state) => {
            return state.store.local.user.initials;
        },

        role: (state) => {
            if (state.store.local.user.role) {
                return state.store.local.user.role[0].name;
            }
        },

        fullName: (state) => {
            return state.store.local.user.fullName;
        },

        discount: (state) => {
            return state.store.local.user.discount;
        },
    },
    actions: {
        add(user) {
            const { name, last_name: lastName } = user;

            this.store.local.user = user;
            this.store.local.user.initials = `${name.charAt(0)}${lastName.charAt(0)}`;
            this.store.local.user.fullName = `${name} ${lastName}`;

            local.save('user-store', this.store.local);
        },
    },
});
