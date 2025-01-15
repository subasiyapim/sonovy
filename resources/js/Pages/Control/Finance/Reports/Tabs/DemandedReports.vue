<script setup>
import {ref} from 'vue';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {usePage} from '@inertiajs/vue3';
import {DocumentIcon, DownloadIcon,TrashIcon} from "@/Components/Icons/index.js";
import moment from 'moment';
import 'moment/dist/locale/tr';


moment.locale('tr');

</script>

<template>
  <AppTable  :showAddButton="false" :buttonLabel="__('control.finance.add_new')" ref="pageTable"
            v-model="usePage().props.reports">
    <template #tableHeader>
      <p class="subheading-regular c-strong-950"> İşlem Tarihçesi</p>
    </template>
    <AppTableColumn :label="__('control.finance.payments.demanded_table.column_1')"  align="left" sortable="name">
      <template #default="scope">
        <div class="flex items-center gap-2">
          <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
            <DocumentIcon color="var(--sub-600)"/>
          </div>
          <div v-if="scope.row.period" class="flex flex-col items-start">
               <p class="label-xs c-strong-950">{{ moment(scope.row.period.split(" ")[0],'MM-YYYY').format("MMMM YYYY") }} - </p>
                <p class="label-xs c-strong-950">{{ moment(scope.row.period.split(" ")[1],'MM-YYYY').format("MMMM YYYY") }}</p>
          </div>

        </div>

      </template>
    </AppTableColumn>
    <AppTableColumn :label="__('control.finance.payments.demanded_table.column_2')" sortable="name">
      <template #default="scope">
     <p class="paragraph-xs c-neutral-500">
                    {{ scope.row.name }}
                </p>

             <!-- <tippy>

                <template #content>
                    dneeme
                </template>
            </tippy> -->

      </template>
    </AppTableColumn>

    <AppTableColumn :label="__('control.finance.payments.demanded_table.column_3')" sortable="name">
      <template #default="scope">
        <div>
          <tippy :allowHtml="true" :sticky="true">
            <p class="paragraph-xs c-strong-950">{{ scope.row.amount }}</p>
            <template #content>
              <p style="color: white !important" class="paragraph-xs" v-html="scope.row.monthly_amount"/>
            </template>
          </tippy>
        </div>
      </template>
    </AppTableColumn>


    <AppTableColumn :label="__('control.finance.payments.demanded_table.column_4')" sortable="name">
      <template #default="scope">
        <div>
            <p class="paragraph-xs c-strong-950">{{ moment(scope.row.created_at).format('D MMMM YYYY') }}</p>
            <p class="paragraph-xs c-sub-600">{{ moment(scope.row.created_at).format('HH:mm:ss') }}</p>
        </div>

      </template>
    </AppTableColumn>
    <AppTableColumn :label="__('control.finance.payments.demanded_table.column_5')" sortable="name">
      <template #default="scope">

        <div class="border border-soft-200 rounded-lg px-3 py-1 flex items-center gap-2">
          <div class="w-2 h-2 rounded-full bg-spotify"></div>
          <p class="label-xs c-neutral-500">{{ scope.row.status_text }}</p>
        </div>
      </template>
    </AppTableColumn>
    <AppTableColumn :label="__('control.finance.payments.demanded_table.column_6')" sortable="name" align="right">
      <template #default="scope">
        <a :href="route('control.finance.reports.download', scope.row.id)">
          <DownloadIcon color="var(--sub-600)"/>

        </a>
        <a class="ms-2" :href="route('control.finance.reports.destroy', scope.row.id)">
          <TrashIcon color="var(--sub-600)"/>
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
