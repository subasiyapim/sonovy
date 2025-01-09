

<template>
    <AppTable  ref="usersTable"
        :isClient="true"
        :hasSearch="false"
        :renderSubWhen="renderSubWhen"
        @addNewClicked="openDialog"
        :buttonLabel="'Alt Kullanıcı Ata'"
        v-model="tableData" >
        <template #sub="scope">
            <NestedTable v-model="scope.row.children"></NestedTable>
        </template>

        <AppTableColumn label="#" sortable="id">
            <template #default="scope">
                    {{scope.row.id}}
            </template>
        </AppTableColumn>
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

                    <button class="c-blue-500 label-xs" @click="usersTable.toggleShowSub(scope.index)" v-if="scope.row?.children?.length>0">
                        {{scope.row?.children?.length}} {{ __('control.user.view_sub_users') }}

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
                    <span class="c-strong-950">%{{scope.row.payment_threshold}} /</span>
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
                    <span class="label-xs c-sub-600">{{scope.row.status == 1 ? 'Aktif' : 'Pasif'}}</span>
                </div>
            </template>
        </AppTableColumn>
        <AppTableColumn label="Aksiyon" align="right">
            <template #default="scope">
                <ActionButton @updateUser="onOpenUpdateModal(scope.row)" :user="scope.row" @onUserDetached="onUserDetached(scope)" />
            </template>
        </AppTableColumn>
    </AppTable>
    <UserModal @update="onUpdated" v-model="isUserModalOn" v-if="isUserModalOn" :user="choosenUser"></UserModal>

     <AssignUserModal @done="onDone" v-if="isAssignModalOn" v-model="isAssignModalOn" :user_id="usePage().props.user.id" ></AssignUserModal>
</template>

<script  setup>
import AppTable from '@/Components/Table/AppTable.vue';
import {IconButton} from '@/Components/Buttons';
import {AssignUserModal} from '@/Components/Dialog';
import {StatusBadge} from '@/Components/Badges';
import {TrashIcon,EditIcon,More2LineIcon} from '@/Components/Icons';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import ActionButton from './Components/user_action_button.vue';
import {computed,ref} from 'vue';
import {useDefaultStore} from "@/Stores/default";
import NestedTable from '@/Components/Table/NestedTable.vue';
import { usePage} from '@inertiajs/vue3';
import {toast} from 'vue3-toastify';

const usersTable = ref();
import {UserModal} from '@/Components/Dialog';


const isUserModalOn = ref(false);
const defaultStore = useDefaultStore();
const isAssignModalOn = ref(false);
const props = defineProps({
    modelValue:{}
});

const choosenUser = ref(null);
const emits = defineEmits(['update:modelValue']);

const tableData = computed({
    get:() => props.modelValue,
    set:(val) => emits('update:modelValue',val)
})
const renderSubWhen = (row) => {
    return row.children?.length > 0;
};

const openDialog =  () => {
    isAssignModalOn.value = true;
};

const onOpenUpdateModal = (row) => {
    choosenUser.value = row;
    isUserModalOn.value = true;
}
const onDone = (e) => {
    console.log("EEE",e);
    e.forEach(element => {
        console.log("ELEMENT",element);

        tableData.value.push(element);
    });
}
const onUserDetached = (scopeRow) => {
    tableData.value.splice(scopeRow.index,1);
    toast.success("İşlem başarılı")
}

const onUpdated = (e) => {
  location.reload();
}

</script>
<style  scoped>

</style>
