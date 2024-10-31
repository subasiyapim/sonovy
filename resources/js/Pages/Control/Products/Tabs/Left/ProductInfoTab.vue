<template>

    <div class="pb-6">
        <p class="label-xl c-strong-950">
            İlk adım, yayın bilgilerinizi dolduralım.✍🏻
        </p>
    </div>
    <div class="flex gap-10">
        <div class="flex-1 flex flex-col overflow-scroll gap-6">
            <FormElement label-width="190px" v-model="form.album_name"  label="Albüm Adı" ></FormElement>
            <FormElement label-width="190px" v-model="form.version"  label="Sürüm" placeholder="Lütfen giriniz"></FormElement>
            <FormElement label-width="190px" v-model="form.main_artists" type="multiselect" label="Sanatçı" placeholder="Sanatçı Seçiniz" :config="artistSelectConfig">
                <template #first_child>
                    <button class="flex items-center gap-2 paragraph-sm c-sub-600 p-2"> <AddIcon color="var(--sub-600)" /> Sanatçı Oluştur</button>
                </template>
                <template #empty>
                    <button class="flex items-center gap-2 label-xs c-dark-green-600 p-2"> <AddIcon color="var(--dark-green-600)" /> Sanatçı Oluştur</button>
                </template>
                    <template #description>
                    <label class="flex items-center gap-2 mt-2 px-1" for="isProductDerleme">
                    <input type="checkbox" id="isProductDerleme" class="focus:ring-0 rounded"></input>
                    <span class="paragraph-xs c-strong-950">Derleme Albüm</span>
                    <span class="paragraph-xs c-sub-600">(birden fazla sanatçı varsa seçin)</span>
                    </label>
                </template>
                <template #option="scope">
                <div class="flex items-center  gap-2">
                        <div class="w-3 h-3 rounded-full overflow-hidden">
                            <img :src="scope.data.image" />
                        </div>
                        <p>{{scope.data.label}}</p>
                </div>
                </template>
                <template #model="scope">
                <div class="flex items-center relative gap-2">
                        <div  class="flex items-center relative" :style="{'width' : scope.data.length * 20+'px'}">
                            <div v-for="(artist,index) in scope.data" :style="{'left': 14*index+'px'}" class="absolute w-5 h-5 rounded-full border border-white flex items-center justify-center bg-blue-300">
                            <span class="label-xs"> {{artist.label[0]}}</span>
                            </div>
                        </div>
                    <p style="white-space:nowrap;">
                            <template v-for="artist in scope.data">
                                {{artist.label}}, &nbsp;
                            </template>
                    </p>

                </div>
                </template>
            </FormElement>
            <FormElement label-width="190px" v-model="form.featuring_artists" type="multiselect" label="Düet Sanatçı" placeholder="Seçiniz"  :config="artistSelectConfig">
                <template #first_child>
                    <button class="flex items-center gap-2 paragraph-sm c-sub-600 p-2"> <AddIcon color="var(--sub-600)" /> Sanatçı Oluştur</button>
                </template>
                <template #empty>
                    <button class="flex items-center gap-2 label-xs c-dark-green-600 p-2"> <AddIcon color="var(--dark-green-600)" /> Sanatçı Oluştur</button>
                </template>
                <template #option="scope">
                <div class="flex items-center  gap-2">
                        <div class="w-3 h-3 rounded-full overflow-hidden">
                            <img :src="scope.data.image" />
                        </div>
                        <p>{{scope.data.label}}</p>
                </div>
                </template>
                <template #model="scope">
                <div class="flex items-center relative gap-2">
                        <div  class="flex items-center relative" :style="{'width' : scope.data.length * 20+'px'}">
                            <div v-for="(artist,index) in scope.data" :style="{'left': 14*index+'px'}" class="absolute w-5 h-5 rounded-full border border-white flex items-center justify-center bg-blue-300">
                            <span class="label-xs"> {{artist.label[0]}}</span>
                            </div>
                        </div>
                    <p style="white-space:nowrap;">
                            <template v-for="artist in scope.data">
                                {{artist.label}}, &nbsp;
                            </template>
                    </p>

                </div>
                </template>
            </FormElement>
            <FormElement label-width="190px" v-model="form.genre_id" label="Tarz" placeholder="Seçiniz"  type="select" :config="genreConfig">

            </FormElement>
            <FormElement label-width="190px" v-model="form.sub_genre_id" label="Alt Tarz" placeholder="Seçiniz"  type="select" :config="genreConfig">
            </FormElement>

            <FormElement label-width="190px" v-model="form.format" label="Biçim" placeholder="Seçiniz"  type="select" :config="formatConfig">

            </FormElement>

        </div>

        <div class="flex-1 flex flex-col overflow-scroll gap-6">
            <FormElement type="select" v-model="form.label_id" label-width="190px"  label="Plak şirketi" placeholder="Şirket Seçiniz" :config="labelConfig"></FormElement>
            <FormElement label-width="190px" v-model="form.p_line" label="p Satırı" placeholder="Lütfen giriniz">
                <template #tooltip>
                        Besteler, şarkı sözleri ve diğer müzikal öğeler, eser sahiplerine eserlerini kullanma, kopyalama, dağıtma, düzenleme ve ticari olarak değerlendirme yetkisi verir.
                </template>
            </FormElement>
            <FormElement label-width="190px" v-model="form.c_line" label="C Satırı" placeholder="Lütfen giriniz"></FormElement>
            <FormElement label-width="190px" v-model="form.upc_code" label="UPC/EAN Kodu" placeholder="Lütfen giriniz"></FormElement>
            <FormElement label-width="190px" v-model="form.catalog_number" label="Katalog No" placeholder="Lütfen giriniz"></FormElement>
            <FormElement type="select" label-width="190px"  v-model="form.language_id" label="Albüm Dili" :config="languageConfig" placeholder="Lütfen giriniz">
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

            <FormElement type="select" label-width="190px" v-model="form.main_price" label="Ana Fiyat" placeholder="Lütfen giriniz" :config="mainPriceConfig">

            </FormElement>
        </div>
    </div>
