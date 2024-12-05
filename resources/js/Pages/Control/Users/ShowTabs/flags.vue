<script setup>
import {ref,reactive,onMounted} from 'vue';
import AppTable from '@/Components/Table/AppTable.vue';
import {IconButton,PrimaryButton,RegularButton} from '@/Components/Buttons';
import {FormElement} from '@/Components/Form';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {usePage} from '@inertiajs/vue3';

import {

  EyeOnIcon,
  TrashIcon
} from '@/Components/Icons'
import {useDefaultStore} from "@/Stores/default";
import {useCrudStore} from "@/Stores/useCrudStore";

const defaultStore = useDefaultStore();
const crudStore = useCrudStore();
const note = ref('');
const props = defineProps({
  user: {},
});
const onBlockClicked = async () => {
    var response = await crudStore.post(route('control.user-management.users.toggle-status',props.user.id),{
        "reason" :note.value
    });
    location.reload();
};
onMounted(() => {
     if(props.user.status == 0){
        if(props.user.tab?.length > 0){

            note.value = props.user.tab[props.user.tab.length-1].reason;
        }
     }

});
</script>
<template>
    <div class="flex flex gap-6 items-center mb-5">
       <FormElement :label="'Kullanıcıyı Blokla'" v-model="note" placeholder="Yorum Ekle" label-width="290px" class="w-[560px]">
            <template #tooltip>
                deneme
            </template>
        </FormElement>
        <button @click="onBlockClicked" :class="user.status == 1 ? 'bg-error-500' : 'bg-blue-500'" class=" px-3 py-2 rounded-lg text-white  label-sm">
          <template v-if="user.status == 1">

              Kullanıcıyı Blokla
          </template>
            <template v-else>

              Bloğu Kaldır
          </template>
        </button>

    </div>

    <AppTable  v-model="user.tab"  :isClient="true" :hasSearch="false" :showAddButton="false" >


        <AppTableColumn label="Aksiyon Tarihi">
            <template #default="scope">
                {{scope.row.created_at}}
            </template>
        </AppTableColumn>


        <AppTableColumn label="Yorum">
            <template #default="scope">
                  {{scope.row.reason}}
            </template>
        </AppTableColumn>

        <AppTableColumn label="Bloklayan Kullanıcı">
            <template #default="scope">
                 {{scope.row.created_by?.name}}
            </template>
        </AppTableColumn>

        <template #empty>
            Tarih Detayı Bulunamadı
        </template>
    </AppTable>
</template>

<style scoped>

</style>
