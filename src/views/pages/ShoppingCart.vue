<script>
// lib
import slug from 'slug';
import camelcaseKeys from 'camelcase-keys';

// utils
import {
    setSlugCharMap,
    calcProductPrice,
    stringifyProductPrice,
} from '@/utils';

// components
import Header from '@/components/Header.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';

// pinia
import { mapStores } from 'pinia';
import { useShoppingCartStore } from '@/store/shoppingCartStore.js';
import { useBreadcrumbsStore } from '@/store/breadcrumbsStore.js';
import { useUIStore } from '@/store/UIStore.js';
import { useUserStore } from '@/store/userStore.js';

// services
import UserService from '../../service/UserService.js';
import OrderService from '@/service/OrderService.js';

const orderService = new OrderService();
// modify slug library (add croatian chars)
setSlugCharMap(slug);

export default {
    components: {
        Header,
        Breadcrumbs,
    },
    data() {
        return {
            selectedProduct: null,

            fees: {
                taxPercentage: 25,
            },

            shoppingNote: '',

            tableData: {
                delivery: null,
            },

            orderTotal: 0,

            sendingOrder: false,
        };
    },
    computed: {
        ...mapStores(
            useShoppingCartStore,
            useBreadcrumbsStore,
            useUserStore,
            useUIStore,
        ),

        order() {
            const userDiscount = this.userStore.discount;
            const cartTotal = this.shoppingCartStore.total;

            const discount = cartTotal * (userDiscount / 100);
            const discountedAmount = cartTotal - discount;

            const tax = discountedAmount * 0.25;
            const totalAmount = discountedAmount + tax;

            this.orderTotal = totalAmount;

            return [
                {
                    name: 'Ukupno',
                    value: `${stringifyProductPrice(cartTotal)} €`,
                },
                {
                    name: `Rabat (${userDiscount}%)`,
                    value: `${stringifyProductPrice(discountedAmount)} €`,
                },
                {
                    name: 'PDV (25%)',
                    value: `${stringifyProductPrice(tax)} €`,
                },
                {
                    name: 'Sveukupno',
                    value: `${stringifyProductPrice(totalAmount)} €`,
                },
            ];
        },
    },
    mounted() {
        UserService.getCurrentUserData()
            .then((response) => {
                const data = camelcaseKeys(response.data);

                this.tableData.delivery = [
                    {
                        name: 'Korisnik',
                        value: `${data.name} ${data.lastName}`,
                    },
                    { name: 'Grad', value: data.city },
                    { name: 'Država', value: data.country },
                    { name: 'Adresa', value: data.address },
                    { name: 'Način plaćanja', value: data.paymentMethod },
                ];
            })
            .catch((err) => console.error(err));
    },
    methods: {
        calcPrice(price, quantity) {
            return `${Number(quantity * price).toFixed(2)}`;
        },

        handleNewProductQuantity(product) {
            if (product.quantity > product.stockQuantity) {
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Košarica',
                    detail: 'Maksimalna količina dodana!',
                    life: 2000,
                });

                product.quantity = product.stockQuantity;
            } else {
                this.$toast.add({
                    severity: 'success',
                    summary: 'Košarica',
                    detail: 'Nova količina ažurirana!',
                    life: 2000,
                });
            }

            this.shoppingCartStore.updateQuantity(product);
        },

        handlePrice(price) {
            return stringifyProductPrice(calcProductPrice(price));
        },

        handleDeleteProduct(product) {
            this.shoppingCartStore.delete(product);
        },

        handleFinishOrderClick() {
            this.sendingOrder = true;
            orderService
                .createOrder({
                    orderTotal: this.orderTotal,
                    items: this.shoppingCartStore.cart,
                    note: this.shoppingNote,
                })
                .then((response) => {
                    if (response.data.ID) {
                        this.sendingOrder = true;
                        this.$router.push('/hvala');
                        //prazni local storage
                        this.shoppingCartStore.clear();
                        this.$toast.add({
                            severity: 'success',
                            summary: 'Uspješno',
                            detail: 'je poslana narudžba.',
                            life: 3000,
                        });
                        setTimeout(this.$router.push('/'), 30000);
                    } else {
                        this.$router.push('/');
                        this.$toast.add({
                            severity: 'error',
                            summary: 'Greška',
                            detail: 'Došlo je do greške pri izradi narudžbe.',
                            life: 3000,
                        });
                    }
                });
        },

        handleProductTableItemClick(product) {
            const productSlug = slug(product.name, { lower: true });

            this.$router.push({
                path: `/${productSlug}`,
                query: { id: product.id },
            });
        },

        handleProductName(name) {
            const max = 60;

            if (name.length > max) {
                return name.substring(0, max) + ', ...';
            }

            return name;
        },
    },
};
</script>

