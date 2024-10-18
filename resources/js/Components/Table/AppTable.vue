<template>

  <div>

    <div class="flex items-center mb-4">
      <div class="flex-1 flex items-center gap-7">
        <div v-for="(filter,index) in config?.filters">
          <span class="label-xs c-sub-600"> {{ filter.title }}:</span>

          <select
              @change="onFilterSelected($event,filter)"

              class="smallSelect mt-1 inline !appearance-none  iconless paragraph-xs !font-semibold border-none focus:outline-none focus:ring-0 c-blue-500">
            <option :selected="!query[filter.param]">Seçiniz</option>
            <option v-for="option in filter.options" :selected="query[filter.param] == option.value"
                    :value="option.value">{{ option.label }}
            </option>
          </select>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <AppTextInput @change="onSearch" @input="onInput" v-model="term" placeholder="Ara...">
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
      <th @click="onClickHeader(column)" :class="column.props.sortable ? 'cursor-pointer' :''" class="bg-white-500"
          v-for="(column, index) in columns" :key="index">
        <div class="flex items-center gap-2 justify-start">
          {{ column.props.label }}
          <TableOrderIcon v-if="column.props.sortable" color="var(--sub-600)"/>
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
  <div v-if="searching" class="h-[400px] flex flex-col items-center justify-center">
    <div class="w-12 h-12">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
        <radialGradient id="a12" cx=".66" fx=".66" cy=".3125" fy=".3125" gradientTransform="scale(1.5)">
          <stop offset="0" stop-color="#43FF0D"></stop>
          <stop offset=".3" stop-color="#43FF0D" stop-opacity=".9"></stop>
          <stop offset=".6" stop-color="#43FF0D" stop-opacity=".6"></stop>
          <stop offset=".8" stop-color="#43FF0D" stop-opacity=".3"></stop>
          <stop offset="1" stop-color="#43FF0D" stop-opacity="0"></stop>
        </radialGradient>
        <circle transform-origin="center" fill="none" stroke="url(#a12)" stroke-width="8" stroke-linecap="round"
                stroke-dasharray="200 1000" stroke-dashoffset="0" cx="100" cy="100" r="70">
          <animateTransform type="rotate" attributeName="transform" calcMode="spline" dur="2" values="360;0"
                            keyTimes="0;1" keySplines="0 0 1 1" repeatCount="indefinite"></animateTransform>
        </circle>
        <circle transform-origin="center" fill="none" opacity=".2" stroke="#43FF0D" stroke-width="8"
                stroke-linecap="round" cx="100" cy="100" r="70"></circle>
      </svg>
    </div>
  </div>
  <div v-else>
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


          <Link :href="route('control.catalog.artists.index',{page:p})"
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
const searching = ref(false);
const query = ref({
  'page': params.get('page') ?? 1,
  'limit': params.get('limit') ?? null,
  'sort': params.get('sort') ?? null,
  'date': params.get('date') ?? null,
  'order': params.get('order') ?? null,
  'mine': params.get('mine') ?? null,
  's': params.get('s') ?? null,
  'e_date': params.get('s') ?? null,
  'e_date': params.get('e_date') ?? null,
  's_date': params.get('s_date') ?? null,
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
  slug: {
    default: null,
  },
  showAddButton: {
    default: true,
  },
  config: {
    type: Object,
  }
})
const emits = defineEmits(['update:modelValue', 'addNewClicked']);

const tableData = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})

const term = ref();

if (params.get('s')) {
  term.value = params.get('s');
}
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

function deleteNullProperties(obj) {

  for (let key in obj) {
    if (obj.hasOwnProperty(key) && obj[key] === null) {
      delete obj[key];
    }
  }
}

const setQuery = (key, value) => {
  query.value[key] = value;
}
const search = (key, value) => {

  if (key == 'daterange') {
    if (value == null) {
      query.value['s_date'] = null;
      query.value['e_date'] = null;
    } else {
      query.value['s_date'] = value[0];
      query.value['e_date'] = value[1];
    }

  } else {
    query.value[key] = value;
  }
  getTableData();
}
const getTableData = () => {

  const path = props.slug ?? route(route().current());
  console.log("QUERY VALUE", query.value);
  deleteNullProperties(query.value);
  router.visit(path, {
    data: query.value,
    preserveScroll: true,
  });
}

const onClickHeader = (column) => {

  if (column.props.sortable) {
    query.value['sort'] = column.props.sortable;
    query.value['order'] = params.get('order') ? (params.get('order') == 'asc' ? 'desc' : 'asc') : 'asc'
    // query.value['order'] = query.value['order'] ? (query.value['order'] == 'ascending' ? 'descending' :'ascending') : 'descending';
    getTableData();
  }


}

const onSearch = (e) => {

  query.value.s = e;
  searching.value = false;
  getTableData();
}

const onInput = (e) => {

  searching.value = true;
}

const getFilterSelects = () => {
  props.config?.filters?.forEach(element => {
    let filterParam = params.get(element.param);
    if (filterParam) {

      query.value[element.param] = filterParam;
    }
  });
}
const onFilterSelected = (event, filter) => {
  query.value[filter.param] = event.target.value;
  getTableData();
}

const removeRowByIndex = (index) => {
  data.value.splice(index, 1);
}
const removeRowData = (row) => {
  const findedIndex = data.value.findIndex((el) => row == el);
  if (findedIndex >= 0) data.value.splice(findedIndex, 1);
}

onMounted(() => {
  getFilterSelects();
});
defineExpose({
  removeRowByIndex,
  removeRowData,
  search
})
</script>

