<template>

  <div class="flex h-screen overflow-hidden">
    <div class="flex-1 flex flex-col">
      <div class="flex-1 relative   ">
        <div class="flex items-center staticTopInfo h-20">
          <div class="flex items-center gap-3.5 flex-1 ">

            <IconButton @click="goBack" hasBorder size="medium">
              <ArrowLeftIcon color="var(--sub-600)"/>
            </IconButton>
            <div class="flex flex-col flex-1">
              <p  class="label-lg c-strong-950">Tüm Yayınlar</p>
              <div class="flex items-center gap-2">
                <span class="label-xs c-soft-400">Katalog</span>
                <span class="label-xs c-soft-400">•</span>
                <span @click="router.visit(route('control.catalog.products.index'))" class="cursor-pointer label-xs c-soft-400">Tüm Yayınlar</span>
                 <span class="label-xs c-soft-400">•</span>
                <span class="label-xs c-soft-400">
                        {{product.type == 1 ? 'Ses Yayın' :(product.type == 2 ? 'Müzik Video' : (product.type == 4 ? 'Apple Video' : 'Zil Sesi') ) }}
                </span>
                 <span class="label-xs c-soft-400">•</span>
                <span class="label-xs c-soft-400">
                    <template v-if="step == 1">
                        Yayın Bilgileri
                    </template>
                    <template v-if="step == 2">
                      {{ product.type == 1 ? 'Şarkı Detay' : (product.type == 2 ? 'Video Detay' : 'Zil Sesi Detay' )}}
                    </template>
                    <template v-if="step == 3">
                       Yayınlama Detayları
                    </template>
                    <template v-if="step == 4">
                        Pazarlama ve Onay
                    </template>
                </span>
              </div>
            </div>
            <RegularButton @click="goOut">Kaydet ve Çık</RegularButton>
            <RegularButton @click="goOut">İptal</RegularButton>


          </div>
        </div>
      </div>
      <div class="w-full h-full bg-white-500 flex flex-col gap-10 p-8 overflow-hidden">

        <AppStepper :modelValue="currentTab" @change="onChangeTab" :count="product.type != 2 ? 4 : 3">
          <AppStepperElement tippy="Yayın genel bilgileri" :showWarning="!completed_steps.step1" title="Yayın Bilgileri"></AppStepperElement>
          <AppStepperElement tippy="Yüklenen içerikler ve onlara ait bilgiler" :showWarning="!completed_steps.step2"
                             :title="product.type == 1 ? 'Şarkı Detay' : (product.type == 2 ? 'Video Detay' : 'Zil Sesi Detay' )"></AppStepperElement>
          <AppStepperElement tippy="Yayına ait tarih, platform ve ülke bilgileri" :showWarning="!completed_steps.step3" title="Yayınlama Detayları"></AppStepperElement>
          <AppStepperElement tippy="Tanıtıma ait bilgiler" v-if="product.type != 2" :showWarning="!completed_steps.step4"
                             title="Pazarlama ve Onay"></AppStepperElement>

        </AppStepper>

        <div class="h-full bg-white w-full shadow rounded-xl px-8 py-8 overflow-scroll">

          <ProductInfoTab v-model="step1Element" @onVideoTypeChange="onVideoTypeChange" :genres="genres" :formats="formats" :product="product"
                          :languages="languages"
                          v-if="currentTab == 0"></ProductInfoTab>
          <SongDetailTab :product="product" :genres="genres" v-model="step2Element"
                         v-if="currentTab == 1"></SongDetailTab>
          <PublishingDetailTab v-if="currentTab == 2 && product.type != 2" v-model="step3Element"
                               :product="product"></PublishingDetailTab>
          <MusicVideoPublishingDetails v-if="currentTab == 2 && product.type == 2" v-model="step3Element"
                                       :product="product"></MusicVideoPublishingDetails>
          <MarketingAndSend v-if="currentTab == 3" v-model="step4Element" :product="product"></MarketingAndSend>


        </div>
        <div class="flex items-center justify-center w-full">
          <div class="flex-1 flex items-center gap-2 justify-center">
            <p class="label-medium">%{{ progress }} Tamamlandı</p>
            <div class="w-48">
              <AppProgressIndicator v-model="progress"/>
            </div>
          </div>
          <PrimaryButton v-if="currentTab < 3" @click="submitStep">

            Devam Et


          </PrimaryButton>
          <tippy v-else :allowHtml="true" :sticky="true" :interactive="true"
                 :trigger="Object.values(props.completed_steps).filter((e) => e == false).length > 0 ? 'mouseenter' : 'manual'">

            <PrimaryButton @click="submitStep">


              Yayına gönder


            </PrimaryButton>

            <template #content>
              Yayına göndermek için tüm eksikleri tamamlamalısınız!
            </template>
          </tippy>

        </div>
      </div>

    </div>
    <div class="w-80 border-l border-soft-200 h-full flex flex-col">
      <div class="flex items-center gap-3.5 px-5 py-4 h-20 border-b  border-soft-200">
        <div class="w-12 h-12 bg-dark-green-800 border border-soft-200 rounded-full flex items-center justify-center">
          <StickyNoteFilledIcon color="var(--dark-green-500)"/>
        </div>
        <h1 class="label-lg c-strong-950">Yayın özeti</h1>


      </div>
      <div class="p-6 flex flex-col h-full gap-5 overflow-scroll">
        <ProductSummaryTab :product="product"></ProductSummaryTab>

      </div>
    </div>

  </div>


