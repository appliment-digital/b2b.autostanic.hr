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
            productDetails: {},
            featuredImage: null,

            itemQuantity: '1',

            productThumbnails: [1, 2, 3],

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
    mounted() {
        const productSlug = decodeURIComponent(this.$route.path.slice(1));

        const productHistoryData =
            this.resultsStore.getProductFromHistory(productSlug)();

        if (productHistoryData) {
            this.productDetails = productHistoryData;
        } else {
            this.productDetails = this.resultsStore.product;
        }

        this.featuredImage = this.productDetails;
        console.log({ featuredImage: this.featuredImage });

        console.log('product mounted', { productDetails: this.productDetails });

        // get the additional product pictures
        ProductService.getProductPictures(this.resultsStore.product.id)
            .then((response) => {
                if (response.data && response.data.length) {
                    this.productThumbnails = response.data;
                }
            })
            .catch((err) => console.err(err));
    },
    methods: {
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
        <div class="col-12 h-20rem md:col-5 md:h-auto">
            <!-- Feature Image -->
            <div class="border-1 border-200 p-6 flex justify-content-center">
                <Image
                    style="object-fit: cover"
                    :src="featuredImage"
                    alt="Image"
                    width="250"
                    preview
                />
            </div>

            <!-- Thumbnails -->
            <div class="flex mt-2 overflow-x-scroll">
                <Image
                    class="block border-1 border-100 mr-2"
                    style="
                        width: 64px;
                        height: 64px;
                        object-fit: cover;
                        max-width: 100%;
                    "
                    v-for="thumbnail in productThumbnails"
                    :src="thumbnail.url550"
                    preview
                    width="64"
                />
            </div>
        </div>

        <div class="col">
            <h2 class="mt-4 md:mt-0">{{ productDetails.name }}</h2>
            <span class="font-bold"
                >SKU:
                <span class="font-normal">{{ productDetails.sku }}</span></span
            >

            <hr />

            <p class="mt-3" v-html="productDetails.shortDescription" />
            <p class="mt-3" v-html="productDetails.fullDescription" />

            <!-- Stock -->
            <span class="block mt-5"
                ><span
                    class="font-bold"
                    :class="
                        productDetails.stockQuantity == 0
                            ? 'text-red-500'
                            : 'text-green-500'
                    "
                    >{{ productDetails.stockQuantity }}</span
                >
                na stanju.</span
            >

            <div
                class="mt-5 flex flex-column row-gap-1 justify-content-between"
            >
                <div>
                    <span class="mb-0 mr-2 mb-2 text-lg">Cijena</span>
                    <span class="font-bold"
                        >{{
                            formatProductPrice(resultsStore.product.price)
                        }}
                        €</span
                    >
                </div>
                <div>
                    <span class="m-0 mr-2 mb-2 text-lg">Cijena s popustom</span>
                    <span class="font-bold"
                        >{{ resultsStore.product.price }} €</span
                    >
                </div>
            </div>

            <!-- Price -->
            <!-- Buttons -->
            <Toolbar class="mt-4 bg-white-alpha-30">
                <template #start>
                    <Button
                        class="button--no-shadow mr-1"
                        icon="pi pi-minus"
                        severity="secondary"
                        outlined
                        @click="itemQuantity--"
                    />
                    <Button
                        class="mr-1"
                        severity="info"
                        outlined
                        :label="itemQuantity"
                        disabled
                    />
                    <Button
                        icon="pi pi-plus"
                        class="button--no-shadow"
                        severity="secondary"
                        outlined
                        @click="itemQuantity++"
                    />
                </template>

                <template #end>
                    <div>
                        <Button
                            class="button--no-shadow mr-2 text-sm"
                            label="Dodaj u košaricu"
                            severity="primary"
                            outlined
                            :disabled="productDetails.stockQuantity == 0"
                            @click="
                                handleAddProdcutToShoppingCart(
                                    resultsStore.product,
                                )
                            "
                        />
                        <Button
                            class="button--no-shadow text-sm"
                            label="Pošalji upit"
                            severity="secondary"
                            outlined
                            @click="handleSendInquiry"
                        />
                    </div>
                </template>
            </Toolbar>
        </div>
    </div>

    <!-- Tab Menu -->
    <div class="mt-6 grid column-gap-6">
        <div class="col">
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
                    <div class="flex justify-content-between">
                        <span class="uppercase">Proizvođač</span>
                        <!-- <span class="uppercase">{{
                        console.log(productDetails)
                    }}</span> -->
                        <span class="uppercase">{{
                            productDetails.manufacturerName
                        }}</span>
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
                        Svi proizvođači NOVIH auto dijelova garantiraju da su
                        njihovi proizvodi bez nedostataka u materijalu i izradi,
                        te da su testirani prije stavljanja u prodaju.
                    </p>
                    <p>
                        Garancija se daje krajnjem korisniku i odnosi se na
                        proizvode koji su kupljeni, instalirani i korišteni u
                        svrhe za koje su prvobitno dizajnirani. Garancija
                        prokriva nedostatke nastale u uobičajenoj uporabi i ne
                        pokriva neispravnost ili kvarove uslijed zloupotrebe,
                        zanemarivanja, nepravilne instalacije ili kvarova kod
                        održavanja.
                    </p>
                    <p>
                        Tijekom garancijskog razdoblja, proizvođač će ili
                        popraviti ili zamjenitit bez naknade proizvod koji je
                        neispravan u materijalu ili izradi.
                    </p>
                </TabPanel>
            </TabView>
        </div>
    </div>

    <!-- <Dialog
    modal
    closeOnEscape
    dismissableMask
    :closable="false"
    :visible="inquiry.isSending"
    @update:visible="inquiry.isSending = false"
    class="w-30rem"
>
    <template #header>
        <h2 class="text-center w-full pt-4">Pošalji upit</h2>
    </template>

    <label>Proizvod</label>
    <InputText
        v-model="inquiry.product"
        class="w-full mt-2 mb-3"
        disabled
    />

    <label>Ime</label>
    <InputText v-model="inquiry.name" class="w-full mt-2 mb-3" disabled />

    <label>Prezime</label>
    <InputText
        v-model="inquiry.lastName"
        class="w-full mt-2 mb-3"
        disabled
    />

    <label>Upit</label>
    <Textarea v-model="inquiry.text" rows="7" class="w-full mt-2 mb-6" />

    <div class="flex justify-content-end gap-2">
        <Button
            type="button"
            label="Odustani"
            severity="danger"
            @click="inquiry.isSending = false"
        />
        <Button
            type="button"
            label="Pošalji"
            severity="success"
            @click="inquiry.isSending = false"
        />
    </div>
</Dialog> -->
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

.button--no-shadow:focus {
    box-shadow: none !important;
}

/* upadate product image on smaller devices */
@media only screen and (max-width: 767px) {
    .image--product {
        height: 100%;
    }
}
</style>
