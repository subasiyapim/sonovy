<template>

  <div class="pb-6">
    <p class="label-xl c-strong-950">
      İlk adım, yayın bilgilerinizi dolduralım.✍🏻
    </p>
  </div>
  <div class="flex gap-10">
    <div class="flex-1 flex flex-col overflow-scroll gap-6">
      <FormElement :required="true" label-width="190px" :error="form.errors.album_name" v-model="form.album_name"
                   label="Albüm Adı"></FormElement>
      <FormElement v-if="form.type != 2" label-width="190px" :error="form.errors.version" v-model="form.version"
                   label="Sürüm"
                   placeholder="Lütfen giriniz"></FormElement>

      <FormElement v-if="form.type == 2" :required="true" :config="{data: usePage().props.video_types}"
                   label-width="190px" type="select" placeholder="Seçiniz" :error="form.errors.video_type"
                   v-model="form.video_type"
                   label="Video Türü">

      </FormElement>
      <FormElement v-if="form.type == 2" :required="true" :config="{letter:5000}" label-width="190px" type="textarea"
                   placeholder="Açıklama giriniz" :error="form.errors.description" v-model="form.description"
                   label="Açıklama">
        <template #tooltip>
          Video Açıklaması
        </template>
      </FormElement>

      <FormElement ref="mainArtistSelect" :required="true" label-width="190px"
                   :error="form.errors.main_artists || form.errors.mixed_album"
                   v-model="form.main_artists" :disabled="form.mixed_album" type="multiselect" label="Sanatçı"
                   placeholder="Sanatçı Seçiniz"
                   :config="mainArtistSelectConfig">

        <template #disabled v-if="form.mixed_album">
          <p class="label-sm !font-normal">
            Various Artists
          </p>

        </template>
        <template #first_child>
          <button @click="openArtistCreateDialog('main_artists')"
                  class="flex items-center gap-2 paragraph-sm c-sub-600 p-2">
            <AddIcon color="var(--sub-600)"/>
            Sanatçı Oluştur
          </button>
        </template>
        <template #empty>
          <button @click="openArtistCreateDialog('main_artists')"
                  class="flex items-center gap-2 label-xs c-dark-green-600 p-2">
            <AddIcon color="var(--dark-green-600)"/>
            Sanatçı Oluştur
          </button>
        </template>
        <template #description>
          <label class="flex items-center gap-2 mt-2 px-1" for="isProductDerleme">
            <input type="checkbox" id="isProductDerleme" v-model="form.mixed_album" class="focus:ring-0 rounded">
            <span class="paragraph-xs c-strong-950">Derleme Albüm</span>
            <span class="paragraph-xs c-sub-600">(birden fazla sanatçı varsa seçin)</span>
          </label>
        </template>
        <template #option="scope">
          <div class="w-full flex justify-between gap-2">
            <div class="flex flex-1 gap-2">
              <div class="w-6 h-6 rounded-lg overflow-hidden">
                <img :src="scope.data.image"/>
              </div>
              <p>{{ scope.data.label }}</p>
            </div>

            <template v-if="scope.data.platforms">
              <div class="flex" v-for="platform in scope.data.platforms">
                <a v-if="platform.code == 'spotify' || platform.code == 'apple'"
                   :href="platform.pivot.url"
                   target="_blank">
                  <icon :icon="platform.icon"/>
                </a>
              </div>
            </template>
          </div>
        </template>
        <template #model="scope">
          <div class="flex items-center relative gap-2">
            <div class="flex -space-x-3 rtl:space-x-reverse">
                <template v-for="artist in scope.data.slice(0,2)">
                    <span class="flex items-center justify-center w-8 h-8  font-medium c-sub-600 label-sm bg-weak-50 border-2 border-white rounded-full" >
                          {{ artist.label[0] }}
                    </span>
                </template>
                <span v-if="scope.data.length > 2" class="flex items-center justify-center w-8 h-8  font-medium c-sub-600 label-sm bg-weak-50 border-2 border-white rounded-full" >
                    +{{scope.data.length-2}}
                </span>
            </div>

            <p class="label-sm !font-normal" style="white-space:nowrap;">
              <template v-for="(artist,artistIndex) in scope.data.slice(0,2)">
                {{ artist.label }}
                <template v-if="artistIndex < scope.data.length-1">
                  , &nbsp;
                </template>

              </template>
            </p>
          </div>
        </template>
      </FormElement>
      <FormElement ref="featuringArtistSelect" label-width="190px" :error="form.errors.featuring_artists"
                   v-model="form.featuring_artists"
                   type="multiselect" label="Düet Sanatçı" placeholder="Seçiniz" :config="featuringArtistSelectConfig">
        <template #first_child>
          <button @click="openArtistCreateDialog('featuring_artists')"
                  class="flex items-center gap-2 paragraph-sm c-sub-600 p-2">
            <AddIcon color="var(--sub-600)"/>
            Sanatçı Oluştur
          </button>
        </template>
        <template #empty>
          <button @click="openArtistCreateDialog('featuring_artists')"
                  class="flex items-center gap-2 label-xs c-dark-green-600 p-2">
            <AddIcon color="var(--dark-green-600)"/>
            Sanatçı Oluştur
          </button>
        </template>
        <template #option="scope">
          <div class="flex items-center gap-2 w-full">
            <div class="w-5 h-5 rounded-md overflow-hidden">
              <img v-if="scope.data.image" alt=""
                   :src="scope.data.image"/>
            </div>
            <div class="flex-1"><p>{{ scope.data.label }}</p></div>

            <template v-if="scope.data.platforms">
              <div class="flex" v-for="platform in scope.data.platforms">
                <a v-if="platform.code == 'spotify' || platform.code == 'apple'"
                   :href="platform.pivot.url"
                   target="_blank">
                  <icon :icon="platform.icon"/>
                </a>
              </div>
            </template>
          </div>
        </template>
        <template #model="scope">
          <div class="flex items-center relative gap-2">
             <div class="flex -space-x-3 rtl:space-x-reverse">
                <template v-for="artist in scope.data.slice(0,2)">
                    <span class="flex items-center justify-center w-8 h-8  font-medium c-sub-600 label-sm bg-weak-50 border-2 border-white rounded-full" >
                          {{ artist.label[0] }}
                    </span>
                </template>
                <span v-if="scope.data.length > 2" class="flex items-center justify-center w-8 h-8  font-medium c-sub-600 label-sm bg-weak-50 border-2 border-white rounded-full" >
                    +{{scope.data.length-2}}
                </span>
            </div>
            <p class="label-sm !font-normal" style="white-space:nowrap;">
              <template v-for="(artist,artistIndex) in scope.data">
                {{ artist.label }}
                <template v-if="artistIndex < scope.data.length-1">
                  , &nbsp;
                </template>
              </template>
            </p>


          </div>
        </template>
      </FormElement>
      <FormElement :required="true" label-width="190px" :error="form.errors.genre_id" v-model="form.genre_id"
                   label="Tarz"
                   placeholder="Seçiniz" type="select" :config="genreConfig">

      </FormElement>
      <FormElement :required="true" label-width="190px" :error="form.errors.sub_genre_id" v-model="form.sub_genre_id"
                   label="Alt Tarz"
                   placeholder="Seçiniz" type="select" :config="genreConfig">
      </FormElement>

      <FormElement v-if="form.type == 1" label-width="190px" :error="form.errors.format_id" v-model="form.format_id"
                   label="Biçim"
                   placeholder="Seçiniz" type="select" :config="formatConfig">

      </FormElement>

      <FormElement v-if="form.type == 2" label-width="190px" :error="form.errors.is_for_kids" v-model="form.is_for_kids"
                   placeholder="Bu video çocuklar için yapıldı" type="fancyCheck">

      </FormElement>

    </div>

    <div class="flex-1 flex flex-col overflow-scroll gap-6">

      <FormElement :required="true" type="select" :error="form.errors.label_id" v-model="form.label_id"
                   label-width="190px"
                   label="Plak şirketi" placeholder="Şirket Seçiniz" :config="labelConfig"></FormElement>
      <FormElement :required="true" label-width="190px" :error="form.errors.p_line" v-model="form.p_line"
                   label="p Satırı"
                   placeholder="Lütfen giriniz">
        <template #tooltip>
          Besteler, şarkı sözleri ve diğer müzikal öğeler, eser sahiplerine eserlerini kullanma, kopyalama, dağıtma,
          düzenleme ve ticari olarak değerlendirme yetkisi verir.
        </template>
      </FormElement>
        <FormElement :required="true" label-width="190px" :error="form.errors.c_line" v-model="form.c_line"
                   label="C Satırı"
                   placeholder="Lütfen giriniz">
                  <template #tooltip>
                    Yayın görselleri telif hakkı
                </template>
        </FormElement>
        <FormElement label-width="190px" :error="form.errors.upc_code" v-model="form.upc_code" label="UPC/EAN Kodu"
                   placeholder="Lütfen giriniz">
            <template #tooltip>
                <div class="flex items-start gap-2 p-2">
                    <div>
                       <WWWIcon color="var(--dark-green-500)" />
                    </div>
                    <div>
                        <h1 class="label-sm text-white ">
                            Universal Product Code
                        </h1>
                        <p class="paragraph-xs c-soft-400">UPC, bir ürünün benzersiz bir tanımlayıcısıdır müzik endüstrisinde albümler için kullanılır.
                            <a href="https://app.muzikdagitim.com/login" target="_blank" class=" underline text-white">Nasıl öğrenirim?</a>
                        </p>
                    </div>
                </div>
            </template>
        </FormElement>
        <FormElement v-if="form.type == 3" label-width="190px" :error="form.errors.grid_code" v-model="form.grid_code" label="Grid Kodu"
                   placeholder="Lütfen giriniz">
            <template #tooltip>
                <div class="flex items-start gap-2 p-2">
                      <p class="paragraph-xs c-soft-400">
                        Grid Kodu

                    </p>
                </div>
            </template>
        </FormElement>
      <FormElement label-width="190px" :error="form.errors.catalog_number" v-model="form.catalog_number"
                   label="Katalog No" placeholder="Lütfen giriniz"></FormElement>
      <FormElement :required="true" type="select" label-width="190px" :error="form.errors.language_id"
                   v-model="form.language_id"
                   label="Albüm Dili" :config="languageConfig" placeholder="Lütfen giriniz">
        <template #model="scope">
          <div v-if="scope.data" class="flex items-center gap-2">
            <span>{{ languageConfig.data?.find((el) => el.value == scope.data)?.iconKey }}</span>
            <span>{{ languageConfig.data?.find((el) => el.value == scope.data)?.label }}</span>
          </div>
        </template>
        <template #option="scope">
          <span>{{ scope.data.iconKey }}</span>
          <span class="paragraph-sm c-strong-950">{{ scope.data.label }}</span>
        </template>
      </FormElement>

      <FormElement :required="true" type="select" label-width="190px" :error="form.errors.main_price"
                   v-model="form.main_price"
                   label="Ana Fiyat" placeholder="Lütfen giriniz" :config="mainPriceConfig">

      </FormElement>
    </div>
  </div>
  <SmallArtistCreateDialog @done="onArtistCreated" v-if="createArtistDialog"
                           v-model="createArtistDialog"></SmallArtistCreateDialog>
