<template>
  <BaseDialog height="400px" v-model="isDialogOn" align="center" :title="'Alt kullanıcıya Yayın Ata'"
              :description="'Seçtiğiniz kullanıcıya yeni yayın ekleyebilirsiniz'">
    <template #icon>
      <PersonIcon color="var(--dark-green-950)"/>
    </template>

    <div  class="p-5 flex flex-col gap-6" style="min-height:250px;">
        <div>

            <div class="relative w-full">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <SearchIcon color="var(--sub-600)" />
                </div>
                <input v-model="queryTerm" v-debounce="400" @input="onInput" @change="onChange" type="text" id="voice-search" class="focus:ring-0 focu border-none text-gray-900 text-sm rounded-lg block w-full ps-10 p-1 " :placeholder="step == 1 ? 'Yayın Ara' : 'Plak şirketi ara'" required />

            </div>

        </div>
        <div v-if="step == 1">
            <div v-for="product in products" class="flex flex-col items-center  justify-between cursor-pointer p-3 rounded-lg"  :class="choosenProducts?.find((e) => e.id == product.id) ? 'bg-white-600 ' : ''" @click="onChoosenItem(product)">

                <div class="flex items-center gap-2 flex-1 w-full">
                    <button class="appCheckBox" :class="choosenProducts?.find((e) => e.id == product.id) ? 'checked' : ''">
                            <CheckIcon color="#fff" />
                        </button>
                    <div class="w-12 h-12 rounded-full overflow-hidden">
                        <img :alt="product.album_name"
                            :src="product.image ? product.image.thumb : defaultStore.profileImage(product.album_name)"
                        >
                    </div>
                    <div class="flex flex-col">
                        <p class="label-sm c-sub-600">
                            {{product.album_name}}
                        </p>
                       <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1">
                                <AudioIcon v-if="product.type == 1" color="var(--sub-600)"/>
                                <MusicVideoIcon v-if="product.type == 2" color="var(--sub-600)"/>
                                <RingtoneIcon v-if="product.type == 3" color="var(--sub-600)"/>
                                <p class="paragraph-xs c-sub-600">
                                    {{product.type == 1 ? 'Ses Yayın' :(product.type == 2 ? 'Müzik Video' : 'Zil Sesi') }}
                                </p>
                            </div>
                             <span class="label-sm c-soft-300">•</span>
                            <p class="paragraph-xs c-sub-600">
                                UPC: {{product.upc_code }}
                            </p>
                             <span class="label-sm c-soft-300">•</span>
                            <p class="paragraph-xs c-sub-600" style="  white-space: nowrap;  overflow: hidden; text-overflow: ellipsis; max-width: 200px;">
                                <template v-for="art in product.main_artists">
                                    {{art.name}}
                                </template>
                            </p>

                       </div>

                    </div>
                    <button @click.stop="onOpenProductSong(product)" class="w-5 h-5  flex items-center justify-center rounded-full ms-auto" :class="choosenProductCheckButtons.find((e) => e == product.id) ? 'bg-dark-green-800' : 'bg-soft-200'">
                        <ChevronRightIcon  :color="choosenProductCheckButtons.find((e) => e == product.id) ? '#fff' : 'var(--sub-600)'"  :class="{
                                'transform rotate-90 transition-transform duration-300': choosenProductCheckButtons.find((e) => e == product.id),
                            'transform rotate-0 transition-transform duration-300': !choosenProductCheckButtons.find((e) => e == product.id)
                            }" />

                    </button>
                </div>
                <template v-if="choosenProductCheckButtons.find((e) => e == product.id)">
                    <div v-for="song in product.songs" class="w-full flex items-center my-2 px-10">
                        <div class="flex flex-col">
                            <p class="label-sm c-sub-600 mb-0.5">{{song.name}}</p>
                            <div class="flex items-center gap-3">
                                    <p class="paragraph-xs c-sub-600">ISRC:{{song.isrc}}</p>
                                    <span class="label-sm c-soft-300">•</span>
                                    <p class="paragraph-xs c-sub-600" style="  white-space: nowrap;  overflow: hidden; text-overflow: ellipsis; max-width: 200px;">
                                        <template v-for="art in song.main_artists">
                                            {{art.name}}
                                        </template>
                                    </p>
                            </div>

                        </div>
                    </div>
                </template>




            </div>
            <div v-if="loading" class="h-full flex-1">
                <div class="flex items-center gap-2 paragraph-sm">
                    <ProgressIcon /> Yükleniyor
                </div>
            </div>
            <div v-else class="h-full flex-1">

                <div v-if="products.length <= 0">
                    <template v-if="!queryTerm">
                        Kullanıcı Aramak için bilgi giriniz.
                    </template>
                    <template v-else>
                        kullanıcı bulunamadı
                    </template>

                </div>
            </div>
        </div>


        <div v-else>
            <div v-for="label in labels" class="flex items-center  justify-between cursor-pointer p-3 rounded-lg my-2" @click="onChoosenItem(label)" :class="choosenLabels?.find((e) => e.id == label.id) ? 'bg-white-600' : ''">

                <div class="flex items-center gap-2 flex-1">
                    <button class="appCheckBox" :class="choosenLabels?.find((e) => e.id == label.id) ? 'checked' : ''">
                            <CheckIcon color="#fff" />
                        </button>
                    <div class="w-12 h-12 rounded-full overflow-hidden">
                        <img :alt="label.name"
                            :src="label.image ? label.image.thumb : defaultStore.profileImage(label.name)"
                        >
                    </div>
                    <div class="flex flex-col">
                        <p class="label-sm c-sub-600">
                            {{label.name}}
                        </p>

                        <p class="paragraph-xs c-sub--600">
                             {{label.email }}
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

                <div v-if="products.length <= 0">
                    <template v-if="!queryTerm">
                        Kullanıcı Aramak için bilgi giriniz.
                    </template>
                    <template v-else>
                        kullanıcı bulunamadı
                    </template>

                </div>
            </div>
        </div>


    </div>


    <div class="flex p-5 border-t border-soft-200 gap-4 sticky bottom-0 bg-white">
        <RegularButton @click="isDialogOn = false" class="flex-1">
                İptal
        </RegularButton>
        <PrimaryButton :loading="submitting" @click="submit" class="flex-1" :disabled="choosenProducts.length<=0">
            <template v-if="step == 1">
                Devam et
            </template>
            <template v-else>
                Tamamla
            </template>
            <template #suffix v-if="step == 1">
                <ChevronRightIcon class="ms-2" color="var(--dark-green-500)" />
            </template>
            <template #suffix v-else>
                <CheckIcon class="ms-2" color="var(--dark-green-500)" />
            </template>
        </PrimaryButton>
    </div>
  </BaseDialog>
