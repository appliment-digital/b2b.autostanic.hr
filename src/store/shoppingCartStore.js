import { defineStore } from 'pinia';

const calcTotal = (items) => {
    return items.reduce((total, item) => total + +item.price, 0);
};

export const useShoppingCartStore = defineStore('shoppingCart', {
    state: () => {
        return {
            shoppingCart:
                JSON.parse(localStorage.getItem('shopping-cart')) || [],
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
            const items = state.shoppingCart.length
                ? state.shoppingCart
                : JSON.parse(localStorage.getItem('shopping-cart'));

                const total = items.reduce((total, item) => {
                total += Number(item.price * item.quantity)
                return total;
            }, 0)

            return total.toFixed(2);
        },
    },
    actions: {
        add(product) {
            const entryId = this.shoppingCart.findIndex(
                (entry) => entry.id == product.id,
            );

            if (entryId != -1) {
                this.shoppingCart[entryId].quantity += product.quantity;
            } else {
                this.shoppingCart.push(product);
            }

            console.log({ shoppingCart: this.shoppingCart });

            localStorage.setItem(
                'shopping-cart',
                JSON.stringify(this.shoppingCart),
            );
        },

        updateQuantity(product) {
            const entryId = this.shoppingCart.findIndex(
                (entry) => entry.id == product.id,
            );

            if (entryId != -1) {
                this.shoppingCart[entryId].quantity = product.quantity;

                localStorage.setItem(
                    'shopping-cart',
                    JSON.stringify(this.shoppingCart),
                );
            }
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
