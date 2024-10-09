<script setup lang="ts">
import AuthLayout from '@/Layouts/AuthLayout.vue'
import {FormElement} from '@/Components/Form'
import {PrimaryButton} from '@/Components/Buttons'
import {LoginIcon} from '@/Components/Icons'
import { Head, Link, useForm } from '@inertiajs/vue3';
const form = useForm({
    email: '',
    password: '',
    remember: false,
});


const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <AuthLayout>
         <h1  class="label-xl c-strong-950 !text-center"> Tekrardan Hoşgeldiniz </h1>
         <p class="paragraph-sm c-sub-600 !text-center mb-6"> Yayınlarınızı yönetmek için giriş yapın </p>
        <form @submit.prevent="submit" class="flex flex-col gap-3">
            <FormElement v-model="form.email" direction="vertical" label="Email Adresi" placeholder="hello@muzikdagitim.com"></FormElement>
            <FormElement v-model="form.password" direction="vertical" label="Şifre" type="password" placeholder="• • • • • • • • • • "></FormElement>
           <div class="text-end">
             <a :href="route('password.request')" class="label-xs c-neutral-500">Şifremi Unuttum</a>
           </div>
            <PrimaryButton @click="submit">
                <template #suffix>
                    <LoginIcon class="ms-1" color="var(--dark-green-500)" />
                </template>
                Giriş Yap
            </PrimaryButton>
            <div class="flex items-center gap-1 justify-center my-3">
                    <p class="label-xs c-sub-600">Hesabınız yok mu?</p>
                    <a href="/register" class="label-xs c-dark-green-600">Hesap Oluştur</a>
            </div>
        </form>
    </AuthLayout>

</template>