<template>
    <div v-if="sendingOrder" class="card flex justify-content-center">
        <div>
            <div class="flex justify-center">
                <ProgressSpinner
                    style="width: 80px; height: 100px"
                    strokeWidth="3"
                    fill="var(--surface-ground)"
                />
            </div>
            <div class="flex justify-center">Slanje narudžbe u tijeku...</div>
        </div>
    </div>
    <div
        v-else
        class="grid grid-nogutter justify-content-between bg-white border-round p-5 border-1 border-100"
    >
        <div class="col border-right-1 border-100 pr-5 mr-5">
            <DataTable
                id="shopping-cart"
                :pt="{
                    column: {
                        headerCell: {
                            class: 'bg-transparent',
                        },
                    },
                    bodyRow: {
                        style: 'border-color: red;',
                    },
                }"
                v-if="shoppingCartStore.cart && shoppingCartStore.cart.length"
                :value="shoppingCartStore.cart"
            >
                <Column field="image" header="Slika">
                    <template #body="{ data }">
                        <img
                            :src="data.picture"
                            style="user-select: none"
                            class="table-image border-round cursor-pointer"
                            @click="handleProductTableItemClick(data)"
                        />
                    </template>
                </Column>

                <Column field="name" header="Proizvod" style="max-width: 200px">
                    <template #body="{ data }">
                        <span
                            class="cursor-pointer text-sm"
                            @click="handleProductTableItemClick(data)"
                            >{{ handleProductName(data.name) }}</span
                        >
                    </template>
                </Column>

                <Column field="price" header="Cijena" style="min-width: 100px">
                    <template #body="{ data }">
                        <span>{{ handlePrice(data.price) }} €</span>
                    </template>
                </Column>

                <Column header="Količina" id="xyz">
                    <template #body="{ data }">
                        <InputNumber
                            showButtons
                            :step="1"
                            inputId="locale-german"
                            locale="de-DE"
                            v-model="data.quantity"
                            inputStyle="width: 60px; text-align: center; box-shadow: none;"
                            @blur="handleNewProductQuantity(data)"
                            min="1"
                        />
                    </template>
                </Column>

                <Column header="Ukupno" style="min-width: 100px">
                    <template #body="{ data }">
                        <span style="user-select: none"
                            >{{
                                handlePrice(data.price * data.quantity)
                            }}
                            €</span
                        >
                    </template>
                </Column>

                <Column header="Obriši">
                    <template #body="{ data }">
                        <Button
                            class="button--no-shadow"
                            icon="pi pi-trash"
                            outlined
                            severity="primary"
                            @click="handleDeleteProduct(data)"
                        />
                    </template>
                </Column>
            </DataTable>

            <div v-else>Nema proizvoda u košarici...</div>
        </div>

        <div
            v-if="shoppingCartStore.cart && shoppingCartStore.cart.length"
            class="col-12 lg:col-3"
        >
            <div class="max-w-24rem mx-auto">
                <DataTable
                    :value="order"
                    :pt="{
                        column: {
                            headerCell: {
                                class: 'bg-transparent',
                            },
                        },
                    }"
                >
                    <Column>
                        <template #header>
                            <i class="pi pi-wallet mr-2"></i>Ukupna narudžba
                        </template>
                        <template #body="{ data }">
                            <div
                                class="flex justify-content-between"
                                :class="
                                    data.name === 'Sveukupno' ? 'font-bold' : ''
                                "
                            >
                                <span>{{ data.name }}</span>
                                <span class="text-right"
                                    >{{ data.value }}
                                </span>
                            </div>
                        </template>
                    </Column>
                </DataTable>

                <DataTable
                    v-if="
                        shoppingCartStore.cart &&
                        shoppingCartStore.cart.length &&
                        tableData.delivery
                    "
                    class="mt-3"
                    :value="tableData.delivery"
                    :pt="{
                        column: {
                            headerCell: {
                                class: 'bg-transparent',
                            },
                        },
                    }"
                >
                    <Column>
                        <template #header>
                            <i class="pi pi-truck mr-2"></i>Dostava
                            <span class="ml-1 text-blue-500">(besplatna)</span>
                        </template>
                        <template #body="{ data }">
                            <div
                                class="flex justify-content-between"
                                :class="
                                    data.name === 'Sveukupno' ? 'font-bold' : ''
                                "
                            >
                                <span>{{ data.name }}</span>
                                <span class="text-right"
                                    >{{ data.value }}
                                </span>
                            </div>
                        </template>
                    </Column>
                </DataTable>

                <Textarea
                    class="w-full mt-3"
                    v-model="shoppingNote"
                    rows="5"
                    cols="30"
                    placeholder="Napomena.."
                />

                <Button
                    class="button--no-shadow w-full mt-3"
                    label="Završi narudžbu"
                    outlined
                    severity="primary"
                    @click="handleFinishOrderClick"
                />
            </div>
        </div>
    </div>
</template>

<style scoped>
.table-image {
    width: 100px;
    object-fit: cover;
    height: 60px;
}

.button--no-shadow:focus {
    box-shadow: none !important;
}

.button--small .p-button-icon {
    font-size: 8px;
}
</style>
