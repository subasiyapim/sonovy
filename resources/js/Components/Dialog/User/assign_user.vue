<template>
  <BaseDialog height="400px" v-model="isDialogOn" align="center" :title="'Alt kullanıcı ata'"
              :description="'Seçtiğiniz kullanıcıya alt kullanıcılar ekleyebilirsiniz'">
    <template #icon>
      <PersonIcon color="var(--dark-green-950)"/>
    </template>

    <div class="p-5 flex flex-col gap-6" style="min-height:250px;">
        <div>

        <div class="relative w-full">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <SearchIcon color="var(--sub-600)" />
                </div>
                <input v-model="queryTerm" v-debounce="400" @input="onInput" @change="onChange" type="text" id="voice-search" class="focus:ring-0 focu border-none text-gray-900 text-sm rounded-lg block w-full ps-10 p-1 " placeholder="Kullanıcı Ara" required />

            </div>

        </div>

        <div v-for="user in users" class="flex items-center  justify-between cursor-pointer" @click="chooseUser(user)">

            <div class="flex items-center gap-2 flex-1">
                <button class="appCheckBox" :class="choosenUsers?.find((e) => e.id == user.id) ? 'checked' : ''">
                        <CheckIcon color="#fff" />
                    </button>
                <div class="w-12 h-12 rounded-full overflow-hidden">
                    <img :alt="user.name"
                        :src="user.image ? user.image.thumb : defaultStore.profileImage(user.name)"
                    >
                </div>
                <div class="flex flex-col">
                    <p class="label-sm c-sub-600">
                        {{user.name}}
                    </p>
                     <p class="paragraph-xs c-sub--600">
                        {{user.email}}
                    </p>

                </div>
            </div>



        </div>
        <div v-if="loading" class="h-full flex-1">
            <div class="flex items-center gap-2 paragraph-sm">
                <ProgressIcon /> Yükleniyor
            </div>
        </div>
        <div v-else class="h-full flex-1">

            <div v-if="users.length <= 0">
                <template v-if="!queryTerm">
                    Kullanıcı Aramak için bilgi giriniz.
                </template>
                <template v-else>
                    kullanıcı bulunamadı
                </template>

            </div>
        </div>

    </div>
    <div class="flex p-5 border-t border-soft-200 gap-4 sticky bottom-0 bg-white">
      <RegularButton @click="isDialogOn = false" class="flex-1">
       İptal
      </RegularButton>
      <PrimaryButton @click="isDialogOn = false" class="flex-1" :disabled="choosenUsers.length<=0">
       Seçilenleri Ata
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
})
const defaultStore = useDefaultStore();
const crudStore = useCrudStore();
const loading = ref(false)

const users = ref([]);
const choosenUsers = ref([]);
const emits = defineEmits(['update:modelValue', 'done']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})


const checkIfDisabled = computed(() => {

  return !form['album_name'];

});

const onInput = (e) => {
    loading.value = true;

}
const onChange = async (e) => {
    let tempUser = [];
    if(queryTerm.value == ""){


        loading.value = false;
       tempUser = [];
    }else {
        tempUser = await crudStore.get(route('control.search.users'),{
            search:queryTerm.value
        })

    }
    users.value = tempUser;
    loading.value = false;


    choosenUsers.value.forEach(element => {
        const finded = users.value.find((e) => e.id == element.id);
        if(!finded){
            users.value.push(element);
        }
    });
}
const chooseUser =  (user) => {
    const findedIndex = choosenUsers.value.findIndex((el) =>el.id == user.id);
    if(findedIndex >= 0 ){
        choosenUsers.value.splice(findedIndex,1);
    }else {
        choosenUsers.value.push(user);
    }
}
</script>

<style scoped>

</style>
