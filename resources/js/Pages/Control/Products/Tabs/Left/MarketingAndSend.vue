<template>

  <div class="pb-6">
            <p class="label-xl c-strong-950">
              Harika, yayınlama ve dağıtım bilgilerini dolduralım. 🌍
            </p>
        </div>

<div class="flex flex-col gap-6">
    <div  v-for="(info,index) in form.promotions">
        <SectionHeader title="YAYIN VE TANITIM METNİ" class="mb-2"></SectionHeader>

        <div class="flex flex-col gap-6 px-1">
            <FormElement label-width="190px" :error="form.errors[`promotions.${index}.language_id`]" type="select" label="Tanıtım Dil" v-model="info.language_id" :required="true" placeholder="Lütfen Seçiniz" :config="selectConfig">
                    <template #option="scope">
                        <span>{{ scope.data.iconKey }}</span>
                        <span class="paragraph-sm c-strong-950">
                                        {{ scope.data.label }}
                                    </span>
                    </template>
                    <template #model="scope">

                        <div v-if="scope.data" class="flex items-center gap-2">

                            <span>{{ selectConfig.data.find((el) => el.value == scope.data)?.iconKey }}</span>
                            <span>{{ selectConfig.data.find((el) => el.value == scope.data)?.label }}</span>
                        </div>
                    </template>
            </FormElement>
            <FormElement label-width="190px"  :error="form.errors[`promotions.${index}.title`]" type="text" label="Vurucu Cümle" v-model="info.title" placeholder="Yapım Yılı" >
            </FormElement>
            <FormElement label-width="190px"  :error="form.errors[`promotions.${index}.description`]" type="textarea" :required="true" v-model="info.description" label="Yayın Tanıtım Metni" placeholder="Metni giriniz">
            </FormElement>
        </div>
        <div class="flex items-center justify-end mt-2">
            <button @click="form.promotions.splice(index,1)" class="flex items-center gap-2">
                <TrashIcon color="var(--error-500)" />
                <span class="label-sm c-error-500">Tanıtım Yazısını Sil</span>
            </button>
        </div>
    </div>
    <div>
        <button @click="form.promotions.push({})" class="flex items-center justify-center gap-2 w-auto">
            <AddIcon color="var(--blue-500)" />
            <p class="label-xs c-blue-500">Farklı Dilde Tanıtım Metni Ekle</p>
        </button>
    </div>

</div>
</template>

<script setup>
import {SectionHeader,AppAccordion} from '@/Components/Widgets';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {computed,ref} from 'vue';
import {FormElement,AppTextInput} from '@/Components/Form';
import {AddIcon,Icon,TrashIcon} from '@/Components/Icons'
import { usePage} from '@inertiajs/vue3';
const props = defineProps({
    product:{},
    modelValue:{},
})

const emits = defineEmits(['update:modelValue']);

const form = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})
const countryRadioValue = ref(2)
const selectConfig = computed(() => {
    return {
        hasSearch:true,
        data: usePage().props.languages,
    }
})
const publicationTexts = ref([{}]);

</script>

<style lang="scss" scoped>

</style>
