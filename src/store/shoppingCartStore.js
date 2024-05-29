import { defineStore } from 'pinia';

const calcTotal = (items) => {
    return items.reduce((total, item) => total + +item.price, 0);
};

export const useShoppingCartStore = defineStore('shoppingCart', {
    state: () => {
        return {
            shoppingCart:
                JSON.parse(localStorage.getItem('shopping-cart')) || [],

            currentTotalWithoutFees: 0,
            currentTotalWithFees: 0,
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

        total: (state) => {
            if (state.shoppingCart.length) {
                const total = state.shoppingCart.reduce((total, product) => {

                    total += Number(product.price * product.quantity);
                    return total;
                }, 0);

                return total;
            }
        },
    },
    actions: {
        add(product) {
            // figure out if product already exists in the cart
            const entryId = this.shoppingCart.findIndex(
                (entry) => entry.id == product.id,
            );

            if (entryId != -1) {
                let newQuantity =
                    Number(this.shoppingCart[entryId].quantity) +
                    Number(product.quantity);

                if (newQuantity > product.stockQuantity) {
                    newQuantity = Number(product.stockQuantity);
                }

                this.shoppingCart[entryId].quantity = newQuantity;
            } else {
                this.shoppingCart.push(product);
            }

            localStorage.setItem(
                'shopping-cart',
                JSON.stringify(this.shoppingCart),
            );
        },

        delete(product) {
            this.shoppingCart = this.shoppingCart.filter(
                (entry) => entry.id !== product.id,
            );

            localStorage.setItem(
                'shopping-cart',
                JSON.stringify(this.shoppingCart),
            );
        },
    },
});
