<script setup>
import {ref, reactive} from 'vue';
import AppTable from '@/Components/Table/AppTable.vue';
import {IconButton} from '@/Components/Buttons';
import {AppActivity} from '@/Components/Widgets';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {usePage} from '@inertiajs/vue3';
import {AppSwitchComponent} from '@/Components/Form';
import {toast} from 'vue3-toastify';

import {Howl} from "howler";
import {
  DocumentIcon,
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

const isEmailVerified = ref(props.user.email_verified_at != null);
const isPhoneVerified = ref(props.user.is_verified == 1);

const onChangeEmailVerification = async (e) => {
 try {
    const response = await crudStore.post(route('control.user-management.users.toggle-email-verification',props.user.id));
    toast.success(response.message);
 } catch (error) {
    toast.success("İşlem Başarısız");
 }
}
const onChangePhoneVerification = async (ee) => {
    try {
        const response = await crudStore.post(route('control.user-management.users.toggle-phone-verification',props.user.id));
        toast.success(response.message);
    } catch (error) {
        toast.success("İşlem Başarısız");
    }
}
const activities = reactive([
    {
        "title":"Curabitur quam sem lobortis imperdiet sagittis ut. Interdum.",
        "description" : "15 Eylül 2024 - 09:30"
    }
]);


</script>
<template>
    <div v-if="user" class="flex items-start gap-8 h-full">
      <div class="flex-1 flex flex-col gap-12">
        <div>
          <h1 class="mb-6 subheading-regular text-start" v-text="'Profil Bilgileri'"/>
          <div class="flex items-start gap-4">
            <div class="flex flex-col gap-8 flex-1">
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="'Ad Soyad'"/>
                  <span class="user-sm c-strong-950" v-text="user.name"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="'E-mail Adresi'"/>
                  <span class="user-sm c-strong-950" v-text="user.email"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="'Oluşturma Tarihi'"/>
                  <span class="user-sm c-strong-950" v-text="user.created_at"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="'İl - İlçe'"/>
                  <span class="user-sm c-strong-950" v-text="user.created_at"/>
                </div>
              </div>

             <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="'Posta Kodu'"/>
                  <span class="user-sm c-strong-950" v-text="user.created_at"/>
                </div>
              </div>


            <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="'Adres'"/>
                  <span class="user-sm c-strong-950" v-text="user.created_at"/>
                </div>
              </div>


               <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="'Ülke'"/>
                  <span class="user-sm c-strong-950" v-text="user.created_at"/>
                </div>
              </div>


            <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="'Kullanıcı Dili'"/>
                  <span class="user-sm c-strong-950" v-text="user.created_at"/>
                </div>
              </div>


            </div>
            <div class="flex flex-col gap-8 flex-1">
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="'Şirket Adı'"/>
                  <span class="user-sm c-strong-950" v-text="'-'"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="'Şirket - Ülke'"/>
                  <span class="user-sm c-strong-950" v-text="user.web"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="'Şirket Vergi Dairesi'"/>
                  <span class="user-sm c-strong-950" v-text="user.email"/>
                </div>
              </div>

              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="'Şirket Vergi Numarası'"/>
                  <span class="user-sm c-strong-950" v-text="'-'"/>
                </div>
              </div>


              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="'Katalog No'"/>
                  <span class="user-sm c-strong-950" v-text="'-'"/>
                </div>
              </div>

              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="'Şirket Tel'"/>
                  <span class="user-sm c-strong-950" v-text="'-'"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div class="h-12 flex flex-col gap-2">
                    <p class="paragraph-xs c-sub-600" v-text="'Email Onayı'"/>
                    <AppSwitchComponent @change="onChangeEmailVerification" v-model="isEmailVerified"/>

                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <DocumentIcon color="var(--sub-600)"/>
                </div>
                <div class="h-12 flex flex-col gap-2">
                    <p class="paragraph-xs c-sub-600" v-text="'Telefon Onayı'"/>
                    <AppSwitchComponent @change="onChangePhoneVerification" v-model="isPhoneVerified"/>


                </div>
              </div>

            </div>
          </div>
        </div>

      </div>
      <div class="h-full bg-soft-200" style="width:1px;">
      </div>
      <div class="w-96 pr-8">
        <h1 class="mb-6 subheading-regular" v-text="'Aktiviteler'"/>

        <AppActivity :items="activities"></AppActivity>
      </div>
    </div>
</template>

<style scoped>

</style>
