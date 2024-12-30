<script setup>
import {ref,computed} from 'vue';
import {FormElement} from '@/Components/Form'
import {Icon} from '@/Components/Icons'
import {useCrudStore} from '@/Stores/useCrudStore';
import {useDefaultStore} from '@/Stores/default';
import {StatusBadge} from '@/Components/Badges';
const date = ref();

const crudStore = useCrudStore();
const defaultStore = useDefaultStore();
const props = defineProps({
    modelValue:{}
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
        showTags: true,
        hasSearch: true,
        data: [],
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
});
const productsSelectConfig = computed(() => {
    return {
        showTags: true,
        hasSearch: true,
        data: [],
        remote: async (query) => {

        const response = await crudStore.get(route('control.search.products', {
            search: query
        }))
        const formattedData = response.map(item => ({
            value: item.id,
            label: item.album_name,
        }));

        return formattedData;

        }
    }
});
const songsSelectConfig = computed(() => {
    return {
        showTags: true,
        hasSearch: true,
        data: [],
        remote: async (query) => {

        const response = await crudStore.get(route('control.search.songs', {
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
const platformsSelectConfig = computed(() => {
  return {
        showTags: true,
        hasSearch: true,
        data: [],
        remote: async (query) => {

        const response = await crudStore.get(route('control.search.platforms', {
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
const countriesSelectConfig = computed(() => {
  return {
        showTags: true,
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
    console.log("EEE",e);

   element.value.choosenValues = e;

};
</script>

<template>

      <div class="flex flex-col items-start">
            <p class="label-medium c-strong-950">Rapor içeriği</p>
            <div class="flex items-center gap-2">
                <p class="paragraph-xs c-sub-600">Lorem ipsum dolor sit amet consectetur. Pellentesque id sed turpis sed condimentum </p>

            </div>
        </div>
    <!-- {{mainArtistSelectConfig}} -->
        <div v-if="element.type == 1" class="flex items-center gap-3 flex-1 my-6">
            <button class="flex items-center gap-2 label-sm c-sub-600" @click="changeContentTab(1)" ><div class="w-4 h-4 rounded-full flex items-center justify-center drop-shadow" :class="element.report_content_type == 1 ? 'bg-dark-green-500' :'bg-white'"><div class="w-2.5 h-2.5 rounded-full bg-white"></div> </div>Tam Rapor</button>
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
        <div class="w-64">
            <FormElement v-if="element.report_content_type == 2" v-model="choosenIds" @change="onChangeValue" direction="vertical" label="Sanatçı Seçimi" placeholder="Sanatçı Seç" type="multiselect" :config="mainArtistSelectConfig">
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
            <FormElement v-else-if="element.report_content_type == 3" v-model="choosenIds"  @change="onChangeValue" direction="vertical" label="Albüm Seçimi" placeholder="Albüm Seç" type="multiselect" :config="productsSelectConfig">
            </FormElement>
            <FormElement v-else-if="element.report_content_type == 4" v-model="choosenIds"  @change="onChangeValue" direction="vertical" label="Şarkı Seçimi" placeholder="Şarkı Seç" type="multiselect" :config="songsSelectConfig">
            </FormElement>
            <FormElement v-else-if="element.report_content_type == 5" v-model="choosenIds"  @change="onChangeValue" direction="vertical" label="Platform Seçimi" placeholder="Platform Seç" type="multiselect" :config="platformsSelectConfig">
            </FormElement>
            <FormElement v-else-if="element.report_content_type == 6" v-model="choosenIds" @change="onChangeValue" direction="vertical" label="Ülke Seçimi" placeholder="Ülke Seç" type="multiselect" :config="countriesSelectConfig">
            </FormElement>
        </div>
</template>

<style scoped>

</style>
