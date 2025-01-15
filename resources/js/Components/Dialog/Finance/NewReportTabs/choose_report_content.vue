<script setup>
import {ref,computed} from 'vue';
import {FormElement} from '@/Components/Form'
import {Icon,CloseIcon,RingtoneIcon} from '@/Components/Icons'
import {useCrudStore} from '@/Stores/useCrudStore';
import {useDefaultStore} from '@/Stores/default';
import {StatusBadge} from '@/Components/Badges';
import {AppAccordion} from '@/Components/Widgets';

const date = ref();
import { usePage} from '@inertiajs/vue3';

const crudStore = useCrudStore();
const defaultStore = useDefaultStore();
const props = defineProps({
    modelValue:{},
    formattedDates:{},
})

const emits = defineEmits(['update:modelValue']);

const element = computed({
    get:() => props.modelValue,
    set:(value) => emits('update:modelValue',value)
});
const choosenArtists = ref([]);
const choosenProducts = ref([]);
const choosenSongs = ref([]);
const choosenPlatforms = ref([]);
const choosenCountries = ref([]);

const mainArtistSelectConfig = computed(() => {
    return {
        value:'id',
        label:'name',
        showTags: false,
        hasSearch: true,
        data: usePage().props.artists,

    }
});
const productsSelectConfig = computed(() => {
    return {
        value:'id',
        label:'album_name',
        showTags: false,
        hasSearch: true,
        data: usePage().props.products,
    }
});
const songsSelectConfig = computed(() => {
    return {
        value:'id',
        label:'name',
        showTags: false,
        hasSearch: true,
        data:  usePage().props.songs,

    }
});
const platformsSelectConfig = computed(() => {
  return {
        value : 'id',
        label:'name',
        showTags: false,
        hasSearch: true,
        data: usePage().props.platforms,

    }
});
const labelSelectConfig = computed(() => {
  return {
        value : 'id',
        label:'name',
        showTags: false,
        hasSearch: true,
        data: usePage().props.labels,

    }
});
const countriesSelectConfig = computed(() => {
  return {
        showTags: false,
        hasSearch: true,
        data: [],
        remote: async (query) => {

        const response = await crudStore.get(route('control.search.countries', {
            search: query
        }))
        const formattedData = response.map(item => ({
            value: item.id,
            label: item.name,
        }));

        return formattedData;

        }
    }
});
const choosenIds = ref([]);
const changeContentTab = (tab) => {
    element.value.choosenValues = [];
    element.value.report_content_type = tab
};

const onChangeValue = (e) => {

//  element.value.choosenValues = e;

};

const onCountryCheck = (e) => {
  const findedIndex = element.value.choosenValues.findIndex((el) => el == e.value);
  if (findedIndex >= 0) {
    element.value.choosenValues.splice(findedIndex, 1);
  } else {
    element.value.choosenValues.push(e.value);
  }

  Object.keys(usePage().props.countriesGroupedByRegion.data).forEach((key) => {

    let total = 0;
    usePage().props.countriesGroupedByRegion.data[key].forEach(element => {
      if (element.value.choosenValues.includes(element.value)) {
        total++;
      }

    });

    usePage().props.countriesGroupedByRegion.countries.counts[key].selected_count = total;

  })
}

const chooseAll = (key) => {
console.log("KEY",key);


  usePage().props.countriesGroupedByRegion.data[key].forEach((e) => {
    const findedIndex = element.value.choosenValues.findIndex((el) => el == e.value);
    if (findedIndex < 0) {
      element.value.choosenValues.push(e.value);
    }
  });


}
const unChooseAll = (key) => {
    usePage().props.countriesGroupedByRegion.data[key].forEach((e) => {
        const findedIndex = element.value.choosenValues.findIndex((el) => el == e.value);
        if (findedIndex >= 0) {
            element.value.choosenValues.splice(findedIndex, 1);
        }
    });

};

const chooseAllCountries = () => {
    removeChoosingCountries();
    Object.keys(usePage().props.countriesGroupedByRegion.data).forEach(key => {
        chooseAll(key)
    });
}
const removeChoosingCountries = () => {
    element.value.choosenValues = [];
};

