<script>
import router from '@/router';

export default {
    data() {
        return {
            visible: false,
            dialog: false,

            users: [],
            searchValue: null,
        };
    },
    methods: {},
};
</script>

<template>
    <div class="flex flex-column sm:w-6 mx-auto mb-4">
        <h2>Admin Panel</h2>
        <span>Upravljanje korisnicima</span>
    </div>

    <Sidebar v-model:visible="visible" header="Panel">
        <Button
            class="block mt-5 mb-3"
            severity="secondary"
            icon="pi pi-user"
            label="Korisnici"
            style="min-width: 220px;"
        />
        <Button
            class="block mb-3"
            severity="success"
            icon="pi pi-wallet"
            label="Kategorije popusta"
            style="min-width: 220px;"
        />
        <Button
            class="block mb-3"
            severity="warning"
            icon="pi pi-globe"
            label="Baneri"
            style="min-width: 220px;"
        />
        <Button
            severity="help"
            class="block mb-3"
            icon="pi pi-megaphone"
            label="Objave"
            style="min-width: 220px;"
        />
    </Sidebar>

    <div class="flex justify-content-between mb-4">
        <Button
            icon="pi pi-chevron-right"
            label="Panel"
            @click="visible = true"
        />
        <div class="flex">
            <Button icon="pi pi-plus" label="Dodaj" class="mr-3" />
            <InputText
                type="text"
                v-model="searchValue"
                placeholder="Pretraži"
                style="min-width: 200px"
            />
        </div>
    </div>

    <DataTable :value="users" tableStyle="min-width: 50rem">
        <Column field="firstName" header="Ime"></Column>
        <Column field="lastName" header="Prezime"></Column>
        <Column field="email" header="E-mail"></Column>
        <Column field="isActive" header="Aktivan"></Column>
        <Column field="actions" header="Akcije"></Column>
        <Column field="deliveryPlace" header="Mjesto isporuke"></Column>
        <Column field="paymentType" header="Način plaćanja"></Column>
        <Column field="discountType" header="Tip rabata"></Column>
    </DataTable>

    <Dialog
        v-model:visible="dialog"
        modal
        header="Detalji korisnika"
        :style="{ width: '25rem' }"
    >
        <p>
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Sint eaque
            incidunt saepe veniam eveniet consequuntur voluptates earum sit
            explicabo nostrum expedita quibusdam, aspernatur obcaecati, illum,
            dicta minus ipsum quas odit.
        </p>
    </Dialog>
</template>

<style scoped></style>
