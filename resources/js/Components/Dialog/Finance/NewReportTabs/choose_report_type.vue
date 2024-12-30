<script setup>
import {ref,computed} from 'vue';
const date = ref();
import {MultipleDocumentIcon,DocumentIcon} from '@/Components/Icons'
import {AppFancyRadio} from '@/Components/Form'

const props = defineProps({
    modelValue:{}
})

const emits = defineEmits(['update:modelValue']);

const element = computed({
    get:() => props.modelValue,
    set:(value) => emits('update:modelValue',value)
})
const chooseProductTypeConfig = ref({
    options:[
        {
            title:"Tek Rapor",
            description:'Kataloğunuzun tamamını içeren veya label, sanatçı, parça, albüm, platform veya ülke bakımından filtrelenmiş tek bir rapor oluştur',
            value:1,
            icon:DocumentIcon,
        },
        {
            title:"Çoklu Rapor",
            description:'Kolayca label, sanatçı, parça, platform veya ülke bakımından ayrılmış tek bir konuda rapor oluşturun.',
            value:2,
            icon:MultipleDocumentIcon,
        },

    ]
});
</script>

<template>
      <div class="flex flex-col items-start">
            <p class="label-medium c-strong-950">Rapor Tipi Seçimi</p>
            <div class="flex items-center gap-2">
                <p class="paragraph-xs c-sub-600">Seçim</p>
                <p class="label-sm c-strong-950">
                    {{element?.type == 1 ? 'Tek Rapor': 'Çoklu Seçim'}}
                </p>
            </div>
        </div>
        <div class="flex">
        <AppFancyRadio v-model="element.type" :config="chooseProductTypeConfig"></AppFancyRadio>


        </div>
</template>

<style scoped>

</style>
