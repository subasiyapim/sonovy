<script setup>

import {ref, onMounted, onUnmounted} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {PrimaryButton} from '@/Components/Buttons'
import {UploadIcon,DownloadIcon} from '@/Components/Icons'
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {FinanceImportReportModal} from '@/Components/Dialog';
const props = defineProps({
    user:{}
});
const isImportModalOn = ref(false);
const data = ref([]);
</script>

<template>
    <AdminLayout :showGoBack="false" :showDatePicker="false" :title="__('control.finance.imports.header')" parentTitle="Finans">
        <template #toolbar>
            <PrimaryButton @click="isImportModalOn = true" >
                <template #icon>
                    <UploadIcon color="var(--dark-green-500)" />
                </template>
                Rapor İçe Aktar
            </PrimaryButton>
        </template>



        <AppTable  v-model="user.earning_reports"  :isClient="true" :hasSearch="false" :showAddButton="false">
                <AppTableColumn label="Platform">
                    <template #default="scope">
                          <p class="paragraph-xs c-strong-950">
                            {{scope.row.platform}}

                          </p>
                    </template>
                </AppTableColumn>

                <AppTableColumn label="Rapor Tarihi">
                    <template #default="scope">
                        <p class="paragraph-xs c-strong-950">
                             {{scope.row.report_date}}
                        </p>
                    </template>
                </AppTableColumn>


                <AppTableColumn label="Rapor Adı">
                    <template #default="scope">
                         <p class="paragraph-xs c-strong-950">
                              {{scope.row.name}}
                         </p>
                    </template>
                </AppTableColumn>

                <AppTableColumn label="Hatalar">
                    <template #default="scope">
                        <p class="paragraph-xs c-strong-950"></p>
                    </template>
                </AppTableColumn>
                <AppTableColumn label="Ücret">
                    <template #default="scope">
                        <p class="paragraph-xs c-strong-950">
                            {{scope.row.net_revenue}}
                            {{scope.row.currency}}
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
                            <p class="paragraph-xs c-strong-950">
                                {{scope.row.created_at.split(" ")[0]}}
                            </p>
                            <p class="paragraph-xs c-strong-950">
                                {{scope.row.created_at.split(" ")[1]}}
                            </p>
                        </div>
                    </template>
                </AppTableColumn>
                <AppTableColumn label="Durum">
                    <template #default="scope">
                        <p class="paragraph-xs c-strong-950">
                               {{scope.row.status}}
                        </p>
                    </template>
                </AppTableColumn>
                <AppTableColumn label="Aksiyon" align="right">
                    <template #default="scope">
                        <a :href="route('control.finance.reports.download',scope.row.id)" target="_blank">
                            <DownloadIcon color="var(--sub-600)" />
                        </a>
                    </template>
                </AppTableColumn>

                <template #empty>
                    Henüz veri yok
                </template>
            </AppTable>
    </AdminLayout>

    <FinanceImportReportModal v-if="isImportModalOn" v-model="isImportModalOn" ></FinanceImportReportModal>


</template>

<style lang="postcss" scoped>

</style>
