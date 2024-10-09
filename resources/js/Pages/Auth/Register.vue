<script setup lang="ts">
import AuthLayout from '@/Layouts/AuthLayout.vue'
import {FormElement} from '@/Components/Form'
import {PrimaryButton} from '@/Components/Buttons'
import { Head, Link, useForm } from '@inertiajs/vue3';
import { AddIcon} from '@/Components/Icons';
const form = useForm({
    email: '',
    password: '',
    remember: false,
});
</script>

<template>
    <AuthLayout>
         <h1  class="label-xl c-strong-950 !text-center"> Ücretsiz Hesap Oluştur </h1>
         <p class="paragraph-sm c-sub-600 !text-center mb-6"> Hızlıca yayın oluşturmak için ücretsiz hesabınızı oluşturun </p>
        <form @submit.prevent="submit" class="flex flex-col gap-3">
           <div class="flex gap-2 items-center">
                <FormElement class="flex-1" direction="vertical" placeholder="Adı" label="Ad"></FormElement>
                <FormElement class="flex-1" direction="vertical" placeholder="Soyadı" label="Soyad"></FormElement>
           </div>
            <FormElement direction="vertical" label="E-mail" placeholder="hello@muzikdagitim.com" type="text"></FormElement>
            <FormElement direction="vertical" label="Telefon" placeholder="(555) 000-0000" type="phone"></FormElement>
            <FormElement direction="vertical" label="Şifre" placeholder="• • • • • • • • • • " type="password"></FormElement>
            <PrimaryButton>
                <template #icon>
                    <AddIcon />
                </template>
                Giriş Yap
            </PrimaryButton>
            <div class="flex items-center gap-1 justify-center my-3">
                    <p class="label-xs c-sub-600">Zaten hesabım var?</p>
                    <a href="/login" class="label-xs c-dark-green-600">Giriş yap</a>
            </div>
        </form>
    </AuthLayout>

</template>
