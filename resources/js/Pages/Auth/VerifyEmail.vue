<script setup>
import {computed,ref} from 'vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import {PrimaryButton} from '@/Components/Buttons';
import {Head, Link, useForm} from '@inertiajs/vue3';
import PinputField from '@/Components/Pinput/PinputField.vue';
import {MessageIcon2,ChevronLeftIcon,CheckIcon,CheckFilledIcon,ChevronRightIcon} from '@/Components/Icons'
const props = defineProps({
  status: Object
});

const form = useForm({
    code:"",
});
const panelState = ref(null);
const submit = () => {
    panelState.value = 'loading';

    form.post(route('verification.send'));
    setTimeout(() => {
        panelState.value = 'completed';
    }, 1000);
};
const onContinueClicked = () => {
    router.visit(route('control.catalog.products.index'));
}
const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
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
                <ChevronRightIcon color="var(--dark-green-500)" />
            </template>
        </PrimaryButton>
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

     <PinputField v-model="form.code" ></PinputField>

      <div class="mt-4 flex flex-col items-center justify-between">
        <PrimaryButton
            @click="submit"
            class="w-full"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.code.length != 6">
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
