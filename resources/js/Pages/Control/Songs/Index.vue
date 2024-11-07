<template>
  <AdminLayout :showDatePicker="false" :title="__('control.song.header')" parentTitle="Katalog">

    <AppTable  :showAddButton="false" ref="pageTable" :config="appTableConfig"
              v-model="usePage().props.songs" @addNewClicked="openDialog">
      <AppTableColumn :label="'Tür'" align="left" sortable="name"  width="50">
        <template #default="scope">
        <div class="border border-soft-200 w-10 h-10 rounded-full flex items-center justify-center">
            <RingtoneIcon color="var(--sub-600)" v-if="scope.row.type == 1" />
            <MusicVideoIcon color="var(--sub-600)" v-if="scope.row.type == 2" />
        </div>

        </template>
      </AppTableColumn>
      <AppTableColumn :label="'Durum'" sortable="name" width="140">
        <template #default="scope">

            <StatusBadge >
                Yayında
            </StatusBadge>
        </template>
      </AppTableColumn>

      <AppTableColumn :label="'Parça Adı'" sortable="name">
        <template #default="scope">
          <div class="flex flex-col items-start">
            <p class="table-name-text c-sub-600">  {{scope.row.name}}</p>
           <p class="paragraph-xs c-sub-600">ISRC: {{scope.row.isrc}}</p>
          </div>
        </template>
      </AppTableColumn>

      <AppTableColumn :label="'Süre'" sortable="name">
        <template #default="scope">

            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full border border-soft-200 flex items-center justify-center">
                    <PlayCircleFillIcon color="var(--dark-green-500)" />
                </div>
                   <p class="label-sm c-strong-950">
                     {{scope.row.duration ?? '2.35'}}
                   </p>
            </div>
        </template>
      </AppTableColumn>
      <AppTableColumn :label="'Sanatçı'" sortable="name">
        <template #default="scope">
                {{scope.row.remixer_artis}}
        </template>
      </AppTableColumn>
      <AppTableColumn :label="'Katılımcı'" sortable="name">
        <template #default="scope">

            <div  class="border border-soft-200 rounded c-strong-950 label-sm px-3 py-1">
                {{scope.row.participants.length}} Katılımcı
            </div>

        </template>
      </AppTableColumn>

      <template #empty>
        <div class="flex flex-col items-center justify-center gap-8">
          <div>
            <h2 class="label-medium c-strong-950">{{ __('control.label.table.empty_header') }}</h2>
            <h3 class="paragraph-medium c-neutral-500">{{ __('control.label.table.empty_description') }}</h3>
          </div>
          <PrimaryButton>
            <template #icon>
              <AddIcon/>
            </template>
            {{ __('control.label.table.empty_button') }}
          </PrimaryButton>
        </div>
      </template>
    </AppTable>

    <LabelDialog :label="choosenLabel" @done="onDone" v-if="isModalOn" v-model="isModalOn"/>
  </AdminLayout>
</template>

<script setup>
import {usePage} from '@inertiajs/vue3';

import {ref, computed} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {PrimaryButton, IconButton} from '@/Components/Buttons'
import {StatusBadge,BasicBadge} from '@/Components/Badges'
import {AddIcon, LabelsIcon, ArtistsIcon, TrashIcon, PlayCircleFillIcon,EditIcon, PhoneIcon, LabelEmailIcon,AudioIcon,MusicVideoIcon,RingtoneIcon} from '@/Components/Icons'
import {AppCard} from '@/Components/Cards'
import {LabelDialog} from '@/Components/Dialog';
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