</template>

<script setup>
import {computed, onBeforeMount, ref} from 'vue';
import {FormElement} from '@/Components/Form';
import {AddIcon,WWWIcon} from '@/Components/Icons'
import {useCrudStore} from '@/Stores/useCrudStore';
import {usePage} from '@inertiajs/vue3';
import {SmallArtistCreateDialog} from '@/Components/Dialog';
import {Icon, PersonIcon} from "@/Components/Icons/index.js";
import {useDefaultStore} from "@/Stores/default";

const defaultStore = useDefaultStore();

const whichSelectToAdd = ref(null);
const openArtistCreateDialog = (artistSelectName) => {
  whichSelectToAdd.value = artistSelectName;
  createArtistDialog.value = true;
}
const createArtistDialog = ref(false);
const crudStore = useCrudStore();
const props = defineProps({
  modelValue: {},
  genres: {},
  languages: {},
  formats: {},
  product: {}
})

const mainArtistSelect = ref();
const featuringArtistSelect = ref();
const emits = defineEmits(['update:modelValue']);


const onArtistCreated = (e) => {
  let row = {
    label: e.name,
    value: e.id,

  };


  if (whichSelectToAdd.value == 'featuring_artists') {
    featuringArtistSelect.value.appMultiSelect.insertData(row);
  } else {
    mainArtistSelect.value.appMultiSelect.insertData(row);
  }

}
const form = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})
const featuringArtistSelectConfig = computed(() => {
  return {
    showTags: true,
    hasSearch: true,
    data: usePage().props.artists,
    remote: async (query) => {

      const response = await crudStore.get(route('control.search.artists', {
        search: query
      }))
      const formattedData = response.map(item => ({
        value: item.id,
        label: item.name,
        image: item.image ? item.image.thumb || item.image.url : defaultStore.profileImage(item.name)  // Use `thumb` if available, fallback to `url`
      }));


      return formattedData;

    }
  }
})
const mainArtistSelectConfig = computed(() => {
  return {
    showTags: true,
    hasSearch: true,
    data: usePage().props.artists,
    remote: async (query) => {

      const response = await crudStore.get(route('control.search.artists', {
        search: query
      }))


      const formattedData = response.map(item => ({
        value: item.id,
        label: item.name,
        image: item.image ? item.image.thumb || item.image.url : defaultStore.profileImage(item.name),  // Use `thumb` if available, fallback to `url`
        platforms: item.platforms
      }));


      return formattedData;

    }
  }
})
const labelConfig = computed(() => {
  return {
    showTags: false,
    hasSearch: true,
    data: usePage().props.labels,
    remote: async (query) => {

      const response = await crudStore.get(route('control.search.labels', {
        search: query
      }))
      const formattedData = response.map(item => ({
        value: item.id,
        label: item.name,
      }));


      return formattedData;

    }
  }
})
const genreConfig = computed(() => {
  return {
    hasSearch: true,
    data: props.genres,
  }
})


