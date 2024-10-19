<script setup>
import {computed} from 'vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import {PrimaryButton} from '@/Components/Buttons';
import {Head, Link, useForm} from '@inertiajs/vue3';
import PinputField from '@/Components/Pinput/PinputField.vue';
import {MessageIcon2,ChevronLeftIcon,CheckIcon,CheckFilledIcon} from '@/Components/Icons'
const props = defineProps({
  status: Object
});

const form = useForm({});

const submit = () => {
  form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
  <AuthLayout>

     <template #icon>
      <MessageIcon2 color="var(--strong-950)"/>
    </template>
    <Head title="Email Verification"/>
       <h1 class="label-xl c-strong-950 !text-center" v-text="__('client.verify_email.title')"/>
        <p class="paragraph-sm c-sub-600 !text-center mb-6" v-text=" __('client.verify_email.description',{email:'asdsd'})"/>





    <div
        class="mb-4 text-sm font-medium text-green-600 dark:text-green-400"
        v-if="verificationLinkSent"
    >
      {{ __('client.verify_email.check_email') }}
    </div>
     <PinputField v-model="form.code" @onCompleted="submit"></PinputField>

      <div class="mt-4 flex flex-col items-center justify-between">
        <PrimaryButton
            class="w-full"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing">
            <template #suffix>
                <CheckIcon color="var(--dark-green-500)" />
            </template>
          {{ __('client.verify_email.submit') }}
        </PrimaryButton>

        <div class="text-end">
            <a :href="route('login')" class="label-xs c-neutral-500 flex items-center gap-1 justify-center mt-2">
            <ChevronLeftIcon color="var(--neutral-500)"/>
            {{ __('client.forgot_password.back_btn') }}</a>
        </div>
      </div>
  </AuthLayout>
</template>
