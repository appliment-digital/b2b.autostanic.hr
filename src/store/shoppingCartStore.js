import { defineStore } from 'pinia';

const calcTotal = (items) => {
    return items.reduce((total, item) => total + +item.price, 0);
};

// TODO: implement FeeService.js to get values
const DELIVERY = 10000;
const TAX = 10;

export const useShoppingCartStore = defineStore('shoppingCart', {
    state: () => {
        return {
            shoppingCart:
                JSON.parse(localStorage.getItem('shopping-cart')) || [],

            currentTotalWithoutFees: 0,
            currentTotalWithFees: 0,

            feeGroup: {
                delivery: DELIVERY,
                tax: TAX,
            },
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
        totalWithoutFees: (state) => {
            const items = state.shoppingCart.length
                ? state.shoppingCart
                : JSON.parse(localStorage.getItem('shopping-cart'));

            const total = items.reduce((total, item) => {
                total += Number(item.price * item.quantity);
                return total;
            }, 0);

            state.currentTotalWithoutFees = total;

            return total.toFixed(2);
        },

        totalWithFees: (state) => {
            if (state.currentTotalWithoutFees) {
                return (state.currentTotalWithoutFees + DELIVERY + TAX).toFixed(2);
            }
        },

        fees: (state) => {
            if (state.feeGroup) {
                return state.feeGroup;
            }
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
