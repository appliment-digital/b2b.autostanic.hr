<script>
// utils
import { capitalizeFirstLetter, formatPrice } from '@/utils';

// pinia
import { mapStores } from 'pinia';
import { useUserStore } from '@/store/userStore.js';
import { useResultsStore } from '@/store/resultsStore.js';
import { useShoppingCartStore } from '@/store/shoppingCartStore.js';
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
            details: null,

            featuredImage: null,
            thumbnails: null,

            itemQuantity: '1',

            displayBasic: false,
        };
    },
    watch: {
        itemQuantity(newVal, oldVal) {
            if (newVal < 1) {
                this.itemQuantity = '1';
            }
        },
    },
    computed: {
        ...mapStores(
            useUserStore,
            useResultsStore,
            useShoppingCartStore,
            useUIStore,
        ),
    },
    beforeMount() {
        // load and set product data
        this.loadProduct();

    },
    mounted() {
        // load and set product details
        // this.loadDetails();
    },
    methods: {
        /**
         * Load product data.
         */
        loadProduct() {
            const productSlug = decodeURIComponent(this.$route.path.slice(1));
            const productHistoryData =
                this.resultsStore.getProductFromHistory(productSlug)();

            if (productHistoryData) {
                this.product = productHistoryData;
            } else {
                this.product = this.resultsStore.product;
            }
        },

        /**
         * Load product details.
         */
        loadDetails() {
            this.UIStore.setIsDataLoading(true);

            ProductService.getDetails(this.product.id)
                .then((res) => {
                    this.details = res;

                    this.UIStore.setIsDataLoading(false);
                })
                .catch((err) => console.error(err));

        },

        handleSendInquiry() {
            this.openCRMForm();
        },

        handleAddProdcutToShoppingCart(product) {
            const productDetails = { ...product, quantity: this.itemQuantity };

            this.shoppingCartStore.add(productDetails);
        },

        formatProductPrice(val) {
            return formatPrice(val);
        },

        openCRMForm() {
            window.open(
                'https://b24-t1zfqc.bitrix24.site/crm_form_vks6q',
                '_blank',
            );
        },
    },
};
</script>

