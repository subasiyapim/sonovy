<script lang="ts" setup>

import {ref, onMounted, onUnmounted} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {PrimaryButton} from '@/Components/Buttons'
import {UploadIcon} from '@/Components/Icons'
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';

const data = ref([]);
</script>

<template>
    <AdminLayout :showGoBack="false" :showDatePicker="false" :title="__('control.finance.imports.header')" parentTitle="Finans">
        <template #toolbar>
            <PrimaryButton><template #icon> <UploadIcon color="var(--dark-green-500)" /></template>Rapor İçe Aktar</PrimaryButton>
        </template>



        <AppTable  v-model="data"  :isClient="true" :hasSearch="false" :showAddButton="false">
                <AppTableColumn label="Platform">
                    <template #default="scope">
                          <p class="paragraph-xs c-strong-950"></p>
                    </template>
                </AppTableColumn>

                <AppTableColumn label="Rapor Tarihi">
                    <template #default="scope">
                        <p class="paragraph-xs c-strong-950"></p>
                    </template>
                </AppTableColumn>


                <AppTableColumn label="Rapor Adı">
                    <template #default="scope">
                         <p class="paragraph-xs c-strong-950"></p>
                    </template>
                </AppTableColumn>

                <AppTableColumn label="Hatalar">
                    <template #default="scope">
                        <p class="paragraph-xs c-strong-950"></p>
                    </template>
                </AppTableColumn>
                <AppTableColumn label="Ücret">
                    <template #default="scope">
                        <p class="paragraph-xs c-strong-950"></p>
                    </template>
                </AppTableColumn>
                <AppTableColumn label="Boyut">
                    <template #default="scope">
                        <p class="paragraph-xs c-strong-950"></p>
                    </template>
                </AppTableColumn>
                <AppTableColumn label="İşlem Tarihi">
                    <template #default="scope">
                        <p class="paragraph-xs c-strong-950"></p>
                    </template>
                </AppTableColumn>
                <AppTableColumn label="Durum">
                    <template #default="scope">
                        <p class="paragraph-xs c-strong-950"></p>
                    </template>
                </AppTableColumn>
                <AppTableColumn label="Aksiyon">
                    <template #default="scope">
                        <p class="paragraph-xs c-strong-950"></p>
                    </template>
                </AppTableColumn>

                <template #empty>
                    Tarih Detayı Bulunamadı
                </template>
            </AppTable>
    </AdminLayout>


</template>

<style lang="postcss" scoped>

</style>