</template>

<script setup>
import BaseDialog from '../BaseDialog.vue';
import {SectionHeader} from '@/Components/Widgets';
import {PersonIcon,AudioIcon,CheckIcon,SearchIcon,ProgressIcon,ChevronRightIcon,MusicVideoIcon,RingtoneIcon} from '@/Components/Icons'
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

const step = ref(1);
const defaultStore = useDefaultStore();
const crudStore = useCrudStore();
const loading = ref(false)

const products = ref([]);
const labels = ref([]);
const choosenProducts = ref([]);
const choosenProductCheckButtons = ref([]);
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
    let tempData = [];
    if(queryTerm.value == ""){
        loading.value = false;
        tempData = [];
    }else {
        let path = route('control.search.products');
    if(step.value == 2){
            path = route('control.search.labels')
    }
        tempData = await crudStore.get(path,{
            search:queryTerm.value
        })

    }
    if(step.value == 1){
        products.value = tempData;
        choosenProducts.value.forEach(element => {
            const finded = products.value.find((e) => e.id == element.id);
            if(!finded){
                products.value.push(element);
            }
        });
    console.log("PRODUCT",products.value);

    }else {
        labels.value = tempData;
        choosenLabels.value.forEach(element => {
            const finded = labels.value.find((e) => e.id == element.id);
            if(!finded){
                labels.value.push(element);
            }
        });
    }

    loading.value = false;


}
const onChoosenItem =  (item) => {
    if(step.value == 1){
        const findedIndex = choosenProducts.value.findIndex((el) =>el.id == item.id);
        if(findedIndex >= 0 ){
            choosenProducts.value.splice(findedIndex,1);
        }else {
            choosenProducts.value.push(item);
        }
    }else {
        const findedIndex = choosenLabels.value.findIndex((el) =>el.id == item.id);
        if(findedIndex >= 0 ){
            choosenLabels.value.splice(findedIndex,1);
        }else {
            choosenLabels.value.push(item);
        }
    }

}

const onOpenProductSong = (product) => {
    const findedIndex = choosenProductCheckButtons.value.findIndex((el) =>el == product.id);
    if(findedIndex >= 0 ){
        choosenProductCheckButtons.value.splice(findedIndex,1)
    }else {
        choosenProductCheckButtons.value.push(product.id);
    }
}
const submit = async () => {
    if(step.value == 1){
        queryTerm.value = "";
        step.value++;
        return;
    };
    submitting.value = true;
    const response = await crudStore.post(route('control.user-management.users.assign-to-products',props.user_id),{
        products: choosenProducts.value.map((e) => e.id),
        labels: choosenLabels.value.map((e) => e.id),
    });
    submitting.value = false;
    isDialogOn.value = false;
    emits('done',response['products']);
    toast(response['message'] ?? 'İşlem Başarılı');

}
</script>

<style scoped>

</style>
