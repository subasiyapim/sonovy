<template>
      <div class="pb-6">
            <p class="label-xl c-strong-950">
              Şimdi, yayına dahil eklemek istediğiniz parçaları yükleyin 👍🏻
            </p>
        </div>
    <div class="flex gap-10">

        <div class="flex-1 flex flex-col overflow-scroll gap-6">
        <AppTable v-model="songs" :showEmptyImage="false" :isClient="true" :hasSearch="false" :showAddButton="false">
                <AppTableColumn label="#">
                    <template #default="scope"> asds{{ scope.row }}</template>
                </AppTableColumn>
                <AppTableColumn label="Parça Adı">
                    <template #default="scope">{{scope.row}}</template>
                </AppTableColumn>

                <AppTableColumn label="Süre">
                    <template #default="scope">{{scope.row}}</template>
                </AppTableColumn>

                <AppTableColumn label="Durum">
                    <template #default="scope">{{scope.row}}</template>
                </AppTableColumn>

                <AppTableColumn label="Aksiyonlar" align="end">
                    <template #default="scope">{{scope.row.name}}</template>
                </AppTableColumn>

                <template #empty>
                    <TusUploadInput></TusUploadInput>
                </template>
            </AppTable>
        </div>

    </div>
</template>

<script setup>
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {computed,ref} from 'vue';
import {FormElement} from '@/Components/Form';
import {AddIcon} from '@/Components/Icons'
import {TusUploadInput} from '@/Components/Form'
const songs = ref([]);

</script>

<style lang="scss" scoped>

</style>
