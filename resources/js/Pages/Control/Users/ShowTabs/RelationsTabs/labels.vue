

<template>
    <AppTable :hasSelect="true" ref="pageTable" :showAddButton="false"  :isClient="true"
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
               class="c-sub-600 table-name-text">{{ scope.row.name }}</a>


          </div>
        </template>
      </AppTableColumn>
      <AppTableColumn :label="__('control.label.fields.country_id')" sortable="name">
        <template #default="scope">

          <div class="flex items-center gap-4">
             <span class="text-xl">
                {{ scope.row?.country?.emoji }}
             </span>
            <p class="paragraph-xs c-sub-600">
              {{ scope.row?.country?.name }}
            </p>
          </div>

        </template>
      </AppTableColumn>

      <AppTableColumn :label="__('control.label.fields.phone')" sortable="name">
        <template #default="scope">
          <div class="flex gap-4">
            <PhoneIcon color="var(--neutral-500)"/>
            <p class="paragraph-xs c-sub-600 w-max">
              {{ scope.row.phone }}
            </p>
          </div>
        </template>
      </AppTableColumn>

      <AppTableColumn :label="__('control.label.fields.email')" sortable="name">
        <template #default="scope">
          <div class="flex gap-4">
            <LabelEmailIcon color="var(--neutral-500)"/>
            <p class="paragraph-xs c-sub-600">
              {{ scope.row.email }}
            </p>
          </div>
        </template>
      </AppTableColumn>
      <AppTableColumn :label="__('control.label.fields.status')" sortable="name">
        <template #default="scope">
          <StatusBadge class="w-max">
            <p class="label-xs">{{ scope.row.status ?? 'Aktif Şirket' }}</p>
          </StatusBadge>
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
            <h2 class="label-medium c-strong-950">Henüz plak şirketi bulunamadı</h2>
          </div>

        </div>
      </template>
    </AppTable>
</template>

<script  setup>
import AppTable from '@/Components/Table/AppTable.vue';
import {AddIcon,LabelEmailIcon,TrashIcon,EditIcon,PhoneIcon} from '@/Components/Icons';
import {PrimaryButton,IconButton} from '@/Components/Buttons';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {computed} from 'vue';
import {useDefaultStore} from "@/Stores/default";

const props = defineProps({
    modelValue:{}
});

const defaultStore = useDefaultStore();
const emits = defineEmits(['update:modelValue']);

const tableData = computed({
    get:() => props.modelValue,
    set:(val) => emits('update:modelValue',value)
})
</script>
<style  scoped>

</style>
