<template>

  <AdminLayout :showDatePicker="false" :title="__('control.product.show_header')" parentTitle="Katalog"
               subParent="Tüm Şarkılar" :hasPadding="false">
    <template #breadcrumb>
      <span class="label-xs c-soft-400">Katalog</span>
      <span class="label-xs c-soft-400">•</span>
      <span class="label-xs c-soft-400 cursor-pointer" @click="router.visit(route('control.catalog.products.index'))">Tüm Yayınlar</span>
      <span class="label-xs c-soft-400">•</span>
      <span class="label-xs c-soft-400">{{ product.type_text }}</span>
      <span class="label-xs c-soft-400">•</span>
      <span class="label-xs c-soft-400">{{ product.album_name }}</span>
    </template>
    <div class="bg-white-400 h-60 p-5 flex  relative ">


      <div
          class=" rounded-lg w-60 h-60 bg-blue-300 left-8 top-8 flex items-center justify-center overflow-hidden">
        <img v-if="product.image" class="w-full h-full object-cover"
             :alt="product.album_name"
             :src="product.image?.small">
      </div>
      <div class=" flex-1 ms-4 flex flex-col">

        <div class="flex items-center gap-2 mb-2">
          <h1 class="label-xl c-strong-950" v-text="product.album_name"/>
          <div class="border border-soft-200 rounded-lg px-2 py-1 flex items-center gap-2 w-min">
            <component :is="statusData.find((e) => e.value == usePage().props.product.status)?.icon"
                       :color="statusData.find((e) => e.value == usePage().props.product.status)?.color"></component>
            <p class="subheading-xs c-sub-600">
              {{ statusData.find((e) => e.value == usePage().props.product.status)?.label }}
            </p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <span class="c-sub-600 paragraph-xs">{{ product.created_at }}</span>
          <span class="label-sm c-soft-300">•</span>
          <span class="c-sub-600 paragraph-xs">{{ product.song_count }}</span>
          <span class="label-sm c-soft-300">•</span>
          <span class="c-sub-600 paragraph-xs">{{ product.total_duration }}</span>
        </div>

        <div class="flex items-center mt-2" v-for="artist in product.main_artists">
          <div class="w-6 h-6 bg-blue-300 rounded-full overflow-hidden me-2">
            <img :alt="artist.name"
                 :src="artist.image ? artist.image.thumb : defaultStore.profileImage(artist.name)"
                 class="border-2 border-white rounded-full "
            >
          </div>
          <p class="label-sm c-sub-600 me-1">{{ artist.name }}</p>
        </div>

        <div class="flex items-center gap-2 w-96" :class="usePage().props.product.status == 4 ? 'mt-5' :'mt-auto' ">

          <AppSelectInput class="bg-white" v-model="productStatus"
                          :config="productStatusConfig">

          </AppSelectInput>
          <RegularButton :loading="changingStatus" @click="changeStatus" class="w-min">
            Onayla
          </RegularButton>

        </div>
        <div v-if="productStatus == 4" class="w-96  mt-2">
          <FormElement v-model="usePage().props.product.note" direction="vertical" placeholder="Red Sebebi"
                       :error="hasError"></FormElement>
        </div>
      </div>
      <div class="flex items-center gap-2 absolute top-5 right-5">
        <RegularButton @click="router.visit(route('control.catalog.products.form.edit',[1,product.id]))">
          <template #icon>
            <EditLineIcon color="var(--sub-600)"/>
          </template>
          Yayını Düzenle

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
    <div class="mt-32 "></div>
    <div v-if="usePage().props.product.status == 4" class="my-3 px-8">
      <div class="bg-red-50 rounded px-3 py-2 my-2 flex items-center gap-2">
        <WarningIcon color="var(--error-500)"/>
        <p class="paragraph-xs c-strong-950"> {{ usePage().props.product.note }}</p>

      </div>
    </div>
    <!---APP TABS --->
    <div>
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
  EditLineIcon,
  AddIcon,
  LabelsIcon,
  CalendarIcon,
  WarningIcon,
  RetractedIcon,
  ArtistsIcon,
  AudioIcon,
  MusicVideoIcon,
  RingtoneIcon,
  CheckFilledIcon
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

const changingStatus = ref(false);
const isModalOn = ref(false);
import {toast} from 'vue3-toastify';

const hasError = ref(null)
const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  statuses: {
    type: Array,
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

const productStatus = ref(props.product.status);
const productStatusConfig = computed(() => {
  return {
    data: props.statuses,
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
  if (changingStatus.value) {
    return;
  }
  changingStatus.value = true;
  if (productStatus.value == 4) {
    if (props.product.note == null || props.product.note == '') {


      toast.error("Ret sebebi için not girmeniz gerekmektedir");
      hasError.value = 'Not Giriniz'
      changingStatus.value = false;
      return;
    }

  }
  const response = await crudStore.post(route('control.catalog.products.changeStatus', props.product.id), {
    status: productStatus.value,
    note: props.product.note,
  });
  if (response.success) {
    props.product.status = productStatus.value;

    changingStatus.value = false;
    toast.success("Durum Başarıyla Değiştirildi");

  }


}

const statusData = ref([
  {
    label: "Taslak",
    value: 1,
    icon: EditLineIcon,
    color: "#FF8447",

  },
  {
    label: "İnceleniyor",
    value: 2,
    icon: EditLineIcon,
    color: "#FF8447",
  },
  {
    label: "Yayınlandı",
    value: 3,
    icon: CheckFilledIcon,
    color: "#335CFF",
  },
  {
    label: "Reddedildi",
    value: 4,
    icon: WarningIcon,
    color: "#FB3748",
  },
  {
    label: "Geri Çekildi",
    value: 5,
    icon: RetractedIcon,
    color: "#717784",
  },
  {
    label: "Planlandı",
    value: 6,
    icon: CheckFilledIcon,
    color: "#FF8447",
  }
]);
</script>

<style lang="scss" scoped>

</style>
