<script setup lang="ts">
import AuthLayout from '@/Layouts/AuthLayout.vue'
import {FormElement} from '@/Components/Form'
import {PrimaryButton} from '@/Components/Buttons'
import {Head, Link, useForm} from '@inertiajs/vue3';
import {AddIcon} from '@/Components/Icons';
import InputError from "@/Components/InputError.vue";

const form = useForm({
  name: '',
  surname: '',
  email: '',
  phone: '',
  password: '',
});

const submit = () => {
  form.post(route('register'));
}
</script>

<template>
  <AuthLayout>
    <h1 class="label-xl c-strong-950 !text-center" v-text="__('client.register.title')"/>
    <p class="paragraph-sm c-sub-600 !text-center mb-6" v-text="__('client.register.subtitle')"/>
    <form @submit.prevent="submit" class="flex flex-col gap-3">
      <div class="flex gap-2 items-center">
        <FormElement class="flex-1"
                     v-model="form.name"
                     :error="form.errors.name"
                     direction="vertical"
                     :placeholder="__('client.register.fields.name_placeholder')"
                     :label="__('client.register.fields.name')"/>
        <FormElement class="flex-1"
                     v-model="form.surname"
                     :error="form.errors.surname"
                     direction="vertical"
                     :placeholder="__('client.register.fields.surname_placeholder')"
                     :label="__('client.register.fields.surname')"/>
      </div>

      <FormElement
          direction="vertical"
          v-model="form.email"
          :error="form.errors.email"
          :label="__('client.register.fields.email')"
          :placeholder="__('client.register.fields.email_placeholder')"
          type="text"/>
      <FormElement
          v-model="form.phone"
          direction="vertical"
          :error="form.errors.phone"
          :label="Telefon"
          :placeholder="(__('client.register.fields.phone_placeholder'))"
          type="phone"/>
      <FormElement
          v-model="form.password"
          direction="vertical"
          :error="form.errors?.password"
          :label="__('client.register.fields.password')"
          :placeholder="__('client.register.fields.password_placeholder')"
          type="password"/>

      <PrimaryButton type="button">
        <template #icon>
          <AddIcon/>
        </template>
        {{ __('client.register.register_btn') }}
      </PrimaryButton>
      <div class="flex items-center gap-1 justify-center my-3">
        <p class="label-xs c-sub-600" v-text="__('client.register.already_have_account')"/>
        <a href="/login" class="label-xs c-dark-green-600" v-text="__('client.register.login_btn')"/>
      </div>
    </form>
  </AuthLayout>

</template>
