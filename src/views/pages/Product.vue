<script>
// utils
import { capitalizeFirstLetter } from '@/utils';

// pinia
import { mapStores } from 'pinia';
import { useUserStore } from '@/store/userStore.js';

// components
import Header from '@/components/Header.vue';

// services
import ProductService from '@/service/ProductService.js';
import Breadcrumbs from '@/components/Breadcrumbs.vue';

export default {
    components: {
        Header,
        Breadcrumbs,
    },
    data() {
        return {
            breadcrumbs: {
                home: {
                    icon: 'pi pi-home',
                    route: '/',
                },
                items: [],
            },

            tabMenuItems: [
                { label: 'Informacije 1', icon: 'pi pi-home' },
                { label: 'Informacije 2', icon: 'pi pi-chart-line' },
                { label: 'Informacije 3', icon: 'pi pi-list' },
            ],

            itemQuantity: '1',

            inquiry: {
                product: 'Product 1',
                isSending: false, // toggle <Dialog />
                name: 'Hello',
                lastName: 'Test',
                text: '',
            },
        };
    },
    watch: {
        itemQuantity(newVal, oldVal) {
            console.log({ newVal, oldVal });

            if (newVal <= 0) {
                this.itemQuantity = '0';
            }
        },
    },
    computed: {
        ...mapStores(useUserStore),
    },
    mounted() {
        // get some data to display as a product
        ProductService.getTestProduct()
            .then((response) => {
                console.log({ response });
                this.placeholderData = response.data;
            })
            .catch((err) => console.error(err));
    },
    updated() {
        const [name, lastName] = this.userStore.fullName.split(' ');

        this.inquiry.name = name;
        this.inquiry.lastName = lastName;
    },
    methods: {

        handleSendInquiry() {
            this.inquiry.isSending = true;
            console.log('sending inquiry...');
        },
    },
};
</script>

<template>
    <Header />

    <Breadcrumbs />

    <!-- Prodcut: Image & Description -->
    <div class="mt-4 grid column-gap-6">
        <div class="col-12 h-20rem md:col-6 md:h-auto">
            <img
                src="https://source.unsplash.com/random/?Car&10"
                class="image--product"
            />
        </div>

        <div class="col">
            <h2 class="mt-4 md:mt-0">Product Title</h2>
            <span class="font-bold"
                >SKU: <span class="font-normal">123883482130-AKVC</span></span
            >

            <hr />

            <p class="mt-3">
                Lorem ipsum, dolor sit amet consectetur adipisicing elit. Dicta
                ea nulla voluptatibus dolorem incidunt vitae animi earum
                necessitatibus tempore dolore! Hic ratione amet reiciendis
                maiores incidunt similique iste culpa repellat!
            </p>
            <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit.
                Necessitatibus velit sequi ex repellat, qui officia at nam nobis
                numquam eius nostrum error explicabo minus a sint alias enim
                nihil deleniti.
            </p>

            <!-- Stock -->
            <span class="block mt-5"
                ><span class="font-bold">5</span> na stanju.</span
            >

            <div
                class="mt-5 flex flex-column row-gap-1 justify-content-between"
            >
                <div>
                    <span class="mb-0 mr-2 mb-2 text-lg">Cijena</span>
                    <span class="font-bold">289,00 €</span>
                </div>
                <div>
                    <span class="m-0 mr-2 mb-2 text-lg">Cijena s popustom</span>
                    <span class="font-bold">259,00 €</span>
                </div>
            </div>

            <!-- Price -->
            <!-- Buttons -->
            <Toolbar class="mt-4 bg-white-alpha-40">
                <template #start>
                    <Button
                        icon="pi pi-plus"
                        class="button--no-shadow mr-1"
                        severity="secondary"
                        @click="itemQuantity++"
                    />
                    <Button
                        class="mr-1"
                        severity="secondary"
                        :label="itemQuantity"
                        disabled
                    />
                    <Button
                        class="button--no-shadow"
                        icon="pi pi-minus"
                        severity="secondary"
                        @click="itemQuantity--"
                    />
                </template>

                <template #end>
                    <div>
                        <Button
                            class="button--no-shadow mr-2 text-sm"
                            label="Dodaj u košaricu"
                            severity="primary"
                        />
                        <Button
                            class="button--no-shadow text-sm"
                            label="Pošalji upit"
                            severity="secondary"
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
            <TabMenu :model="tabMenuItems" />
        </div>
    </div>

    <Dialog
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

        <!-- Dialog Input: Subject -->
        <label>Proizvod</label>
        <InputText
            v-model="inquiry.product"
            class="w-full mt-2 mb-3"
            disabled
        />

        <!-- Dialog Input: Name -->
        <label>Ime</label>
        <InputText v-model="inquiry.name" class="w-full mt-2 mb-3" disabled />

        <!-- Dialog Input: Last Name -->
        <label>Prezime</label>
        <InputText
            v-model="inquiry.lastName"
            class="w-full mt-2 mb-3"
            disabled
        />

        <!-- Dialog Input: Text -->
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
    </Dialog>
</template>

<style scoped>
.image--product {
    display: block;
    width: 100%;
    height: 500px;
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
