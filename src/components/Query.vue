<script>
// pinia
import { mapStores } from 'pinia';
import { useUserStore } from '@/store/userStore.js';

import BitrixService from '@/service/BitrixService.js';

const bitrixService = new BitrixService();
export default {
    props: ['showQueryModal'],

    data() {
        return {
            user: null,
            isDialogVisible: false,
            sendingQuery: false,
        };
    },
    mounted() {
        this.user = this.userStore.user;
        this.isDialogVisible = this.showQueryModal;
    },
    watch: {
        showQueryModal(newVal) {
            this.isDialogVisible = newVal;
        },
    },
    computed: {
        ...mapStores(useUserStore),
    },
    methods: {
        closeDialog() {
            this.isDialogVisible = false;
            this.$emit('on-query-modal-click');
        },
        sendQuery() {
            console.log('test');
            this.sendingQuery = true;
            bitrixService.sendQuery(this.user).then((response) => {
                if (response.data > 0) {
                    this.sendingQuery = false;
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Uspješno',
                        detail: 'je poslan upit.',
                        life: 3000,
                    });
                    this.closeDialog();
                } else {
                    this.sendingQuery = false;
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Greška',
                        detail: 'Došlo je do greške pri slanju upita.',
                        life: 3000,
                    });
                }
            });
        },
    },
};
</script>

<template>
    <Dialog
        modal
        dismissableMask
        closeOnEscape
        header="POŠALJITE UPIT"
        :visible="isDialogVisible"
        :style="{ width: '30rem' }"
        :filters="filters"
    >
        <div v-if="sendingQuery" class="card flex justify-content-center">
            <div class="flex justify-center">
                <ProgressSpinner
                    style="width: 80px; height: 100px"
                    strokeWidth="3"
                    fill="var(--surface-ground)"
                />
            </div>
            <div class="flex justify-center align-items-center">
                Slanje upita u tijeku...
            </div>
        </div>
        <div v-else>
            <p class="text-red-500">
                Polja označena s zvijezdicom(*) su obavezna!
            </p>
            <label>Ime<span class="text-red-500">*</span></label>
            <InputText v-model="user.name" class="w-full mt-2 mb-3" />

            <label>Prezime<span class="text-red-500">*</span></label>
            <InputText v-model="user.last_name" class="w-full mt-2 mb-3" />

            <label>E-mail<span class="text-red-500">*</span></label>
            <InputText v-model="user.email" class="w-full mt-2 mb-3" />

            <label>Upit<span class="text-red-500">*</span></label>
            <Textarea
                class="w-full mt-3"
                v-model="user.message"
                rows="5"
                cols="30"
                placeholder="Vaš upit..."
            />

            <div class="flex justify-content-end">
                <Button
                    class="block mt-5 mr-2"
                    severity="secondary"
                    label="Odustani"
                    style="min-width: 100px"
                    @click="closeDialog()"
                />
                <Button
                    class="block mt-5"
                    severity="success"
                    label="Spremi"
                    style="min-width: 100px"
                    @click="sendQuery()"
                />
            </div>
        </div>
    </Dialog>
</template>

<style scoped></style>
