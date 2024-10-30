<template>
    <AdminLayout class="">
        <div class="flex items-start gap-4 mb-12">
            <AppCard class="flex-1">
                <template #header>
                     <p class="font-normal leading-3 text-sm">Toplam Yayın Sayısı</p>
                    <span class="font-semibold leading-8 text-2xl">0</span>
                </template>
                 <template #tool>
                   <div class="w-28 max-w-xs mx-auto">

                        <select id="options" name="options" class="block w-full pl-3 pr-10  paragraph-xs border border-soft-200 focus:outline-none  radius-8">
                            <option>7. Sayfa</option>
                            <option>8. Sayfa</option>
                            <option>9. Sayfa</option>
                        </select>
                    </div>
                </template>
            </AppCard>

             <AppCard class="flex-1">
                <template #header>
                   <div class="flex items-center gap-2">
                        <LabelsIcon color="var(--sub-600)" />
                          <p class="font-normal leading-3 text-sm">Toplam Yayın Sayısı</p>
                   </div>

                </template>
                 <template #tool>
                   <div class="w-28 max-w-xs mx-auto">

                        <select id="options" name="options" class="block w-full pl-3 pr-10  paragraph-xs border border-soft-200 focus:outline-none  radius-8">
                            <option>7. Sayfa</option>
                            <option>8. Sayfa</option>
                            <option>9. Sayfa</option>
                        </select>
                    </div>
                </template>
            </AppCard>

             <AppCard class="flex-1">
                <template #header>
                    <div class="flex items-center gap-2">
                         <ArtistsIcon color="var(--sub-600)" />
                          <p class="font-normal leading-3 text-sm">Toplam Yayın Sayısı</p>
                   </div>

                </template>
                 <template #tool>
                   <div class="w-28 max-w-xs mx-auto">

                        <select id="options" name="options" class="block w-full pl-3 pr-10  paragraph-xs border border-soft-200 focus:outline-none  radius-8">
                            <option>7. Sayfa</option>
                            <option>8. Sayfa</option>
                            <option>9. Sayfa</option>
                        </select>
                    </div>
                </template>
            </AppCard>
        </div>
        <AppTable v-model="usePage().props.products" :slug="route('control.catalog.products.index')">
            <AppTableColumn label="Tür" sortable="type">
                <template #default="scope">
                    {{scope.row.type}}
                </template>
            </AppTableColumn>
            <AppTableColumn label="Durum">
                <template #default="scope">
                    {{scope.row.status}}
                </template>
            </AppTableColumn>

            <AppTableColumn label="Yayın Bilgisi">
                <template #default="scope">
                    <a :href="route('control.catalog.products.form.edit',[1,scope.row.id])"  class="paragraph-xs c-blue-500">
                        {{scope.row.album_name}}
                    </a>
                </template>
            </AppTableColumn>

            <AppTableColumn label="Sanatçı">
                <template #default="scope">
                    -
                </template>
            </AppTableColumn>

            <AppTableColumn label="Plak Şirketi">
                <template #default="scope">

                </template>
            </AppTableColumn>
            <AppTableColumn label="Yayın Tarih">
                <template #default="scope">

                </template>
            </AppTableColumn>
            <AppTableColumn label="Parçalar">
                <template #default="scope">

                </template>
            </AppTableColumn>
             <AppTableColumn label="UPC/Katalog">
                <template #default="scope">

                </template>
            </AppTableColumn>
             <AppTableColumn label="Mağazalar">
                <template #default="scope">

                </template>
            </AppTableColumn>
            <template #empty>
                <div class="flex flex-col items-center justify-center gap-8">
                    <div>
                        <h2 class="label-medium c-strong-950">Henüz yayınız bulunmamaktadır.</h2>
                        <h3 class="paragraph-medium c-neutral-500">Oluşturucağınız tüm yayınlar burada listelenecektir.</h3>
                    </div>
                    <PrimaryButton>
                        <template #icon>
                                <AddIcon />
                        </template>
                        İlk Yayını Oluştur
                    </PrimaryButton>
                </div>
            </template>
        </AppTable>
    </AdminLayout>
</template>

<script setup>
import {ref} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {PrimaryButton} from '@/Components/Buttons'
import {AddIcon,LabelsIcon, ArtistsIcon} from '@/Components/Icons'
import {AppCard} from '@/Components/Cards'
import {usePage} from '@inertiajs/vue3';
const data =  ref([
    {
        name:"asdasd"
    },
     {
        name:"ikinci"
    },
])
</script>

<style lang="scss" scoped>

</style>
