<script setup>

import {ref, onMounted, onUnmounted, computed} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {PrimaryButton} from '@/Components/Buttons'
import {UploadIcon,DownloadIcon,CheckFilledIcon} from '@/Components/Icons'
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {FinanceImportReportModal,ImportErrorsModal} from '@/Components/Dialog';
import moment from 'moment';

const props = defineProps({
    reportFiles:{},
    platforms:{}, // Tüm platformlar
    statuses:{}, // Durum seçenekleri
    filters:{}
});
const emits = defineEmits(['updated']);
const isImportModalOn = ref(false);
const data = computed({
    get:() => props.reportFiles,
    set:(vale) => emits('updated'),
});

const choosenErrors = ref([]);
const isErrorModalOn = ref(false)

const openErrorModal = (list) => {
    choosenErrors.value = list;
    isErrorModalOn.value = true;
}

const appTableConfig = computed(() => {
  return {
    filters: props.filters,
  }
})
// const data = ref([]);
</script>

<template>
    <AdminLayout :showGoBack="false"  :showDatePicker="false" :title="__('control.finance.imports.header')" parentTitle="Finans">
        <template #toolbar>
            <PrimaryButton @click="isImportModalOn = true" >
                <template #icon>
                    <UploadIcon color="var(--dark-green-500)" />
                </template>
                Rapor İçe Aktar
            </PrimaryButton>
        </template>

        <AppTable v-model="data" :showAddButton="false" :config="appTableConfig">
            <AppTableColumn label="Platform">
                <template #default="scope">
                    <div class="flex items-center gap-2">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full border border-soft-200 p-1">
                            <span v-if="scope.row?.platform?.icon" v-html="scope.row?.platform?.icon"></span>
                            <div v-else class="bg-weak-50 w-full h-full rounded-full flex items-center justify-center">
                               <p class="paragraph-xs c-strong-950">{{(scope.row.platform?.name || '?').substring(0,1)}}</p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="paragraph-xs c-strong-950">
                                {{scope.row.platform?.name || 'Platform Bulunamadı'}}
                            </p>
                        </div>
                    </div>
                </template>
            </AppTableColumn>

            <AppTableColumn label="Rapor Tarihi">
                <template #default="scope">
                    <p class="paragraph-xs c-strong-950 whitespace-nowrap">
                        {{scope.row.report_date || 'Tarih Bulunamadı'}}
                    </p>
                </template>
            </AppTableColumn>

            <AppTableColumn label="Rapor Adı" width="260">
                <template #default="scope">
                     <p class="paragraph-xs c-strong-950">
                             {{scope.row.report_name}}
                     </p>
                </template>
            </AppTableColumn>

            <AppTableColumn label="Hatalar">
                <template #default="scope">
                    <p @click="openErrorModal(scope.row.errors)" class="border border-soft-200 rounded px-2 py-0.5 cursor-pointer paragraph-xs c-strong-950 whitespace-nowrap">
                        {{scope.row.errors?.length ?? 0}} adet Hata
                    </p>
                </template>
            </AppTableColumn>

            <AppTableColumn label="Ücret">
                <template #default="scope">
                    <p class="paragraph-xs c-strong-950">
                        {{scope.row.total}} $
                    </p>
                </template>
            </AppTableColumn>

            <AppTableColumn label="Boyut">
                <template #default="scope">
                    <p class="paragraph-xs c-strong-950">
                        {{scope.row.file_size}}
                    </p>
                </template>
            </AppTableColumn>

            <AppTableColumn label="İşlem Tarihi">
                <template #default="scope">
                    <div class="flex flex-col items-start">
                        <p class="paragraph-xs c-strong-950 whitespace-nowrap">
                            {{moment(scope.row.created_at).format('D MMMM Y')}}
                        </p>
                        <p class="paragraph-xs c-strong-950">
                            {{moment(scope.row.created_at).format('hh:ss')}}
                        </p>
                    </div>
                </template>
            </AppTableColumn>

            <AppTableColumn label="Durum" width="50">
                <template #default="scope">
                    <span class="paragraph-xs flex items-center gap-2 c-strong-950 border border-soft-200 rounded px-2 py-0.5">
                        <template v-if="scope.row.status == 3">
                            <CheckFilledIcon color="#49A668" />
                            Başarılı
                        </template>
                    </span>
                </template>
            </AppTableColumn>

            <AppTableColumn label="Aksiyon" align="right">
                <template #default="scope">
                    <div v-if="scope.row.file && scope.row.file.id">
                        <a :href="route('control.finance.reports.download-file', { file: scope.row.id })" target="_blank">
                            <DownloadIcon color="var(--sub-600)" />
                        </a>
                    </div>
                    <div v-else class="text-xs text-gray-400">
                        Dosya mevcut değil
                    </div>
                </template>
            </AppTableColumn>

            <template #empty>
                Henüz veri yok
            </template>
        </AppTable>
    </AdminLayout>

    <FinanceImportReportModal v-if="isImportModalOn" v-model="isImportModalOn" ></FinanceImportReportModal>
    <ImportErrorsModal :errors="choosenErrors" v-if="isErrorModalOn" v-model="isErrorModalOn" />
</template>

<style lang="postcss" scoped>

</style>
