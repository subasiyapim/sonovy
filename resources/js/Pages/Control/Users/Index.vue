<template>
  <AdminLayout :showDatePicker="false"
  :filters="appTableConfig.filters"
  title="Kulanıcılar" >

    <AppTable  ref="usersTable" :showAddButton="true"
            :buttonLabel="'Yeni Kullanıcı Ekle'"
            :config="appTableConfig"
             @addNewClicked="openAddDialog"
            :renderSubWhen="renderSubWhen"
              v-model="usePage().props.users" :slug="route('control.user-management.users.index')">

        <template #sub="scope">
            <NestedTable v-model="scope.row.children"></NestedTable>
        </template>
        <AppTableColumn label="Kullanıcı Adı" sortable="type">
            <template #default="scope">
            <div class="flex justify-start items-center gap-3 w-full">
                <div class="w-10 h-10 rounded-full overflow-hidden">
                <img :alt="scope.row.name"
                    :src="scope.row.image ? scope.row.image.thumb : defaultStore.profileImage(scope.row.name)"
                >
                </div>
                <div class="flex flex-col items-start ">
                    <a :href="route('control.user-management.users.show',scope.row.id)"
                        class="paragraph-xs c-blue-500 mb-0.5">{{ scope.row.name }}</a>
                    <span class="c-sub-600 paragraph-xs mb-2">{{scope.row.email}}</span>

                    <button class="c-blue-500 label-xs" @click="usersTable.toggleShowSub(scope.index)" v-if="scope.row?.children?.length>0">
                        {{scope.row?.children?.length}} {{ __('control.user.view_sub_users') }}

                    </button>

                </div>
            </div>
            </template>

        </AppTableColumn>

        <AppTableColumn label="Kullanıcı Rolü" sortable="type">
            <template #default="scope">
                <template v-for="role in scope.row.roles">
                    <div class="px-3 py-1 rounded-full" :class="role.code == 'super_admin' ? 'bg-[#CAC0FF]' : (role.code == 'admin' ? 'bg-[#D8E5ED]' : 'bg-[#C0D5FF]')">
                    <p class="paragraph-xs" :class="role.code == 'super_admin' ? 'text-[#351A75]' : (role.code == 'admin' ? 'text-[#060E2F]' : 'text-[#122368]')">  {{role.name}}</p>
                    </div>
                </template>
            </template>
        </AppTableColumn>

        <AppTableColumn label="Hakediş/Realizasyon" sortable="type">
            <template #default="scope">

                <div class="flex items-center gap-1 label-sm">
                    <span class="paragraph-xs c-soft-400">%{{scope.row.commission_rate}}/</span>
                    <span class="paragraph-xs c-strong-950">%{{scope.row.real_commission_rate}}</span>

                </div>
            </template>
        </AppTableColumn>
        <AppTableColumn label="Kayıt Tarihi" sortable="type">
            <template #default="scope">
            <span class="paragraph-xs c-strong-950"> {{scope.row.created_at}}</span>
            </template>
        </AppTableColumn>
        <AppTableColumn label="Durum" sortable="type" width="80">
            <template #default="scope">
                <div class="flex items-center gap-2  rounded-lg px-3 py-1" :class="scope.row.status == 0 ? 'bg-red-500 ' :' border border-soft-200'">
                    <span class="paragraph-xs " :class="scope.row.status == 0 ? 'text-white' :'c-strong-950'">•</span>
                    <span class="paragraph-xs " :class="scope.row.status == 0 ? 'text-white' :'c-sub-600'">{{scope.row.status_text}}</span>
                </div>
            </template>
        </AppTableColumn>
        <AppTableColumn label="Aksiyon" align="right">
            <template #default="scope">

                <IconButton @click="deleteRow(scope.row)">
                <TrashIcon color="var(--sub-600)"/>
                </IconButton>
                <IconButton @click="editRow(scope.row)">

                    <EditIcon color="var(--sub-600)"/>

                </IconButton>
            </template>
        </AppTableColumn>
    </AppTable>
  </AdminLayout>
  <UserModal :user="choosenUser" @done="onDone" @update="onUpdate" v-model="isUserModalOn" v-if="isUserModalOn"></UserModal>

</template>

<script setup>
import {ref,computed} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppTable from '@/Components/Table/AppTable.vue';
import NestedTable from '@/Components/Table/NestedTable.vue';
import {UserModal} from '@/Components/Dialog';
import {RegularButton} from '@/Components/Buttons';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {PrimaryButton, IconButton} from '@/Components/Buttons'
import Vue3Apexcharts from 'vue3-apexcharts'
import {useDefaultStore} from "@/Stores/default";
import {
  AddIcon,
  EditIcon,
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
import  'moment/dist/locale/tr';
moment.locale('tr');
const usersTable = ref();
const defaultStore = useDefaultStore();
const isUserModalOn = ref(false);

const props = defineProps({
  statistics: Object,
  filters: {
    type: Array,
  }
})

const choosenUser = ref(null)
const appTableConfig = computed(() => {
  return {
    filters: props.filters,
  }
})

const openAddDialog = () => {
    isUserModalOn.value = true;
}
const deleteRow = (row) => {
  usersTable.value.removeRowDataFromRemote(row);
}
const editRow = (row) => {
    choosenUser.value = row;
    isUserModalOn.value = true;
}



const data = ref([
  {
    name: "asdasd"
  },
  {
    name: "ikinci"
  },
])

const renderSubWhen = (row) => {
    return row.children?.length > 0;
}
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
    categories: [],
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
    data: [],
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
    categories: [],
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
});

const barSeries = ref([
  {
    name: "Yayın Sayısı",
    data: [],
  },
]);


const onUpdate = (e) => {
    usersTable.value.editRow(e);
}
const onDone = (e) => {
    usersTable.value.addRow(e);
}

</script>

<style lang="scss" scoped>

</style>
