

<template>
    <AppTable :hasSelect="true"
         :hasSearch="false"
            :buttonLabel="'Plak Şirketi Ata'" ref="pageTable"  :isClient="true"
              v-model="tableData" @addNewClicked="openDialog">
        <AppTableColumn :label="__('control.label.title_singular')" align="left" sortable="name">
            <template #default="scope">

            <div class="flex items-center gap-2 ">
                <div class="w-12 h-12 rounded-full overflow-hidden">
                <img :alt="scope.row.name"
                    :src="scope.row.image ? scope.row.image.thumb : defaultStore.profileImage(scope.row.name)"
                >
                </div>
                <a :href="route('control.catalog.labels.show',scope.row.id)"
                class="c-blue-500 paragraph-xs">{{ scope.row.name }}</a>


            </div>
            </template>
        </AppTableColumn>

        <AppTableColumn :label="__('control.label.fields.product_count')" sortable="name" >
            <template #default="scope">
            <span class="paragraph-xs c-strong-950">
                    {{ scope.row.products_count }} yayın

            </span>
            </template>
        </AppTableColumn>
        <AppTableColumn :label="__('control.label.fields.song_count')" sortable="name" >
            <template #default="scope">
                <span class="paragraph-xs c-strong-950">
                    {{ scope.row.song_count ?? 0 }} parça
                </span>
            </template>
        </AppTableColumn>

        <AppTableColumn :label="__('control.label.fields.added_by')" sortable="name" width="140">
            <template #default="scope">
                <div class="flex items-center gap-4">

                    <a v-if="scope.row.user" :href="route('control.user-management.users.show',scope.row.user?.id)" class="paragraph-xs c-blue-500 hover:underline">
                        {{ scope.row.user?.name }}
                    </a>
                </div>
            </template>
        </AppTableColumn>

        <AppTableColumn :label="__('control.general.actions')" align="right">
            <template #default="scope">
            <div class="flex gap-3">
                    <ActionButton :label="scope.row" @onDetached="onDetached(scope)" />
            </div>
            </template>
        </AppTableColumn>
        <template #empty>
            <div class="flex flex-col items-center justify-center gap-8">
            <div>
                <h2 class="label-medium c-strong-950">{{ __('control.user.no_label_company') }}</h2>
            </div>

            </div>
        </template>
    </AppTable>
    <AssignUserLabelModal :assignedLabels="tableData.map((e) => e.id)" @done="onDone" v-if="isAssignModalOn" v-model="isAssignModalOn" :user_id="usePage().props.user.id" ></AssignUserLabelModal>

</template>

<script  setup>
import AppTable from '@/Components/Table/AppTable.vue';
import {AddIcon,LabelEmailIcon,TrashIcon,EditIcon,PhoneIcon} from '@/Components/Icons';
import {PrimaryButton,IconButton} from '@/Components/Buttons';
import {StatusBadge} from '@/Components/Badges';
import {AssignUserLabelModal} from '@/Components/Dialog';
import ActionButton from './Components/labels_action_button.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {computed,ref} from 'vue';
import {toast} from 'vue3-toastify';
import {useDefaultStore} from "@/Stores/default";
import { usePage} from '@inertiajs/vue3';
const props = defineProps({
    modelValue:{}
});



const defaultStore = useDefaultStore();
const emits = defineEmits(['update:modelValue']);
const isAssignModalOn = ref(false);
const tableData = computed({
    get:() => props.modelValue,
    set:(val) => emits('update:modelValue',val)
})

const openDialog =  () => {
    isAssignModalOn.value = true;
};

const onDone = (e) => {
    e.forEach(element => {
        tableData.value.push(element);
    });
}

const onDetached = (scopeRow) => {
    tableData.value.splice(scopeRow.index,1);
    toast.success("İşlem başarılı")
};

</script>
<style  scoped>

</style>
