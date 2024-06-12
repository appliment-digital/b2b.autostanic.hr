<script>
// pinia
import { mapStores } from 'pinia';
import { useUserStore } from '@/store/userStore.js';
import { useShoppingCartStore } from '@/store/shoppingCartStore.js';

// services
import UserService from '../../service/UserService.js';

const userService = new UserService();

export default {
    data() {
        return {
            email: null,
            password: null,
        };
    },
    computed: {
        ...mapStores(useUserStore, useShoppingCartStore),
    },
    methods: {
        handleForgotPasswordClick() {
            this.$router.push('/auth/forgot-password');
        },
        login() {
            userService
                .login(this.email, this.password)
                .then((response) => {
                    if (response.error) {
                        this.$toast.add({
                            severity: 'error',
                            summary: 'Error',
                            detail: response.error,
                            life: 3000,
                        });
                    }
                    if (response.success) {
                        this.$toast.add({
                            severity: 'success',
                            summary: 'Uspješna prijava',
                            detail:
                                response.message +
                                ', ' +
                                response.data.name +
                                ' ' +
                                response.data.last_name,
                            life: 3000,
                        });
                        this.userStore.add(response.data);
                    }
                })
                .then(() => {
                    // set shopping cart data relative to user
                    this.$router.push('/');
                    this.shoppingCartStore.init();
                })
                .catch((error) => {
                    console.log({ error });
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Greška',
                        detail: error.data.error,
                        life: 3000,
                    });
                });
        },
    },
};
</script>

<template>
    <section>
        <img
            src="/images/as-logo.png"
            class="block mx-auto mb-6"
            style="height: 48px"
        />

        <InputText
            class="w-full mb-3"
            type="text"
            v-model="email"
            placeholder="E-mail"
            @keyup.enter="login()"
        />
        <Password
            class="w-full"
            inputClass="w-full"
            placeholder="Lozinka"
            :feedback="false"
            v-model="password"
            @keyup.enter="login()"
        />

        <div class="flex justify-content-between mt-6 w-full">
            <a
                @click="handleForgotPasswordClick"
                label=""
                class="underline text-300 flex align-items-center cursor-pointer"
                >Zaboravljena lozinka?</a
            >
            <Button
                @click="login()"
                label="Prijavi se"
                style="background-color: #123649; border: none"
            />
        </div>
    </section>
</template>
