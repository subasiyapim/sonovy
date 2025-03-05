<template>
  <AdminLayout :showDatePicker="false" :title="__('control.label.header')" parentTitle="Katalog">

    <AppTable :hasSelect="true" :buttonLabel="__('control.label.add_new')" ref="pageTable" :config="appTableConfig"
              v-model="usePage().props.labels" @addNewClicked="openDialog">
        <AppTableColumn :label="__('control.label.title_singular')" align="left" sortable="name">
            <template #default="scope">

            <div class="flex items-center gap-2 ">
                <div class="w-12 h-12 rounded-full overflow-hidden">
                <img :alt="scope.row.name"
                    :src="scope.row.image ? scope.row.image.thumb : defaultStore.profileImage(scope.row.name)"
                >
                </div>
                <a :href="route('control.catalog.labels.show',scope.row.id)"
                class="paragraph-xs c-blue-500">{{ scope.row.name }}</a>


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
                    {{ scope.row.song_count }} parça
                </span>
            </template>
        </AppTableColumn>
        <AppTableColumn :label="__('control.label.fields.added_by')" sortable="name" width="140">
            <template #default="scope">
            <div class="flex items-center gap-4">
                <a v-if="scope.row.user" :href="route('control.user-management.users.show',scope.row.user?.id)" class="paragraph-xs c-blue-500 hover:underline">
                    {{ scope.row.user?.name }}
                </a>
                <p v-else class="paragraph-xs c-strong-950">
                   Ekleyen Bulunmuyor
                </p>
            </div>
            </template>
        </AppTableColumn>

        <!-- <AppTableColumn :label="__('control.label.fields.phone')" sortable="name">
            <template #default="scope">
            <div class="flex gap-4">
                <PhoneIcon color="var(--neutral-500)"/>
                <p class="paragraph-xs c-sub-600 w-max">
                {{ scope.row.phone }}
                </p>
            </div>
            </template>
        </AppTableColumn> -->

        <!-- <AppTableColumn :label="__('control.label.fields.email')" sortable="name">
            <template #default="scope">
            <div class="flex gap-4">
                <LabelEmailIcon color="var(--neutral-500)"/>
                <p class="paragraph-xs c-sub-600">
                {{ scope.row.email }}
                </p>
            </div>
            </template>
        </AppTableColumn> -->

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
            <h2 class="label-medium c-strong-950">{{ __('control.label.table.empty_header') }}</h2>
            <h3 class="paragraph-medium c-neutral-500">{{ __('control.label.table.empty_description') }}</h3>
          </div>
          <PrimaryButton>
            <template #icon>
              <AddIcon  color="var(--dark-green-500)" />
            </template>
            {{ __('control.label.table.empty_button') }}
          </PrimaryButton>
        </div>
      </template>
    </AppTable>

    <LabelDialog :label="choosenLabel" @update="onUpdate" @done="onDone" v-if="isModalOn" v-model="isModalOn"/>
  </AdminLayout>
</template>

<script setup>
import {usePage} from '@inertiajs/vue3';

import {ref, computed} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {PrimaryButton, IconButton} from '@/Components/Buttons'
import {StatusBadge} from '@/Components/Badges'
import {AddIcon, LabelsIcon, ArtistsIcon, TrashIcon, EditIcon, PhoneIcon, LabelEmailIcon} from '@/Components/Icons'
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
    choosenLabel.value = null;
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
