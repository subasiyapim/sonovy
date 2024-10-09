<template>

  <div>
    <div class="flex items-center mb-4">
      <div class="flex-1 flex items-center gap-7">

        <div>
          <span class="label-xs c-sub-600"> Durum:</span>

          <select
              class="smallSelect mt-1 inline !appearance-none  iconless paragraph-xs !font-semibold border-none focus:outline-none focus:ring-0 c-blue-500">
            <option>7. Sayfa</option>
            <option>8. Sayfa</option>
            <option>9. Sayfa</option>
          </select>
        </div>

        <div>
          <span class="label-xs c-sub-600"> Tür:</span>

          <select
              class="smallSelect mt-1 inline !appearance-none  iconless paragraph-xs !font-semibold border-none focus:outline-none focus:ring-0 c-blue-500">
            <option>7. Sayfa</option>
            <option>8. Sayfa</option>
            <option>9. Sayfa</option>
          </select>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <AppTextInput placeholder="Ara...">
          <template #icon>
            <SearchIcon color="var(--sub-600)"/>
          </template>
        </AppTextInput>
        <PrimaryButton @click="$emit('addNewClicked',$event)" v-if="showAddButton"> Ekle</PrimaryButton>
      </div>
    </div>
  </div>
  <table class="w-full appTable">
    <thead>
    <tr class="border border-white-600">
      <th class="bg-white-500" v-for="(column, index) in columns" :key="index">
        <div class="flex items-center gap-2 justify-start">
          {{ column.props.label }}
          <TableOrderIcon color="var(--sub-600)"/>
        </div>
      </th>
    </tr>
    </thead>
    <tbody>
    <tr v-for="(row, rowIndex) in data" :key="rowIndex">
      <td v-for="(column, colIndex) in columns" :key="colIndex">
        <render :rowIndex="rowIndex" :colIndex="colIndex" :row="row"></render>
      </td>
    </tr>
    </tbody>
  </table>
  <template v-if="!(data == null || data.length <= 0)">
    <div v-if="!isClient" class="flex items-center  c-sub-600">
      <p class="w-28">
        Sayfa 1 of {{ Math.ceil((tableData.total / tableData.per_page)) }}
      </p>
      <div class="flex flex-1 justify-center  gap-3">

        <a :disabled="!tableData.first_page_url" :href="tableData.first_page_url"
           class="p-2 radius-8 w-10 h-10 flex items-center justify-center">
          <ArrowDoubleLeftIcon color="var(--sub-600)"/>

        </a>
        <a :disabled="!tableData.prev_page_url" :href="tableData.prev_page_url"
           class="p-2 radius-8 w-10 h-10 flex items-center justify-center">
          <ChevronLeftIcon color="var(--sub-600)"/>

        </a>


        <Link :href="route('control.artists.index',{page:p})"
              v-for="p in Math.ceil((tableData.total / tableData.per_page)) >= 7 ? 6 : Math.ceil((tableData.total / tableData.per_page))"
              class="p-2 radius-8 w-10 h-10 border border-soft-200-500 flex items-center justify-center">
          {{ p }}
        </Link>


        <a :disabled="!tableData.next_page_url" :href="tableData.next_page_url"
           class="p-2 radius-8 w-10 h-10 flex items-center justify-center">
          <ChevronRightIcon color="var(--sub-600)"/>

        </a>
        <a :disabled="!tableData.last_page_url" :href="tableData.last_page_url"
           class="p-2 radius-8 w-10 h-10 flex items-center justify-center">
          <ArrowDoubleRightIcon color="var(--sub-600)"/>

        </a>


      </div>
      <div class="w-28 max-w-xs mx-auto">

        <select id="options" name="options"
                class="mt-1 block w-full pl-3 pr-10 py-2 paragraph-sm border border-soft-200 focus:outline-none  radius-8">
          <option v-for="page in Math.ceil((tableData.total / tableData.per_page))">{{ page }}. Sayfa</option>
        </select>
      </div>
    </div>
  </template>

  <div v-if="data == null || data.length <= 0" class="h-[400px] flex flex-col items-center justify-center gap-8">
    <img src="@/assets/images/empty_state.png" class="w-32 h-32">
    <slot name="empty"/>
  </div>
</template>

<script setup>

import {computed, useSlots, ref, onMounted, h} from 'vue';
import {
  TableOrderIcon,
  SearchIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  ArrowDoubleLeftIcon,
  ArrowDoubleRightIcon
} from '@/Components/Icons';
import {AppTextInput} from '@/Components/Form';
import {router, Link} from '@inertiajs/vue3';
import {PrimaryButton} from '@/Components/Buttons'

let params = new URLSearchParams(window.location.search)

const queries = ref({
  'page': params.get('page') ?? 1,
  'limit': params.get('limit') ?? null,
  'sort': params.get('sort') ?? null,
  'date': params.get('date') ?? null,
  'order': params.get('order') ?? null,
  'mine': params.get('mine') ?? null,
  's': params.get('s') ?? null,

})
const totalPage = ref(16)
const props = defineProps({
  modelValue: {
    type: Object,
    required: true
  },
  isClient: {
    default: false,
  },
  showAddButton: {
    default: true,
  }
})
const emits = defineEmits(['update:modelValue', 'addNewClicked']);

const tableData = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})

const data = ref(props.isClient ? tableData.value : tableData.value.data)

const slots = useSlots()

const columns = ref(slots.default().map((tab) => tab));

onMounted(() => {
  slots.default().forEach((element, index) => {
    element.props['row'] = data.value[index];
  });
});

const render = (e) => {

  return h(slots.default()[e.colIndex], {
    row: data.value[e.rowIndex],
    index: e.rowIndex
  }, slots.default()[e.colIndex].children);
};


</script>

