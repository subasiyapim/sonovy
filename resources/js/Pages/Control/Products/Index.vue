<template>
  <AdminLayout :showDatePicker="false" class="">

    <div class="flex grid grid-cols-3 gap-3 mb-5">
      <AppCard class="flex-1 w-full">
        <template #header>
          <p class="font-normal leading-3 text-sm">Toplam Yayın Sayısı</p>
          <span class="font-semibold leading-8 text-2xl">{{ statistics.product_count ?? 0 }} Yayın</span>
        </template>
        <template #tool>
          <div class="w-28 max-w-xs mx-auto">

            <select id="options" name="options"
                    class="block w-full pl-3 pr-10  paragraph-xs border border-soft-200 focus:outline-none  radius-8">
              <option>Haftalık</option>
              <option>Aylık</option>
              <option>Yıllık</option>
            </select>
          </div>
        </template>
        <template #body>

          <Vue3Apexcharts height="120" :options="options" :series="series"></Vue3Apexcharts>

        </template>
      </AppCard>

      <AppCard class="flex-1 w-full">
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
              <option>Haftalık</option>
              <option>Aylık</option>
              <option>Yıllık</option>
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

      <AppCard class="flex-1 w-full">
        <template #header>
          <div class="flex mt-2 items-center gap-2">
            <ArtistsIcon color="var(--sub-600)"/>
            <p class="font-normal leading-3 text-sm">Sanatçılar</p>
          </div>

        </template>


        <template #body>

          <hr class="my-3">
          <div class="flex flex-col items-start">
            <p class="label-medium c-strong-950 !font-semibold">{{ statistics.artists.length }}</p>
            <span class="paragraph-xs c-sub-600 mb-4">Toplam Sanatçılar </span>
            <div class="flex items-center gap-2">
              <div class="flex -space-x-3 rtl:space-x-reverse">
                <template v-for="artist in statistics.artists.slice(0,5)">
                  <img class="w-8 h-8 border border-soft-200 rounded-full "
                       :src="artist.image ? artist.image.thumb : defaultStore.profileImage(artist.name)" alt="">
                </template>
                <template v-if="statistics.artists.length > 5">
                     <span class="w-8 h-8 border-2 rounded-full bg-gray-500 flex items-center justify-center paragraph-xs text-white">
                     +{{statistics.artists.length-5}}
                     </span>
                 </template>
              </div>
              <span class="paragraph-xs c-sub-600">{{ statistics.artists.length }} Yeni eklendi</span>
            </div>
          </div>
        </template>
      </AppCard>
    </div>

    <AppTable ref="productTable" :showAddButton="false" :renderRowNoteText="renderRowNoteText"
              :showNoteIf="showNoteIfFn"
            :config="appTableConfig"
              v-model="usePage().props.products" :slug="route('control.catalog.products.index')">
      <AppTableColumn label="Tür" sortable="type">
        <template #default="scope">
          <div class="border border-soft-200 w-10 h-10 rounded-full flex items-center justify-center">
            <AudioIcon v-if="scope.row.type == 1" color="var(--sub-600)"/>
            <MusicVideoIcon v-if="scope.row.type == 2" color="var(--sub-600)"/>
            <RingtoneIcon v-if="scope.row.type == 3" color="var(--sub-600)"/>
          </div>
        </template>
      </AppTableColumn>
      <AppTableColumn label="Durum" sortable="status">
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

      <AppTableColumn label="Yayın Bilgisi" width="250">
        <template #default="scope">
          <div class="flex gap-x-2 items-start">
            <div class="w-8 h-8 rounded overflow-hidden">
              <img class="w-10 h-10" alt=""
                   :src="scope.row.image ? scope.row.image.thumb : 'https://loremflickr.com/400/400'">

              <img :alt="scope.row.album_name"
                   :src="scope.row.image ? scope.row.image.thumb : defaultStore.profileImage(scope.row.album_name)"
              >

            </div>
            <div class="flex flex-col flex-1 items-start justisy-start">
              <a :href="route('control.catalog.products.show',scope.row.id)" class="paragraph-xs c-blue-500">
                {{ scope.row.album_name }}
              </a>

              <div class=" paragraph-xs c-strong-950 ">
                <p>
                  <template v-for="(artist,artistIndex) in scope.row.main_artists">
                    {{ artist.name }}
                    <template v-if="artistIndex != scope.row.main_artists.length-1">,&nbsp;</template>
                  </template>
                </p>

              </div>
            </div>
          </div>
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
            <p class="paragraph-xs c-sub-600 whitespace-nowrap">
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
      <AppTableColumn label="UPC/Katalog" width="240">
        <template #default="scope">
          <div class="flex flex-col justify-start ">
            <span class="paragraph-xs c-sub-600">UPC:{{ scope.row.upc_code ?? 'Boş' }}</span>
            <span class="paragraph-xs c-sub-600">Katalog Numarası: {{ scope.row.catalog_number ?? 'Boş' }}</span>

          </div>
        </template>
      </AppTableColumn>
      <AppTableColumn label="Mağazalar">
        <template #default="scope">
          <div class="flex flex-col items-start paragraph-xs c-sub-600">
            <p>

              {{ scope.row.selected_count ?? 0 }} Bölge
            </p>
            <p>
              {{ scope.row.download_platforms?.length }} Mağaza
            </p>
          </div>
        </template>
      </AppTableColumn>
      <AppTableColumn label="Aksiyonlar" align="end">
        <template #default="scope">
          <IconButton :confirmDelete="true" @confirm="deleteProduct(scope.row)"
                      title="Ürünü Silmek İstediğine Emin misin?" description="">
            <TrashIcon color="var(--sub-600)"/>

          </IconButton>
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
import {ref,computed} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppTable from '@/Components/Table/AppTable.vue';
import {ProductDialog} from '@/Components/Dialog';
import {RegularButton} from '@/Components/Buttons';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {PrimaryButton, IconButton} from '@/Components/Buttons'
import Vue3Apexcharts from 'vue3-apexcharts'
import {useDefaultStore} from "@/Stores/default";
import {
  AddIcon,
  LabelsIcon,
  EditLineIcon,
  WarningIcon,
  RetractedIcon,
  ArtistsIcon,
  AudioIcon,
  MusicVideoIcon,
  RingtoneIcon,
  CheckFilledIcon,
  TrashIcon
} from '@/Components/Icons'
import {AppCard} from '@/Components/Cards'
import {usePage} from '@inertiajs/vue3';
import moment from 'moment';

