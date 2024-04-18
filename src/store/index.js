import { defineStore } from 'pinia';

export const useUserStore = defineStore('user', {
    state: () => {
        return {
            count: 0,
            name: 'Eduardo',
        };
    },
    getters: {
        counter: (state) => state.count,
        user: (state) => state.name,
    },
    actions: {
        increment() {
            console.log('incrementing...');
            this.count++;
        },
        changeUser(name) {
            this.name = name;
        },
    },
});
