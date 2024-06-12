import { defineStore } from 'pinia';

// pinia
import { useUserStore } from '@/store/userStore.js';

// storage
import { session, local } from '@/utils/browser-storage';

export const useShoppingCartStore = defineStore('shoppingCart', {
    state: () => {
        const { user } = useUserStore();

        return {
            store: {
                local: {
                    cart: local.load('shopping-cart-store')?.cart || [],
                    cartV2: local.load('shopping-cart-store')?.cartV2 || {},
                },

                userId: user.id,
            },
        };
    },
    getters: {
        cart: (state) => {
            return state.store.local.cartV2[state.store.userId];
        },

        total: (state) => {
            const { userId } = state.store;

            return state.store.local.cartV2[userId].reduce((total, product) => {
                const amount = Number(
                    product.priceWithDiscount * product.quantity,
                );
                total += amount;
                return total;
            }, 0);
        },

        totalWithTax: (state) => {
            const { userId } = state.store;

            const total = state.store.local.cartV2[userId].reduce(
                (total, product) => {
                    const amount = Number(
                        product.priceWithDiscount * product.quantity,
                    );
                    total += amount;
                    return total;
                },
                0,
            );

            const tax = 0.25;
            const taxAmount = total * tax;
            const totalWithTax = total + taxAmount;

            return totalWithTax;
        },

        taxAmount: (state) => {
            const { userId } = state.store;

            const total = state.store.local.cartV2[userId].reduce(
                (total, product) => {
                    const amount = Number(
                        product.priceWithDiscount * product.quantity,
                    );
                    total += amount;
                    return total;
                },
                0,
            );

            const tax = 0.25;
            const taxAmount = total * tax;

            return taxAmount;
        },
    },
    actions: {
        init() {
            const { user } = useUserStore();
            console.log('initializing cart...', user.id);

            const existingCart = local.load('shopping-cart-store')?.cartV2[
                user.id
            ];
            console.log({ existingCart });

            if (!existingCart) {
                console.log('not existing');
                this.store.local.cartV2[user.id] = [];
                this.store.userId = user.id

                local.save('shopping-cart-store', this.store.local);
            } else {
                console.log('existing');
                this.store.local.cartV2[user.id] = existingCart;
                this.store.userId = user.id
            }
        },

        // add(product) {
        //     const entryId = this.store.local.cart.findIndex(
        //         (entry) => entry.id == product.id,
        //     );

        //     if (entryId != -1) {
        //         // item exists in the cart
        //         let newQuantity =
        //             Number(this.store.local.cart[entryId].quantity) +
        //             Number(product.quantity);

        //         if (newQuantity > product.stockQuantity) {
        //             newQuantity = Number(product.stockQuantity);
        //         }

        //         this.store.local.cart[entryId].quantity = newQuantity;
        //     } else {
        //         // item doesn't exists in the cart
        //         this.store.local.cart.push(product);
        //     }

        //     local.save('shopping-cart-store', this.store.local);
        // },

        add(product) {
            const { userId } = this.store;

            const cart = this.store.local.cartV2[userId];
            console.log('adding product', { userId, cart });

            const entryId = cart.findIndex((entry) => entry.id == product.id);

            if (entryId != -1) {
                // item exists in the cart
                let newQuantity =
                    Number(cart[entryId].quantity) + Number(product.quantity);

                if (newQuantity > product.stockQuantity) {
                    newQuantity = Number(product.stockQuantity);
                }

                cart[entryId].quantity = newQuantity;
            } else {
                // item doesn't exists in the cart
                cart.push(product);
            }

            local.save('shopping-cart-store', this.store.local);
        },

        addQuantity(product, quantity) {
            const { userId } = this.store;
            const cart = this.store.local.cartV2[userId];

            cart.forEach((entry) => {
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
            const { userId } = this.store;
            const cart = this.store.local.cartV2[userId];

            cart.forEach((entry) => {
                if (entry.id == product.id) {
                    entry.quantity = product.quantity;
                }
            });

            local.save('shopping-cart-store', this.store.local);
        },

        delete(product) {
            console.log('deleting product from cart...');

            const { userId } = this.store;
            let cart = this.store.local.cartV2[userId];

            cart = cart.filter((entry) => entry.id != product.id);
            console.log('filtered cart:', { cart });

            this.store.local.cartV2[userId] = cart;

            local.save('shopping-cart-store', this.store.local);
        },

        clear() {
            const { userId } = this.store;
            this.store.local.cartV2[userId] = [];

            local.save('shopping-cart-store', this.store.local);
        },
    },
});
