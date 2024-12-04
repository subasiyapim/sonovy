<script setup>
import {ref,reactive,computed} from 'vue';

import {IconButton,PrimaryButton,RegularButton} from '@/Components/Buttons';
import {FormElement} from '@/Components/Form';

import {usePage} from '@inertiajs/vue3';

import {

  EyeOnIcon,
  TrashIcon
} from '@/Components/Icons'
import {useDefaultStore} from "@/Stores/default";

const defaultStore = useDefaultStore();

const props = defineProps({
  user: {},
});
const activeTab = ref('sub_users');
import UsersTab from './RelationsTabs/users.vue';
import LabelsTab from './RelationsTabs/labels.vue';
import ParticipantsTab from './RelationsTabs/participants.vue';
import ProductsTab from './RelationsTabs/products.vue';

const tableData = computed(() => {
    if(activeTab.value == 'sub_users'){
        return usePage().props.user.tab.relations;
    }else if(activeTab.value == 'products'){
         return usePage().props.user.tab.products;
    }else if(activeTab.value == 'labels'){
         return usePage().props.user.tab.labels;
    }else if(activeTab.value == 'participants'){
         return usePage().props.user.tab.participants;
    }
});
</script>
<template>
    <!-- {{Object.keys(usePage().props)}}
    {{usePage().props.user}} -->
      {{usePage().props.user.tab}}
    <div class="flex gap-2">
        <div @click="activeTab = 'sub_users'" class="rounded-full px-2 py-1 cursor-pointer subheading-xs" :class="activeTab == 'sub_users' ? 'bg-dark-green-800 text-white':'bg-weak-50 c-sub-600' ">
            Alt Kullanıcılar
        </div>
         <div @click="activeTab = 'products'" class="rounded-full px-2 py-1 cursor-pointer subheading-xs" :class="activeTab == 'products' ? 'bg-dark-green-800 text-white':'bg-weak-50 c-sub-600' ">
            Yayınlar
        </div>
         <div @click="activeTab = 'labels'" class="rounded-full px-2 py-1 cursor-pointer subheading-xs" :class="activeTab == 'labels' ? 'bg-dark-green-800 text-white':'bg-weak-50 c-sub-600' ">
            Plak Şirketleri
        </div>
         <div @click="activeTab = 'participants'" class="rounded-full px-2 py-1 cursor-pointer subheading-xs" :class="activeTab == 'participants' ? 'bg-dark-green-800 text-white':'bg-weak-50 c-sub-600' ">
            Katılımcılar
        </div>
    </div>


    <UsersTab v-if="activeTab == 'sub_users'" v-model="tableData"></UsersTab>
    <LabelsTab v-else-if="activeTab == 'labels'" v-model="tableData"></LabelsTab>
    <ParticipantsTab v-else-if="activeTab == 'participants'" v-model="tableData"></ParticipantsTab>
    <ProductsTab v-else-if="activeTab == 'products'" v-model="tableData"></ProductsTab>
</template>

<style scoped>

</style>
