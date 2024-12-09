<script setup>
import AppTable from '@/Components/Table/AppTable.vue';
import NestedTable from '@/Components/Table/NestedTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {computed,ref} from 'vue';
import {useDefaultStore} from "@/Stores/default";
const props = defineProps({
    modelValue:{},
});
const defaultStore = useDefaultStore();

import {IconButton} from '@/Components/Buttons'
import {EditIcon,TrashIcon} from '@/Components/Icons'
const emits = defineEmits(['update:modelValue']);
const nestedTable = ref();
const tableData = computed({
    get:() => props.modelValue,
  set: (value) => emits('update:modelValue', value)
});

const renderSubWhen = (row) => {

    return row.children?.length > 0;
};

</script>

<template>

 <div class="ps-4 py-4">
    <AppTable ref="nestedTable" :showAddButton="false"  :renderSubWhen="renderSubWhen" :hasSearch="false" :isClient="true" v-model="tableData"  :scrollable="false">
        <template #sub="scope">
            <NestedTable v-model="scope.row.children"></NestedTable>
        </template>
        <AppTableColumn label="Kullanıcı Adı" sortable="type">
            <template #default="scope">
            <div class="flex justify-start items-center gap-2 w-full">
                <div class="w-12 h-12 rounded-full overflow-hidden">
                <img :alt="scope.row.name"
                    :src="scope.row.image ? scope.row.image.thumb : defaultStore.profileImage(scope.row.name)"
                >
                </div>
                <div class="flex flex-col items-start ">
                <a :href="route('control.user-management.users.show',scope.row.id)"
                    class="font-poppins table-name-text c-sub-600">{{ scope.row.name }}</a>
                    <span class="c-sub-600 paragraph-xs">{{scope.row.email}}</span>

                    <button class="c-blue-500 label-xs" @click="nestedTable.toggleShowSub(scope.index)" v-if="scope.row?.children?.length>0">
                        {{scope.row?.children?.length}} Alt Kullanıcıyı Gör

                    </button>

                </div>
            </div>
            </template>

        </AppTableColumn>

        <AppTableColumn label="Kullanıcı Rolü" sortable="type">
            <template #default="scope">
                <template v-for="role in scope.row.roles">
                    <div class="px-3 py-1 rounded-full" :class="role.code == 'super_admin' ? 'bg-[#CAC0FF]' : (role.code == 'admin' ? 'bg-[#D8E5ED]' : 'bg-[#C0D5FF]')">
                    <p class="label-xs" :class="role.code == 'super_admin' ? 'text-[#351A75]' : (role.code == 'admin' ? 'text-[#060E2F]' : 'text-[#122368]')">  {{role.name}}</p>
                    </div>
                </template>
            </template>
        </AppTableColumn>
        <AppTableColumn label="Realizasyon/Hakediş" sortable="type">
            <template #default="scope">

                <div class="flex items-center gap-1 label-sm">
                    <span class="c-strong-950">%{{scope.row.real_commission_rate}} /</span>
                    <span class="c-soft-400">%{{scope.row.commission_rate}}</span>
                </div>
            </template>
        </AppTableColumn>
        <AppTableColumn label="Kayıt Tarihi" sortable="type">
            <template #default="scope">
            <span class="label-sm c-strong-950"> {{scope.row.created_at}}</span>
            </template>
        </AppTableColumn>
        <AppTableColumn label="Durum" sortable="type">
            <template #default="scope">
                <div class="flex items-center gap-2 border border-soft-200 rounded-lg px-3 py-1">
                    <span class="label-xs c-strong-950">•</span>
                    <span class="label-xs c-sub-600">{{scope.row.status_text}}</span>
                </div>
            </template>
        </AppTableColumn>
        <AppTableColumn label="Aksiyon" align="right">
            <template #default="scope">
                <IconButton @click="deleteRow(scope.row)">
                <TrashIcon color="var(--sub-600)"/>
                </IconButton>
                <IconButton @click="editRow(scope.row)">
                <EditIcon color="var(--sub-600)"/>
                </IconButton>
            </template>
        </AppTableColumn>
    </AppTable>
 </div>
</template>

<style scoped>

</style>
