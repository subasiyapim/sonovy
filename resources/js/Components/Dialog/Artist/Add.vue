<template>
    <BaseDialog v-model="isDialogOn" align="right" title="Sanatçı Ekle" description="Temel bilgileri girerek sanatçı oluşturabilirsiniz. ">
        <template #icon>
            <AddIcon color="var(--dark-green-950)" />
        </template>
        <SectionHeader title="SANATÇI HAKKINDA" />
       <div class="p-5 flex flex-col gap-6">
            <FormElement label-width="190px" label="Fotoğraf" type="upload" :config="{label:'Fotoğraf Yükle',note:'Min 400x400px, PNG or JPEG'}"></FormElement>
            <FormElement label-width="190px" label="Ad Soyad" placeholder="Lütfen giriniz"></FormElement>
            <FormElement label-width="190px" label="Sanatçı Hakkında" type="textarea" placeholder="Sanatçı Hakkında" ></FormElement>
            <FormElement label-width="190px" label="Sanat Dalları" placeholder="Lütfen giriniz"></FormElement>
            <FormElement label-width="190px" label="Ülke"  placeholder="Seçiniz"></FormElement>
            <FormElement label-width="190px" label="IPI"  placeholder="Lütfen giriniz"></FormElement>
            <FormElement label-width="190px" label="ISNI" placeholder="Lütfen giriniz"> </FormElement>
       </div>
        <SectionHeader title="İLETİŞİM BİLGİLERİ" />
        <div class="p-5 flex flex-col gap-6">
            <FormElement label-width="190px" label="Telefon Numarası" type="phone"  placeholder="(555) 000-0000" ></FormElement>
            <FormElement label-width="190px" label="Websitesi" placeholder="www.example.com" type="web"> </FormElement>
       </div>
        <SectionHeader title="PLATFORMLAR" />
        <div class="p-5 flex flex-col">
            <div v-for="platform in platforms" class="flex gap-4">
                <FormElement class="flex-1" direction="vertical" label-width="190px" label="Platform"  placeholder="Platform Seç" ></FormElement>
                <FormElement class="flex-1" direction="vertical" label-width="190px" label="Platform Link" placeholder="lütfen giriniz"> </FormElement>
            </div>
            <button class="flex items-center gap-2">
                <AddIcon color="var(--blue-500)" />
                <p class="label-xs c-blue-500">Platform Ekle</p>
            </button>
       </div>
       <div class="flex p-5 border-t border-soft-200 gap-4">
        <RegularButton  class="flex-1">İptal</RegularButton>
        <PrimaryButton :disabled="checkIfDisabled"  class="flex-1">
            <template #icon><AddIcon /></template>
            Kaydet</PrimaryButton>
       </div>
    </BaseDialog>
</template>

<script setup>
import BaseDialog from '../BaseDialog.vue';
import SectionHeader from '../Components/SectionHeader.vue';
import {AddIcon} from '@/Components/Icons'
import {RegularButton,PrimaryButton} from '@/Components/Buttons'
import {computed,ref} from 'vue';
import {FormElement} from '@/Components/Form'
const props = defineProps({
    modelValue: {
        default:false,
    }
})

const emits = defineEmits(['update:modelValue']);
const isDialogOn = computed({
    get:() => props.modelValue,
    set:(value) => emits('update:modelValue',value)
})
const platforms = ref([{}]);
const checkIfDisabled = computed(() => {
    return false;
})
</script>

<style lang="scss" scoped>

</style>
