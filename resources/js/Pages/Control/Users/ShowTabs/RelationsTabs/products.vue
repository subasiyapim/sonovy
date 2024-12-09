<template>
  <AppTable ref="productTable"
            v-model="tableData"
            @addNewClicked="openDialog"
            :buttonLabel="'Yayın Ata'"
            :isClient="true">
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

            <img :alt="scope.row.album_name"
                 :src="scope.row.image ? scope.row.image.thumb : scope.row.album_name ? defaultStore.profileImage(scope.row.album_name) : ''"
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
            <ActionButton :product="scope.row" @onDetached="onDetached(scope)" />
      </template>
    </AppTableColumn>
    <template #empty>
      <div class="flex flex-col items-center justify-center gap-8">
        <div>
          <h2 class="label-medium c-strong-950">Henüz yayınız bulunmamaktadır.</h2>
          <h3 class="paragraph-medium c-neutral-500">Oluşturucağınız tüm yayınlar burada listelenecektir.</h3>
        </div>

      </div>


    </template>
  </AppTable>
  <AssignUserProductModal @done="onDone" v-if="isAssignModalOn" :user_id="usePage().props.user.id" v-model="isAssignModalOn" ></AssignUserProductModal>
</template>

<script setup>
import AppTable from '@/Components/Table/AppTable.vue';
import {AssignUserProductModal} from '@/Components/Dialog';
import {IconButton} from '@/Components/Buttons';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {computed, ref} from 'vue';
import {useDefaultStore} from "@/Stores/default";
import { usePage} from '@inertiajs/vue3';
import ActionButton from './Components/products_action_button.vue';
import {toast} from 'vue3-toastify';
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

const defaultStore = useDefaultStore();
const props = defineProps({
  modelValue: {}
});

const emits = defineEmits(['update:modelValue']);
const isAssignModalOn = ref(false);
const tableData = computed({
  get: () => props.modelValue,
  set: (val) => emits('update:modelValue', value)
})

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

const openDialog =  () => {
    isAssignModalOn.value = true;
};

const onDone = (e) => {
    e.forEach(element => {
        tableData.value.push(element);
    });
}

const onDetached = (scopeRow) => {
    tableData.value.splice(scopeRow.index);
    toast.success("İşlem başarılı")
};

</script>
<style scoped>

</style>
