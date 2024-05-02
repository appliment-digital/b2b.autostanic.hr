<script>
// components
import Header from '@/components/Header.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';

// pinia
import { mapStores } from 'pinia';
import { useShoppingCartStore } from '@/store/shoppingCartStore.js';

export default {
    components: {
        Header,
        Breadcrumbs,
    },
    data() {
        return {
            mock: {
                products: [
                    {
                        id: 1,
                        image: 'https://via.placeholder.com/48',
                        name: 'Product 1',
                        price: 10.99,
                        quantity: 2,
                    },
                    {
                        id: 2,
                        image: 'https://via.placeholder.com/48',
                        name: 'Product 2',
                        price: 19.99,
                        quantity: 1,
                    },
                    {
                        id: 3,
                        image: 'https://via.placeholder.com/48',
                        name: 'Product 3',
                        price: 5.49,
                        quantity: 3,
                    },
                    {
                        id: 4,
                        image: 'https://via.placeholder.com/48',
                        name: 'Product 4',
                        price: 8.79,
                        quantity: 1,
                    },
                ],
            },

            products: null,
        };
    },
    computed: {
        ...mapStores(useShoppingCartStore),
    },

    mounted() {
        console.log('cart mounted', this.shoppingCartStore.cart);

    }
};
</script>

<template>
    <Header />

    <Breadcrumbs />

    <h2 class="mb-4">Košarica</h2>

    <div class="grid grid-nogutter justify-content-between column-gap-3">
        <div class="col">
            <div class="card mb-0">
                <DataTable :value="shoppingCartStore.cart" tableStyle="min-width: 50rem">
                    <Column field="image" header="Slika">
                        <template #body="{ data }">
                            <img :src="data.pictureUrls[0]" class="table-image"/>
                        </template>
                    </Column>

                    <Column field="name" header="Proizvod"> </Column>

                    <Column field="price" header="Cijena"> </Column>

                    <Column header="Količina"> </Column>

                    <Column header="Obriši"> </Column>
                </DataTable>
            </div>
        </div>

        <div class="col">
            <div
                class="card min-h-full flex flex-column justify-content-evenly"
            >
                <h3 class="text-center">Ukupna narudžba</h3>

                <div class="grid w-15rem mx-auto">
                    <span class="col-6">Ukupno</span
                    ><span class="col-6 text-right">123,00 €</span>
                    <span class="col-6">Dostava</span
                    ><span class="col-6 text-right">10,00 €</span>
                    <span class="col-6">Porez</span
                    ><span class="col-6 text-right">10,00 €</span>
                    <span class="col-6">Sveukupno</span
                    ><span class="col-6 text-right">143,00 €</span>
                </div>

                <Button
                    label="Završi narudžbu"
                    class="block mx-auto mt-4"
                ></Button>
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
</style>
