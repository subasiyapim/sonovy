<script setup>
import {ref, reactive} from 'vue';
import AppTable from '@/Components/Table/AppTable.vue';
import {IconButton, PrimaryButton, RegularButton} from '@/Components/Buttons';
import {FormElement} from '@/Components/Form';
import {ChangePackageDialog} from '@/Components/Dialog';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {usePage} from '@inertiajs/vue3';
import {toast} from 'vue3-toastify';
import ActionButton from './Components/pricing_action_button.vue';
import {AppProgressIndicator} from '@/Components/Widgets';

import {
  StackIcon,
  More2LineIcon,
  CreditCardIcon,
  RingtoneIcon,
  MusicVideoIcon,
  PlayCircleFillIcon,
  ChartsIcon,
  EyeOnIcon,
  EditIcon
} from '@/Components/Icons'
import {useDefaultStore} from "@/Stores/default";
import {useCrudStore} from "@/Stores/useCrudStore";

const defaultStore = useDefaultStore();

const crudStore = useCrudStore();
const props = defineProps({
  user: {},
});
const usages = reactive([
  {"title": "deneme"},
  {"title": "deneme"},
  {"title": "deneme"},
  {"title": "deneme"},
]);
const commission_rate = ref(props.user.tab.commission_rate ?? 0);
const payment_threshold = ref(props.user.tab.payment_threshold ?? 0);


const onChangeComissonRate = (e) => {

  const response = crudStore.post(route('control.user-management.users.assign-to-commission-rate', props.user.id), {
    commission_rate: commission_rate.value
  });
  props.user.commission_rate = commission_rate.value;
  toast.success('işlem başarılı');
};
const onChangeThreshold = (e) => {
  const response = crudStore.post(route('control.user-management.users.assign-to-payment-threshold', props.user.id), {
    payment_threshold: payment_threshold.value
  });
  toast.success('işlem başarılı');
};
const isDialogOn = ref(false);
const onChangeClicked = () => {
    isDialogOn.value = true;
};


