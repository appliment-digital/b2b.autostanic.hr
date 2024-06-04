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
        add(product) {
            console.log('adding product...');
            const entryId = this.store.local.cart.findIndex(
                (entry) => entry.id == product.id,
            );

            if (entryId != -1) {
                // item exists in the cart
                let newQuantity =
                    Number(this.store.local.cart[entryId].quantity) +
                    Number(product.quantity);

                if (newQuantity > product.stockQuantity) {
                    newQuantity = Number(product.stockQuantity);
                }

                this.store.local.cart[entryId].quantity = newQuantity;
            } else {
                // item doesn't exists in the cart
                this.store.local.cart.push(product);
            }

            local.save('shopping-cart-store', this.store.local);
        },

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