</template>

<script setup>
import {computed, ref, onBeforeMount} from 'vue';
import {IconButton} from '@/Components/Buttons'

import {
  ArrowLeftIcon,
  StickyNoteFilledIcon,
} from '@/Components/Icons';
import {router} from '@inertiajs/vue3';
import ProductInfoTab from './Tabs/Left/ProductInfoTab.vue'
import ProductSummaryTab from './Tabs/Right/ProductSummaryTab.vue'
import {useForm} from '@inertiajs/vue3';
import SongDetailTab from './Tabs/Left/SongDetailTab.vue'
import {PrimaryButton,RegularButton} from '@/Components/Buttons'
import {AppProgressIndicator} from '@/Components/Widgets'
import PublishingDetailTab from './Tabs/Left/PublishingDetailTab.vue'
import MusicVideoPublishingDetails from './Tabs/Left/MusicVideoPublishingDetails.vue'
import MarketingAndSend from './Tabs/Left/MarketingAndSend.vue'
import {AppStepper, AppStepperElement} from '@/Components/Stepper';
import {useCrudStore} from '@/Stores/useCrudStore';
import {toast} from 'vue3-toastify';
import {Link} from '@inertiajs/vue3';


const crudStore = useCrudStore();
const props = defineProps({
  product: {},
  genres: {},
  step: {},
  languages: {},
  formats: {},
  progress: Number,
  product_publish_country_types: {},
  completed_steps: {},
})

const progress = ref(props.progress);
const onChangeTab = (e) => {

  router.visit(route('control.catalog.products.form.edit', [e + 1, props.product.id]))

}

const step1Element = useForm({
  type: props.product.type,
  step: props.step,
  album_name: props.product.album_name,
  version: props.product.version,
  main_artists: [],
  mixed_album: props.product.mixed_album,
  featuring_artists: [],
  genre_id: props.product.genre_id,
  sub_genre_id: props.product.sub_genre_id,
  format_id: props.product.format_id,
  label_id: props.product.label_id,
  p_line: props.product.p_line,
  c_line: props.product.c_line,
  upc_code: props.product.upc_code,
  ean_code: props.product.ean_code,
  catalog_number: props.product.catalog_number,
  language_id: props.product.language_id,
  main_price: props.product.main_price,
  video_type: props.product.video_type,
  description: props.product.description,
  is_for_kids: props.product.is_for_kids,
  grid_code: props.product.grid_code,
});


const step2Element = useForm({
  step: props.step,
  songs: [],
});
const step3Element = useForm({
  production_year: props.product.production_year,
  publishing_country_type: props.product.publishing_country_type ?? 1,
  step: props.step,
  published_countries: [],
  previously_released: props.product.previously_released == null ? false : props.product.previously_released,
  previous_release_date: props.product.previous_release_date == null ? null : new Date(props.product.previous_release_date),
  physical_release_date: props.product.physical_release_date == null ? null : new Date(props.product.physical_release_date),
  platforms: {}
});
const step4Element = useForm({
  step: props.step,
  promotions: props.product.promotions,
});

