<script>
import Header from '@/components/Header.vue';

// utils
import { capitalizeFirstLetter } from '@/utils';

// pinia
import { mapStores } from 'pinia';
import { useUserStore } from '@/store/userStore.js';

// services
import ProductService from '@/service/ProductService.js';

export default {
    components: {
        Header,
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

            subcategories: ['user 1', 'user 2', 'user 3'],

            tabMenuItems: [
                { label: 'Informacije 1', icon: 'pi pi-home' },
                { label: 'Informacije 2', icon: 'pi pi-chart-line' },
                { label: 'Informacije 3', icon: 'pi pi-list' },
            ],

            itemQuantity: '3',

            // Inquiry (Pošalji upit)
            inquiry: {
                subject: 'Inquiry 1',
                isSending: false,
                name: 'Hello',
                lastName: "Test",
                text: "",
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
        const previousUrl = window.history.state.back;
        console.log({ previousUrl });
        this.handleNavigatingCategories(previousUrl);

        console.log({ robohas: this.userStore.robohashName });

        // get some data to display as a product
        ProductService.getTestProduct()
            .then((response) => {
                console.log({ response });
                this.placeholderData = response.data;
            })
            .catch((err) => console.error(err));
    },
    updated() {
        console.log(this.placeholderData);
    },
    methods: {
        handleNavigatingCategories(previousUrl) {
            console.log(this.$route.path);

            let parts;
            parts = previousUrl
                .split('/')
                .concat([this.$route.path.replace('/', '')]);

            console.log({ parts });

            parts = parts.filter((part) => part !== '');

            let url = '';

            this.breadcrumbs.items = parts.map((part, i) => {
                url += `/${part}`;

                return {
                    label: capitalizeFirstLetter(part.replaceAll('-', ' ')),
                    route: url,
                };
            });
        },

        handleSendInquiry() {
            this.inquiry.isSending = true;
            console.log('sending inquiry...');
        },
    },
};
</script>

<template>
    <Header />

    <Breadcrumb
        :home="breadcrumbs.home"
        :model="breadcrumbs.items"
        class="mt-5"
    >
        <template #item="{ item, props }">
            <RouterLink
                v-if="item.route"
                v-slot="{ href, navigate }"
                :to="item.route"
                custom
            >
                <a :href="href" v-bind="props.action" @click="navigate">
                    <span :class="[item.icon, 'text-800']" />
                    <span class="text-800">{{ item.label }}</span>
                </a>
            </RouterLink>
            <a
                v-else
                :href="item.url"
                :target="item.target"
                v-bind="props.action"
            >
                <span class="text-color">{{ item.label }}</span>
            </a>
        </template>
    </Breadcrumb>

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
                            severity="secondary"
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
        :visible="inquiry.isSending"
        @update:visible="inquiry.isSending = false"
        class="w-30rem"
    >

        <!-- Dialog Input: Subject -->
        <label>Subjekt</label>
        <InputText v-model="inquiry.subject" class="w-full mt-2 mb-3" />

        <!-- Dialog Input: Name -->
        <label>Ime</label>
        <InputText v-model="inquiry.name" class="w-full mt-2 mb-3" />

        <!-- Dialog Input: Last Name -->
        <label>Prezime</label>
        <InputText v-model="inquiry.lastName" class="w-full mt-2 mb-3" />

        <!-- Dialog Input: Text -->
        <label>Upit</label>
        <Textarea v-model="inquiry.text" rows="5" class="w-full mt-2 mb-6" />

        <div class="flex justify-content-end gap-2">
            <Button
                type="button"
                label="Cancel"
                severity="secondary"
                @click="inquiry.isSending = false"
            ></Button>
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
