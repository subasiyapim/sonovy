<template>
    <BaseDialog v-model="isDialogOn" align="right" title="Sanatçı Ekle" description="Temel bilgileri girerek sanatçı oluşturabilirsiniz. ">
        <template #icon>
            <AddIcon color="var(--dark-green-950)" />
        </template>
        <SectionHeader title="SANATÇI HAKKINDA" />

       <div class="p-5 flex flex-col gap-6">
            <FormElement label-width="190px" :error="form.errors.image" v-model="image" label="Fotoğraf" type="upload" :config="{label:'Fotoğraf Yükle',note:'Min 400x400px, PNG or JPEG'}"></FormElement>
            <FormElement label-width="190px" :error="form.errors.name" v-model="form.name" label="Ad Soyad" placeholder="Lütfen giriniz"></FormElement>
            <FormElement label-width="190px" :error="form.errors.about" :config="{letter:500}" v-model="form.about" label="Sanatçı Hakkında" type="textarea" placeholder="Sanatçı Hakkında" ></FormElement>
            <FormElement label-width="190px" :error="form.errors.artist_branches" v-model="form.artist_branches" :config="artistBranchesMultiSelect" label="Sanat Dalları" type="multiselect" placeholder="Lütfen giriniz"></FormElement>
            <FormElement label-width="190px" :error="form.errors.country_id" v-model="form.country_id" label="Ülke" :config="countryConfig"  placeholder="Seçiniz" type="select"></FormElement>
            <FormElement label-width="190px" :error="form.errors.ipi_code" v-model="form.ipi_code" label="IPI"  placeholder="Lütfen giriniz"></FormElement>
            <FormElement label-width="190px" :error="form.errors.isni_code" v-model="form.isni_code" label="ISNI" placeholder="Lütfen giriniz"> </FormElement>
       </div>
        <SectionHeader title="İLETİŞİM BİLGİLERİ" />
        <div class="p-5 flex flex-col gap-6">
            <FormElement label-width="190px" v-model="form.phone" label="Telefon Numarası" type="phone"  placeholder="(555) 000-0000" ></FormElement>
            <FormElement label-width="190px" v-model="form.web" label="Websitesi" placeholder="www.example.com" type="web"> </FormElement>
       </div>
        <SectionHeader title="PLATFORMLAR" />
        <div class="p-5 flex flex-col">
            <div v-for="platform in form.platforms" class="flex gap-4">
                <FormElement class="flex-1" direction="vertical" v-model="platform.id" label-width="190px" label="Platform"  placeholder="Platform Seç" ></FormElement>
                <FormElement class="flex-1" direction="vertical" v-model="platform.id" label-width="190px" label="Platform Link" placeholder="lütfen giriniz"> </FormElement>
            </div>
            <button @click="form.platforms.push({})" class="flex items-center gap-2">
                <AddIcon  color="var(--blue-500)" />
                <p class="label-xs c-blue-500">Platform Ekle</p>
            </button>
       </div>
       <div class="flex p-5 border-t border-soft-200 gap-4">
        <RegularButton @click="isDialogOn = false"  class="flex-1">İptal</RegularButton>
        <PrimaryButton @click="onSubmit" :disabled="checkIfDisabled"  class="flex-1">
            <template #icon><AddIcon /></template>
            Kaydet</PrimaryButton>
       </div>
    </BaseDialog>
</template>

<script setup>
import BaseDialog from '../BaseDialog.vue';
import {SectionHeader} from '@/Components/Widgets';
import {AddIcon} from '@/Components/Icons'
import {RegularButton,PrimaryButton} from '@/Components/Buttons'
import {computed,ref} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';

import {FormElement} from '@/Components/Form'

const props = defineProps({
    modelValue: {
        default:false,
    }
})
const adding = ref(false)
const image = ref();
const form = useForm({
    name: '',
    country_id: "",
    about:"",
    artist_branches: [],
    image: "",
    ipi_code: "",
    isni_code: "",
    platforms: [
        {}
    ],
});
const emits = defineEmits(['update:modelValue']);
const isDialogOn = computed({
    get:() => props.modelValue,
    set:(value) => emits('update:modelValue',value)
})
const platforms = ref([{}]);
const checkIfDisabled = computed(() => {
    return false;
})
const artistBranchesMultiSelect = computed(() => {
    return {
        data:[
            {value:1,label:"Pop"},
            {value:2,label:"Rock"},
        ]
    };
})

const countryConfig = computed(() => {
    return {
        data: usePage().props.countries,
    };
})
const onSubmit = (e) => {
    adding.value = true;
    if(image.value){
        form.image = image.value?.file;
    }
    form.post(route('control.artists.store'), {
        onFinish: () => {
            adding.value = false;
        },
        onSuccess: async (e) => {

            // const artistResponse = await queryStore.last(route('dashboard.last.artists'));
            // artistResponse.url  = artistResponse.pivot?.url;
            // console.log("ARTİST RESPONSE",artistResponse);
            // emits('onArtistAdded', artistResponse)
            console.log("BARLAIRLII",e);
            isDialogOn.value = false;
        },
        onError: (e) => {
            console.log("HATAAAA",e);
        },
    });

}
</script>

<style lang="scss" scoped>

</style>
