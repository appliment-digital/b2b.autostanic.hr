import { defineStore } from 'pinia';

// storage
import { session, local } from '@/utils/browser-storage';

export const useShoppingCartStore = defineStore('shoppingCart', {
    state: () => {
        return {
            store: {
                local: {
                    cart: local.load('shopping-cart-store')?.cart || [],
                },
            },
        };
    },
    getters: {
        cart: (state) => {
            return state.store.local.cart;
        },

        total: (state) => {
            return state.store.local.cart.reduce((total, product) => {
                const amount = Number(product.price * product.quantity);

                total += amount;

                return total;
            }, 0);
        },
    },
    actions: {
        addQuantity(product, quantity) {
            this.store.local.cart.forEach((entry) => {
                if (entry.id == product.id) {
                    let newQuantity = entry.quantity + Number(quantity);

                    if (newQuantity > entry.stockQuantity) {
                        newQuantity = entry.stockQuantity;
                    }

                    entry.quantity = newQuantity;
                }
            });

            local.save('shopping-cart-store', this.store.local);
        },

        updateQuantity(product) {
            this.store.local.cart.forEach((entry) => {
                if (entry.id == product.id) {
                    entry.quantity = product.quantity;
                }
            });

            local.save('shopping-cart-store', this.store.local);
        },

        delete(product) {
            const filteredCart = this.store.local.cart.filter(
                (entry) => entry.id != product.id,
            );

            this.store.local.cart = filteredCart;
            local.save('shopping-cart-store', this.store.local);
        },

        clear() {
            this.store.local.cart = [];
            local.save('shopping-cart-store', this.store.local);
        },
    },
});
