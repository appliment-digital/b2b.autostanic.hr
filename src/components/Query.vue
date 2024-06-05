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
        v-model:visible="isDialogVisible"
        header="POŠALJITE UPIT"
        :style="{ width: '30rem' }"
        :modal="true"
        :closable="false"
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
            <label>Ime</label>
            <InputText v-model="user.name" disabled class="w-full mt-2 mb-3" />

            <label>Prezime</label>
            <InputText
                v-model="user.last_name"
                disabled
                class="w-full mt-2 mb-3"
            />

            <label>E-mail</label>
            <InputText v-model="user.email" disabled class="w-full mt-2 mb-3" />

            <label>Upit<span class="text-red-500">*</span></label>
            <Textarea
                class="w-full mt-3"
                v-model="user.message"
                rows="5"
                cols="30"
                placeholder="Vaš upit..."
            />

            <div class="flex justify-content-between">
                <Button
                    class="block mt-5 mr-2"
                    severity="secondary"
                    label="Odustani"
                    style="min-width: 100px"
                    @click="closeDialog()"
                />
                <Button
                    v-if="user.message"
                    class="block mt-5"
                    severity="success"
                    label="Spremi"
                    style="min-width: 100px"
                    @click="sendQuery()"
                />
                <Button
                    v-else
                    class="block mt-5"
                    severity="success"
                    label="Spremi"
                    style="min-width: 100px"
                    disabled
                />
            </div>
        </div>
    </Dialog>
</template>

<style scoped></style>
