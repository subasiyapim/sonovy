<script setup>
import {ref, computed} from 'vue';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {usePage, router} from '@inertiajs/vue3';
import {DocumentIcon, DownloadIcon,TrashIcon} from "@/Components/Icons/index.js";
import moment from 'moment';
import 'moment/dist/locale/tr';
import {ConfirmDeleteDialog} from '@/Components/Dialog';
import {IconButton} from '@/Components/Buttons';
import {toast} from 'vue3-toastify';

moment.locale('tr');

const tippyRefs = ref({});

const deleteReport = (id) => {
    router.delete(route('control.finance.reports.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Rapor başarıyla silindi');
            window.location.reload();
        },
        onError: (error) => {
            toast.error('Rapor silinirken bir hata oluştu');
        }
    });
};

const onCancel = (id) => {
    tippyRefs.value[id]?.hide();
};

const getBody = computed(() => {
    return document.querySelector('body');
});
</script>

<template>
  <AppTable  :showAddButton="false" :buttonLabel="__('control.finance.add_new')" ref="pageTable"
            v-model="usePage().props.reports">
    <template #tableHeader>
      <p class="subheading-regular c-strong-950">{{ __('control.finance.payments.transaction_history') }}</p>
    </template>
    <AppTableColumn :label="__('control.finance.payments.demanded_table.column_1')"  align="left" sortable="name">
      <template #default="scope">
        <div class="flex items-center gap-2">
          <div class="w-10 h-10 rounded-full border border-soft-200 flex items-center justify-center">
            <DocumentIcon color="var(--sub-600)"/>
          </div>
          <div v-if="scope.row.period" class="flex flex-col items-start">
               <p class="label-xs c-strong-950 whitespace-nowrap">{{ moment(scope.row.period.split(" ")[0],'MM-YYYY').format("MMMM YYYY") }} -  {{ moment(scope.row.period.split(" ")[1],'MM-YYYY').format("MMMM YYYY") }}</p>

          </div>

        </div>

      </template>
    </AppTableColumn>
    <AppTableColumn :label="__('control.finance.payments.demanded_table.column_2')" sortable="name">
      <template #default="scope">
        <tippy :allowHtml="true" :sticky="true" :interactive="true">
            <p class="paragraph-xs c-neutral-500">
                {{ scope.row.name }}
            </p>

            <template #content>
                <div class="flex items-center gap-0.5">
                    <span v-for="d in scope.row.tooltipData?.items">
                        {{d.name}}
                    </span>
                </div>
            </template>
        </tippy>

      </template>
    </AppTableColumn>

    <AppTableColumn :label="__('control.finance.payments.demanded_table.column_3')" sortable="name">
      <template #default="scope">
        <div>
          <tippy :allowHtml="true" :sticky="true">
            <p class="paragraph-xs c-strong-950">{{ scope.row.amount }}</p>
            <template #content>
              <p style="color: white !important" class="label-sm" v-html="scope.row.monthly_amount"/>
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
    <AppTableColumn :label="__('control.general.actions')" sortable="name" align="right">
      <template #default="scope">
        <div class="flex items-center gap-2">
            <a :href="route('control.finance.reports.download', scope.row.id)">
                <DownloadIcon color="var(--sub-600)"/>
            </a>
            <tippy :maxWidth="440" :ref="el => tippyRefs[scope.row.id] = el" theme="light" :allowHtml="true" :sticky="true" trigger="click"
                :interactive="true" :appendTo="getBody">
                <IconButton>
                    <TrashIcon color="var(--sub-600)"/>
                </IconButton>
                <template #content>
                    <ConfirmDeleteDialog
                        @confirm="deleteReport(scope.row.id)"
                        @cancel="() => onCancel(scope.row.id)"
                        title="Raporu silmek istediğinize emin misiniz?"
                        description="Bu işlem geri alınamaz ve rapor kalıcı olarak silinecektir."
                    />
                </template>
            </tippy>
        </div>
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
