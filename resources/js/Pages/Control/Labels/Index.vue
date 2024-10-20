<template>
  <AdminLayout :showDatePicker="false" title="Tüm Plak Şirketleri" parentTitle="Katalog">

    <AppTable :hasSelect="true" buttonLabel="Platform Ekle" ref="pageTable"   :config="appTableConfig" v-model="usePage().props.labels" @addNewClicked="openDialog">
      <AppTableColumn label="Plak Şirketi" align="left" sortable="name">
        <template #default="scope">

          <div class="flex items-center gap-2 ">
            <div class="w-12 h-12 rounded-full overflow-hidden">
              <img v-if="scope.row.image" :src="scope.row.image.thumb">
            </div>
             <a :href="route('control.catalog.labels.show',scope.row.id)" class="c-sub-600 text-sm leading-4 font-bold">{{ scope.row.name }}</a>


          </div>
        </template>
      </AppTableColumn>
      <AppTableColumn label="Ülke" >
        <template #default="scope">{{ scope.row.name }}</template>
      </AppTableColumn>

      <AppTableColumn label="Telefon">
        <template #default="scope">{{ scope.row.phone }}</template>
      </AppTableColumn>

      <AppTableColumn label="E-mail">
        <template #default="scope">{{ scope.row.email }}</template>
      </AppTableColumn>
      <AppTableColumn label="Hakediş Durum">
        <template #default="scope">-</template>
      </AppTableColumn>
      <AppTableColumn label="Aksiyonlar" align="right">
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
            <h2 class="label-medium c-strong-950">Henüz yayınız bulunmamaktadır.</h2>
            <h3 class="paragraph-medium c-neutral-500">Oluşturucağınız tüm yayınlar burada listelenecektir.</h3>
          </div>
          <PrimaryButton>
            <template #icon>
              <AddIcon/>
            </template>
            İlk Yayını Oluştur
          </PrimaryButton>
        </div>
      </template>
    </AppTable>

    <LabelDialog :label="choosenLabel" @done="onDone" v-if="isModalOn" v-model="isModalOn"/>
  </AdminLayout>
</template>

<script setup>
import {usePage} from '@inertiajs/vue3';

import {ref,computed} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {PrimaryButton, IconButton} from '@/Components/Buttons'
import {AddIcon, LabelsIcon, ArtistsIcon, TrashIcon, EditIcon} from '@/Components/Icons'
import {AppCard} from '@/Components/Cards'
import {LabelDialog} from '@/Components/Dialog';
const pageTable = ref();

const props = defineProps({
  filters: {
    type: Array,
    default: () => [],
    required: true
  }
})


const data = ref([
])
const choosenLabel = ref(null);
const isModalOn = ref(false);
const openDialog = () => {
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
</script>

<style lang="scss" scoped>

</style>
