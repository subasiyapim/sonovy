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

            <select id="options" name="options"
                    class="block w-full pl-3 pr-10  paragraph-xs border border-soft-200 focus:outline-none  radius-8">
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
            <LabelsIcon color="var(--sub-600)"/>
            <p class="font-normal leading-3 text-sm">Toplam Yayın Sayısı</p>
          </div>

        </template>
        <template #tool>
          <div class="w-28 max-w-xs mx-auto">

            <select id="options" name="options"
                    class="block w-full pl-3 pr-10  paragraph-xs border border-soft-200 focus:outline-none  radius-8">
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
            <ArtistsIcon color="var(--sub-600)"/>
            <p class="font-normal leading-3 text-sm">Toplam Yayın Sayısı</p>
          </div>

        </template>
        <template #tool>
          <div class="w-28 max-w-xs mx-auto">

            <select id="options" name="options"
                    class="block w-full pl-3 pr-10  paragraph-xs border border-soft-200 focus:outline-none  radius-8">
              <option>7. Sayfa</option>
              <option>8. Sayfa</option>
              <option>9. Sayfa</option>
            </select>
          </div>
        </template>
      </AppCard>
    </div>
    <AppTable :showAddButton="false" v-model="usePage().props.products" :slug="route('control.catalog.products.index')">
      <AppTableColumn label="Tür" sortable="type">
        <template #default="scope">
          <div class="border border-soft-200 w-10 h-10 rounded-full flex items-center justify-center">
            <AudioIcon v-if="scope.row.type == 1" color="var(--sub-600)"/>
            <MusicVideoIcon v-if="scope.row.type == 2" color="var(--sub-600)"/>
            <RingtoneIcon v-if="scope.row.type == 3" color="var(--sub-600)"/>
          </div>
        </template>
      </AppTableColumn>
      <AppTableColumn label="Durum">
        <template #default="scope">

           <div class="border border-soft-200 rounded-lg px-2 py-1 flex items-center gap-2">
                <component :is="statusData.find((e) => e.value == scope.row.status)?.icon" :color="statusData.find((e) => e.value == scope.row.status)?.color"></component>
                <p class="subheading-xs c-sub-600">
                    {{statusData.find((e) => e.value == scope.row.status)?.label}}
                </p>
           </div>
        </template>
      </AppTableColumn>

      <AppTableColumn label="Yayın Bilgisi">
        <template #default="scope">
          <div v-if="scope.row.image" class="w-8 h-8 rounded overflow-hidden">
            <img :src="scope.row.image.thumb">
          </div>
          <a :href="route('control.catalog.products.show',scope.row.id)" class="paragraph-xs c-blue-500">
            {{ scope.row.album_name }}
          </a>
        </template>
      </AppTableColumn>

      <AppTableColumn label="Sanatçı">
        <template #default="scope">
          <span class="paragraph-xs c-strong-950" v-for="artist in scope.row.artists">{{ artist.name }}</span>
        </template>
      </AppTableColumn>

      <AppTableColumn label="Plak Şirketi">
        <template #default="scope">

          <span class="paragraph-xs c-sub-600">{{ scope.row.label?.name }}</span>

        </template>
      </AppTableColumn>

            <AppTableColumn label="Yayın Tarih">
                <template #default="scope">
                    <div v-if="scope.row.physical_release_date" class="flex items-center gap-3">
                        <CalendarIcon color="var(--sub-600)" />
                        <p class="paragraph-xs c-sub-600">

                            {{scope.row.physical_release_date}}
                        </p>
                    </div>
                </template>
            </AppTableColumn>
            <AppTableColumn label="Parçalar">
                <template #default="scope">
                    <span class="paragraph-xs c-sub-600" >{{scope.row.songs?.length}} Parça</span>
                </template>
            </AppTableColumn>
             <AppTableColumn label="UPC/Katalog">
                <template #default="scope">
                    <div class="flex flex-col justify-start ">
                        <span class="paragraph-xs c-sub-600" >{{scope.row.upc_code}} Parça</span>
                        <span class="paragraph-xs c-sub-600" >{{scope.row.catalog_number}} Parça</span>

                    </div>
                </template>
            </AppTableColumn>
             <AppTableColumn label="Mağazalar">
                <template #default="scope">
                   <div class="flex flex-col items-start paragraph-xs c-sub-600">
                         <p>

                            {{scope.row.published_countries?.length ?? 0}} Bölge
                        </p>
                        <p>
                            {{scope.row.download_platforms?.length}} Mağaza
                        </p>
                   </div>
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
import {AddIcon,LabelsIcon,CalendarIcon, EditLineIcon,WarningIcon,RetractedIcon,ArtistsIcon,AudioIcon,MusicVideoIcon,RingtoneIcon,CheckFilledIcon} from '@/Components/Icons'
import {AppCard} from '@/Components/Cards'
import {usePage} from '@inertiajs/vue3';

const data = ref([
  {
    name: "asdasd"
  },
  {
    name: "ikinci"
  },
])


const statusData = ref([
    {
        label: "Taslak",
        value: 1,
         icon:EditLineIcon,
        color:"#FF8447",

      },
      {
        label: "İnceleniyor",
        value: 2,
        icon:EditLineIcon,
        color:"#FF8447",
      },
      {
        label: "Yayınlandı",
        value: 3,
        icon:CheckFilledIcon,
        color:"#335CFF",
      },
      {
        label: "Reddedildi",
        value: 4,
        icon:WarningIcon,
         color:"##FB3748",
      },
      {
        label: "Geri Çekildi",
        value: 5,
         icon:RetractedIcon,
          color:"#717784",
      },
      {
        label: "Planlandı",
        value: 6,
        icon:CheckFilledIcon,
         color:"#FF8447",
      }
]);
</script>

<style lang="scss" scoped>

</style>
