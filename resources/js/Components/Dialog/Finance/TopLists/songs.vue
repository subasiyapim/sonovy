<template>
  <BaseDialog
    :showClose="true"
    width="800px"
    v-model="isDialogOn"
    height="min-content"
    align="center"
    title="Parçalara Göre Gelir"
    :description="formattedDates"
  >
    <template #icon>
      <RingtoneIcon color="var(--dark-green-950)" />
    </template>
    <hr />

    <div class="p-5">
      <table class="w-full" v-if="!loading">
        <thead>
          <tr>
            <td class="label-sm c-strong-950 !font-semibold">Albüm Adı</td>
            <td class="label-sm c-strong-950 !font-semibold">ISRC</td>
            <td class="label-sm c-strong-950 !font-semibold">Sanatçı Adı</td>
            <td class="label-sm c-strong-950 !text-end !font-semibold">Oran</td>
            <td class="label-sm c-strong-950 !font-semibold ps-3">Gelir</td>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(song, index) in visibleData" :key="index">
            <td class="py-3">
              <span class="label-sm c-strong-950">{{ song.song_name }}</span>
            </td>
            <td class="py-3" style="width: 130px">
              <span class="paragraph-xs c-strong-950">{{ song.isrc_code }}</span>
            </td>
            <td class="py-3">
              <span class="paragraph-xs c-strong-950">{{ song.artist_name }}</span>
            </td>
            <td style="width: 25%">
              <div class="flex items-center gap-2">
                <div class="w-[64%]">
                  <AppProgressIndicator color="#335CFF" :modelValue="song.percentage" />
                </div>
                <span class="paragraph-xs c-sub-600 !text-end flex-1">
                  {{ song.percentage }}%
                </span>
              </div>
            </td>
            <td class="ps-3">
              <span class="paragraph-xs c-sub-600">{{ song.earning }}</span>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-else class="h-64 flex items-center justify-center"> Yükleniyor </div>

      <!-- Load More Button -->
      <div v-if="visibleCount < tableData.length" class="flex justify-center mt-4">
       <button @click="loadMore" class=" c-blue-500 label-sm ">Daha Fazla Yükle</button>
      </div>
    </div>
  </BaseDialog>
</template>

<script setup>
import BaseDialog from '@/Components/Dialog/BaseDialog.vue';
import moment from 'moment';
import 'moment/dist/locale/tr';
moment.locale('tr');
import { RingtoneIcon } from '@/Components/Icons';
import { useCrudStore } from '@/Stores/useCrudStore';
import { computed, ref, onMounted } from 'vue';
import { AppProgressIndicator } from '@/Components/Widgets';

const crudStore = useCrudStore();
const props = defineProps({
  modelValue: { default: false },
  choosenDates: {},
  formattedDates: {},
});

const emits = defineEmits(['update:modelValue']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value),
});

const loading = ref(false);
const tableData = ref([]);
const visibleCount = ref(20); // Initially show 10 items

// Computed property to show only a part of the data
const visibleData = computed(() => tableData.value.slice(0, visibleCount.value));

const loadMore = () => {
  const nextBatch = 10; // Load 10 more items
  visibleCount.value = Math.min(visibleCount.value + nextBatch, tableData.value.length);
};

const getData = async () => {
  loading.value = true;
  try {
    const response = await crudStore.get(route('control.finance.analysis.show'), {
      slug: 'top_songs',
      request_type: 'view',
      start_date: props.choosenDates ? props.choosenDates[0] : moment().subtract(1, 'year'),
      end_date: props.choosenDates ? props.choosenDates[1] : moment(),
    });
    tableData.value = response;
  } catch (error) {
    console.error('Error fetching data:', error);
  }
  loading.value = false;
};

onMounted(() => {
  getData();
});
</script>
