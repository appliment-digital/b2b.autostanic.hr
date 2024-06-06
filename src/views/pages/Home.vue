<script>
import slug from 'slug';

// utils
import {
    setSlugCharMap,
    shortenCarAcousticsAndElectronicsCategoryName,
} from '@/utils';

// pinia
import { mapStores } from 'pinia';
import { useCategoryStore } from '@/store/categoryStore.js';
import { useUIStore } from '@/store/UIStore.js';

// components
import Header from '@/components/Header.vue';
import Query from '@/components/Query.vue';

// services
import CategoryService from '@/service/CategoryService.js';

setSlugCharMap(slug);

const news = [
    {
        text: `Run flat gume su gume na kojima nakon probijanja ili
        puknuća možete nastaviti voziti kako bi mogli stići
        do vulkanizera ili pronaći sigurno, ravno mjesto za
        promjenu gume bez da oštetite felge.`,
    },
    {
        text: `Na našem području još uvijek ima više automobila s
        ručnim mjenjačem nego s automatskim. Ipak, u novije
        vrijeme sve je više automobila s automatskim
        mjenjačem, a neki proizvođači polako ukidaju ručne
        mjenjače.`,
    },
    {
        text: `Tipični automatski mjenjači nemaju papučicu spojke
        kao automobil s ručnim mjenjačem. Umjesto toga, oni
        koriste uređaj koji se zove pretvarač okretnog
        momenta. U nastavku ćemo objasniti što je DSG, kako
        radi.`,
    },
];

export default {
    components: {
        Header,
        Query,
    },
    data() {
        return {
            categories: null,
            news,
            showQueryModal: false,
            searchCode: [
                { name: 'Broj artikla' },
                { name: 'Broj dijela' },
                { name: 'Šifra motora' },
                { name: 'Šifra mjenjača' },
            ],
            selectedCode: null,
        };
    },
    computed: {
        ...mapStores(useCategoryStore, useUIStore),
    },
    mounted() {
        this.loadMainCategories();
    },
    watch: {
        selectedCode() {
            console.log(this.selectedCode);
        },
    },
    methods: {
        loadMainCategories() {
            this.UIStore.setIsDataLoading(true);

            CategoryService.getMainCategories()
                .then((response) => {
                    if (response.data.length) {
                        this.UIStore.setIsDataLoading(false);

                        this.categories = response.data;
                        this.categoryStore.addMainCategories(response.data);

                        // update category name to be shorter for better UX
                        this.categories.forEach(
                            shortenCarAcousticsAndElectronicsCategoryName,
                        );
                    }
                })
                .catch((err) => console.error(err));
        },

        handleCategoryClick(category) {
            this.categoryStore.addHistory(category);

            this.$router.push({
                path: `/${slug(category.name)}`,
            });
        },

        handleNewsCardClick(link) {
            window.open(link, '_blank');
        },

        handleNewsCardTextSize(text) {
            const allowedNumOfChars = 200;

            if (text.length > allowedNumOfChars) {
                return text.substring(0, allowedNumOfChars) + ' ...,';
            } else {
                return text;
            }
        },

        handleQueryModalClick() {
            this.showQueryModal = !this.showQueryModal;
        },

        getSearchResults() {},
    },
};
</script>

