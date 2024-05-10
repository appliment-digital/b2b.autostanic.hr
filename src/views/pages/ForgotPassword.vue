<script>
import UserService from '@/service/UserService.js';

const userService = new UserService();
export default {
    data() {
        return {
            email: null,
        };
    },
    methods: {
        handleSendResetEmailClick() {
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
                        this.$router.push('/auth/reset-password');
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
    <section>
        <h2>Resetiranje lozinke</h2>
        <p>
            Molim vas unesite svoju e-mail adresu kako bi vam poslali
            verifikacijski kod za ponovno postavljenje lozinke.
        </p>

        <div class="mt-6 flex justify-content-between align-items-center column-gap-3">
            <InputText
                class="w-full"
                type="text"
                v-model="email"
                placeholder="email"
            />

            <Button
                class="w-7rem"
                label="Pošalji"
                @click="handleSendResetEmailClick()"
            />
        </div>
    </section>
</template>

<style scoped></style>
