<script>
// lib
import slug from 'slug';
import camelcaseKeys from 'camelcase-keys';

// utils
import {
    capitalizeFirstLetter,
    calcProductPrice,
    stringifyProductPrice,
    setSlugCharMap,
} from '@/utils';

// pinia
import { mapStores } from 'pinia';
import { useUserStore } from '@/store/userStore.js';
import { useShoppingCartStore } from '@/store/shoppingCartStore.js';
import { useBreadcrumbsStore } from '@/store/breadcrumbsStore.js';
import { useUIStore } from '@/store/UIStore.js';
import { useProductStore } from '@/store/productStore.js';

// components
import Header from '@/components/Header.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import Query from '@/components/Query.vue';

// services
import ProductService from '@/service/ProductService';

// modify slug library (add croatian chars)
setSlugCharMap(slug);

export default {
    components: {
        Header,
        Breadcrumbs,
        Query,
    },
    data() {
        return {
            product: null,

            details: {
                pictures: null,
                specifications: null,
                oemCodes: null,
                carTypes: null,
            },

            itemQuantity: 1,

            showQueryModal: false,

            breadcrumbs: null,
        };
    },
    watch: {
        itemQuantity(newVal) {
            const min = 1;

            if (newVal <= min) {
                this.itemQuantity = min;
            }
        },
    },
    computed: {
        ...mapStores(
            useUserStore,
            useShoppingCartStore,
            useBreadcrumbsStore,
            useProductStore,
            useUIStore,
        ),

        isProductInShoppingCart() {
            const cart = this.shoppingCartStore.cart;

            if (cart) {
                const product = this.shoppingCartStore.cart.find(
                    (product) => product.id == this.product.id,
                );

                if (product) return true;
            }
        },

        availableItemQuantity() {
            if (!this.isProductInShoppingCart) return;

            const product = this.shoppingCartStore.cart.find(
                (entry) => entry.id == this.product.id,
            );

            return Math.abs(
                product.quantity - Number(this.product.stockQuantity),
            );
        },
    },
    beforeMount() {
        const storedProduct = this.productStore.products[this.$route.query.id];

        if (storedProduct) {
            this.product = storedProduct;
            this.details = storedProduct.details;

            this.breadcrumbsStore.set(storedProduct.breadcrumbs);
        } else {
            this.loadProduct(this.$route.query.id);
            this.loadProductDetails(this.$route.query.id);
        }
    },
    methods: {
        loadProduct(id) {
            this.UIStore.setIsDataLoading(true);

            ProductService.getProductById(id)
                .then((response) => {
                    if (response.data) {
                        this.product = camelcaseKeys(response.data, {
                            deep: true,
                        });

                        // make breadcrumbs
                        const ids = this.product.categories
                            .reverse()
                            .map((c) => c.id);
                        const breadcrumbs = this.product.categories.map(
                            (c, i) => ({
                                label: c.name.trim(),
                                route: `/category?id=${ids.slice(0, i + 1).join(encodeURIComponent('&'))}`,
                            }),
                        );
                        breadcrumbs.push({
                            icon: 'pi pi-car',
                            route: `/${slug(this.product.name)}?id=${this.product.id}`,
                        });

                        this.breadcrumbs = breadcrumbs;

                        this.breadcrumbsStore.set(this.breadcrumbs);
                    }
                })
                .catch((err) => console.error(err));
        },

        loadProductDetails(id) {
            this.UIStore.setIsDataLoading(true);

            ProductService.getDetails(id)
                .then((res) => {
                    this.details = {
                        pictures: res.data.pictures,
                        specifications: res.data.specifications,
                        oemCodes: res.data.oemCodes,
                        carTypes: res.data.carTypes,
                    };

                    this.UIStore.setIsDataLoading(false);

                    if (this.product) {
                        this.productStore.add({
                            product: this.product,
                            details: this.details,
                            breadcrumbs: this.breadcrumbs,
                        });
                    }
                })
                .catch((err) => console.error(err));
        },

        handleSendInquiry() {
            this.openCRMForm();
        },

        handleAddProductToShoppingCart() {
            // update product cart quantity if product is already in the cart
            if (this.isProductInShoppingCart) {
                this.shoppingCartStore.addQuantity(
                    this.product,
                    this.itemQuantity,
                );

                this.$toast.add({
                    severity: 'success',
                    summary: 'Košarica',
                    detail: 'Nova količina ažurirana!',
                    life: 2000,
                });

                return;
            }

            // update quantity on the product
            this.product.quantity = this.itemQuantity;

            // add product in the car
            const productDetails = {
                ...this.product,
                quantity: this.itemQuantity,
                picture: this.details.pictures[0],
            };

            this.breadcrumbsStore.addProductCrumbs(
                this.product.id,
                this.breadcrumbsStore.current,
            );

            // this.shoppingCartStore.setUser(this.user)
            this.shoppingCartStore.add(productDetails);

            this.$toast.add({
                severity: 'success',
                summary: 'Košarica',
                detail: 'Proizvod dodan!',
                life: 2000,
            });
        },

        getProductQuantity() {
            const product = this.shoppingCartStore.cart.find(
                (entry) => entry.id === this.product.id,
            );

            if (product) {
                return product.quantity;
            }

            return 0;
        },

        openCRMForm() {
            window.open(
                'https://b24-t1zfqc.bitrix24.site/crm_form_vks6q',
                '_blank',
            );
        },

        capitalize(text) {
            return capitalizeFirstLetter(text);
        },

        handleQueryModalClick() {
            this.showQueryModal = !this.showQueryModal;
        },
    },
};
</script>

