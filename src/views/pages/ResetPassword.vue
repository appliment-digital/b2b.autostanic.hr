<script>
import router from '@/router';
import UserService from '../../service/UserService.js';

const userService = new UserService();

export default {
    data() {
        return {
            email: null,
            verificationCode: null,
            password: null,
            passwordConfirm: null,
            errors: [],
        };
    },
    methods: {
        async resetPassword() {
            if (!this.validateForm()) return;

            try {
                const response = await userService.resetPassword({
                    email: this.email,
                    verification_code: this.verificationCode,
                    password: this.password,
                });

                if (response.data.error) {
                    this.errors.push(response.data.error);
                } else if (response.data.message) {
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Uspješno',
                        detail: response.data.message,
                        life: 3000,
                    });
                    router.push('/');
                }
            } catch (error) {
                this.errors.push(error.message);
            }
        },
        validateForm() {
            this.errors = [];

            if (
                !this.email ||
                !this.verificationCode ||
                !this.password ||
                !this.passwordConfirm
            ) {
                this.errors.push('Potrebno je ispuniti sva polja.');
            }

            if (this.password !== this.passwordConfirm) {
                this.errors.push(
                    'Nova lozinka i lozinka potvrde vam nisu iste.',
                );
            }

            return this.errors.length === 0;
        },
    },
};
</script>

<template>
    <h2 class="text-center">Postavi lozinku</h2>

    <div class="flex flex-column sm:w-3 mx-auto">
        <div class="mt-6">
            <label>E-mail<span class="text-red-500">*</span></label>
            <InputText v-model="email" class="w-full mt-2 mb-3" />

            <label>Verifikacijski kod<span class="text-red-500">*</span></label>
            <InputText v-model="verificationCode" class="w-full mt-2 mb-3" />

            <label>Nova lozinka<span class="text-red-500">*</span></label>
            <Password
                v-model="password"
                class="w-full mt-2 mb-3"
                inputClass="w-full"
                :feedback="false"
            />

            <label>Potvrdi lozinku<span class="text-red-500">*</span></label>
            <Password
                v-model="passwordConfirm"
                class="w-full mt-2 mb-3"
                inputClass="w-full"
                :feedback="false"
            />

            <div v-if="errors.length > 0" class="text-red-500 mb-3">
                <ul>
                    <li v-for="(error, index) in errors" :key="index">
                        {{ error }}
                    </li>
                </ul>
            </div>

            <Button
                class="block w-full"
                label="Resetiraj"
                @click="resetPassword()"
            />
        </div>
    </div>
</template>

<style scoped></style>
