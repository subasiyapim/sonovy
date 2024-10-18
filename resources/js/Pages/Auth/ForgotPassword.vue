<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import {FormElement} from '@/Components/Form'
import {PrimaryButton} from '@/Components/Buttons'
import {LockIcon, ChevronLeftIcon, SendIcon} from '@/Components/Icons'
import {Head, Link, useForm} from '@inertiajs/vue3';

const form = useForm({
  email: '',
});

const submit = () => {
  form.post(route('password.email'), {
    onFinish: () => {
      form.reset();
    },
  });
}
</script>

<template>
  <AuthLayout>
    <template #icon>
      <LockIcon color="var(--strong-950)"/>
    </template>
    <h1 class="label-xl c-strong-950 !text-center"> {{ __('client.forgot_password.title') }} </h1>
    <p class="paragraph-sm c-sub-600 !text-center mb-6">{{ __('client.forgot_password.subtitle') }}</p>
    <form @submit.prevent="submit" class="flex flex-col gap-3">
      <FormElement v-model="form.email"
                   :error="form.errors.email"
                   direction="vertical"
                   :label="__('client.forgot_password.email')"
                   :placeholder="__('client.forgot_password.email_placeholder')"></FormElement>


      <PrimaryButton>
        {{ __('client.forgot_password.send_reset_link') }}
        <template #suffix>
          <SendIcon class="ms-1" color="var(--dark-green-500)"/>
        </template>
      </PrimaryButton>
      <div class="text-end">
        <a :href="route('login')" class="label-xs c-neutral-500 flex items-center gap-1 justify-center mt-2">
          <ChevronLeftIcon color="var(--neutral-500)"/>
          {{ __('client.forgot_password.back_btn') }}</a>
      </div>
    </form>
  </AuthLayout>

</template>
