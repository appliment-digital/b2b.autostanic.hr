<script>
import router from '@/router';
import UserService from '../../service/UserService.js';

const userService = new UserService();
export default {
    data() {
        return {
            email: null,
        };
    },
    methods: {
        forgotPassword() {
            userService
                .forgotPassword({ email: this.email })
                .then((data) => {
                    router.push('/reset');
                })
                .catch((response) => {});
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
                v-model="email"
                placeholder="email"
            />

            <Button
                @click="forgotPassword()"
                label="Submit"
                class="login-button"
            />
        </div>
    </div>
</template>

<style scoped></style>
