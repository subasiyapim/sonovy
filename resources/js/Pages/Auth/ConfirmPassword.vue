<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, useForm} from '@inertiajs/vue3';

import {inject} from "vue";

const __ = inject('__');
const form = useForm({
  password: '',
});

const submit = () => {
  form.post(route('password.confirm'), {
    onFinish: () => {
      form.reset();
    },
  });
};
</script>

<template>
  <GuestLayout>
    <Head title="Confirm Password"/>

    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
      {{ __('pages.confirm_password_warning') }}

    </div>
    <form @submit.prevent="submit">
      <div>
        <InputLabel for="password" value="Password"/>
        <TextInput
            id="password"
            type="password"
            class="mt-1 block w-full"
            v-model="form.password"
            required
            autocomplete="current-password"
            autofocus
        />
        <InputError class="mt-2" :message="form.errors.password"/>
      </div>

      <div class="mt-4 flex justify-end">
        <PrimaryButton
            class="ms-4"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
        >
          {{ __('pages.confirm') }}
        </PrimaryButton>
      </div>
    </form>
  </GuestLayout>
</template>
