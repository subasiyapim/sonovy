<template>
  <BaseDialog height="min-content" v-model="isDialogOn" align="center" :title="'Paketler'"
              :description="'Seçili kullanıcıya ait paket değişikliği yapabilirsiniz'">
    <template #icon>
      <PackagesIcon color="var(--dark-green-950)"/>
    </template>

    <div class="p-5 flex  justify-center " >
        <div class="bg-weak-50 flex items-center p-1 rounded-lg w-max gap-2">
            <button @click="choosenPeriod = 'monthly'" class="label-sm rounded-lg w-24 h-7 flex items-center justify-center" :class="choosenPeriod == 'monthly' ? 'c-strong-950 bg-white shadow' : 'c-soft-400 bg-transparent'">Aylık</button>
            <button @click="choosenPeriod = 'annual'" class="label-sm rounded-lg w-24 h-7 flex items-center justify-center" :class="choosenPeriod == 'annual' ? 'c-strong-950 bg-white shadow' : 'c-soft-400 bg-transparent' ">Yıllık %10</button>
        </div>
    </div>
    <div class="px-4 grid grid-cols-2 gap-6">
        <AppCard v-for="item in planItems" :class="item.selected ? 'border-dark-green-500 bg-dark-green-50' : 'bg-white'" @click="onItemClicked(item)" class=" cursor-pointer">
            <template #header>
               <div class="flex flex-col">
                    <span class="subheading-regular c-strong-950">{{item.title}} </span>
                    <span class="label-sm c-sub-600">Plan Adı </span>
               </div>
            </template>
            <template #tool>
                <div class="w-4 h-4 rounded-full bg-white flex items-center justify-center" :class="item.selected ? 'bg-dark-green-500' : ''" style="box-shadow: 0px 2.4px 2.4px 0px #1B1C1D1F;">
                     <div class="w-2.5 h-2.5 rounded-full bg-white">

                    </div>
                </div>

            </template>
            <template #body>
                <hr class="my-2">
                <p class="paragraph-sm c-soft-400 mb-2">Mevcut paket fiyatı</p>
                <div class="flex items-end label-lg">
                <span class="c-strong-950">$30.</span><span class="c-soft-400">99/</span> <span
                    class="c-soft-400">aylık</span>
                </div>
            </template>
        </AppCard>


    </div>

    <div class="p-5">
        <AppCard @click="onInfinitySelected" :class="infinitySelected ? 'border-dark-green-500 bg-dark-green-50' : 'bg-white'" class=" cursor-pointer">
            <template #header>
               <div class="flex flex-col">
                    <span class="label-sm">Sınırsız Paket </span>
                    <span class="paragraph-xs c-sub-600">lorem ipsum </span>
               </div>
            </template>
            <template #tool>
                <div class="w-4 h-4 rounded-full bg-white flex items-center justify-center" :class="infinitySelected ? 'bg-dark-green-500' : ''" style="box-shadow: 0px 2.4px 2.4px 0px #1B1C1D1F;">
                     <div class="w-2.5 h-2.5 rounded-full bg-white">

                    </div>
                </div>

            </template>

        </AppCard>
    </div>


    <div class="flex p-5 border-t border-soft-200 gap-4 sticky bottom-0 bg-white mt-6">
        <RegularButton @click="isDialogOn = false" class="flex-1">
                İptal
        </RegularButton>
        <PrimaryButton :loading="submitting" @click="submit" class="flex-1">
            Seçili Paketi Tanımla
        </PrimaryButton>
    </div>
  </BaseDialog>
</template>

<script setup>
import BaseDialog from '../BaseDialog.vue';
import {SectionHeader} from '@/Components/Widgets';
import {AppCard} from '@/Components/Cards';
import {PackagesIcon,AudioIcon,CheckIcon,SearchIcon,ProgressIcon} from '@/Components/Icons'
import {RegularButton, PrimaryButton} from '@/Components/Buttons'
import {computed, ref, onMounted,reactive} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import {toast} from 'vue3-toastify';
import {useCrudStore} from '@/Stores/useCrudStore';
import {FormElement, AppFancyRadio} from '@/Components/Form'
import {useDefaultStore} from "@/Stores/default";
const queryTerm = ref();
const props = defineProps({
    modelValue: {
        default: false,
    },
    user_id:{

    }
})
const defaultStore = useDefaultStore();
const crudStore = useCrudStore();
const loading = ref(false)
const choosenPeriod = ref('monthly')
const labels = ref([]);
const choosenLabels = ref([]);
const emits = defineEmits(['update:modelValue', 'done']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})

const onItemClicked = (item) => {
     infinitySelected.value = false;
    planItems.value.map((e) => e.selected = false)
    item.selected = true;
}

const onInfinitySelected = () => {
    planItems.value.map((e) => e.selected = false)
    infinitySelected.value = !infinitySelected.value;
}
const submitting = ref(false);

const choosenPackage = ref([]);
const infinitySelected = ref(false);
const planItems = ref([
    {title : "Paket 1"},
    {title : "Paket 2"},
    {title : "Paket 3"},
    {title : "Paket 4"},
]);


const submit = async () => {

}
</script>

<style scoped>

</style>
