<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import {FormElement} from '@/Components/Form'
import {PrimaryButton} from '@/Components/Buttons'
import {CommentIcon, ChevronLeftIcon, SendIcon, ProgressIcon} from '@/Components/Icons'
import {Head, Link, useForm} from '@inertiajs/vue3';
import {ref} from "vue";
import PinputField from '@/Components/Pinput/PinputField.vue';
import InputError from "@/Components/InputError.vue";

const props = defineProps({
  email: {
    type: String,
    required: true,
  },
  masked_email: {
    type: String,
    required: true,
  }
});
const form = useForm({
  code: null,
  email: props.email,
});

const time = ref(60)

setInterval(() => {
  if (time.value > 0) {
    time.value--;
  }
}, 1000);


const resetCode = () => {
  form.post(route('password.reset.pin'), {
    onFinish: () => {
      form.reset();
      time.value = 60;
    },
  });
}

const submit = () => {
  form.post(route('password.store.pin'), {
    onFinish: () => {
      form.reset();
    },
  });
}
</script>

<template>
  <AuthLayout>
    <template #icon>
      <CommentIcon color="var(--strong-950)"/>
    </template>
    <h1 class="label-xl c-strong-950 !text-center"> {{ __('client.forgot_password_pin.title') }} </h1>
    <p class="paragraph-sm c-sub-600 !text-center mb-6">
      {{ __('client.forgot_password_pin.subtitle', {email: masked_email}) }}</p>
    <form @submit.prevent="submit" class="flex flex-col gap-3">
      <PinputField v-model="form.code" @onCompleted="submit"></PinputField>
      <input-error :message="form.errors.code" class="flex justify-center"/>
      <div class="flex flex-row justify-center items-center gap-x-1">
        <p v-text="__('client.forgot_password_pin.time', {time: `${String(Math.floor(time / 60)).padStart(2, '0')}:${String(time % 60).padStart(2, '0')}`})"/>
        <button @click="resetCode"
                :disabled="time >0"
                :class="[time > 0 ? 'text-gray-400' : 'text-gray-600 font-bold']"
                v-text="__('client.forgot_password_pin.resend_code')"
                type="button"/>
      </div>
      <PrimaryButton>
        {{
          form.processing ? __('client.forgot_password.send_reset_link_loading') : __('client.forgot_password.send_reset_link')
        }}
        <template #suffix>
          <ProgressIcon v-if="form.processing" class="ms-1" color="var(--dark-green-500)"/>
          <SendIcon v-else class="ms-1" color="var(--dark-green-500)"/>
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
