<script>
// lib
import camelcaseKeys from 'camelcase-keys';

// utils
import {
    capitalizeFirstLetter,
    calcProductPrice,
    stringifyProductPrice,
} from '@/utils';

// pinia
import { mapStores } from 'pinia';
import { useUserStore } from '@/store/userStore.js';
import { useShoppingCartStore } from '@/store/shoppingCartStore.js';
import { useBreadcrumbsStore } from '@/store/breadcrumbsStore.js';
import { useUIStore } from '@/store/UIStore.js';

// components
import Header from '@/components/Header.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';

// services
import ProductService from '@/service/ProductService';

export default {
    components: {
        Header,
        Breadcrumbs,
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
        };
    },
    watch: {
        itemQuantity(newVal) {
            const min = 1 

            if (newVal <= min) {
                this.itemQuantity = min;
            }

            console.log('item quantity', this.itemQuantity);
        },
    },
    computed: {
        ...mapStores(
            useUserStore,
            useShoppingCartStore,
            useBreadcrumbsStore,
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
    },
    beforeMount() {
        this.loadProduct(this.$route.query.id);
        this.loadProductDetails(this.$route.query.id);
    },
    methods: {
        loadProduct(id) {
            this.UIStore.setIsDataLoading(true);

            ProductService.getProductById(id)
                .then((response) => {
                    if (response.data) {
                        this.product = camelcaseKeys(response.data);
                        console.log('this.product', this.product);
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
                })
                .catch((err) => console.error(err));
        },

        handleSendInquiry() {
            this.openCRMForm();
        },

        handleModifyProductQuantity(modifier) {
            if (modifier === 'increment') {
                this.itemQuantity++;
            }

            if (modifier === 'decrement') {
                this.itemQuantity--;
            }
        },

        handleAddProdcutToShoppingCart(product) {

            if (this.isProductInShoppingCart) {
                this.shoppingCartStore.update(product, this.itemQuantity)
                return;
            }

            product.quantity = this.itemQuantity;

            const productDetails = {
                ...product,
                quantity: this.itemQuantity,
                picture: this.details.pictures[0],
            };

            this.breadcrumbsStore.addProductCrumbs(
                product.id,
                this.breadcrumbsStore.current,
            );

            this.shoppingCartStore.add(productDetails);

            this.$toast.add({
                severity: 'success',
                summary: 'Status',
                detail: `${this.isProductInShoppingCart ? 'Količina ažurirana!' : 'Proizvod dodan u košaricu!'} `,
                life: 1000,
            });
        },

        handleRemoveProductFromShoppingCart() {
            this.shoppingCartStore.delete(this.product);
        },

        handlePrice(price, discount) {
            return stringifyProductPrice(calcProductPrice(price, discount));
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

                <p class="mt-3" v-html="product.shortDescription" />
                <p class="mt-3" v-html="product.fullDescription" />

                <!-- Stock -->
                <div class="mt-4" style="height: 66px">
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
                        na stanju.</span
                    >
                    <span class="mt-2 flex align-items-center"
                        ><i class="pi pi-truck text-blue-500"></i>
                        <span class="ml-2">Besplatna dostava.</span></span
                    >
                    <div class="mt-2" v-if="isProductInShoppingCart">
                        <i class="pi pi-shopping-cart text-blue-500"></i>
                        <span class="ml-2"
                            >Proizvod 
                            (<span class="text-blue-500 font-bold">{{
                                getProductQuantity()
                            }}</span
                            >)<span class="font-bold"> u košarici.</span></span
                        >
                    </div>
                </div>

                <div
                    class="mt-4 flex flex-column row-gap-1 justify-content-between"
                >
                    <div>
                        <span class="mb-0 mr-2 mb-2 text-lg"
                            >Veleprodajna cijena (VPC):</span
                        >
                        <span v-if="details" class="font-bold">
                            {{ product.priceWithDiscountString }} €</span
                        >
                    </div>
                    <div>
                        <span class="m-0 mr-2 mb-2 text-lg"
                            >Maloprodajna cijena (MPC):
                        </span>
                        <span v-if="details" class="font-bold"
                            >{{ product.priceString }} €</span
                        >
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
                                @click="
                                    handleModifyProductQuantity('decrement')
                                "
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
                                @update:modelValue="
                                    handleModifyProductQuantity('input')
                                "
                            />
                            <Button
                                icon="pi pi-plus"
                                class="button--no-shadow"
                                :disabled="
                                    itemQuantity >= product.stockQuantity
                                "
                                outlined
                                severity="primary"
                                @click="
                                    handleModifyProductQuantity('increment')
                                "
                            />
                            <Button
                                class="button--no-shadow ml-1 text-sm"
                                :label="
                                    isProductInShoppingCart
                                        ? 'Ažuriraj košaricu'
                                        : 'Dodaj u košaricu'
                                "
                                severity="primary"
                                @click="handleAddProdcutToShoppingCart(product)"
                            />
                            <Button
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
                            />
                        </div>
                        <Button
                            class="button--no-shadow text-sm"
                            label="Pošalji upit"
                            outlined
                            severity="primary"
                            @click="handleSendInquiry"
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
                            <p style="max-width: 600px">
                                OEM je skraćenica od „Original Equipment
                                Manufacturer“ ili u prijevodu OEM se odnosi na
                                originalne dijelove za automobilsku industriju.
                                Na primjer, ako imate Audi i potreban vam je
                                motor, možete ga kupiti od drugog proizvođača
                                ili autentičan Audi motor. <br /><br />
                                Iako proizvođač ne može napraviti identičan dio,
                                OEM se odnosi na dio koji proizvođač koristi u
                                originalnom vozilu. Ljudi često traže originalne
                                OEM dijelove kako bi zamjenili potrgani dio jer
                                mogu biti sigurni u kvalitetu dijela. U većini
                                slučajeva proizvođač vozila ne proizvodi i OEM
                                dijelove već angažira vanjsku tvrtku da bude
                                službeni proizvođač tog dijela kao što je BOSCH,
                                VALO, LUK itd. To znači da ti proizvođači mogu
                                te dijelove prodavati i kasnije pod oznakom OEM
                                dio samo bez oznake proizvođača vozila.
                            </p>
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
                                v-if="details.carTypes"
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