const mainPriceConfig = computed(() => {
  return {
    hasSearch: false,
    data: usePage().props.main_prices,
  }
})

const languageConfig = computed(() => {
  return {
    hasSearch: true,
    data: props.languages
  }
})
const formatConfig = computed(() => {
  return {
    data: props.formats
  }
})

onBeforeMount(() => {
  if (props.product.label) {
    labelConfig.value.data.push({
      "value": props.product.label.id,
      "label": props.product.label.name,
    });
  }

  if (props.product.main_artists) {


    props.product.main_artists.forEach(element => {

      const findedIndex = mainArtistSelectConfig.value.data.findIndex((art) => art.value == element.id);
      if (findedIndex < 0) {
        mainArtistSelectConfig.value.data.push({
          "image": element.media[0]?.original_url,
          "value": element.id,
          "label": element.name,
          "platforms": element.platforms,
        });
      }

    });

  }
  if (props.product.featured_artists) {

    props.product.featured_artists.forEach(element => {
      console.log("ELEENT", element);
      const findedIndex = featuringArtistSelectConfig.value.data.findIndex((art) => art.value == element.id);
      if (findedIndex < 0) {
        featuringArtistSelectConfig.value.data.push({
          "image": element.media[0]?.original_url,
          "value": element.id,
          "label": element.name,
        });
      }
    });

  }


});

</script>

<style lang="scss" scoped>

</style>
