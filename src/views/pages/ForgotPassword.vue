<script>
import router from '@/router';

export default {
    data() {
        return {
            message: 'Hello Vue.js!',
        };
    },
    methods: {
        changeMessage() {
            this.message = 'Vue.js is awesome!';
        },

        handleResetButtonClick() {
            router.push('/reset');
            console.log('hi');
        },
    },
};
</script>

<template>
    <div class="flex flex-column sm:w-4 mx-auto">
        <h2>Resetiranje Lozinke</h2>
        <p>
            Molim vas unesite svoju e-mail adresu kako bi vam poslali
            verifikacijski kod za ponovno postavljenje lozinke.
        </p>

        <div class="mt-6 flex justify-content-between align-items-center">
            <InputText
                class="login-input w-8"
                type="text"
                v-model="value"
                placeholder="email"
            />

            <Button @click="handleResetButtonClick" label="Submit" class="login-button" />
        </div>
    </div>
</template>

<style scoped></style>