<template>
    <!-- Home Page: Banner -->
    <div
        class="banner mt-3 surface-300 flex flex-column align-items-center justify-content-center overflow-hidden border-round"
    >
        <h3 class="text-white uppercase text-5xl m-0">B2B Auto Stanić</h3>
        <span class="text-white text-xl mt-2">Sve na jednom mjestu</span>
    </div>

    <!-- Home Page: Search & Categories -->
    <div class="w-full mx-auto mt-2 grid gap-2">
        <!-- Search -->
        <div
            class="col-12 pt-7 flex justify-content-center surface-100 bg-white border-100 border-round border-1 md:col"
        >
            <div>
                <h3 class="text-700 mt-0 mb-2 pt-1">Pretraži prema kodu</h3>
                <span class="block text-500 mb-6"
                    >Pretraži auto dijelove koristeći tvoj kod proizvoda za brzu
                    pretragu.</span
                >

                <!-- Checkboxes -->
                <div
                    class="p-0 flex flex-column column-gap-3 sm:col sm:flex-row"
                >
                    <div class="flex flex-row gap-3">
                        <div
                            v-for="code in searchCode"
                            class="flex align-items-center"
                        >
                            <RadioButton
                                v-model="selectedCode"
                                :inputId="code"
                                name="dynamic"
                                :value="code"
                            />
                            <label :for="code" class="ml-2">{{
                                code.name
                            }}</label>
                        </div>
                    </div>
                </div>

                <!-- Input -->
                <div
                    class="flex flex-column justify-content-between mt-2 gap-2 sm:flex-row md:flex-column lg:flex-row"
                >
                    <InputText
                        type="text"
                        class="w-20rem sm:w-full md:w-23rem"
                        placeholder="Pretraživanje..."
                    />
                    <Button
                        label="Pretraži"
                        class="button--submit block w-full sm:w-4 md:w-full lg:w-4"
                        @click="getSearchResults()"
                    />
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div
            class="categories py-7 col-12 md:col bg-white border-100 border-round border-1 flex align-items-center justify-content-center"
        >
            <div
                class="grid justify-content-center row-gap-1 column-gap-1 align-items-start"
            >
                <!-- prettier-ignore -->
                <ProgressSpinner v-if="UIStore.isDataLoading" class="mx-auto" strokeWidth="2"/>
                <div
                    v-else
                    v-for="category in categories"
                    class="col-4 md:col-4 lg:col-3 flex flex-column justify-content-center align-items-center cursor-pointer"
                    @click="handleCategoryClick(category)"
                >
                    <div
                        class="py-3 border-1 border-100 p-4 border-round hover:shadow-2"
                    >
                        <img
                            :src="category.pictureUrl"
                            style="width: 68px"
                            class="block mx-auto"
                        />
                    </div>
                    <span class="text-sm text-center mt-2">{{
                        category.name
                    }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Home Page: News -->
    <section class="mt-2 bg-white border-1 border-100 border-round p-8">
        <h3 class="text-700 mt-0 mb-2 text-center">
            Savjeti za Vas i Vaš automobil
        </h3>
        <span class="block text-500 mb-6 text-center"
            >Pročitajte kratke zanimljivosti vezane uz vašeg limenog
            ljubimca.</span
        >

        <div
            class="grid grid-nogutter row-gap-6 md:column-gap-0 lg:column-gap-3"
        >
            <div class="mx-auto col-12 sm:col-8 md:col">
                <Card
                    :pt="{
                        title: {
                            class: 'mb-0',
                        },
                    }"
                    class="surface-50 cursor-pointer border-1 border-200 hover:shadow-3"
                    @click="
                        handleNewsCardClick(
                            'https://www.autostanic.hr/blog/sto-su-run-flat-gume',
                        )
                    "
                >
                    <template #header>
                        <div class="p-4 pb-0">
                            <img
                                src="https://www.autostanic.hr/Content/Images/uploaded/test/run flat gume.jpeg"
                                class="h-10rem w-full border-1 border-200 border-round"
                                style="object-fit: cover"
                            />
                        </div>
                    </template>
                    <template #title
                        ><span class="text-xl"
                            >Prednosti i mane run flat guma</span
                        ></template
                    >
                    <template #content>
                        <p>
                            {{ handleNewsCardTextSize(news[0].text) }}
                            <span class="font-bold">saznaj više.</span>
                        </p>
                    </template>
                </Card>
            </div>
            <div class="mx-auto col-12 sm:col-8 md:col">
                <Card
                    :pt="{
                        title: {
                            class: 'mb-0',
                        },
                    }"
                    class="surface-50 cursor-pointer border-1 border-200 hover:shadow-3"
                    @click="
                        handleNewsCardClick(
                            'https://www.autostanic.hr/blog/simptomi-kvara-kvacila',
                        )
                    "
                >
                    <template #header>
                        <div class="p-4 pb-0">
                            <img
                                src="https://www.autostanic.hr/Content/Images/uploaded/test/simptomi-kvara-kvačila_web.jpg"
                                class="h-10rem w-full border-1 border-200 border-round"
                                style="object-fit: cover"
                            />
                        </div>
                    </template>
                    <template #title
                        ><span class="text-xl"
                            >5 Simptoma Kvara Kvačila</span
                        ></template
                    >
                    <template #content>
                        <p>
                            {{ handleNewsCardTextSize(news[1].text) }}
                            <span class="font-bold">saznaj više.</span>
                        </p>
                    </template>
                    hello
                </Card>
            </div>
            <div class="mx-auto col-12 sm:col-8 md:col">
                <Card
                    class="surface-50 cursor-pointer border-1 border-200 hover:shadow-3"
                    :pt="{
                        title: {
                            class: 'mb-0',
                        },
                    }"
                    @click="
                        handleNewsCardClick(
                            'https://www.autostanic.hr/blog/simptomi-kvara-dsg-mjenjaca',
                        )
                    "
                >
                    <template #header>
                        <div class="p-4 pb-0">
                            <img
                                src="https://www.autostanic.hr/Content/Images/uploaded/test/simptomi-kvara-dsg-mjenjaca_web.jpg"
                                class="h-10rem w-full border-1 border-200 border-round"
                                style="object-fit: cover"
                            />
                        </div>
                    </template>
                    <template #title
                        ><span class="text-xl"
                            >4 Simptoma kvara DSG Mjenjača</span
                        ></template
                    >
                    <template #content>
                        <p>
                            {{ handleNewsCardTextSize(news[2].text) }}
                            <span class="font-bold">saznaj više.</span>
                        </p>
                    </template>
                </Card>
            </div>
        </div>
    </section>

    <!-- Home Page: Footer -->
    <footer class="mt-3">
        <div
            class="w-full mx-auto p-8 bg-white border-round grid column-gap-2 row-gap-8 justify-content-center align-items-center border-100 border-round border-1 md:column-gap-4"
        >
            <!-- Footer: Company Information -->
            <div class="col-12 md:col-5">
                <span class="block"
                    ><span class="font-bold">Naziv</span>: Auto Stanić
                    d.o.o.</span
                >
                <span class="block mt-2"
                    ><span class="font-bold">Sjedište</span>: Dravska 80c, 42202
                    Trnovec Bartolovečki</span
                >
                <span class="block mt-2"
                    ><span class="font-bold">OIB</span>: 75746481867</span
                >
                <span class="block mt-2"
                    ><span class="font-bold">Direktor</span>: mag. inf. Alen
                    Stanić</span
                >
                <span class="block mt-2"
                    ><span class="font-bold">Nadležni sud</span>: Trgovački sud
                    u Varaždinu</span
                >
                <span class="block mt-2"
                    ><span class="font-bold">Temeljni kapital</span>: 2.654,46
                    EUR uplaćen u cijelosti</span
                >
                <span class="block mt-2"
                    ><span class="font-bold">Transakcijski računi</span>:<br />
                    OTP banka d.d. <br />
                    SWIFT:OTPVHR2X <br />
                    Varaždin. IBAN: HR9824070001100504805
                </span>
            </div>

            <!-- Footer: Contact Us  -->
            <div class="flex-order-1 col-12 md:col-5">
                <div
                    class="flex flex-column justify-content-center align-items-center gap-3 cursor-pointer"
                >
                    <div>
                        <h2>Kontaktiraj nas</h2>
                        <Button
                            icon="pi pi-envelope"
                            label="Pošalji upit"
                            class="button--submit block w-full"
                            @click="handleQueryModalClick()"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer: Copy Rights -->
        <div class="h-6rem">
            <p class="mt-6 text-center">
                Copyright & copy; 2024 Auto Stanić. Sva prava pridržana. Powered
                by Appliment
            </p>
        </div>
    </footer>
    <Query
        :showQueryModal="showQueryModal"
        @on-query-modal-click="handleQueryModalClick"
    ></Query>
</template>

<style scoped>
.banner {
    width: 100%;
    height: 280px;
    background: url('/images/banner-old.jpeg');
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
}

.img--banner {
    width: 100%;
    object-fit: cover;
}

.button--submit {
    background-color: #123649;
    border: none;
}

.categories {
    min-height: 390.5px;
}
</style>
