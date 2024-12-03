<script setup>
import {ref} from 'vue';
import AppTable from '@/Components/Table/AppTable.vue';
import {IconButton} from '@/Components/Buttons';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {usePage} from '@inertiajs/vue3';
import {Howl} from "howler";
import {
  DocumentIcon,
  EyeOnIcon,
  EditIcon
} from '@/Components/Icons'
import {useDefaultStore} from "@/Stores/default";

const defaultStore = useDefaultStore();

const props = defineProps({
  user: {},
});


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

            </div>
          </div>
        </div>

      </div>
      <div class="h-full bg-soft-200" style="width:1px;">
      </div>
      <div class="w-96 pr-8">
        <h1 class="mb-6 subheading-regular" v-text="'Aktiviteler'"/>

        <template v-if="user.products?.length > 0">
          <div v-for="product in user.products" class="flex p-4">
            <div class="flex-1 flex items-center gap-4">
              <div class="w-8 h-8 rounded-lg overflow-hidden">
                <img src="https://placehold.co/400x400"/>
              </div>
              <div>
                <p class="text-sm c-strong-950">Parça Adı</p>
                <span class="paragraph-xs c-blue-500">Albüm Adı</span>
              </div>
            </div>
            <div class="flex items-end gap-2">
              <div class="h-3.5">
                <PlayFilledIcon color="var(--strong-950)"/>
              </div>
              <span class="paragraph-xs c-neutral-500">02:35</span>
            </div>
          </div>
        </template>
        <template v-else>
          <p v-text="__('control.user.album_notfound')"/>
        </template>
      </div>
    </div>
</template>

<style scoped>

</style>
