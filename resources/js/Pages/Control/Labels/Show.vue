<template>

  <AdminLayout :showDatePicker="false" :title="__('control.label.show_header')"
               :parentTitle="__('control.label.title_singular')"
               :hasPadding="false">

    <div class="bg-white-400 h-44 p-5 relative">
      <div class="">
        <h1 class="label-xl c-strong-950" v-text="label.name"/>
        <span class="c-sub-600 paragraph-medium" v-text="label.id"/>
      </div>

      <div
          class="absolute rounded-full w-32 h-32 bg-blue-300 left-8 -bottom-16 flex items-center justify-center overflow-hidden">
        <img class="w-full h-full object-cover"
             :alt="label.name"
             :src="label.image ? label.image.thumb : defaultStore.profileImage(label.name)">
      </div>
      <div class="flex items-center gap-2 absolute top-5 right-5">
        <PrimaryButton @click="remove">
          <template #icon>
            <TrashIcon color="var(--dark-green-500)"/>
          </template>
        </PrimaryButton>
        <PrimaryButton @click="isModalOn = true">
          <template #icon>
            <EditIcon color="var(--dark-green-500)"/>
          </template>
        </PrimaryButton>
      </div>
    </div>

    <div class="mt-32 flex items-start gap-8 h-full">
      <div class="px-8 flex-1 flex flex-col gap-12">
        <div>
          <h1 class="mb-6 subheading-regular text-start" v-text="__('control.label.label_info')"/>
          <div class="flex items-start gap-4">
            <div class="flex flex-col gap-8 flex-1">
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <PersonCardIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.label.title_singular')"/>
                  <span class="label-sm c-strong-950" v-text="label.name"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <WorldIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.label.fields.country_id')"/>
                  <span class="label-sm c-strong-950" v-text="label.country.name"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <PhoneIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.label.fields.address')"/>
                  <span class="label-sm c-strong-950" v-text="label.address"/>
                </div>
              </div>

            </div>
            <div class="flex flex-col gap-8 flex-1">
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <PhoneIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.label.fields.phone')"/>
                  <span class="label-sm c-strong-950" v-text="label.phone"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <LinkIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.label.fields.web')"/>
                  <span class="label-sm c-strong-950" v-text="label.web"/>
                </div>
              </div>
              <div class="flex gap-3.5 items-center">
                <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                  <MessageIcon color="var(--sub-600)"/>
                </div>
                <div>
                  <p class="paragraph-xs c-sub-600" v-text="__('control.label.fields.email')"/>
                  <span class="label-sm c-strong-950" v-text="label.email"/>
                </div>
              </div>

            </div>
          </div>
        </div>

      </div>
      <div class="h-full bg-soft-200" style="width:1px;">
      </div>
      <div class="w-96 pr-8">
        <h1 class="mb-6 subheading-regular" v-text="__('control.label.label_albums')"/>

        <template v-if="label.products.length > 0">
          <div v-for="product in label.products" class="flex p-4">
            <div class="flex-1 flex items-center gap-4">
              <div class="w-8 h-8 rounded-lg overflow-hidden">
                <img src="https://placehold.co/400x400"/>
              </div>
              <div>
                <p class="text-sm c-strong-950">{{ __('control.label.song_name') }}</p>
                <span class="paragraph-xs c-blue-500">{{ __('control.label.album_name') }}</span>
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
          <p v-text="__('control.label.album_notfound')"/>
        </template>
      </div>
    </div>
    <LabelDialog :label="label" @done="onDone" v-if="isModalOn" v-model="isModalOn"/>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {router} from '@inertiajs/vue3';
import {
  PersonCardIcon,
  PercantageIcon,
  MessageIcon,
  PhoneIcon,
  WorldIcon,
  TrashIcon,
  EditIcon,
  SpotifyIcon,
  GenreIcon,
  PlayFilledIcon,
  LinkIcon
} from '@/Components/Icons'

const isModalOn = ref(false);
import {PrimaryButton} from '@/Components/Buttons'
import {AppIncrementer} from '@/Components/Form'
import {ref} from 'vue';
import {useDefaultStore} from "@/Stores/default";
import {LabelDialog} from '@/Components/Dialog';

const props = defineProps({
  label: {
    type: Object,
    required: true
  },
})

const onDone = () => {
  isModalOn.value = false;
}

const defaultStore = useDefaultStore();

const appIncrementerConfig = {
  formatter: (value) => {
    return '%' + value;
  }
};

const remove = () => {
  router.delete(route('control.catalog.labels.destroy', props.label.id), {});
}
</script>

<style lang="scss" scoped>

</style>
