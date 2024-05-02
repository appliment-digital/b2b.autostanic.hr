import { defineStore } from 'pinia';

export const useShoppingCartStore = defineStore('shoppingCart', {
    state: () => {
        return {
            shoppingCart: JSON.parse(localStorage.getItem('shopping-cart')) || [],
        };
    },
    getters: {
        cart: (state) => {
            if (state.shoppingCart.length) {
                return state.shoppingCart;
            } else {
                return JSON.parse(localStorage.getItem('shopping-cart'));
            }
        },
    },
    actions: {
        addProduct(item) {
            console.log('item added to shopping cart...');
            this.shoppingCart.push(item);
            console.log({shoppingCart: this.shoppingCart});

            localStorage.setItem(
                'shopping-cart',
                JSON.stringify(this.shoppingCart),
            );
        },
    },
});