</script>
<template>
  <div class="flex flex-col gap-6 items-start">
    <h1 class="subheading-regular text-start" v-text="'Fiyatlandırma'"/>
    <p class="paragraph-sm c-sub-600">Et semper orci donec varius sed faucibus hendrerit. Vel nunc mauris gravida nullam
      nulla ut nisl nibh. </p>
    <FormElement :label="'Hakediş Oranı'" v-model="commission_rate" placeholder="Lütfen Giriniz" label-width="290px"
                 class="w-[560px]">
      <template #tooltip>
        deneme
      </template>
      <template #description>
        <div class="text-end">
          <button @click="onChangeComissonRate" class="paragraph-xs c-blue-500">
            kaydet
          </button>
        </div>
      </template>
    </FormElement>




    <FormElement :label="'Satış Ödemesi Eşiği'" type="custom" placeholder="Lütfen Giriniz"
                 label-width="290px" class="w-[560px]">
      <template #tooltip>
        deneme
      </template>

        <div class="flex">
            <label for="currency-input" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Your Email</label>
                <div class="relative w-full">
                    <div class="absolute h-full flex items-center ps-2.5 pointer-events-none paragraph-sm c-sub-600">
                           $
                    </div>
                <input  v-model="payment_threshold" class="block p-2 w-full z-20 ps-6 paragraph-sm c-strong-950 border border-soft-200 rounded-s-lg  border-e-none focus:ring-none focus:border-[#E1E4EA] focus:ring-0 focus:outline-none focus:border-soft-200  " placeholder="Lütfen giriniz" required />
            </div>
            <button id="dropdown-currency-button" data-dropdown-toggle="dropdown-currency" class="flex-shrink-0 z-10 inline-flex items-center py-2 px-4 text-sm font-medium text-center text-gray-900 bg-white border-y border-e border-soft-200 rounded-e-lg hover:bg-gray-200 focus " type="button">
                <svg fill="none" aria-hidden="true" class="h-4 w-4 me-2" viewBox="0 0 20 15"><rect width="19.6" height="14" y=".5" fill="#fff" rx="2"/><mask id="a" style="mask-type:luminance" width="20" height="15" x="0" y="0" maskUnits="userSpaceOnUse"><rect width="19.6" height="14" y=".5" fill="#fff" rx="2"/></mask><g mask="url(#a)"><path fill="#D02F44" fill-rule="evenodd" d="M19.6.5H0v.933h19.6V.5zm0 1.867H0V3.3h19.6v-.933zM0 4.233h19.6v.934H0v-.934zM19.6 6.1H0v.933h19.6V6.1zM0 7.967h19.6V8.9H0v-.933zm19.6 1.866H0v.934h19.6v-.934zM0 11.7h19.6v.933H0V11.7zm19.6 1.867H0v.933h19.6v-.933z" clip-rule="evenodd"/><path fill="#46467F" d="M0 .5h8.4v6.533H0z"/><g filter="url(#filter0_d_343_121520)"><path fill="url(#paint0_linear_343_121520)" fill-rule="evenodd" d="M1.867 1.9a.467.467 0 11-.934 0 .467.467 0 01.934 0zm1.866 0a.467.467 0 11-.933 0 .467.467 0 01.933 0zm1.4.467a.467.467 0 100-.934.467.467 0 000 .934zM7.467 1.9a.467.467 0 11-.934 0 .467.467 0 01.934 0zM2.333 3.3a.467.467 0 100-.933.467.467 0 000 .933zm2.334-.467a.467.467 0 11-.934 0 .467.467 0 01.934 0zm1.4.467a.467.467 0 100-.933.467.467 0 000 .933zm1.4.467a.467.467 0 11-.934 0 .467.467 0 01.934 0zm-2.334.466a.467.467 0 100-.933.467.467 0 000 .933zm-1.4-.466a.467.467 0 11-.933 0 .467.467 0 01.933 0zM1.4 4.233a.467.467 0 100-.933.467.467 0 000 .933zm1.4.467a.467.467 0 11-.933 0 .467.467 0 01.933 0zm1.4.467a.467.467 0 100-.934.467.467 0 000 .934zM6.533 4.7a.467.467 0 11-.933 0 .467.467 0 01.933 0zM7 6.1a.467.467 0 100-.933.467.467 0 000 .933zm-1.4-.467a.467.467 0 11-.933 0 .467.467 0 01.933 0zM3.267 6.1a.467.467 0 100-.933.467.467 0 000 .933zm-1.4-.467a.467.467 0 11-.934 0 .467.467 0 01.934 0z" clip-rule="evenodd"/></g></g><defs><linearGradient id="paint0_linear_343_121520" x1=".933" x2=".933" y1="1.433" y2="6.1" gradientUnits="userSpaceOnUse"><stop stop-color="#fff"/><stop offset="1" stop-color="#F0F0F0"/></linearGradient><filter id="filter0_d_343_121520" width="6.533" height="5.667" x=".933" y="1.433" color-interpolation-filters="sRGB" filterUnits="userSpaceOnUse"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" result="hardAlpha" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="1"/><feColorMatrix values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.06 0"/><feBlend in2="BackgroundImageFix" result="effect1_dropShadow_343_121520"/><feBlend in="SourceGraphic" in2="effect1_dropShadow_343_121520" result="shape"/></filter></defs></svg>
                USD <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>
            </button>
            <div id="dropdown-currency" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-36 dark:bg-gray-700">
                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdown-currency-button">
                    <li>
                        <button type="button" class="inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">
                            <div class="inline-flex items-center">
                                <svg fill="none" aria-hidden="true" class="h-4 w-4 me-2" viewBox="0 0 20 15"><rect width="19.6" height="14" y=".5" fill="#fff" rx="2"/><mask id="a" style="mask-type:luminance" width="20" height="15" x="0" y="0" maskUnits="userSpaceOnUse"><rect width="19.6" height="14" y=".5" fill="#fff" rx="2"/></mask><g mask="url(#a)"><path fill="#D02F44" fill-rule="evenodd" d="M19.6.5H0v.933h19.6V.5zm0 1.867H0V3.3h19.6v-.933zM0 4.233h19.6v.934H0v-.934zM19.6 6.1H0v.933h19.6V6.1zM0 7.967h19.6V8.9H0v-.933zm19.6 1.866H0v.934h19.6v-.934zM0 11.7h19.6v.933H0V11.7zm19.6 1.867H0v.933h19.6v-.933z" clip-rule="evenodd"/><path fill="#46467F" d="M0 .5h8.4v6.533H0z"/><g filter="url(#filter0_d_343_121520)"><path fill="url(#paint0_linear_343_121520)" fill-rule="evenodd" d="M1.867 1.9a.467.467 0 11-.934 0 .467.467 0 01.934 0zm1.866 0a.467.467 0 11-.933 0 .467.467 0 01.933 0zm1.4.467a.467.467 0 100-.934.467.467 0 000 .934zM7.467 1.9a.467.467 0 11-.934 0 .467.467 0 01.934 0zM2.333 3.3a.467.467 0 100-.933.467.467 0 000 .933zm2.334-.467a.467.467 0 11-.934 0 .467.467 0 01.934 0zm1.4.467a.467.467 0 100-.933.467.467 0 000 .933zm1.4.467a.467.467 0 11-.934 0 .467.467 0 01.934 0zm-2.334.466a.467.467 0 100-.933.467.467 0 000 .933zm-1.4-.466a.467.467 0 11-.933 0 .467.467 0 01.933 0zM1.4 4.233a.467.467 0 100-.933.467.467 0 000 .933zm1.4.467a.467.467 0 11-.933 0 .467.467 0 01.933 0zm1.4.467a.467.467 0 100-.934.467.467 0 000 .934zM6.533 4.7a.467.467 0 11-.933 0 .467.467 0 01.933 0zM7 6.1a.467.467 0 100-.933.467.467 0 000 .933zm-1.4-.467a.467.467 0 11-.933 0 .467.467 0 01.933 0zM3.267 6.1a.467.467 0 100-.933.467.467 0 000 .933zm-1.4-.467a.467.467 0 11-.934 0 .467.467 0 01.934 0z" clip-rule="evenodd"/></g></g><defs><linearGradient id="paint0_linear_343_121520" x1=".933" x2=".933" y1="1.433" y2="6.1" gradientUnits="userSpaceOnUse"><stop stop-color="#fff"/><stop offset="1" stop-color="#F0F0F0"/></linearGradient><filter id="filter0_d_343_121520" width="6.533" height="5.667" x=".933" y="1.433" color-interpolation-filters="sRGB" filterUnits="userSpaceOnUse"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" result="hardAlpha" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="1"/><feColorMatrix values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.06 0"/><feBlend in2="BackgroundImageFix" result="effect1_dropShadow_343_121520"/><feBlend in="SourceGraphic" in2="effect1_dropShadow_343_121520" result="shape"/></filter></defs></svg>
                                USD
                            </div>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">
                            <div class="inline-flex items-center">
                                <svg fill="none" aria-hidden="true" class="h-4 w-4 me-2" viewBox="0 0 20 15"><rect width="19.6" height="14" y=".5" fill="#fff" rx="2"/><mask id="a" style="mask-type:luminance" width="20" height="15" x="0" y="0" maskUnits="userSpaceOnUse"><rect width="19.6" height="14" y=".5" fill="#fff" rx="2"/></mask><g mask="url(#a)"><path fill="#043CAE" d="M0 .5h19.6v14H0z"/><path fill="#FFD429" fill-rule="evenodd" d="M9.14 3.493L9.8 3.3l.66.193-.193-.66.193-.66-.66.194-.66-.194.193.66-.193.66zm0 9.334l.66-.194.66.194-.193-.66.193-.66-.66.193-.66-.193.193.66-.193.66zm5.327-4.86l-.66.193L14 7.5l-.193-.66.66.193.66-.193-.194.66.194.66-.66-.193zm-9.994.193l.66-.193.66.193L5.6 7.5l.193-.66-.66.193-.66-.193.194.66-.194.66zm9.369-2.527l-.66.194.193-.66-.194-.66.66.193.66-.193-.193.66.193.66-.66-.194zm-8.743 4.86l.66-.193.66.193-.194-.66.194-.66-.66.194-.66-.194.193.66-.193.66zm7.034-6.568l-.66.193.194-.66-.194-.66.66.194.66-.193-.193.66.193.66-.66-.194zm-5.326 8.276l.66-.193.66.193-.194-.66.194-.66-.66.194-.66-.193.193.66-.193.66zM13.84 10.3l-.66.193.194-.66-.194-.66.66.194.66-.194-.193.66.193.66-.66-.193zM5.1 5.827l.66-.194.66.194-.194-.66.194-.66-.66.193-.66-.193.193.66-.193.66zm7.034 6.181l-.66.193.194-.66-.194-.66.66.194.66-.193-.193.66.193.66-.66-.194zm-5.326-7.89l.66-.193.66.193-.194-.66.194-.66-.66.194-.66-.193.193.66-.193.66z" clip-rule="evenodd"/></g></svg>
                                EUR
                            </div>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">
                            <div class="inline-flex items-center">
                                <svg fill="none" aria-hidden="true" class="h-4 w-4 me-2" viewBox="0 0 20 15"><rect width="19.1" height="13.5" x=".25" y=".75" fill="#fff" stroke="#F5F5F5" stroke-width=".5" rx="1.75"/><mask id="a" style="mask-type:luminance" width="20" height="15" x="0" y="0" maskUnits="userSpaceOnUse"><rect width="19.1" height="13.5" x=".25" y=".75" fill="#fff" stroke="#fff" stroke-width=".5" rx="1.75"/></mask><g fill="#FF3131" mask="url(#a)"><path d="M14 .5h5.6v14H14z"/><path fill-rule="evenodd" d="M0 14.5h5.6V.5H0v14zM11.45 6.784a.307.307 0 01-.518-.277l.268-1.34-.933.466-.467-1.4-.467 1.4-.933-.466.268 1.34a.307.307 0 01-.518.277.307.307 0 00-.434 0l-.25.25-.933-.467L7 7.5l-.231.231a.333.333 0 000 .471l1.164 1.165h1.4l.234 1.4h.466l.234-1.4h1.4l1.164-1.165a.333.333 0 000-.471l-.231-.23.467-.934-.934.466-.25-.25a.307.307 0 00-.433 0z" clip-rule="evenodd"/></g></svg>
                                CAD
                            </div>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">
                            <div class="inline-flex items-center">
                                <svg fill="none" aria-hidden="true" class="h-4 w-4 me-2" viewBox="0 0 20 15"><rect width="19.6" height="14" y=".5" fill="#fff" rx="2"/><mask id="a" style="mask-type:luminance" width="20" height="15" x="0" y="0" maskUnits="userSpaceOnUse"><rect width="19.6" height="14" y=".5" fill="#fff" rx="2"/></mask><g mask="url(#a)"><path fill="#0A17A7" d="M0 .5h19.6v14H0z"/><path fill="#fff" fill-rule="evenodd" d="M-.898-.842L7.467 4.8V-.433h4.666V4.8l8.365-5.642L21.542.706l-6.614 4.46H19.6v4.667h-4.672l6.614 4.46-1.044 1.549-8.365-5.642v5.233H7.467V10.2l-8.365 5.642-1.044-1.548 6.614-4.46H0V5.166h4.672L-1.942.706-.898-.842z" clip-rule="evenodd"/><path stroke="#DB1F35" stroke-linecap="round" stroke-width=".667" d="M13.068 4.933L21.933-.9M14.009 10.088l7.948 5.357M5.604 4.917L-2.686-.67M6.503 10.024l-9.19 6.093"/><path fill="#E6273E" fill-rule="evenodd" d="M0 8.9h8.4v5.6h2.8V8.9h8.4V6.1h-8.4V.5H8.4v5.6H0v2.8z" clip-rule="evenodd"/></g></svg>
                                GBP
                            </div>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
      <template #description>
        <div class="text-end">
          <button @click="onChangeThreshold" class="paragraph-xs c-blue-500">
            kaydet
          </button>
        </div>
      </template>
    </FormElement>
  </div>
  <hr class="my-6">
  <h1 class="mb-6 subheading-regular text-start mb-3" v-text="'Mevcut Paket'"/>
  <p class="paragraph-sm c-sub-600">Et semper orci donec varius sed faucibus hendrerit. Vel nunc mauris gravida nullam
    nulla ut nisl nibh. </p>
  <div class="flex flex-1 gap-3 w-full mt-12 ">
    <div class="border border-soft-200 rounded-lg px-4 py-6 w-96">
      <div class="flex items-center justify-between mb-4">
        <StackIcon color="var(--dark-green-500)"/>
        <ActionButton @onChangeClicked="onChangeClicked" />
      </div>
      <div>
        <p class="label-xl c-strong-950">Gelişmiş Paket</p>
        <p class="label-sm c-sub-600">Enterprise Plan</p>
      </div>
      <div class="my-12">
        <p class="paragraph-sm c-soft-400">Mevcut paket fiyatı</p>
        <div class="flex items-end label-lg">
          <span class="c-strong-950">$30.</span><span class="c-soft-400">99/</span> <span
            class="c-soft-400">aylık</span>
        </div>
      </div>
      <hr class=" mb-8">
      <div class="flex gap-2">
        <img src="@/assets/images/Mastercard.png" class="image-fluid h-min">
        <div class="flex flex-col">
          <div class="flex items-center gap-3">
            <p class="subheading-2xs c-sub-600">KAYITLI ÖDEME YÖNTEMİ</p>
            <span class="subheading-2xs text-[#122368] bg-[#C0D5FF] rounded-full px-2 py-1">KREDİ KARTI</span>
          </div>
          <div>
            <p class="label-sm c-strong-950">5269 **** **** 1234 - Garanti BBVA</p>
          </div>
        </div>
      </div>
      <div class="my-12">
        <div class="flex items-center justify-between">
          <p class="label-sm c-strong-950">Yıllık paket</p>
          <p class="paragraph-xs c-sub-600">Kalan 40 gün</p>
        </div>
        <AppProgressIndicator :height="6" :modelValue="10" color="#FF8447" class="my-2"/>

        <p class="paragraph-xs c-sub-600">30.12.2024 - Üyelik Bitiş</p>
      </div>

      <div class="flex gap-2 items-center">
        <PrimaryButton class="flex-1">
          <template #icon>
            <CreditCardIcon color="var(--dark-green-500)"/>
          </template>
          Ödeme Yap
        </PrimaryButton>

        <RegularButton class="flex-1">
          Paketi Yükselt
        </RegularButton>
      </div>
    </div>
    <div class="border border-soft-200 rounded-lg px-4 py-6 flex-1">
      <p class="subheading-regular c-strong-950">Kalan Kullanımlar</p>
      <p class="label-sm c-strong-950 mb-5">Enterprise Plan</p>

      <div v-for="usage in usages">
        <div class="flex items-center gap-2">
          <p class="flex-1 label-sm c-strong-950">{{ usage.title }}</p>
          <div class="flex items-center gap-1  w-32">
            <AppProgressIndicator :height="3" class="w-12" :modelValue="50" color="#FF8447"/>
            <p class="paragraph-xs c-sub-600  whitespace-nowrap">Kalan: 5/10</p>
          </div>
        </div>
        <hr class="mb-3 mt-2">
      </div>
    </div>
  </div>
  <hr class="my-6">
  <h1 class="mb-6 subheading-regular text-start" v-text="'Tarihçe'"/>

  <AppTable v-model="user.histories" :isClient="true" :hasSearch="false" :showAddButton="false">
    <AppTableColumn label="Tarih">
      <template #default="scope">

      </template>
    </AppTableColumn>

    <AppTableColumn label="Değişiklik">
      <template #default="scope">
        <p class="paragraph-xs c-sub-600">{{ scope.row.pre_order_date }}</p>
      </template>
    </AppTableColumn>


    <AppTableColumn label="Aksiyon Sahibi">
      <template #default="scope">
        <p class="paragraph-xs c-sub-600">{{ scope.row.publish_date }}</p>
      </template>
    </AppTableColumn>


    <template #empty>
      Tarih Detayı Bulunamadı
    </template>
  </AppTable>
  <ChangePackageDialog v-model="isDialogOn" v-is="isDialogOn"></ChangePackageDialog>
</template>

<style scoped>


</style>
