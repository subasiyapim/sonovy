<template>
  <AdminLayout  :showGoBack="false" :showDatePicker="false" :title="'Plan Yönetimi'">
    <template #breadcrumb>
      <span class="label-xs c-soft-400">Yönetici Ayarları</span>
      <span class="label-xs c-soft-400">•</span>
      <span class="label-xs c-soft-400 " >Plan Yönetimi</span>
      <span class="label-xs c-soft-400">•</span>
      <span class="label-xs c-neutral-500 " >Plan Öğeleri</span>

    </template>
    <AppTable :hasSelect="true"    @addNewClicked="openAddDialog" :showAddButton="true" :buttonLabel="'Plan Öğesi Ekle'" ref="pageTable" :config="appTableConfig"
              v-model="data" >

        <AppTableColumn :label="'Kategori'" >
            <template #default="scope">

            </template>
        </AppTableColumn>
        <AppTableColumn :label="'Hizmet Adı'" >
            <template #default="scope">

            </template>
        </AppTableColumn>
        <AppTableColumn :label="'Durum'" >
            <template #default="scope">

            </template>
        </AppTableColumn>

        <AppTableColumn :label="__('control.general.actions')" align="right">
            <template #default="scope">
            <div class="flex gap-3">

                <IconButton @click="editRow(scope.row)">
                    <EditIcon color="var(--sub-600)"/>
                </IconButton>
            </div>
            </template>
        </AppTableColumn>
      <template #empty>
        <div class="flex flex-col items-center justify-center gap-8">
          <div>
            <h2 class="label-medium c-strong-950">Henüz veri yok</h2>
            <h3 class="paragraph-medium c-neutral-500">Plan öğesi eklediğinizde burada gözükecek</h3>
          </div>
         <div class="flex items-center gap-2">
             <PrimaryButton @click="openTemplateModal">
                <template #icon>
                <AddIcon  color="var(--dark-green-500)" />
                </template>
                Plan Öğeleri Ekle
            </PrimaryButton>

         </div>
        </div>
      </template>
    </AppTable>

    <PlanItems  @update="onUpdate" @done="onDone" v-if="isModalOn" v-model="isModalOn"/>
   </AdminLayout>
</template>

<script setup>
import {usePage} from '@inertiajs/vue3';

import {ref, computed} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {PrimaryButton, IconButton} from '@/Components/Buttons'
import {PlanItems} from '@/Components/Dialog';
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

const isModalOn = ref(false);
const openAddDialog = () => {
    isModalOn.value = !isModalOn.value;
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
