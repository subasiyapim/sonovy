<script setup lang="ts">
import {computed} from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';

const props = defineProps<{
  status?: string;
}>();

const form = useForm({});

const submit = () => {
  form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
  <GuestLayout>
    <Head title="Email Verification"/>
    <h2 class="text-lg font-bold text-white py-2">{{ __('client.verify_email.title') }}</h2>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
      {{ __('client.verify_email.description') }}
    </div>

    <div
        class="mb-4 text-sm font-medium text-green-600 dark:text-green-400"
        v-if="verificationLinkSent"
    >
      {{ __('client.verify_email.check_email') }}
    </div>

    <form @submit.prevent="submit">
      <div class="mt-4 flex items-center justify-between">
        <PrimaryButton
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
        >
          {{ __('client.verify_email.resend_verification_email') }}
        </PrimaryButton>

        <Link
            :href="route('logout')"
            method="post"
            as="button"
            class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
        >{{ __('client.verify_email.logout') }}
        </Link
        >
      </div>
    </form>
  </GuestLayout>
</template>
