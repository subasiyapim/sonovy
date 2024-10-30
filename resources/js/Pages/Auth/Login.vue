<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import {FormElement} from '@/Components/Form'
import {PrimaryButton} from '@/Components/Buttons'
import {LoginIcon} from '@/Components/Icons'
import {Head, Link, useForm} from '@inertiajs/vue3';

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
    <h1 class="label-xl c-strong-950 !text-center" v-text="__('client.login.title')"/>
    <p class="paragraph-sm c-sub-600 !text-center mb-6" v-text="__('client.login.subtitle')"/>
    <form @submit.prevent="submit" class="flex flex-col gap-3">
      <FormElement :required="true"
                   v-model="form.email"
                   :error="form.errors.email"
                   direction="vertical"
                   :label="__('client.login.fields.email')"
                   :placeholder="__('client.login.fields.email_placeholder')"/>
      <FormElement :required="true"
                   v-model="form.password"
                   :error="form.errors.password"
                   direction="vertical"
                   :label="__('client.login.fields.password')"
                   type="password"
                   :placeholder="__('client.login.fields.password_placeholder')"/>
      <div class="text-end">
        <a :href="route('password.request')" class="label-xs c-neutral-500"
           v-text="__('client.login.forgot_password')"/>
      </div>
      <PrimaryButton @click="submit">
        <template #suffix>
          <LoginIcon class="ms-1" color="var(--dark-green-500)"/>
        </template>
        {{ __('client.login.login_btn') }}
      </PrimaryButton>
      <div class="flex items-center gap-1 justify-center my-3">
        <p class="label-xs c-sub-600" v-text="__('client.login.dont_have_account')"/>
        <a href="/register" class="label-xs c-dark-green-600" v-text="__('client.login.register')"/>
      </div>
    </form>
  </AuthLayout>

</template>
