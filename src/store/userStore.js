import { defineStore } from 'pinia';

export const useUserStore = defineStore('user', {
    state: () => {
        return {
            count: 0,
            user: null,
            isLoggedIn: false,
        };
    },
    getters: {
        counter: (state) => state.count,
        initials: (state) => {
            if (state.user) {
                const { name, last_name: lastName } = state.user;
                return `${name.charAt(0)}${lastName.charAt(0)}`;
            }

            const storedInitials = localStorage.getItem('userInitials')
            if (storedInitials) {
                return storedInitials
            }
            
        },
        fullName: (state) => {
            if (state.user) {
                const { name, last_name: lastName } = state.user;
                return `${name} ${lastName}`;
            }

            const storedFullName = localStorage.getItem('userFullName')
            if (storedFullName) {
                return storedFullName
            }
        },
    },
    actions: {
        increment() {
            console.log('incrementing...');
            this.count++;
        },
        addUser(data) {
            console.log('adding user to the store...', { userData: data });
            this.user = data;

            // add initials to the local storage
            const { name, last_name: lastName } = data;
            localStorage.setItem('userInitials', `${name.charAt(0)}${lastName.charAt(0)}`)
            localStorage.setItem('userFullName', `${name} ${lastName}`)
        },
        login() {
            this.isLoggedIn = true;
            localStorage.setItem('isLoggedIn', true) 
        },
        logout() {
            this.isLoggedIn = false;
            localStorage.setItem('isLoggedIn', false) 
        }
    },
});
