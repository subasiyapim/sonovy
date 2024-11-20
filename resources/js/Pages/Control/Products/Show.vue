<template>

  <AdminLayout :showDatePicker="false" :title="__('control.product.show_header')" parentTitle="Katalog"
               subParent="Tüm Şarkılar" :hasPadding="false">

    <div class="bg-white-400 h-60 p-5 flex  relative ">


      <div
          class=" rounded-lg w-60 h-60 bg-blue-300 left-8 top-8 flex items-center justify-center overflow-hidden">
        <img v-if="product.image" class="w-full h-full object-cover"
             :alt="product.album_name"
             :src="product.image?.original_url">
      </div>
      <div class=" flex-1 ms-4 flex flex-col">

        <h1 class="label-xl c-strong-950" v-text="product.album_name"/>

        <div class="flex items-center gap-2">
          <span class="c-sub-600 paragraph-xs">{{ product.created_at }}</span>
          <span class="label-sm c-soft-300">•</span>
          <span class="c-sub-600 paragraph-xs">{{ product.song_count }}</span>
          <span class="label-sm c-soft-300">•</span>
          <span class="c-sub-600 paragraph-xs">{{ product.total_duration }}</span>
        </div>

        <div class="flex items-center mt-2" v-for="artist in product.main_artists">
          <div class="w-6 h-6 bg-blue-300 rounded-full overflow-hidden me-3">
            <img class="w-full h-full image-fluid" :src="artist.image"/>
          </div>
          <p class="label-sm c-sub-600 me-1">{{ artist.name }}</p>
        </div>
        <div class="flex items-center gap-2 w-96" :class="product.status == 4 ? 'mt-5' :'mt-auto' ">

          <AppSelectInput  class="bg-white" v-model="product.status"
                          :config="productStatusConfig">

          </AppSelectInput>
          <RegularButton @click="changeStatus" class="w-full">
            <template #icon>
              <EditLineIcon color="var(--sub-600)"/>
            </template>
            Yayını Düzenle

          </RegularButton>

        </div>
        <div v-if="product.status == 4" class="w-96  mt-2">
            <FormElement v-model="product.note" direction="vertical" placeholder="Red Sebebi" :error="hasError"></FormElement>
        </div>
      </div>
      <div class="flex items-center gap-2 absolute top-5 right-5">
        <RegularButton @click="router.visit(route('control.catalog.products.form.edit',[1,product.id]))">
          Güncelle

        </RegularButton>

        <RegularButton @click="remove">
          Geri Çek
        </RegularButton>
        <RegularButton @click="remove">
          Tekrar Gönder
        </RegularButton>
      </div>
      <div class="flex items-center gap-4 absolute bottom-5 right-5">
        <a v-for="(platform, index) in filteredPlatforms"
           class="flex items-center gap-2"
           :href="platform.url"
           :key="platform.id"
           target="_blank"
        >
          <Icon :icon="platform.icon"/>
          <span class="c-strong-950 label-sm">{{ platform.name }}</span>
          <span class="label-sm c-soft-300" v-if="index < filteredPlatforms.length - 1">•</span>
        </a>


      </div>


    </div>
    <!---APP TABS --->
    <div class="mt-32 ">
      <AppTabs :slug="currentTab" :tabs="tabs" class="my-5" @change="onTabChange"></AppTabs>
    </div>
    <div class="px-8 pb-10">
      <component :product="product" :is="tabs.find(e => e.slug == currentTab)?.component"></component>
    </div>

  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {ArtistDialog} from '@/Components/Dialog';
import {FormElement} from '@/Components/Form';
import {
  DocumentIcon,
  EditIcon,
  Icon,
  LinkIcon,
  PersonCardIcon,
  PersonIcon,
  PhoneIcon,
  PlayFilledIcon,
  SpotifyIcon,
  TrashIcon,
  WorldIcon,
  EditLineIcon
} from '@/Components/Icons'
import {PrimaryButton, RegularButton} from '@/Components/Buttons'
import {AppSelectInput} from '@/Components/Form'
import {AppTabs} from '@/Components/Widgets'
import {BasicBadge} from '@/Components/Badges'


import {useDefaultStore} from "@/Stores/default";
import {TidalIcon, YoutubeIcon} from "@/Components/Icons/index.js";
import {computed, ref} from "vue";
import {useForm, usePage, router} from '@inertiajs/vue3';
import MetadataTab from './ShowTabs/metadata.vue';
import HistoryTab from './ShowTabs/history.vue';
import RegionsTab from './ShowTabs/regions.vue';
import DistributionsTab from './ShowTabs/distributions.vue';
import PromotionsTab from './ShowTabs/promotions.vue';
import SongsTab from './ShowTabs/songs.vue';
import {useCrudStore} from '@/Stores/useCrudStore';

const isModalOn = ref(false);
import {toast} from 'vue3-toastify';
const hasError = ref(null)
const props = defineProps({
  product: {
    type: Object,
    required: true
  }
});


const crudStore = useCrudStore();
const defaultStore = useDefaultStore();
let params = new URLSearchParams(window.location.search)

const currentTab = ref(params.get('slug') ?? 'metadata')
const filteredPlatforms = computed(() => {
  return [];
});
const onArtistProcessDone = () => {
  location.reload();
}
const remove = () => {

}


const productStatusConfig = computed(() => {
  return {
    data: [
      {
        label: "Taslak",
        value: 1,
      },
      {
        label: "İnceleniyor",
        value: 2,
      },
      {
        label: "Yayınlandı",
        value: 3,
      },
      {
        label: "Reddedildi",
        value: 4,
      },
      {
        label: "Geri Çekildi",
        value: 5,
      },
      {
        label: "Planlandı",
        value: 6,
      }
    ],
  };
})
const tabs = ref([
  {
    title: "Metadata",
    slug: "metadata",
    component: MetadataTab,
  },
  {
    title: "Parçalar",
    slug: "songs",
    component: SongsTab,
  },
  {
    title: "Bölgeler",
    slug: "regions",
    component: RegionsTab,
  },
  {
    title: "Promosyon",
    slug: "promotions",
    component: PromotionsTab,
  },
  {
    title: "Dağıtım",
    slug: "distributions",
    component: DistributionsTab,
  },
  {
    title: "Tarihçe",
    slug: "histories",
    component: HistoryTab,
  },
])

const onTabChange = (tab) => {

  router.visit(route(route().current(), props.product.id ?? 4), {
    data: {slug: tab.slug}
  });
}

const changeStatus = async () => {

    if(props.product.status == 4){
        console.log(props.product.note);

        if(props.product.note == null || props.product.note == ''){
            console.log("GELDİİİ");

            toast.error("Ret sebebi için not girmeniz gerekmektedir");
            hasError.value = 'Not Giriniz'
            return;
        }

    }
  const response = await crudStore.post(route('control.catalog.products.changeStatus', props.product.id), {
    status: props.product.status,
    note:props.product.note,
  });
  console.log("RESPONSE",response);

  if (response.success) {
    toast.success("Durum Başarıyla Değiştirildi");

  }


}
</script>

<style lang="scss" scoped>

</style>