<template>
    <!-- Product: Image & Description -->
    <div class="grid column-gap-6 justify-content-between">
        <div class="col-12 md:col-5 md:h-auto">
            <!-- Feature Image -->
            <!-- prettier-ignore -->
            <div
                v-if="details && details.pictures.data.url550.length"
                class="bg-white-alpha-50 border-1 border-100 border-round"
            >
                <Image
                    class="block w-full"
                    imageClass="block border-round max-w-full"
                    :src="details.pictures.data.url550[0]"
                    alt="Image"
                    preview
                    :pt="{
                        preview: {
                            class: 'bg-blue-200'
                        },
                    }"
                />
            </div>
            <!-- Thumbnails -->
            <div
                v-if="details && details.pictures.data.url100"
                class="flex mt-2"
            >
                <div class="flex align-items-center justify-content-center">
                    <Image
                        v-for="img in details.pictures.data.url550"
                        class="mr-2 p-0 border-1 border-100 border-round"
                        imageClass="border-round"
                        imageStyle="width:100%; height: 60px; object-fit: cover;"
                        :src="img"
                        preview
                        width="62"
                    />
                </div>
            </div>
        </div>

        <div class="col">
            <h2 class="mt-4 md:mt-0">{{ product.name }}</h2>
            <span class="font-bold"
                >SKU: <span class="font-normal">{{ product.sku }}</span></span
            >

            <hr />

            <p class="mt-3" v-html="product.shortDescription" />
            <p class="mt-3" v-html="product.fullDescription" />

            <!-- Stock -->
            <span class="block mt-5"
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

            <div
                class="mt-5 flex flex-column row-gap-1 justify-content-between"
            >
                <div>
                    <span class="mb-0 mr-2 mb-2 text-lg">Cijena</span>
                    <span class="font-bold"
                        >{{ formatProductPrice(product.price) }} €</span
                    >
                </div>
                <div>
                    <span class="m-0 mr-2 mb-2 text-lg">Cijena s popustom</span>
                    <span class="font-bold">{{ product.price }} €</span>
                </div>
            </div>

            <!-- Price -->
            <!-- Buttons -->
            <!-- prettier-ignore -->
            <div
                class="mt-5 bg-white-alpha-10 border-1 border-100 py-3 
                border-noround border-left-none border-right-none"
                :class="product.stockQuantity > 0 && 'flex justify-content-between'"
            >
                <div
                    v-if="product.stockQuantity > 0"
                >
                    <Button
                        class="button--no-shadow mr-1"
                        icon="pi pi-minus"
                        severity="secondary"
                        outlined
                        @click="itemQuantity--"
                    />
                    <Button
                        class="button--no-shadow mr-1"
                        severity="primary"
                        :label="itemQuantity"
                    />
                    <Button
                        icon="pi pi-plus"
                        class="button--no-shadow"
                        :disabled="itemQuantity >= product.stockQuantity"
                        outlined
                        severity="secondary"
                        @click="itemQuantity++"
                    />
                </div>

                <div>
                    <Button
                        v-if="product.stockQuantity > 0"
                        class="button--no-shadow mr-2 text-sm"
                        label="Dodaj u košaricu"
                        severity="primary"
                        @click="handleAddProdcutToShoppingCart(product)"
                    />
                    <Button
                        class="button--no-shadow text-sm"
                        label="Pošalji upit"
                        outlined
                        severity="secondary"
                        @click="handleSendInquiry"
                    />
                </div>
            </div>

            <!-- Tab Menu -->
            <div class="mt-6 grid column-gap-6">
                <div class="col-12">
                    <TabView
                        style="max-width: 600px"
                        :pt="{
                            panelContainer: {
                                style: 'background-color: transparent',
                            },
                            nav: {
                                style: 'background-color: transparent',
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
                                <div class="flex justify-content-between">
                                    <!-- Manufacturer -->
                                    <span class="uppercase">Proizvođač</span>
                                    <span class="uppercase">{{
                                        product.manufacturerName
                                    }}</span>
                                </div>

                                <!-- OEM Codes -->
                                <!-- <div
                                    v-if="details && details.oemCodes.data"
                                    class="mt-4"
                                >
                                    <span class="block uppercase mb-2"
                                        >OEM Kodovi</span
                                    >
                                    <p>OEM je skraćenica od „Original Equipment Manufacturer“ ili u prijevodu OEM se odnosi na originalne dijelove za automobilsku industriju. Na primjer, ako imate Audi i potreban vam je motor, možete ga kupiti od drugog proizvođača ili autentičan Audi motor. Iako proizvođač ne može napraviti identičan dio, OEM se odnosi na dio koji proizvođač koristi u originalnom vozilu. Ljudi često traže originalne OEM dijelove kako bi zamjenili potrgani dio jer mogu biti sigurni u kvalitetu dijela. U većini slučajeva proizvođač vozila ne proizvodi i OEM dijelove već angažira vanjsku tvrtku da bude službeni proizvođač tog dijela kao što je BOSCH, VALO, LUK itd. To znači da ti proizvođači mogu te dijelove prodavati i kasnije pod oznakom OEM dio samo bez oznake proizvođača vozila.</p>
                                    <div
                                        class="flex align-items-center justify-content-between"
                                    >
                                        <span>{{
                                            product.manufacturerName
                                        }}</span>

                                        <ul
                                            class="flex"
                                            style="list-style: none"
                                        >
                                            <li
                                                v-for="oemCode in details
                                                    .oemCodes.data"
                                                class="mr-2"
                                            >
                                                {{ oemCode.id }}
                                            </li>
                                        </ul>
                                    </div>
                                </div> -->

                                <!-- Related Vehicles -->
                                <!-- <div v-if="details && details.relatedVehicles" class="mt-4">

                        </div> -->
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

                            <p class="mt-5">
                                Svi proizvođači NOVIH auto dijelova garantiraju
                                da su njihovi proizvodi bez nedostataka u
                                materijalu i izradi, te da su testirani prije
                                stavljanja u prodaju.
                            </p>
                            <p>
                                Garancija se daje krajnjem korisniku i odnosi se
                                na proizvode koji su kupljeni, instalirani i
                                korišteni u svrhe za koje su prvobitno
                                dizajnirani. Garancija prokriva nedostatke
                                nastale u uobičajenoj uporabi i ne pokriva
                                neispravnost ili kvarove uslijed zloupotrebe,
                                zanemarivanja, nepravilne instalacije ili
                                kvarova kod održavanja.
                            </p>
                            <p>
                                Tijekom garancijskog razdoblja, proizvođač će
                                ili popraviti ili zamjenitit bez naknade
                                proizvod koji je neispravan u materijalu ili
                                izradi.
                            </p>
                        </TabPanel>
                    </TabView>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.image--product {
    display: block;
    width: 100%;
    height: 300px;
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