</template>

<script setup>
import {computed} from 'vue';
import {FormElement} from '@/Components/Form';
import {AddIcon} from '@/Components/Icons'
import {useCrudStore} from '@/Stores/useCrudStore';
import {usePage} from '@inertiajs/vue3';


const crudStore = useCrudStore();
const props = defineProps({
    modelValue:{},
    genres:{},
    languages:{},
    formats:{}
})

const emits = defineEmits(['update:modelValue']);

const form = computed({
    get:() => props.modelValue,
    set:(value) => emits('update:modelValue',value)
})
const selectConfig = computed(() => {
    return {
        hasSearch:true,
        data: [
            {value:1,label:"MERHABA"},
            {value:2,label:"MERHABA"},
        ]
    }
})
const artistSelectConfig = computed(() => {
    return {
        showTags:false,
        hasSearch:true,
        data: [],
        remote:async (query) => {

            const  response = await crudStore.get(route('control.search.artists',{
                search:query
            }))
            const formattedData = response.map(item => ({
                value: item.id,
                label: item.name,
                image: item.image ? item.image.thumb || item.image.url : null  // Use `thumb` if available, fallback to `url`
            }));


            return formattedData;

        }
    }
})

const labelConfig = computed(() => {
    return {
        showTags:false,
        hasSearch:true,
        data: [],
        remote:async (query) => {

            const  response = await crudStore.get(route('control.search.labels',{
                search:query
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
        hasSearch:true,
        data: props.genres,
    }
})
const mainPriceConfig = computed(() => {
    return {
        hasSearch:false,
        data: [],
    }
})
console.log(props.languages);

const languageConfig = computed(() => {
    return {
        hasSearch:true,
        data: props.languages
    }
})
const formatConfig = computed(() => {
    return {
        data: props.formats
    }
})




</script>

<style lang="scss" scoped>

</style>
