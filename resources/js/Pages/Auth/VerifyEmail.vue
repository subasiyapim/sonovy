<script setup>
import {computed, ref} from 'vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import {PrimaryButton} from '@/Components/Buttons';
import {Head, Link, router, usePage} from '@inertiajs/vue3';
import PinputField from '@/Components/Pinput/PinputField.vue';
import {MessageIcon2, ChevronLeftIcon, CheckIcon, ChevronRightIcon} from '@/Components/Icons';
import InputError from "@/Components/InputError.vue";
import {useCrudStore} from '@/Stores/useCrudStore';

// Store tanımlaması
const crudStore = useCrudStore();

// Props tanımlaması
const props = defineProps({
  status: String,
});

// Reactive değişkenler
const form = ref({
  code: '',
});
const panelState = ref(null);
const error = ref(null);

// Submit fonksiyonu
const submit = async () => {
  panelState.value = 'loading';

  try {
    const response = await crudStore.post(route('verification.send'), form.value);
    console.log("RESPONNSE", response);

    if (!response.success) {
      setTimeout(() => {
        panelState.value = null;
        error.value = response.message;
      }, 1000);
    } else {
      setTimeout(() => {
        panelState.value = 'completed';
      }, 1000);
    }
  } catch (err) {
    console.error(err);
    setTimeout(() => {
      panelState.value = null;
      error.value = 'Bir hata oluştu. Lütfen tekrar deneyin.';
    }, 1000);
  }
};

const time = ref(usePage().props.verification_code_expire * 60);

setInterval(() => {
  if (time.value > 0) {
    time.value--;
  }
}, 1000);

const resetCode = () => {
  axios.get(route('verification.notice'), {email: usePage().props?.auth?.user?.email})
      .then((response) => {
        time.value = usePage().props.verification_code_expire * 60;
      })
      .catch((error) => {
        console.error(error);
      });
}

// Devam et butonuna tıklandığında yönlendirme
const onContinueClicked = () => {
  router.visit(route('control.dashboard'));
}
const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent'
);
</script>

<template>
  <AuthLayout :state="panelState">

    <template #icon>
      <MessageIcon2 color="var(--strong-950)"/>
    </template>

    <template #loading>
      <p class="c-strong-950 label-xl"> {{ __('auth.verifying') }} </p>
      <p class="label-sm c-sub-600 !text-center">
        {{ __('auth.verifying_description') }}
      </p>
    </template>

    <template #completed>
      <p class="c-strong-950 label-xl"> {{ __('auth.congratulations') }} </p>
      <p class="paragraph-sm c-sub-600 !text-center">
        {{ __('auth.account_created') }}<br>
        {{ __('auth.create_your_first_publication') }}
      </p>
      <PrimaryButton class="mt-6" @click="onContinueClicked">
        {{ __('auth.start_now') }}
        <template #suffix>
          <ChevronRightIcon color="var(--dark-green-500)"/>
        </template>
      </PrimaryButton>
    </template>

    <Head title="Email Verification"/>

    <h1 class="label-xl c-strong-950 !text-center">
      {{ __('client.verify_email.title') }}
    </h1>
    <p class="paragraph-sm c-sub-600 !text-center mb-6">
      {{ __('client.verify_email.description', {email: usePage().props?.auth?.user?.email}) }}
    </p>

    <div
        class="mb-4 text-sm font-medium text-green-600 dark:text-green-400"
        v-if="verificationLinkSent"
    >
      {{ __('client.verify_email.check_email') }}
    </div>
    <PinputField v-model="form.code"></PinputField>
    <InputError :message="error" v-if="error"/>

    <div class="flex flex-row justify-center items-center gap-x-1">
      <p v-text="__('client.forgot_password_pin.time', {time: `${String(Math.floor(time / 60)).padStart(2, '0')}:${String(time % 60).padStart(2, '0')}`})"/>
      <button @click="resetCode"
              :disabled="time >0"
              :class="[time > 0 ? 'text-gray-400' : 'text-gray-600 font-bold']"
              v-text="__('client.forgot_password_pin.resend_code')"
              type="button"/>
    </div>

    <div class="mt-4 flex flex-col items-center justify-between">
      <PrimaryButton
          @click="submit"
          class="w-full"
          :disabled="form.code.length !== 6"
      >
        <template #suffix>
          <CheckIcon color="var(--dark-green-500)"/>
        </template>
        {{ __('client.verify_email.submit') }}
      </PrimaryButton>

      <div class="text-end">
        <Link :href="route('login')" class="label-xs c-neutral-500 flex items-center gap-1 justify-center mt-2">
          <ChevronLeftIcon color="var(--neutral-500)"/>
          {{ __('client.forgot_password.back_btn') }}
        </Link>
      </div>
    </div>
  </AuthLayout>
</template>