<template>
    <section v-if="product && details">
        <!-- Product: Image & Description -->
        <div class="grid column-gap-6 justify-content-between pb-8">
            <div class="col-12 md:col-5 md:h-auto">
                <!-- featured image -->
                <div
                    class="p-2 border-1 border-100 border-round bg-white"
                    style="width: 100%; height: 360px; overflow: hidden"
                >
                    <Image
                        v-if="details.pictures && details.pictures.length"
                        :src="details.pictures[0]"
                        style="width: 100%; height: 100%"
                        imageStyle="display: block; border-radius: 8px; max-width: 100%; width: 100%; height: 100%; object-fit: contain; transform: scale(.5)"
                        preview
                    />
                </div>

                <!-- thumbnails -->
                <div
                    v-if="details.pictures"
                    class="mt-2 flex overflow-x-scroll"
                >
                    <Image
                        v-for="img in details.pictures"
                        :src="img"
                        class="bg-white border-round mr-1 border-100 border-1 p-1"
                        style="min-width: 64px; width: 64px; height: 64px"
                        imageStyle="display: block; border-radius: 8px; max-width: 100%; height: 100%; object-fit: contain;"
                        preview
                    />
                </div>
            </div>
            <div class="col">
                <h2 class="mt-4 md:mt-0">{{ capitalize(product.name) }}</h2>
                <span class="block font-bold border-bottom-1 border-100 pb-2"
                    >SKU:
                    <span class="font-normal">{{ product.sku }}</span></span
                >

                <!-- Stock -->
                <div class="mt-4 text-normal">
                    <span class="block"
                        ><span
                            class="font-bold"
                            :class="
                                product.stockQuantity == 0
                                    ? 'text-red-500'
                                    : 'text-green-500'
                            "
                            >{{ product.stockQuantity }}</span
                        >
                        na stanju kod dobavljača</span
                    >
                    <span class="mt-2 flex align-items-center"
                        ><i class="pi pi-truck text-blue-500"></i>
                        <span class="ml-2">Besplatna dostava</span></span
                    >
                    <div class="mt-2" v-if="isProductInShoppingCart">
                        <i class="pi pi-shopping-cart text-blue-500"></i>
                        <span class="ml-2"
                            >Proizvod (<span class="text-blue-500 font-bold">{{
                                getProductQuantity()
                            }}</span
                            >)<span class="font-bold"> u košarici</span></span
                        >
                    </div>
                    <div class="mt-2" v-else>
                        <i class="pi pi-shopping-cart text-red-500"></i>
                        <span class="ml-2"
                            >Proizvod <span class="font-normal">nije </span>u
                            košarici</span
                        >
                    </div>
                    <div v-if="product.warrent" class="mt-2">
                        <label class="font-semibold">Garancija: </label>
                        <span class="ml-2">{{ product.warrent }}</span>
                    </div>
                    <div v-if="product.deliveryDeadline" class="mt-2">
                        <label class="font-semibold">Rok isporuke: </label>
                        <span class="ml-2">{{ product.deliveryDeadline }}</span>
                    </div>
                </div>

                <div class="mt-4 flex flex-column justify-content-between">
                    <div>
                        <span class="mb-0 mr-2 text-lg font-bold"
                            >Veleprodajna cijena
                            <span class="font-bold"
                                >(VPC <span class="">s rabatom</span>):</span
                            ></span
                        >
                        <span
                            v-if="details"
                            class="text-lg font-bold text-green-500"
                        >
                            {{ product.priceWithDiscountString }} €</span
                        >
                    </div>
                    <div class="text-normal mt-2">
                        <span class="mr-2 mb-2"
                            >Maloprodajna cijena (MPC):
                        </span>
                        <span v-if="details">{{ product.priceString }} €</span>
                    </div>
                </div>

                <div
                    v-if="!UIStore.isDataLoading"
                    class="mt-5 bg-white-alpha-10 border-1 border-100 py-2 border-noround border-left-none border-right-none"
                    :class="
                        product.stockQuantity > 0 &&
                        'flex justify-content-between'
                    "
                >
                    <div
                        class="w-full flex justify-content-between align-items-center"
                    >
                        <div v-if="product.stockQuantity >= 0">
                            <Button
                                class="button--no-shadow mr-1"
                                icon="pi pi-minus"
                                severity="primary"
                                outlined
                                @click="itemQuantity--"
                            />
                            <InputNumber
                                class="mr-1"
                                style="box-shadow: none"
                                inputId="locale-german"
                                locale="de-DE"
                                v-model="itemQuantity"
                                inputClass="font-bold"
                                inputStyle="width: 60px; text-align: center; border: 1px solid #5297ff; box-shadow: none; background: transparent; color: #5297ff"
                                :max="product.stockQuantity"
                                :min="1"
                                @keyup.enter="handleAddProductToShoppingCart"
                            />
                            <Button
                                icon="pi pi-plus"
                                class="button--no-shadow"
                                :disabled="
                                    itemQuantity >= availableItemQuantity
                                "
                                outlined
                                severity="primary"
                                @click="itemQuantity++"
                            />
                            <Button
                                :disabled="product.stockQuantity == 0"
                                class="button--no-shadow ml-1 text-sm"
                                label="Dodaj u košaricu"
                                severity="primary"
                                @click="handleAddProductToShoppingCart"
                            />
                            <!-- <Button
                                v-if="
                                    shoppingCartStore.cart.find(
                                        (entry) => entry.id == product.id,
                                    )
                                "
                                icon="pi pi-trash"
                                class="button--no-shadow ml-1 text-sm"
                                severity="primary"
                                outlined
                                @click="handleRemoveProductFromShoppingCart"
                            /> -->
                        </div>
                        <Button
                            class="button--no-shadow text-sm"
                            label="Pošalji upit"
                            outlined
                            severity="primary"
                            @click="handleQueryModalClick"
                        />
                    </div>
                </div>
            </div>
            <div class="w-full mx-auto mt-6 grid">
                <div class="col-12">
                    <TabView
                        :pt="{
                            panelContainer: {
                                style: 'background-color: transparent',
                                class: 'p-0 pt-4',
                            },
                            nav: {
                                style: 'background-color: transparent',
                                class: 'border-bottom-1',
                            },
                        }"
                    >
                        <TabPanel
                            header="Opis"
                            :pt="{
                                headerAction: {
                                    style: 'background-color: transparent',
                                },
                            }"
                        >
                            <div class="grid column-gap-6">
                                <p
                                    class="mt-3 col"
                                    v-html="product.fullDescription"
                                />
                                <p
                                    class="mt-3 col"
                                    v-html="product.shortDescription"
                                />
                            </div>
                        </TabPanel>
                        <TabPanel
                            header="Dodatne informacije"
                            :pt="{
                                headerAction: {
                                    style: 'background-color: transparent',
                                },
                            }"
                        >
                            <div>
                                <!-- Manufacturer -->
                                <div
                                    v-for="(
                                        key, val, i
                                    ) in details.specifications"
                                    class="grid surface-100 mt-1"
                                    :class="
                                        i % 2 === 0
                                            ? 'surface-50'
                                            : 'bg-transparent'
                                    "
                                >
                                    <div class="col-6">
                                        {{ val }}
                                    </div>
                                    <div class="col-6">{{ key }}</div>
                                </div>
                            </div>
                        </TabPanel>
                        <TabPanel
                            header="Garancija"
                            :pt="{
                                headerAction: {
                                    style: 'background-color: transparent',
                                },
                            }"
                        >
                            <ul>
                                <li class="mb-2">Nova roba 12-36 mjeseci</li>
                                <li>Rabljena roba 1-6 mjeseci</li>
                            </ul>

                            <div style="max-width: 600px">
                                <p class="mt-5">
                                    Svi proizvođači NOVIH auto dijelova
                                    garantiraju da su njihovi proizvodi bez
                                    nedostataka u materijalu i izradi, te da su
                                    testirani prije stavljanja u prodaju.
                                </p>
                                <p>
                                    Garancija se daje krajnjem korisniku i
                                    odnosi se na proizvode koji su kupljeni,
                                    instalirani i korišteni u svrhe za koje su
                                    prvobitno dizajnirani. Garancija prokriva
                                    nedostatke nastale u uobičajenoj uporabi i
                                    ne pokriva neispravnost ili kvarove uslijed
                                    zloupotrebe, zanemarivanja, nepravilne
                                    instalacije ili kvarova kod održavanja.
                                </p>
                                <p>
                                    Tijekom garancijskog razdoblja, proizvođač
                                    će ili popraviti ili zamjenitit bez naknade
                                    proizvod koji je neispravan u materijalu ili
                                    izradi.
                                </p>
                            </div>
                        </TabPanel>
                        <TabPanel
                            header="OEM Kodovi"
                            :pt="{
                                headerAction: {
                                    style: 'background-color: transparent',
                                },
                            }"
                        >
                            <div v-if="details.oemCodes" class="mt-4">
                                <div
                                    v-for="(
                                        codes, manufacturer, i
                                    ) in details.oemCodes"
                                    class="mt-1 grid"
                                    :class="
                                        i % 2 === 0
                                            ? 'surface-100'
                                            : 'bg-transparent'
                                    "
                                >
                                    <div class="col-6">{{ manufacturer }}</div>
                                    <div class="col-6">
                                        <span
                                            class="inline-block mr-2"
                                            v-for="(code, i) in codes"
                                            >{{ code
                                            }}{{ i > 0 ? '' : ',' }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </TabPanel>
                        <TabPanel
                            header="Povezana vozila"
                            :pt="{
                                headerAction: {
                                    style: 'background-color: transparent',
                                },
                            }"
                        >
                            <div
                                v-if="
                                    details.carTypes &&
                                    Object.keys(details.carTypes)?.length
                                "
                                v-for="(
                                    details, carType, index
                                ) in details.carTypes"
                            >
                                <Accordion :activeIndex="index">
                                    <AccordionTab
                                        :header="carType"
                                        :pt="{
                                            headerAction: {
                                                class: 'p-4',
                                            },
                                            content: {
                                                class: 'p-4',
                                            },
                                        }"
                                    >
                                        <table
                                            style="
                                                width: 100%;
                                                border-collapse: collapse;
                                            "
                                        >
                                            <tr class="text-left">
                                                <th class="p-2">Marka</th>
                                                <th>Model</th>
                                                <th>Tip</th>
                                                <th>kW</th>
                                                <th>Tip motora</th>
                                                <th>Godina</th>
                                            </tr>

                                            <tr
                                                v-for="(
                                                    entry, index
                                                ) in details"
                                                :class="
                                                    index % 2 === 0
                                                        ? 'surface-50'
                                                        : 'bg-transparent'
                                                "
                                            >
                                                <td class="p-2">
                                                    {{ carType }}
                                                </td>
                                                <td v-for="(x, y) in entry">
                                                    {{ x }}
                                                </td>
                                            </tr>
                                        </table>
                                    </AccordionTab>
                                </Accordion>
                            </div>
                            <div v-else>Nema povezanih vozila...</div>
                        </TabPanel>
                    </TabView>
                </div>
            </div>
        </div>
    </section>
    <Query
        :showQueryModal="showQueryModal"
        @on-query-modal-click="handleQueryModalClick"
    ></Query>
</template>

<style scoped>
.image--product {
    display: block;
    width: 100%;
    height: 300pxl;
    object-fit: cover;
    object-position: center;
    border-radius: 4px;
}

/* upadate product image on smaller devices */
@media only screen and (max-width: 767px) {
    .image--product {
        height: 100%;
    }
}
</style>
