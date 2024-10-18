<template>
    <BaseDialog v-model="isDialogOn" align="right" title="Plak şirketi" description="Temel bilgileri girerek sanatçı oluşturabilirsiniz. ">
        <template #icon>
            <AddIcon color="var(--dark-green-950)" />
        </template>
        <SectionHeader title="PLAK ŞİRKETİ HAKKINDA" />

       <div class="p-5 flex flex-col gap-6">
            <FormElement label-width="190px"  :error="form.errors.image" v-model="form.image" label="Logo" type="upload" :config="{label:'Logo Yükle',note:'Min 400x400px, PNG or JPEG'}"></FormElement>
            <FormElement label-width="190px"  :error="form.errors.name" v-model="form.name" label="Plak Şirketi Adı" placeholder="Lütfen giriniz"></FormElement>
            <FormElement label-width="190px"  :error="form.errors.address" v-model="form.address" :config="{letter:500}"   label="Adres" type="textarea" placeholder="Firma adresi" ></FormElement>
            <FormElement label-width="190px"  :error="form.errors.country_id" v-model="form.country_id" label="Ülke" type="select" :config="countryConfig" placeholder="Seçiniz"></FormElement>
       </div>
        <SectionHeader title="İLETİŞİM BİLGİLERİ" />
        <div class="p-5 flex flex-col gap-6">
            <FormElement label-width="190px"  :error="form.errors.phone" v-model="form.phone" label="Telefon Numarası" type="phone"  placeholder="(555) 000-0000" ></FormElement>
            <FormElement label-width="190px"  :error="form.errors.email" v-model="form.email" label="E-mail" type="text"  placeholder="examp@example.com" ></FormElement>
            <FormElement label-width="190px"  :error="form.errors.web" v-model="form.web" label="Websitesi" placeholder="www.example.com" type="web"> </FormElement>
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
import {FormElement} from '@/Components/Form'
import {useForm,usePage} from '@inertiajs/vue3';

const props = defineProps({
    modelValue: {
        default:false,
    },
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
    form.post(route('control.labels.store'), {
        onFinish: () => {
            adding.value = false;
        },
        onSuccess: async (e) => {
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
