<template>
  <AdminLayout class="">
    <div class="flex items-start gap-4 mb-12">
      <AppCard class="flex-1">
        <template #header>
          <p class="font-normal leading-3 text-sm">Toplam Yayın Sayısı</p>
          <span class="font-semibold leading-8 text-2xl">{{ statistics.product_count }} Yayın</span>
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
        <template #body>

                <Vue3Apexcharts height="120" :options="options" :series="series"></Vue3Apexcharts>

         </template>
      </AppCard>

      <AppCard class="flex-1">
        <template #header>
          <div class="flex items-center gap-2">
            <LabelsIcon color="var(--sub-600)"/>
            <p class="font-normal leading-3 text-sm">Plak Şirketleri</p>
            <span class="font-semibold leading-8 text-2xl">{{ statistics.label_count }}</span>
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
        <template #body>
            <hr class="my-3">
            <Vue3Apexcharts
            height="120"
            type="bar"
            width="100%"
            :options="barOptions"
            :series="barSeries"
            />

        </template>
      </AppCard>

      <AppCard class="flex-1 ">
        <template #header>
          <div class="flex mt-2 items-center gap-2">
            <ArtistsIcon color="var(--sub-600)"/>
            <p class="font-normal leading-3 text-sm">Sanatçılar</p>
          </div>

        </template>
        <template #tool>
          <div class="">

            <RegularButton>Detay</RegularButton>
          </div>
        </template>

         <template #body>
            <hr class="my-3">
            <div class="flex flex-col items-start">
                <p class="label-medium c-strong-950 !font-semibold">{{statistics.artist_count}}</p>
                <span class="paragraph-xs c-sub-600 mb-4">Toplam Sanatçılar</span>
                <div class="flex items-center gap-2">
                    <div class="flex -space-x-3 rtl:space-x-reverse">
                        <img class="w-8 h-8 border-2 border-white rounded-full " v-for="a in 3" src="/docs/images/people/profile-picture-5.jpg" alt="">
                    </div>
                    <span class="paragraph-xs c-sub-600">12 Yeni eklendi</span>
                </div>
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
            <component :is="statusData.find((e) => e.value == scope.row.status)?.icon"
                       :color="statusData.find((e) => e.value == scope.row.status)?.color"></component>
            <p class="subheading-xs c-sub-600">
              {{ statusData.find((e) => e.value == scope.row.status)?.label }}
            </p>
          </div>
        </template>
      </AppTableColumn>

      <AppTableColumn label="Yayın Bilgisi">
        <template #default="scope">
          <div class="flex gap-x-2 items-center">
            <div class="w-8 h-8 rounded overflow-hidden">
              <img class="w-10 h-10" alt=""
                   :src="scope.row.image ? scope.row.image.thumb : 'https://loremflickr.com/400/400'">
            </div>
            <a :href="route('control.catalog.products.show',scope.row.id)" class="paragraph-xs c-blue-500">
              {{ scope.row.album_name }}
            </a>
          </div>
        </template>
      </AppTableColumn>

      <AppTableColumn label="Sanatçı">
        <template #default="scope">
          <p class="paragraph-xs c-strong-950 px-1" v-for="artist in scope.row.main_artists">
            {{ artist.name }}
          </p>
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
            <CalendarIcon color="var(--sub-600)"/>
            <p class="paragraph-xs c-sub-600">

              {{ scope.row.physical_release_date }}
            </p>
          </div>
        </template>
      </AppTableColumn>
      <AppTableColumn label="Parçalar">
        <template #default="scope">
          <span class="paragraph-xs c-sub-600">{{ scope.row.songs?.length }} Parça</span>
        </template>
      </AppTableColumn>
      <AppTableColumn label="UPC/Katalog">
        <template #default="scope">
          <div class="flex flex-col justify-start ">
            <span class="paragraph-xs c-sub-600">{{ scope.row.upc_code }}</span>
            <span class="paragraph-xs c-sub-600">{{ scope.row.catalog_number }}</span>

          </div>
        </template>
      </AppTableColumn>
      <AppTableColumn label="Mağazalar">
        <template #default="scope">
          <div class="flex flex-col items-start paragraph-xs c-sub-600">
            <p>

              {{ scope.row.published_countries?.length ?? 0 }} Bölge
            </p>
            <p>
              {{ scope.row.download_platforms?.length }} Mağaza
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
          <PrimaryButton @click="openCreateProductDialog">
            <template #icon>
              <AddIcon/>
            </template>
            İlk Yayını Oluştur
          </PrimaryButton>
        </div>


      </template>
    </AppTable>
  </AdminLayout>
  <ProductDialog v-model="isCreateProductDialogOn" v-if="isCreateProductDialogOn"></ProductDialog>

</template>