const goOut = () => {
    goBack();
}
const currentTab = ref(props.step - 1);
const onVideoTypeChange = async (e) => {
    props.product.type = e.value;

    await crudStore.post(route('control.catalog.products.change.type',props.product.id),{
        type: e.value,
    });
}

const submitStep = async () => {
  console.log(currentTab.value);

  if (currentTab.value == 0) {
    step1Element.post(route('control.catalog.products.form.step.store', props.product.id), {

      onError: (e) => {
        console.log("HTAA", e);
        toast.error(Object.values(e)[0])
      },
      onSuccess: (e) => {
        //console.log("EEE", e);

        router.visit(route('control.catalog.products.form.edit', [2, props.product.id]))
      }
    });
  }
  if (currentTab.value == 1) {
    if (step2Element.songs.length == 0) {
      toast.error("Şarkı eklemelisiniz");
      return;
    }
    let isCompleted = true;
    step2Element.songs.forEach(element => {

      if (element.is_completed == 0) {
        isCompleted = false;
      }

    });
    if (isCompleted) {
      router.visit(route('control.catalog.products.form.edit', [3, props.product.id]))
    } else {
      toast.error("Eksik şarkı bilgilerini tamamlamalısınız");

    }

  }
  if (currentTab.value == 2) {
    if (props.product.type == 2) {
        let tempPlatform = JSON.parse(JSON.stringify(step3Element.platforms));
       step3Element.platforms = step3Element.platforms.filter((e) => e.isChecked);
        step3Element.platforms.map((e) => e.id = e.value);
      step3Element.post(route('control.catalog.products.form.step.store', props.product.id), {
        onError: (e) => {
          toast.error(Object.values(e)[0])
        },
        onSuccess: (e) => {
          router.visit(route('control.catalog.products.show', props.product.id))

        },

      });
      return;
    } else {

    }

    if (step3Element.physical_release_date instanceof Date) {
      step3Element.physical_release_date = step3Element.physical_release_date.toISOString().split("T")[0];
    } else if (step3Element.physical_release_date) {
      // Tarih stringine dönüştürebileceğiniz bir formatta olduğundan emin olun
      let date = new Date(step3Element.physical_release_date);
      if (!isNaN(date)) {
        step3Element.physical_release_date = date.toISOString().split("T")[0];
      }
    }
    if (step3Element.previous_release_date instanceof Date) {
      step3Element.previous_release_date = step3Element.previous_release_date.toISOString().split("T")[0];
    } else if (step3Element.previous_release_date) {
      // Tarih stringine dönüştürebileceğiniz bir formatta olduğundan emin olun
      let date = new Date(step3Element.previous_release_date);
      if (!isNaN(date)) {
        step3Element.previous_release_date = date.toISOString().split("T")[0];
      }
    }

    let tempPlatforms = [];
    let toUploadBackPlatforms = step3Element.platforms;


    step3Element.platforms.forEach(element => {
      if (element.isSelected) {
        let data = {
          id: element.value,
          price: element.price,
          publish_date: element.publish_date?.toISOString().split("T")[0]
        }
        if (element.pre_order_date) {
          data['pre_order_date'] = element.pre_order_date.toISOString().split("T")[0];
        }
        tempPlatforms.push(data);
      }

    });


    step3Element.platforms = tempPlatforms;

    step3Element.post(route('control.catalog.products.form.step.store', props.product.id), {

      onError: (e) => {
        toast.error(Object.values(e)[0])
      },
      onSuccess: (e) => {
        router.visit(route('control.catalog.products.form.edit', [4, props.product.id]))
      },
      onFinish: () => {
        step3Element.platforms = toUploadBackPlatforms;

      }
    });
  }

  if (currentTab.value == 3) {


    step4Element.post(route('control.catalog.products.form.step.store', props.product.id), {

      onError: (e) => {

        if (e.image) {
          toast.error(e.image);
        }

      },
      onSuccess: (e) => {
        router.visit(route('control.catalog.products.show', props.product.id))
      }
    });


  }

}
onBeforeMount(() => {

  step1Element.main_artists = props.product.main_artists.map((e) => e.id) ?? [];
  step1Element.featuring_artists = props.product.featured_artists.map((e) => e.id) ?? [];
});
const goBack = () => {
    window.history.back();
}

</script>

<style lang="scss" scoped>

</style>
