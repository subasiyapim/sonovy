<template>
  <BaseDialog height="400px" v-model="isDialogOn" align="center" :title="'Alt kullanıcıya Plak şirketi ata'"
              :description="'Seçtiğiniz kullanıcıya yeni plak şirketi tanımlayabilirsiniz'">
    <template #icon>
      <PersonIcon color="var(--dark-green-950)"/>
    </template>
    <hr>
    <div class="p-5 flex flex-col gap-2" style="min-height:250px;">
        <div>

        <div class="relative w-full">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <SearchIcon color="var(--sub-600)" />
                </div>
                <input v-model="queryTerm" v-debounce="400" @input="onInput" @change="onChange" type="text" id="voice-search" class="focus:ring-0 focu border-none text-gray-900 text-sm rounded-lg block w-full ps-10 p-1 " placeholder="Yayın Ara" required />

            </div>

        </div>

        <div v-for="label in labels.filter((e) => !assignedLabels.includes(e.id))" class="flex items-center  justify-between cursor-pointer p-2 rounded-xl" :class="choosenLabels?.find((e) => e.id == label.id) ? 'bg-white-600' : ''" @click="onChoosenItem(label)">

            <div class="flex items-center gap-2 flex-1">
                <button class="appCheckBox" :class="choosenLabels?.find((e) => e.id == label.id) ? 'checked' : ''">
                        <CheckIcon color="#fff" />
                    </button>
                    <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
                        <div class="w-6 h-6  rounded-full overflow-hidden">
                            <img :alt="label.name"
                                :src="label.image ? label.image.thumb : defaultStore.profileImage(label.name)"
                            >
                        </div>
                    </div>

                <div class="flex flex-col">

                    <p class="label-sm c-sub-600">
                        {{label.name}}
                    </p>
                     <div class="flex items-center gap-1 paragraph-xs">
                        <span class="c-soft-400 ">⇢ Bağlı Kullanıcı: </span> <span class=" c-sub--600">{{label.user?.name }}</span>
                    </div>

                </div>
            </div>



        </div>
        <div v-if="loading" class="h-full flex-1">
            <div class="flex items-center gap-2 paragraph-sm">
                <ProgressIcon /> Yükleniyor
            </div>
        </div>
        <div v-else class="h-full flex-1">

            <div v-if="labels.length <= 0" class="paragraph-sm c-strong-950">
                <template v-if="!queryTerm">
                    Plak şirketi aramak için bilgi giriniz.
                </template>
                <template v-else>
                    Plak şirketi bulunamadı
                </template>

            </div>
        </div>

    </div>
    <div class="flex p-5 border-t border-soft-200 gap-4 sticky bottom-0 bg-white">
        <RegularButton @click="isDialogOn = false" class="flex-1">
                İptal
        </RegularButton>
        <PrimaryButton :loading="submitting" @click="submit" class="flex-1" :disabled="choosenLabels.length<=0">
            Seçilenleri Ata
            <template #suffix>
                <CheckIcon color="var(--dark-green-500)" />
            </template>
        </PrimaryButton>
    </div>
  </BaseDialog>
</template>

<script setup>
import BaseDialog from '../BaseDialog.vue';
import {SectionHeader} from '@/Components/Widgets';
import {PersonIcon,AudioIcon,CheckIcon,SearchIcon,ProgressIcon} from '@/Components/Icons'
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

    },
    assignedLabels:{
        default:[],
    }
})
const defaultStore = useDefaultStore();
const crudStore = useCrudStore();
const loading = ref(true)
const form = useForm({
  id: "",
  name: '',
  type:1

});
const labels = ref([]);
const choosenLabels = ref([]);
const emits = defineEmits(['update:modelValue', 'done']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})

const submitting = ref(false);

const onInput = (e) => {
    loading.value = true;

}
const onChange = async (e) => {
    let tempUser = [];
    if(queryTerm.value == ""){


        loading.value = false;
       tempUser = [];
    }else {
        tempUser = await crudStore.get(route('control.search.labels'),{
            search:queryTerm.value
        })

    }
    labels.value = tempUser;
    loading.value = false;


    choosenLabels.value.forEach(element => {
        const finded = labels.value.find((e) => e.id == element.id);
        if(!finded){
            labels.value.push(element);
        }
    });
}
const onChoosenItem =  (label) => {
    const findedIndex = choosenLabels.value.findIndex((el) =>el.id == label.id);
    if(findedIndex >= 0 ){
        choosenLabels.value.splice(findedIndex,1);
    }else {
        choosenLabels.value.push(label);
    }
}


const submit = async () => {
    submitting.value = true;
    const response = await crudStore.post(route('control.user-management.users.assign-to-labels',props.user_id),{
        labels: choosenLabels.value.map((e) => e.id),
    });
    submitting.value = false;
    isDialogOn.value = false;
    emits('done',response['labels']);
    toast(response['message'] ?? 'İşlem Başarılı');

}
onMounted( async() => {

    labels.value = await crudStore.get(route('control.search.labels'),{
        search:"*"
    })
    console.log("GELDİİİ");

    loading.value = false

});
</script>

<style scoped>

</style>
