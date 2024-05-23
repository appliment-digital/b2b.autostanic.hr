import { defineStore } from 'pinia';

export const useUserStore = defineStore('user', {
    state: () => {
        return {
            count: 0,
            user: null,
            isLoggedIn: false,
            robohashName: '',
            isAdmin: false,
        };
    },
    getters: {
        counter: (state) => state.count,
        initials: (state) => {
            if (state.user) {
                const { name, last_name: lastName } = state.user;
                return `${name.charAt(0)}${lastName.charAt(0)}`;
            }

            const storedInitials = localStorage.getItem('userInitials');
            if (storedInitials) {
                return storedInitials;
            }
        },
        fullName: (state) => {
            if (state.user) {
                const { name, last_name: lastName } = state.user;
                return `${name} ${lastName}`;
            }

            const storedFullName = localStorage.getItem('userFullName');
            if (storedFullName) {
                return storedFullName;
            }
        },
        isUserAdmin: (state) => {
            if (state.isAdmin) {
                return state.isAdmin;
            } else {
                return JSON.parse(sessionStorage.getItem('user-isAdmin'));
            }
        },
        discount: (state) => {
            if (state.user && state.user.discount) {
                return state.user.discount;
            } else {
                const discount = JSON.parse(
                    localStorage.getItem('userData'),
                ).discount;
                return discount;
            }
        },
    },
    actions: {
        addUser(data) {
            this.user = data;

            localStorage.setItem('userData', JSON.stringify(data));

            this.setIsAdmin(false);

            if (
                data.role &&
                data.role.length &&
                data.role[0].name === 'admin'
            ) {
                this.setIsAdmin(true);
            }

            // add initials to the local storage
            const { name, last_name: lastName } = data;

            localStorage.setItem(
                'userInitials',
                `${name.charAt(0)}${lastName.charAt(0)}`,
            );

            localStorage.setItem('userFullName', `${name} ${lastName}`);
        },
        login() {
            this.isLoggedIn = true;
            localStorage.setItem('isLoggedIn', true);
        },
        logout() {
            this.isLoggedIn = false;
            this.setIsAdmin(false);

            localStorage.setItem('isLoggedIn', false);
        },
        robohash(name) {
            this.robohashName = name;
        },
        setIsAdmin(val) {
            this.isAdmin = val;

            sessionStorage.setItem('user-isAdmin', val);
        },
    },
});
