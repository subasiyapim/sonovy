<script setup>

import {ref, onMounted, onUnmounted} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {PrimaryButton} from '@/Components/Buttons'
import {UploadIcon,DownloadIcon} from '@/Components/Icons'
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
const props = defineProps({

});
const isImportModalOn = ref(false);
const data = ref([]);
</script>

<template>
    <AdminLayout :showGoBack="false" :showDatePicker="false" :title="'Katılımcı Raporları'" parentTitle="Finans">
        <AppTable  v-model="data"  :isClient="true" :hasSearch="false" :showAddButton="false">
                <AppTableColumn label="Kullanıcı Adı">
                    <template #default="scope">
                          <p class="paragraph-xs c-strong-950">


                          </p>
                    </template>
                </AppTableColumn>

                <AppTableColumn label="Tarih">
                    <template #default="scope">
                        <p class="paragraph-xs c-strong-950">

                        </p>
                    </template>
                </AppTableColumn>


                <AppTableColumn label="DSP">
                    <template #default="scope">
                         <p class="paragraph-xs c-strong-950">

                         </p>
                    </template>
                </AppTableColumn>

                <AppTableColumn label="T. Kazanç">
                    <template #default="scope">
                        <p class="paragraph-xs c-strong-950"></p>
                    </template>
                </AppTableColumn>
                <AppTableColumn label="Kazancım">
                    <template #default="scope">
                        <p class="paragraph-xs c-strong-950">

                        </p>
                    </template>
                </AppTableColumn>
                <AppTableColumn label="Katılımcı Kazancı">
                    <template #default="scope">
                        <p class="paragraph-xs c-strong-950">
                            {{scope.row.file_size}}
                        </p>
                    </template>
                </AppTableColumn>
                <AppTableColumn label="Katılımcı Yüzdesi">
                    <template #default="scope">
                        <div class="flex flex-col items-start">

                        </div>
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


</template>

<style lang="postcss" scoped>

</style>