</script>

<template>
      <div class="flex flex-col items-start">
            <p class="label-medium c-strong-950">Rapor içeriği</p>
            <div class="flex items-center gap-2">
                <p class="paragraph-xs c-sub-600">Lorem ipsum dolor sit amet consectetur. Pellentesque id sed turpis sed condimentum </p>

            </div>
        </div>
        <div v-if="element.type == 1" class="flex items-center gap-3 flex-1 my-6">
            <button class="flex items-center gap-2 label-sm c-sub-600" @click="changeContentTab(1)" ><div class="w-4 h-4 rounded-full flex items-center justify-center drop-shadow" :class="element.report_content_type == 1 ? 'bg-dark-green-500' :'bg-white'"><div class="w-2.5 h-2.5 rounded-full bg-white"></div> </div>Tam Rapor</button>
            <button class="flex items-center gap-2 label-sm c-sub-600" @click="changeContentTab(12)" ><div class="w-4 h-4 rounded-full flex items-center justify-center drop-shadow" :class="element.report_content_type == 12 ? 'bg-dark-green-500' :'bg-white'"><div class="w-2.5 h-2.5 rounded-full bg-white"></div> </div>Label Seç</button>
            <button class="flex items-center gap-2 label-sm c-sub-600" @click="changeContentTab(2)" ><div class="w-4 h-4 rounded-full flex items-center justify-center drop-shadow" :class="element.report_content_type == 2 ? 'bg-dark-green-500' :'bg-white'"><div class="w-2.5 h-2.5 rounded-full bg-white"></div> </div>Sanatçı Seç</button>
            <button class="flex items-center gap-2 label-sm c-sub-600" @click="changeContentTab(3)" ><div class="w-4 h-4 rounded-full flex items-center justify-center drop-shadow" :class="element.report_content_type == 3 ? 'bg-dark-green-500' :'bg-white'"><div class="w-2.5 h-2.5 rounded-full bg-white"></div> </div>Albüm Seç</button>
            <button class="flex items-center gap-2 label-sm c-sub-600" @click="changeContentTab(4)" ><div class="w-4 h-4 rounded-full flex items-center justify-center drop-shadow" :class="element.report_content_type == 4 ? 'bg-dark-green-500' :'bg-white'"><div class="w-2.5 h-2.5 rounded-full bg-white"></div> </div>Parça Seç</button>
            <button class="flex items-center gap-2 label-sm c-sub-600" @click="changeContentTab(5)" ><div class="w-4 h-4 rounded-full flex items-center justify-center drop-shadow" :class="element.report_content_type == 5 ? 'bg-dark-green-500' :'bg-white'"><div class="w-2.5 h-2.5 rounded-full bg-white"></div> </div>Platform Seç</button>
            <button class="flex items-center gap-2 label-sm c-sub-600" @click="changeContentTab(6)" ><div class="w-4 h-4 rounded-full flex items-center justify-center drop-shadow" :class="element.report_content_type == 6 ? 'bg-dark-green-500' :'bg-white'"><div class="w-2.5 h-2.5 rounded-full bg-white"></div> </div>Ülke Seç</button>
        </div>
        <div v-if="element.type == 2" class="flex items-center gap-3 flex-1 my-6">
            <button class="flex items-center gap-2 label-sm c-sub-600" @click="changeContentTab(7)" ><div class="w-4 h-4 rounded-full flex items-center justify-center drop-shadow" :class="element.report_content_type == 7 ? 'bg-dark-green-500' :'bg-white'"><div class="w-2.5 h-2.5 rounded-full bg-white"></div> </div>Label'e Göre</button>
            <button class="flex items-center gap-2 label-sm c-sub-600" @click="changeContentTab(8)" ><div class="w-4 h-4 rounded-full flex items-center justify-center drop-shadow" :class="element.report_content_type == 8 ? 'bg-dark-green-500' :'bg-white'"><div class="w-2.5 h-2.5 rounded-full bg-white"></div> </div>Sanatçıya Göre</button>
            <button class="flex items-center gap-2 label-sm c-sub-600" @click="changeContentTab(9)" ><div class="w-4 h-4 rounded-full flex items-center justify-center drop-shadow" :class="element.report_content_type == 9 ? 'bg-dark-green-500' :'bg-white'"><div class="w-2.5 h-2.5 rounded-full bg-white"></div> </div>Albüme Göre</button>
            <button class="flex items-center gap-2 label-sm c-sub-600" @click="changeContentTab(10)" ><div class="w-4 h-4 rounded-full flex items-center justify-center drop-shadow" :class="element.report_content_type == 10 ? 'bg-dark-green-500' :'bg-white'"><div class="w-2.5 h-2.5 rounded-full bg-white"></div> </div>Mağazaya Göre</button>
            <button class="flex items-center gap-2 label-sm c-sub-600" @click="changeContentTab(11)" ><div class="w-4 h-4 rounded-full flex items-center justify-center drop-shadow" :class="element.report_content_type == 11 ? 'bg-dark-green-500' :'bg-white'"><div class="w-2.5 h-2.5 rounded-full bg-white"></div> </div>Ülkeye Göre</button>
        </div>
        <div class="">
            <div v-if="element.report_content_type == 2 && element.type == 1">
                <div class="w-64">
                    <FormElement  v-model="element.choosenValues" direction="vertical" label="Sanatçı Seçimi" placeholder="Sanatçı Seç" type="multiselect" :config="mainArtistSelectConfig">
                        <template #option="scope">
                            <div class="w-full flex justify-between gap-2">
                                <div class="flex flex-1 gap-2">
                                <div class="w-6 h-6 rounded-lg overflow-hidden">
                                    <img :src="scope.data.image?.url"/>
                                </div>
                                <p class="paragraph-sm c-strong-950">{{ scope.data.name }}</p>
                                </div>

                                <template v-if="scope.data.platforms">
                                <div class="flex" v-for="platform in scope.data.platforms">
                                    <a v-if="platform.code == 'spotify' || platform.code == 'apple'"
                                    :href="platform.pivot.url"
                                    target="_blank">
                                    <Icon :icon="platform.icon"/>
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
                                            {{ artist.name[0] }}
                                        </span>
                                    </template>
                                    <span v-if="scope.data.length > 2" class="flex items-center justify-center w-8 h-8  font-medium c-sub-600 label-sm bg-weak-50 border-2 border-white rounded-full" >
                                        +{{scope.data.length-2}}
                                    </span>
                                </div>

                                <p class="label-sm !font-normal" style="white-space:nowrap;">
                                    <template v-for="(artist,artistIndex) in scope.data.slice(0,2)">
                                        {{ artist.name }}
                                        <template v-if="artistIndex < scope.data.length-1">
                                        , &nbsp;
                                        </template>

                                    </template>
                                </p>
                            </div>
                        </template>
                    </FormElement>
                 </div>
                <div  class="flex items-center gap-2">
                    <div v-for="(choosenValue,i) in element.choosenValues" class="border border-soft-200 rounded px-2 py-1 flex items-center gap-1">
                       <img class="rounded-full" width="24" height="24" :src="mainArtistSelectConfig.data.find((e) => e.id == choosenValue)?.image?.url">

                      <div class="whitespace-nowrap w-auto"> <p class="label-xs c-sub-600 !text-start"> {{mainArtistSelectConfig.data.find((e) => e.id == choosenValue)?.name}}</p></div>
                        <button @click="element.choosenValues.splice(i,1)"><CloseIcon color="var(--sub-600)" /></button>
                    </div>
                </div>
            </div>
            <div v-if="element.report_content_type == 3">
                <div class="w-64">
                    <FormElement v-model="element.choosenValues"  direction="vertical" label="Albüm Seçimi" placeholder="Albüm Seç" type="multiselect" :config="productsSelectConfig">
                        <template #option="scope">
                            <div class="w-full flex justify-between gap-2">
                                <div class="flex flex-1 gap-2">
                                    <div class="w-6 h-6 rounded-lg overflow-hidden">
                                        <img :src="scope.data.image"/>
                                    </div>
                                    <div class="flex flex-col">
                                        <p class="paragraph-sm c-strong-950">{{ scope.data.album_name }}</p>
                                        <div class="flex items-center gap-1">
                                            <!-- <p class="paragraph-xs c-sub-600 w-max">{{ scope.data.type == 1 ? 'Ses Dosyası' : (scope.data.type == 2 ? 'Müzik Video' : (scope.data.type == 2 ? 'Zil Sesi'  : 'Apple Video')) }}</p> -->
                                            <span class="paragraph-xs c-sub-600">UPC: {{scope.data.upc_code}}</span>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </template>
                        <template #model="scope">
                            <div class="flex items-center relative gap-2">
                                <div class="flex -space-x-3 rtl:space-x-reverse">
                                    <template v-for="song in scope.data.slice(0,2)">
                                        <span class="flex items-center justify-center w-8 h-8  font-medium c-sub-600 label-sm bg-weak-50 border-2 border-white rounded-full" >
                                            {{ song.album_name[0] }}
                                        </span>
                                    </template>
                                    <span v-if="scope.data.length > 2" class="flex items-center justify-center w-8 h-8  font-medium c-sub-600 label-sm bg-weak-50 border-2 border-white rounded-full" >
                                        +{{scope.data.length-2}}
                                    </span>
                                </div>

                                <p class="label-sm !font-normal" style="white-space:nowrap;">
                                    <template v-for="(song,songIndex) in scope.data.slice(0,2)">

                                        {{ song.album_name }}
                                        <template v-if="songIndex < scope.data.length-1">
                                        , &nbsp;
                                        </template>

                                    </template>
                                </p>
                            </div>
                        </template>
                    </FormElement>
                </div>
                <div  class="flex flex-wrap items-center gap-2">
                    <div v-for="(choosenValue,i) in element.choosenValues" class="border border-soft-200 rounded px-2 py-1 flex items-center gap-1">
                       <img class="rounded-full" width="24" height="24" :src="productsSelectConfig.data.find((e) => e.id == choosenValue)?.iconKey">

                      <div class="whitespace-nowrap w-auto">
                        <p class="label-xs c-sub-600 !text-start"> {{productsSelectConfig.data.find((e) => e.id == choosenValue)?.album_name}}</p>
                        <span class="paragraph-xs c-sub-600">{{productsSelectConfig.data.find((e) => e.id == choosenValue)?.upc_code}}</span>

                      </div>
                        <button @click="element.choosenValues.splice(i,1)"><CloseIcon color="var(--sub-600)" /></button>
                    </div>
                </div>
            </div>
            <div  v-else-if="element.report_content_type == 4">
                <div class="w-64">
                    <FormElement v-model="element.choosenValues"  direction="vertical" label="Şarkı Seçimi" placeholder="Şarkı Seç" type="multiselect" :config="songsSelectConfig">
                         <template #option="scope">
                            <div class="w-full flex justify-between gap-2 flex-1">
                                <div class="flex items-center flex-1 gap-2 flex-1">

                                    <RingtoneIcon color="var(--sub-600)" />

                                    <div class="flex-1 flex flex-col">
                                        <p class="paragraph-sm c-strong-950 flex-1">{{ scope.data.name.substring(0,19)  }} {{scope.data.name.length > 19 ? '...' : ''}}</p>
                                        <span class="paragraph-xs c-neutral-500">{{scope.data.isrc}}</span>
                                    </div>
                                </div>

                                <template v-if="scope.data.platforms">
                                    <div class="flex" v-for="platform in scope.data.platforms">
                                        <a v-if="platform.code == 'spotify' || platform.code == 'apple'"
                                        :href="platform.pivot.url"
                                        target="_blank">
                                        <Icon :icon="platform.icon"/>
                                        </a>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template #model="scope">
                            <div class="flex items-center relative gap-2">
                                <div class="flex -space-x-3 rtl:space-x-reverse">
                                    <template v-for="song in scope.data.slice(0,2)">
                                        <span class="flex items-center justify-center w-8 h-8  font-medium c-sub-600 label-sm bg-weak-50 border-2 border-white rounded-full" >
                                            {{ song.name[0] }}
                                        </span>
                                    </template>
                                    <span v-if="scope.data.length > 2" class="flex items-center justify-center w-8 h-8  font-medium c-sub-600 label-sm bg-weak-50 border-2 border-white rounded-full" >
                                        +{{scope.data.length-2}}
                                    </span>
                                </div>

                                <p class="label-sm !font-normal" style="white-space:nowrap;">
                                    <template v-for="(song,songIndex) in scope.data.slice(0,2)">

                                        {{ song.name }}
                                        <template v-if="songIndex < scope.data.length-1">
                                        , &nbsp;
                                        </template>

                                    </template>
                                </p>
                            </div>
                        </template>
                    </FormElement>
                </div>
                <div  class="flex flex-wrap items-center gap-2">
                    <div v-for="(choosenValue,i) in element.choosenValues" class="border border-soft-200 rounded px-2 py-1 flex items-center gap-1">
                        <div class="whitespace-nowrap w-auto flex  items-start gap-2">
                            <RingtoneIcon class="mt-0.5" color="var(--sub-600)" />
                           <div>
                                <p class="label-xs c-sub-600 !text-start">  {{songsSelectConfig.data.find((e) => e.id == choosenValue)?.name}}</p>
                                <span class="paragraph-xs c-neutral-500">{{songsSelectConfig.data.find((e) => e.id == choosenValue)?.isrc}}</span>

                           </div>
                        </div>
                        <button @click="element.choosenValues.splice(i,1)"><CloseIcon color="var(--sub-600)" /></button>
                    </div>
                </div>
            </div>
            <div  v-else-if="element.report_content_type == 5">

                 <div class="w-64">
                    <FormElement v-model="element.choosenValues"  direction="vertical" label="Platform Seçimi" placeholder="Platform Seç" type="multiselect" :config="platformsSelectConfig">
                        <template #option="scope">
                            <div class="w-full flex justify-between gap-2">
                                <div class="flex flex-1 gap-2">

                                        <Icon :icon="scope.data.icon"/>

                                    <div class="flex flex-col">
                                        <p class="paragraph-sm c-strong-950">{{ scope.data.name }}</p>


                                    </div>
                                </div>


                            </div>
                        </template>
                        <template #model="scope">

                           <div class="flex items-center relative gap-2">
                                <div class="flex -space-x-3 rtl:space-x-reverse">
                                    <template v-for="platform in scope.data.slice(0,2)">
                                        <span class="flex items-center justify-center w-8 h-8  font-medium c-sub-600 label-sm bg-weak-50 border-2 border-white rounded-full" >
                                            <Icon :icon="platform.icon" />
                                        </span>
                                    </template>
                                    <span v-if="scope.data.length > 2" class="flex items-center justify-center w-8 h-8  font-medium c-sub-600 label-sm bg-weak-50 border-2 border-white rounded-full" >
                                        +{{scope.data.length-2}}
                                    </span>

                                </div>
                                <p class="label-sm !font-normal" style="white-space:nowrap;">
                                        <template v-for="(platform,index) in scope.data.slice(0,2)">
                                            {{ platform.name }}
                                            <template v-if="index < scope.data.slice(0,2).length-1">
                                                , &nbsp;
                                            </template>

                                        </template>
                                    </p>
                            </div>

                        </template>
                    </FormElement>
                   <div  class="flex items-center gap-2">
                        <div v-for="(choosenValue,i) in element.choosenValues" class="border border-soft-200 rounded px-2 py-1 flex items-center gap-1">
                            <Icon :icon="platformsSelectConfig.data.find((e) => e.id == choosenValue)?.icon" />
                            <div class="whitespace-nowrap w-auto ">  <p class="label-xs c-sub-600 !text-start">    {{platformsSelectConfig.data.find((e) => e.id == choosenValue)?.name}}</p></div>
                            <button @click="element.choosenValues.splice(i,1)"><CloseIcon color="var(--sub-600)" /></button>
                        </div>
                    </div>
                </div>
            </div>
            <div  v-else-if="element.report_content_type == 6">

                <div class="flex flex-col" >

                    <div class="mb-2 flex items-center ">
                        <p class="label-sm c-strong-950 flex-1">Yayınlanacak Ülkeler</p>
                        <div>
                            <button @click="chooseAllCountries" class="label-sm c-blue-500 hover:underline">Tümünü Seç</button>
                            <button v-if="element.choosenValues.length > 0 " @click="removeChoosingCountries" class="label-sm c-error-500 hover:underline ms-2">Seçimleri Kaldır</button>
                        </div>
                    </div>
                    <div class="flex flex-col w-full gap-3">

                        <div class="w-full" v-for="(value,key) in usePage().props.countriesGroupedByRegion.data">
                            <AppAccordion :title="key" :description="usePage().props.countriesGroupedByRegion.data[key].filter((e) => element.choosenValues.includes(e.value)).length+' Ülke Seçildi'">

                            <div class="flex items-center ">
                                <div class="flex-1">
                                    <p class="label-medium c-strong-950 !text-start">Ülkeler</p>

                                </div>

                                <div class="flex items-center gap-2">
                                    <button @click.stop="chooseAll(key)" class="c-blue-500 label-xs hover:underline">Tümünü Seç</button>
                                    <button @click.stop="unChooseAll(key)" class="c-blue-500 label-xs hover:underline">Seçimi Kaldır
                                </button>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="grid grid-cols-3 gap-2 ">
                                <div v-for="country in value">
                                <label @click.stop class="flex items-center gap-2">
                                    <input type="checkbox" @click="onCountryCheck(country)"
                                        :checked="element.choosenValues.find((el) => el == country.value)"
                                        class="focus:ring-0 rounded appCheckbox border border-soft-200"/>

                                    {{ country.iconKey }}
                                    <span class="paragraph-xs c-strong-950">{{ country.label }}</span>
                                </label>
                                </div>
                            </div>
                            </AppAccordion>
                        </div>
                    </div>
                </div>
            </div>
             <div  v-else-if="element.report_content_type == 12">
                <div class="w-64">
                    <FormElement  v-model="element.choosenValues" direction="vertical" label="Label Seçimi" placeholder="Label Seç" type="multiselect" :config="labelSelectConfig">
                      <template #model="scope">

                           <div class="flex items-center relative gap-2">
                                <div class="flex -space-x-3 rtl:space-x-reverse">
                                    <template v-for="label in scope.data.slice(0,2)">
                                        <span class="flex items-center justify-center w-8 h-8  font-medium c-sub-600 label-sm bg-weak-50 border-2 border-white rounded-full" >
                                            {{label.name[0]}}
                                        </span>
                                    </template>
                                    <span v-if="scope.data.length > 2" class="flex items-center justify-center w-8 h-8  font-medium c-sub-600 label-sm bg-weak-50 border-2 border-white rounded-full" >
                                        +{{scope.data.length-2}}
                                    </span>

                                </div>
                                <p class="label-sm !font-normal" style="white-space:nowrap;">
                                        <template v-for="(label,index) in scope.data.slice(0,2)">
                                            {{ label.name }}
                                            <template v-if="index < scope.data.slice(0,2).length-1">
                                                , &nbsp;
                                            </template>

                                        </template>
                                    </p>
                            </div>

                        </template>
                    </FormElement>
                </div>
                <div  class="flex flex-wrap items-center gap-2">
                        <div v-for="(choosenValue,i) in element.choosenValues" class="border border-soft-200 rounded px-2 py-1 flex items-center gap-1">
                            <Icon :icon="labelSelectConfig.data.find((e) => e.id == choosenValue)?.icon" />
                            <div class="whitespace-nowrap w-auto ">  <p class="label-xs c-sub-600 !text-start">    {{labelSelectConfig.data.find((e) => e.id == choosenValue)?.name}}</p></div>
                            <button @click="element.choosenValues.splice(i,1)"><CloseIcon color="var(--sub-600)" /></button>
                        </div>
                    </div>
            </div>

        </div>
</template>

<style scoped>

</style>
