

<template>
    <AppTable :hasSelect="true" :isClient="true" :buttonLabel="'Yeni Plak Şirketi Ata'"
              v-model="tableData">
      <AppTableColumn :label="__('control.label.title_singular')" align="left" sortable="name">
        <template #default="scope">


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
</template>

<script  setup>
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {computed} from 'vue';
const props = defineProps({
    modelValue:{}
});
const emits = defineEmits(['update:modelValue']);

const tableData = computed({
    get:() => props.modelValue,
    set:(val) => emits('update:modelValue',value)
})
</script>
<style  scoped>

</style>
