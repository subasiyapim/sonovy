<script setup>
import {usePage} from '@inertiajs/vue3';
import {DownloadIcon, DocumentIcon} from '@/Components/Icons';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import moment from 'moment';
import  'moment/dist/locale/tr';
moment.locale('tr');
const data = usePage().props.reports;

</script>

<template>
  <AppTable :showAddButton="false" :buttonLabel="__('control.finance.add_new')" ref="pageTable"
            v-model="data">
    <template #tableHeader>
      <p class="subheading-regular c-strong-950"> İşlem Tarihçesi</p>
    </template>
    <AppTableColumn :label="__('control.finance.payments.table.column_1')" width="140" align="left" sortable="name">
      <template #default="scope">
        <div class="flex items-center gap-2">
          <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
            <DocumentIcon color="var(--sub-600)"/>
          </div>
          <p class="subheading-xs c-strong-950">
            <!-- {{ moment(scope.row.period,"YYYY-m").format('MMMM YYYY') }} -->
            {{ scope.row.period }}</p>
        </div>

      </template>
    </AppTableColumn>
    <AppTableColumn :label="__('control.finance.payments.table.column_2')" sortable="name">
      <template #default="scope">
        <p class="paragraph-xs c-neutral-500">
        <!-- {{ scope.row.period }} -->
        Tam katalog hakkında Tek Rapor
        </p>
      </template>
    </AppTableColumn>

    <AppTableColumn :label="__('control.finance.payments.table.column_3')" sortable="name">
      <template #default="scope">
        <div>
          <tippy :allowHtml="true" :sticky="true">
            <p class="paragraph-xs c-neutral-500">{{ scope.row.amount }}</p>
            <template #content>
              <p style="color: white !important" class="paragraph-xs" v-html="scope.row.monthly_amount"/>
            </template>
          </tippy>
        </div>
      </template>
    </AppTableColumn>

    <AppTableColumn :label="__('control.finance.payments.table.column_4')" sortable="name">
      <template #default="scope">

        <div class="border border-soft-200 rounded px-3 py-1 flex items-center gap-2">
          <div class="w-2 h-2 rounded-full bg-spotify"></div>
          <p class="label-xs c-sub-600">{{ scope.row.status_text }}</p>
        </div>
      </template>
    </AppTableColumn>
    <AppTableColumn :label="__('control.finance.payments.table.column_5')" sortable="name" align="right">
      <template #default="scope">
        <a :href="route('control.finance.reports.download', scope.row.id)">
          <DownloadIcon color="var(--sub-600)"/>

        </a>
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

<style scoped>

</style>
