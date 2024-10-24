<script setup>
import {computed, ref} from 'vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import {PrimaryButton} from '@/Components/Buttons';
import {Head, Link, useForm, usePage, router} from '@inertiajs/vue3';
import PinputField from '@/Components/Pinput/PinputField.vue';
import InputError from "@/Components/InputError.vue";
import {MessageIcon2, ChevronLeftIcon, CheckIcon, ChevronRightIcon} from '@/Components/Icons'
import {useCrudStore} from '@/Stores/useCrudStore'

const props = defineProps({
  status: Object
});

const form = ref({
  code: null
})

const crudStore = useCrudStore();
const panelState = ref(null);
const error = ref(null);
const time = ref(usePage().props.verification_code_expire * 60);

setInterval(() => {
  if (time.value > 0) {
    time.value--;
  }
}, 1000);

const submit = async () => {
  panelState.value = 'loading';

  const response = await crudStore.post(route('verification.phonePost'), form.value)

  if (!response['success']) {
    setTimeout(() => {
      panelState.value = null;
      error.value = response['message'];
    }, 1000);

  } else {
    setTimeout(() => {
      panelState.value = 'completed';
    }, 1000);
  }
};
const onContinueClicked = () => {
  router.visit(route('control.dashboard'));
}
const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);

const resetCode = () => {
  axios.get(route('verification.phone'), {email: usePage().props?.auth?.user?.email})
      .then((response) => {
        time.value = usePage().props.verification_code_expire * 60;
      })
      .catch((error) => {
        console.error(error);
      });
}

</script>

<template>
  <AuthLayout :state="panelState">

    <template #icon>
      <MessageIcon2 color="var(--strong-950)"/>
    </template>
    <template #loading>
      <p class="c-strong-950 label-xl">Doğrulanıyor...</p>
      <p class="label-sm c-sub-600 !text-center">Telefon numaranız doğrulanıyor, <br>
        lütfen bekleyiniz.</p>
    </template>
    <template #completed>
      <p class="c-strong-950 label-xl">Tebrikler, aramıza hoşgeldiniz.</p>
      <p class="paragraph-sm c-sub-600 !text-center">
        Hesabınız başarılı bir şekilde oluşturuldu.<br>
        Hemen ilk yayınızı oluşturabilirsiniz.</p>
      <PrimaryButton class="mt-6" @click="onContinueClicked">
        Hemen Başla
        <template #suffix>
          <ChevronRightIcon color="var(--dark-green-500)"/>
        </template>
      </PrimaryButton>
    </template>
    <Head title="Email Verification"/>
    <h1 class="label-xl c-strong-950 !text-center" v-text="__('client.verify_phone.title')"/>
    <p class="paragraph-sm c-sub-600 !text-center mb-6"
       v-text=" __('client.verify_phone.description',{phone:usePage().props?.auth?.user?.phone})"/>


    <div
        class="mb-4 text-sm font-medium text-green-600 dark:text-green-400"
        v-if="verificationLinkSent"
    >
      {{ __('client.verify_phone.check_email') }}
    </div>

    <PinputField v-model="form.code"></PinputField>
    <input-error :message="error" v-if="error"/>

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
          :disabled="form?.code?.length != 6">
        <template #suffix>
          <CheckIcon color="var(--dark-green-500)"/>
        </template>
        {{ __('client.verify_phone.submit') }}
      </PrimaryButton>

      <div class="text-end">
        <a :href="route('login')" class="label-xs c-neutral-500 flex items-center gap-1 justify-center mt-2">
          <ChevronLeftIcon color="var(--neutral-500)"/>
          {{ __('client.forgot_password.back_btn') }}</a>
      </div>
    </div>
  </AuthLayout>
</template>
