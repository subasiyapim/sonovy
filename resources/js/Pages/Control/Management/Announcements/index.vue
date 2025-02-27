<template>
  <AdminLayout :showDatePicker="false" :title="__('control.announcement.announcements')" parentTitle="Yönetici Ayarları">

    <AppTable :hasSelect="true" :showAddButton="false" ref="pageTable" :config="appTableConfig"
              v-model="data" >
              <template #toolbar v-if="data.length > 0 ">
                <div class="flex items-center gap-2">
                    <PrimaryButton @click="openTemplateModal">
                        <template #icon>
                        <AddIcon  color="var(--dark-green-500)" />
                        </template>
                        {{ __('control.announcement.add_new_template') }}
                    </PrimaryButton>
                    <PrimaryButton @click="openAnnouncementModal">
                        <template #icon>
                        <AddIcon  color="var(--dark-green-500)" />
                        </template>
                        {{ __('control.announcement.add_new_announcement') }}
                    </PrimaryButton>
                </div>
              </template>
        <AppTableColumn :label="__('control.announcement.form.name')" >
            <template #default="scope">

            </template>
        </AppTableColumn>
        <AppTableColumn :label="__('control.announcement.form.text')" >
            <template #default="scope">

            </template>
        </AppTableColumn>
        <AppTableColumn :label="__('control.announcement.form.type')" >
            <template #default="scope">

            </template>
        </AppTableColumn>
        <AppTableColumn :label="__('control.announcement.form.from-to')"  >
            <template #default="scope">

            </template>
        </AppTableColumn>
        <AppTableColumn :label="__('control.announcement.form.status')"  >
            <template #default="scope">

            </template>
        </AppTableColumn>


        <AppTableColumn :label="__('control.general.actions')" align="right">
            <template #default="scope">
            <div class="flex gap-3">
                <IconButton @click="deleteRow(scope.row)">
                    <TrashIcon color="var(--sub-600)"/>
                </IconButton>
                <IconButton @click="editRow(scope.row)">
                    <EditIcon color="var(--sub-600)"/>
                </IconButton>
            </div>
            </template>
        </AppTableColumn>
      <template #empty>
        <div class="flex flex-col items-center justify-center gap-8">
          <div>
            <h2 class="label-medium c-strong-950">{{ __('control.announcement.table_empty_header') }}</h2>
            <h3 class="paragraph-medium c-neutral-500">{{ __('control.announcement.table_empty_description') }}</h3>
          </div>
         <div class="flex items-center gap-2">
             <PrimaryButton @click="openTemplateModal">
                <template #icon>
                <AddIcon  color="var(--dark-green-500)" />
                </template>
                {{ __('control.announcement.add_new_template') }}
            </PrimaryButton>
             <PrimaryButton @click="openAnnouncementModal">
                <template #icon>
                <AddIcon  color="var(--dark-green-500)" />
                </template>
                {{ __('control.announcement.add_new_announcement') }}
            </PrimaryButton>
         </div>
        </div>
      </template>
    </AppTable>

    <AnnouncementTemplateModal  @update="onUpdate" @done="onDone" v-if="isTemplateModalOn" v-model="isTemplateModalOn"/>
    <AnnouncementModal  @update="onUpdate" @done="onDone" v-if="isAnnouncementModalOn" v-model="isAnnouncementModalOn"/>
  </AdminLayout>
</template>

<script setup>
import {usePage} from '@inertiajs/vue3';

import {ref, computed} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {PrimaryButton, IconButton} from '@/Components/Buttons'
import {AnnouncementTemplateModal,AnnouncementModal} from '@/Components/Dialog';
import {AddIcon} from '@/Components/Icons'
import {AppCard} from '@/Components/Cards'
import {useDefaultStore} from "@/Stores/default";

const defaultStore = useDefaultStore();
const pageTable = ref();

const props = defineProps({
  filters: {
    type: Array,
    default: () => [],
    required: true
  }
})


const data = ref([])
const choosenLabel = ref(null);

const isTemplateModalOn = ref(false);
const isAnnouncementModalOn = ref(false);
const openAnnouncementModal = () => {
    isAnnouncementModalOn.value = !isAnnouncementModalOn.value;
}
const openTemplateModal = () => {
    isTemplateModalOn.value = !isTemplateModalOn.value;
}

const deleteRow = (row) => {
  pageTable.value.removeRowDataFromRemote(row);
}
const editRow = (label) => {

  choosenLabel.value = label;
  isModalOn.value = !isModalOn.value;
}
const onDone = (e) => {
  pageTable.value.addRow(e);
}

const appTableConfig = computed(() => {
  return {
    filters: props.filters,
  }
})
const onUpdate = (e) => {
  pageTable.value.editRow(e);
}
</script>

<style lang="scss" scoped>

</style>
