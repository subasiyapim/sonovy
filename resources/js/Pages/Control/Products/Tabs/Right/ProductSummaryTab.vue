<template>


  <DragUploadInput ref="imageUploadFile" @onImageDelete="onImageDelete" @change="onChange" :image="product.image?.small" label="Albüm Kapağı"
                   note="JPEG, PNG"></DragUploadInput>

  <div class="flex gap-3.5 items-center">
    <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
      <BroadcastTitleIcon color="var(--sub-600)"/>
    </div>
    <div>
      <p class="paragraph-xs c-sub-600">Albüm Adı</p>
      <span class="label-sm c-strong-950">{{ product.album_name ?? '-' }}</span>
    </div>
  </div>

  <div class="flex gap-3.5 items-center">
    <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
      <PersonIcon color="var(--sub-600)"/>
    </div>
    <div class="flex-1">
      <p class="paragraph-xs c-sub-600">Sanatçılar</p>
      <span class="label-sm c-strong-950">
            <template v-for="(artist,artistIndex) in product.main_artists ">
                {{ artist.name }} <template v-if="artistIndex != product.main_artists.length-1"> , </template>
            </template>
        </span>
    </div>
  </div>
  <div class="flex gap-3.5 items-center">
    <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
      <PersonIcon color="var(--sub-600)"/>
    </div>
    <div class="flex-1">
      <p class="paragraph-xs c-sub-600">Düet</p>
      <span class="label-sm c-strong-950">
            <template v-for="(artist,artistIndex) in product.featured_artists">
                {{ artist.name }} <template v-if="artistIndex != product.featured_artists.length-1"> , </template>
            </template>
        </span>
    </div>
  </div>

  <div class="flex gap-3.5 items-center">
    <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
      <GenreIcon color="var(--sub-600)"/>
    </div>
    <div>
      <p class="paragraph-xs c-sub-600">Tarz</p>
      <span class="label-sm c-strong-950">{{ product.genre?.name }}</span>
    </div>
  </div>


  <div class="flex gap-3.5 items-center">
    <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
      <CalendarIcon color="var(--sub-600)"/>
    </div>
    <div>
      <p class="paragraph-xs c-sub-600">Yayın Tarihi</p>
      <span class="label-sm c-strong-950">{{ product.physical_release_date }}</span>
    </div>


  </div>


  <div class="flex gap-3.5 items-center">
    <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
      <DurationIcon color="var(--sub-600)"/>
    </div>
    <div>
      <p class="paragraph-xs c-sub-600">Toplam Süre</p>
      <span class="label-sm c-strong-950">{{ product.duration }}</span>
    </div>


  </div>

</template>

<script setup>
import {ref} from 'vue';
import {DragUploadInput} from '@/Components/Form';
import {toast} from 'vue3-toastify';

import {
  AddIcon,
  BroadcastTitleIcon,
  PersonIcon,
  GenreIcon,
  CalendarIcon,
  DurationIcon,
  InfoFilledIcon
} from '@/Components/Icons';
import {useCrudStore} from '@/Stores/useCrudStore';
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
  product: {},
})

const imageUploadFile = ref()
const crudStore = useCrudStore();



const onChange = async (e) => {

  if (e) {
  try {
        const response = await crudStore.formData(route('control.image.upload', {
            model: "Product",
            id: props.product.id
        }), {
            "file": e
        });
        imageUploadFile.value.showImage = true;
  } catch (error) {
    toast.error(error.response?.data?.errors?.file);

  }


  }
}

const onImageDelete = async () => {
   var response = await  crudStore.del(route('control.media.destroy',props.product?.image?.id))

}
</script>

<style lang="scss" scoped>

</style>
