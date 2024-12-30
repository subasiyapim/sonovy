

<script  setup>
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {usePage} from '@inertiajs/vue3';
const data = [];
</script>

<template>
     <AppTable :showAddButton="false"  :buttonLabel="__('control.finance.add_new')" ref="pageTable"
            v-model="data" >
        <template #tableHeader>
          <p class="subheading-regular c-strong-950">  İşlem Tarihçesi</p>
        </template>
      <AppTableColumn :label="__('control.finance.payments.demanded_table.column_1')" align="left" sortable="name">
        <template #default="scope">


        </template>
      </AppTableColumn>
      <AppTableColumn :label="__('control.finance.payments.demanded_table.column_2')" sortable="name" width="140">
        <template #default="scope">

        </template>
      </AppTableColumn>

      <AppTableColumn :label="__('control.finance.payments.demanded_table.column_3')" sortable="name">
        <template #default="scope">

        </template>
      </AppTableColumn>

      <AppTableColumn :label="__('control.finance.payments.demanded_table.column_4')" sortable="name">
        <template #default="scope">

        </template>
      </AppTableColumn>
      <AppTableColumn :label="__('control.finance.payments.demanded_table.column_5')" sortable="name">
        <template #default="scope">

        </template>
      </AppTableColumn>
      <AppTableColumn :label="__('control.general.actions')" align="right">
        <template #default="scope">

        </template>
      </AppTableColumn>
      <template #empty>
        <div class="flex flex-col items-center justify-center gap-8">
          <div>
            <h2 class="label-medium c-strong-950">{{ __('control.finance.payments.table.empty_header') }}</h2>
          </div>

        </div>
      </template>
    </AppTable>
</template>

<style  scoped>

</style>
