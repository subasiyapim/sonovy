<script setup lang="ts">
import AuthLayout from '@/Layouts/AuthLayout.vue'
import {FormElement} from '@/Components/Form'
import {PrimaryButton} from '@/Components/Buttons'
import {LockIcon, ChevronLeftIcon, SendIcon} from '@/Components/Icons'
import {Head, Link, useForm} from '@inertiajs/vue3';

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('register'));
}
</script>

<template>
  <AuthLayout>
    <template #icon>
      <LockIcon color="var(--strong-950)"/>
    </template>
    <h1 class="label-xl c-strong-950 !text-center"> Şifremi Sıfırla </h1>
    <p class="paragraph-sm c-sub-600 !text-center mb-6"> Şifrenizi sıfırlamak için e-posta adresinize 6 haneli sıfırlama
      kodu göndereceğiz. </p>
    <form @submit.prevent="submit" class="flex flex-col gap-3">
      <FormElement direction="vertical" label="Email Adresi" placeholder="hello@muzikdagitim.com"></FormElement>


      <PrimaryButton>
        Kod Gönder
        <template #suffix>
          <SendIcon class="ms-1" color="var(--dark-green-500)"/>
        </template>
      </PrimaryButton>
      <div class="text-end">
        <a :href="route('login')" class="label-xs c-neutral-500 flex items-center gap-1 justify-center mt-2">
          <ChevronLeftIcon color="var(--neutral-500)"/>
          Geri Dön</a>
      </div>
    </form>
  </AuthLayout>

</template>