const productTable = ref();
const defaultStore = useDefaultStore();

const props = defineProps({
  statistics: Object,
  filters: {
    type: Array,
  }
})

const appTableConfig = computed(() => {
  return {
    filters: props.filters,
  }
})
const deleteProduct = (row) => {
  productTable.value.removeRowDataFromRemote(row);

}
const showNoteIfFn = (row) => {
  if (row.note && row.status == 4) {
    return true;
  } else {
    return false;
  }
}
const renderRowNoteText = (row) => {
  return row.note
}
const data = ref([
  {
    name: "asdasd"
  },
  {
    name: "ikinci"
  },
])
const isCreateProductDialogOn = ref(false);
const openCreateProductDialog = () => {
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
    categories: usePage().props.statistics?.products.data.map((e) => e.label),
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
    data: usePage().props.statistics?.products.data.map((e) => e.value)
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
    categories: usePage().props.statistics?.labels.map((e) => e.label), // Labels for the y-axis
    labels: {
      enabled: false,  // Disable category labels under the bars
    },
    axisBorder: {
      show: false, // Hide the axis border
    },
    legend: {
      show: false,
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
   tooltip: {
    custom: function({ series, seriesIndex, dataPointIndex, w }) {

     return `<div class="p-3">
                <span>${usePage().props.statistics?.labels[dataPointIndex].name} : ${usePage().props.statistics?.labels[dataPointIndex].value}</span>
              </div>`;
    },
  },
});

const barSeries = ref([
  {
    name: "Yayın Sayısı",
    data: usePage().props.statistics?.labels.map((e) => e.value)
  },
]);

</script>

<style lang="scss" scoped>

</style>