<script setup>
import {ref} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppTable from '@/Components/Table/AppTable.vue';
import {ProductDialog} from '@/Components/Dialog';
import {RegularButton} from '@/Components/Buttons';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {PrimaryButton} from '@/Components/Buttons'
import Vue3Apexcharts from 'vue3-apexcharts'
import {
  AddIcon,
  LabelsIcon,
  CalendarIcon,
  EditLineIcon,
  WarningIcon,
  RetractedIcon,
  ArtistsIcon,
  AudioIcon,
  MusicVideoIcon,
  RingtoneIcon,
  CheckFilledIcon
} from '@/Components/Icons'
import {AppCard} from '@/Components/Cards'
import {usePage} from '@inertiajs/vue3';

const props = defineProps({
  statistics: Object,
})

const data = ref([
  {
    name: "asdasd"
  },
  {
    name: "ikinci"
  },
])
const isCreateProductDialogOn = ref(false);
const openCreateProductDialog  = () => {
    isCreateProductDialogOn.value = true;
}
const statusData = ref([
  {
    label: "Taslak",
    value: 1,
    icon: EditLineIcon,
    color: "#FF8447",

  },
  {
    label: "İnceleniyor",
    value: 2,
    icon: EditLineIcon,
    color: "#FF8447",
  },
  {
    label: "Yayınlandı",
    value: 3,
    icon: CheckFilledIcon,
    color: "#335CFF",
  },
  {
    label: "Reddedildi",
    value: 4,
    icon: WarningIcon,
    color: "#FB3748",
  },
  {
    label: "Geri Çekildi",
    value: 5,
    icon: RetractedIcon,
    color: "#717784",
  },
  {
    label: "Planlandı",
    value: 6,
    icon: CheckFilledIcon,
    color: "#FF8447",
  }
]);
const options = ref({
  chart: {
    type: 'area',
    height: 120,
     toolbar: {
      show: false, // Hides zoom and other toolbar options
    },
  },
   dataLabels: {
    enabled: false
  },
   colors: ['#5BCF82'], // Custom color for the line
   stroke: {
    curve: 'smooth',

  },
  fill: {
    type: 'gradient', // Enable gradient fill
    gradient: {
      shade: 'light',
      type: 'vertical', // Linear vertical gradient
      shadeIntensity: 0.4,


      inverseColors: false,
      opacityFrom: 0.8, // Opaque at the line
      opacityTo: 0, // Fully transparent at the bottom
      stops: [0, 100], // Gradient starts from the line (0%) to bottom (100%)
    },
  },

   grid: {
    show: true,
    xaxis: {
      lines: {
        show: true, // Enable vertical grid lines
      },
    },
    yaxis: {
      lines: {
        show: false, // Disable horizontal grid lines
      },
    },
    borderColor: '#CCCCCC', // Custom color for grid lines
    strokeDashArray: 3, // Make grid lines dashed
  },
  yaxis: {
    labels: {
      show: false, // Hides the Y-axis labels
    },
  },
  xaxis: {
    categories: ['O', 'Ş', 'M', 'N', 'M','H'],
  axisBorder: {
      show: false,
    },
    axisTicks: {
      show: false,
    },
  },
});

const series = ref([
  {
    name: "Yayın Sayısı",
    data: [44, 55, 41, 17, 15],
  },
]);




const barOptions = ref({
  chart: {
    type: 'bar', // Specify the chart type as 'bar'

    toolbar: {
      show: false, // Hide zoom and other toolbar options
    },
  },

  plotOptions: {
    bar: {
      horizontal: true, // Bars will come out from the y-axis
      barHeight: '70%', // Adjust bar thickness
      borderRadius: 2, // Rounded corners for bars
      distributed: true,
    },
  },
  dataLabels: {
    enabled: false, // Hide values on bars
  },
  xaxis: {
    categories: ['ABC', 'XYZ', 'TYZ'], // Labels for the y-axis
     labels: {
      enabled: false,  // Disable category labels under the bars
    },
     axisBorder: {
      show: false, // Hide the axis border
    },
    legend:{
        show:false,
    },
    axisTicks: {
      show: false, // Hide the x-axis ticks
    },
  },
  yaxis: {
    labels: {
      style: {
        colors: ['#333'], // Custom color for y-axis labels
        fontSize: '12px',
      },
    },
  },
  grid: {
    xaxis: {
      lines: {
        show: true, // Show vertical grid lines
      },
    },
     yaxis: {
      lines: {
        show: false, // Show vertical grid lines
      },
    },
  },
  colors: ['#1A7544', '#49A668', '#5BCF82'], // Custom colors for the bars
  legend: {
    show: false, // Hide the legend
  },
});

const barSeries = ref([
  {
     name: "Yayın Sayısı",
    data: [30, 70, 50], // Data points for the bars
  },
]);

</script>

<style lang="scss" scoped>

</style>
