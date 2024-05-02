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
                .then((response) => {
                    if (response.data.error) {
                        this.$toast.add({
                            severity: 'error',
                            summary: 'Greška',
                            detail: response.data.error,
                            life: 3000,
                        });
                    }
                    if (response.data.message) {
                        this.$toast.add({
                            severity: 'success',
                            summary: 'Uspješno',
                            detail: response.data.message,
                            life: 3000,
                        });
                        router.push('/reset');
                    }
                })
                .catch((response) => {
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Greška',
                        detail: response.data.error,
                        life: 3000,
                    });
                });
        },
    },
};
</script>

<template>
    <div class="flex flex-column sm:w-4 mx-auto">
        <h2>Resetiranje lozinke</h2>
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
                label="Pošalji"
                class="login-button"
            />
        </div>
    </div>
</template>

<style scoped></style>
