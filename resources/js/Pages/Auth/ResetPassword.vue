<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import {PrimaryButton} from '@/Components/Buttons';
import TextInput from '@/Components/TextInput.vue';
import {Head, useForm} from '@inertiajs/vue3';
import {FormElement} from '@/Components/Form'
import {LockIcon, ChevronLeftIcon} from '@/Components/Icons'
import {Link} from '@inertiajs/vue3';

const props = defineProps({
  token: String,
  email: String,
})

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post(route('password.store'), {
    onFinish: () => {
      form.reset('password', 'password_confirmation');
    },
  });
};
</script>

<template>
  <AuthLayout>
    <template #icon>
      <LockIcon color="var(--strong-950)"/>
    </template>
    <h1 class="label-xl c-strong-950 !text-center" v-text="__('client.forgot_password.title')"/>
    <p class="paragraph-sm c-sub-600 !text-center mb-6" v-text="__('client.forgot_password.subtitle')"/>
    <form @submit.prevent="submit">
      <div>
        <FormElement :required="true"
                     v-model="form.email"
                     :error="form.errors.email"
                     direction="vertical"
                     :label="__('client.login.fields.email')"
                     :placeholder="__('client.login.fields.email_placeholder')"/>

      </div>

      <div class="mt-4">
        <FormElement :required="true"
                     type="password"
                     v-model="form.password"
                     :error="form.errors.password"
                     direction="vertical"
                     :label="__('client.login.fields.password')"
                     :placeholder="__('client.login.fields.password_placeholder')"/>

      </div>

      <div class="mt-4">

        <FormElement :required="true"
                     type="password"
                     v-model="form.password_confirmation"
                     :error="form.errors.password_confirmation"
                     direction="vertical"
                     :label="__('client.login.fields.password')"
                     :placeholder="__('client.login.fields.password_placeholder')"/>


      </div>

      <div class="mt-4 flex flex-col gap-2 items-center justify-end">
        <PrimaryButton
            class="w-full"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
        >
          Reset Password
        </PrimaryButton>
        <div class="text-end">
          <Link :href="route('login')" class="label-xs c-neutral-500 flex items-center gap-1 justify-center mt-2">
            <ChevronLeftIcon color="var(--neutral-500)"/>
            Ana Sayfaya Dön
          </Link>
        </div>

      </div>
    </form>
  </AuthLayout>
</template>
