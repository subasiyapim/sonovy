<template>

  <div>

    <div class="flex items-center mb-4 ">
    <slot v-if="hasSlot('tableHeader')" name="tableHeader" />
      <div class="flex-1 flex items-center gap-7">
        <div v-for="(filter,index) in config?.filters" class="flex items-end">
          <span class="label-xs c-sub-600"> {{ filter.title }}:</span>

          <select
              @change="onFilterSelected($event,filter)"

              class="smallSelect pb-0 mt-1 inline !appearance-none  iconless paragraph-xs !font-semibold border-none focus:outline-none focus:ring-0 c-blue-500">
            <option :selected="!query[filter.param]">Tümü</option>
            <option v-for="option in filter.options" :selected="query[filter.param] == option.value"
                    :value="option.value">{{ option.label }}
            </option>
          </select>
        </div>
      </div>

      <div v-if="(showAddButton || hasSearch)" class="flex items-center gap-2">
        <div class="w-64">
          <AppTextInput v-if="hasSearch" @change="onSearch" @input="onInput" v-model="term" placeholder="Ara...">
            <template #icon>
              <SearchIcon color="var(--sub-600)"/>
            </template>
          </AppTextInput>
        </div>
        <div>
          <PrimaryButton class="w-auto" v-if="showAddButton" @click="$emit('addNewClicked',$event)">
            <template #icon>
              <AddIcon color="var(--dark-green-500)"/>
            </template>
            <p >{{ buttonLabel ?? 'Ekle' }}</p>
          </PrimaryButton>
        </div>
      </div>
    </div>
  </div>

 <div class="relative"  @dragenter="emits('dragenter',$e)" @dragleave="emits('dragleave',e)">
  <div class="relative" >
     <div ref="currentAppTable" class="overflow-x-scroll overflow-y-hidden w-full border border-soft-200 rounded-lg tableWrapper ">
        <table  class="w-full appTable">
            <thead>
                <tr class="border-b border-white-600">

                <th v-if="hasSelect" class="bg-white-500 w-6">


                    <button class="appCheckBox " @click="selectAll"
                            :class="selectedRowIndexes?.length == data?.length  ? 'checked' : ''">
                        <CheckIcon color="#fff" />
                    </button>
                </th>
                <th @click="onClickHeader(column)"
                    :style="column?.props.width ? {'width' : `${column?.props.width}px`} :{'width' : 'auto'}"
                    :class="column.props.sortable ? 'cursor-pointer' :''" class="bg-white-500" v-for="(column, index) in columns"
                    :key="index">

                    <div class="flex items-center gap-2 paragraph-xs c-sub-600"
                        :class="column.props.align == 'center' ? 'justify-center' : (column.props.align == 'right' ? 'justify-end' : 'justify-start' )">
                    {{ column.props.label }}
                    <TableOrderIcon v-if="column.props.sortable" color="var(--sub-600)"/>
                    </div>
                </th>
                </tr>
                </thead>
                <tbody>
                <template v-for="(row, rowIndex) in data" :key="rowIndex">
                    <tr class="tableRow"
                        :class="(selectedRowIndexes.includes(row) ? 'bg-white-600 border border-white-700' : ''),(showNoteIf != null && showNoteIf(row) ? '' : ( renderSubWhen != null && renderSubWhen(row) ? (data.isSelected ? '' : 'hasBorder') :'hasBorder')) ">
                        <td v-if="hasSelect" class="tableCell">

                        <button class="appCheckBox" :class="selectedRowIndexes.includes(row) ? 'checked' : ''"
                                @click="onSelectRow(row)">
                                <CheckIcon color="#fff" />
                        </button>
                        </td>
                        <td colspan="1" v-for="(column, colIndex) in columns" :key="colIndex" class="tableCell" :style="column.props.width ? {width:`${column.props.width}px`,display:'inline-block'} :{}">
                            <render :rowIndex="rowIndex" :colIndex="colIndex" :row="row"></render>
                        </td>


                    </tr>
                    <template v-if="!loadingApp && hasSlot('sub') && (renderSubWhen != null && renderSubWhen(row))" >
                        <tr class="hasBorder" :class="row.isSelected ? '' : 'hidden'">
                            <td :colspan="columns?.length+1" >
                                <slot name="sub" :index="rowIndex" :row="row" />
                            </td>
                        </tr>
                    </template>
                    <tr class="hasBorder" v-if="showNoteIf != null && showNoteIf(row)">
                        <td :colspan="columns?.length+1">
                        <div class="bg-red-50 rounded px-3 py-2 my-2 flex items-center gap-2">
                            <WarningIcon color="var(--error-500)"/>
                            <p class="paragraph-xs c-strong-950"> {{ renderRowNoteText(row) }}</p>

                        </div>
                        </td>
                    </tr>
                </template>

                <tr v-if="hasSlot('appends')">
                <td :colspan="columns?.length+1" class="!p-0">
                    <slot name="appends"/>
                </td>

                </tr>
                </tbody>
        </table>
        <div @click="scrollTable('left')" class="side-indicator left-indicator">
            <div class="w-5 h-5 rounded flex items-center justify-center border border-soft-200 bg-white"><ChevronLeftIcon color="var(--sub-600)" /> </div>
        </div>
        <div @click="scrollTable('right')" class="side-indicator right-indicator">
            <div class="w-5 h-5 rounded flex items-center justify-center border border-soft-200 bg-white "> <ChevronRightIcon color="var(--sub-600)" /> </div>
        </div>
    </div>
  </div>

  <div v-if="searching" class="h-[300px] flex flex-col items-center justify-center">
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

  <div v-else >
    <template v-if="!(data == null || data?.length <= 0)">
      <div v-if="!isClient" class="flex items-center  c-sub-600 mt-5">
        <p class="w-28 paragraph-sm c-sub-600">
          Sayfa {{ query.page }} of {{ Math.ceil((tableData.total / tableData.per_page)) }}
        </p>
        <div class="flex flex-1 justify-center  gap-3" >

          <a :disabled="!tableData.first_page_url" :href="tableData.first_page_url"
             class="p-2 radius-8 w-10 h-10 flex items-center justify-center">
            <ArrowDoubleLeftIcon color="var(--sub-600)"/>

          </a>
          <a :disabled="!tableData.prev_page_url" :href="tableData.prev_page_url"
             class="p-2 radius-8 w-10 h-10 flex items-center justify-center">
            <ChevronLeftIcon color="var(--sub-600)"/>

          </a>


          <Link :href="route(route().current(),{page:p})"
                v-for="p in Math.ceil((tableData.total / tableData.per_page)) >= 7 ? 6 : Math.ceil((tableData.total / tableData.per_page))"
                :class="query.page == p ? 'bg-weak-50' : 'bg-white border border-soft-200'"
                class="p-2 radius-8 w-10 h-10  flex items-center justify-center">
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
        <div class="w-28 max-w-xs mx-auto" >
          <select id="options" name="options"
                @change="onSelectChange"
                  class="mt-1 block w-full pl-3 pr-10 py-2 paragraph-sm border border-soft-200 focus:outline-none  radius-8">
            <option v-for="page in Math.ceil((tableData.total / tableData.per_page))" :value="page">{{ page }}. Sayfa</option>
          </select>
        </div>
      </div>
    </template>

    <div v-show="(showEmptyOnDrag || (data == null || data?.length <= 0) && !hasSlot('appends'))"
         class="h-[300px] flex flex-col items-center justify-center gap-8  " :class="(showEmptyOnDrag &&  data?.length > 0) ?  'absolute inset-0 bg-white' : ''">
      <img v-if="showEmptyImage" src="@/assets/images/empty_state.png" class="w-32 h-32">
      <slot name="empty"/>
    </div>
  </div>
 </div>

</template>

<script setup>

import {computed, useSlots, ref, onMounted, h,nextTick} from 'vue';
import {useCrudStore} from '@/Stores/useCrudStore';


import {
  TableOrderIcon,
  SearchIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  ArrowDoubleLeftIcon,
  ArrowDoubleRightIcon,
  AddIcon,
  WarningIcon,
  CheckIcon
} from '@/Components/Icons';

import {AppTextInput} from '@/Components/Form';
import {router, Link} from '@inertiajs/vue3';
import {PrimaryButton} from '@/Components/Buttons'
import {toast} from 'vue3-toastify';

const slots = useSlots()
const songs = ref([]);
const currentAppTable = ref();

const crudStore = useCrudStore();
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
  showNoteIf: {},
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
  },
  hasSearch: {
    default: true,
  },
  showEmptyImage: {
    default: true
  },
  buttonLabel: {
    default: null,
  },
  hasSelect: {
    default: false,
  },
  renderRowNoteText: {},
  showEmptyOnDrag:{
    default:false
  },
  renderSubWhen:{},
  scrollable:{
    default:true,
  }
})
const emits = defineEmits(['update:modelValue', 'addNewClicked', 'selectionChange','dragenter','dragleave']);
const loadingApp = ref(true);
const tableData = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})

const term = ref();
const scrollTable = (direction) => {
//    console.log(currentAppTable.value);
    const wrapper = currentAppTable.value;
    // const wrapper = document.querySelector(".tableWrapper");
    // console.log("WRAPPER",wrapper);

    let scrollOption = {  behavior: "smooth" }
    if(direction == 'left'){
        scrollOption['left'] = -100;
    }else {
          scrollOption['left'] = 100;
    }
    console.log("SCROOOLLL OOPTİON",scrollOption);

     wrapper.scrollBy(scrollOption);
}
if (params.get('s')) {
  term.value = params.get('s');
}
const data = ref(props.isClient ? tableData.value : tableData.value.data ?? []);


const columns = ref(slots.default().map((tab) => tab));

onMounted(() => {
  slots.default().forEach((element, index) => {
    if (data.value) {
      element.props['row'] = data.value[index];
    }

  });
});

const render = (e) => {
  return h(slots.default()[e.colIndex], {
    row: data.value[e.rowIndex],
    index: e.rowIndex,
    ...slots.default()[e.colIndex].props
  }, slots.default()[e.colIndex].children);
};

function deleteNullProperties(obj) {

  for (let key in obj) {
    if (obj.hasOwnProperty(key) && obj[key] === null) {
      delete obj[key];
    }
  }
}

const onSelectChange = (e) => {
    console.log(e.target.value);
    // route(route().current(),{page:e.target.value})

    query.value['page'] = e.target.value;
    getTableData();

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
const onSelectRow = (row, index) => {
  const findedIndex = selectedRowIndexes.value.findIndex((el) => el == row);
  if (findedIndex >= 0) {
    selectedRowIndexes.value.splice(findedIndex, 1);
  } else {
    selectedRowIndexes.value.push(row)

  }
  emits('selectionChange', selectedRowIndexes.value);
}
const selectAll = () => {
  if (data.value?.length == selectedRowIndexes.value?.length) {
    selectedRowIndexes.value = [];
  } else {
    deSelect();
    data.value.forEach(element => {
      const findedIndex = selectedRowIndexes.value.findIndex((el) => el == element);
      if (findedIndex < 0) selectedRowIndexes.value.push(element);
    });
  }
  emits('selectionChange', selectedRowIndexes.value);
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
const addRow = (row, direction = 'start') => {
  if (direction == 'end') data.value.push(row);
  else if (direction == 'start') data.value.unshift(row);

}
const editRow = (row) => {

  const findedIndex = data.value.findIndex((e) => e.id == row.id)
  if (findedIndex >= 0) {
    data.value[findedIndex] = row;
  }


}
const removeRowData = (row) => {
  const findedIndex = data.value.findIndex((el) => row == el);
  if (findedIndex >= 0) data.value.splice(findedIndex, 1);
}

const selectedRowIndexes = ref([]);
const removeRowDataFromRemote = async (row) => {

  const path = props.slug ?? route(route().current());
  console.log("PATHH",path);

  if (row.id) {
    try {
      const response = await crudStore.del(`${path}/${row.id}`);
      console.log("REPSONSE", response);
      const findedIndex = data.value.findIndex((el) => row.id == el.id);
      if (findedIndex >= 0) data.value.splice(findedIndex, 1);

      toast.success(response.message);
    } catch (error) {
      toast.error("HATAA");
    }

  } else {
    toast.error("Id sağlanmalı");
  }

}
const hasSlot = (name) => {
  return !!slots[name];
}

const deSelect = () => {
  selectedRowIndexes.value = [];
}
const selectRows = (arr) => {

  arr.forEach(i => {
    //console.log("EEE",i);
    console.log("DATA", data.value);

    selectedRowIndexes.value.push(data.value[i]);
  });

}
onMounted(() => {
  getFilterSelects();

    nextTick(() => {
        const wrapper = document.querySelector(".tableWrapper");

        // Create the side indicators
        const leftIndicator = wrapper.querySelector('.left-indicator');

        const rightIndicator = wrapper.querySelector('.right-indicator');


        const updateIndicators = () => {
            const maxScrollLeft = wrapper.scrollWidth - wrapper.offsetWidth;


            // Show/hide indicators based on the scroll position
            leftIndicator.style.display = wrapper.scrollLeft > 0 ? "flex" : "none";
            rightIndicator.style.display = wrapper.scrollLeft < maxScrollLeft ? "flex" : "none";


            const rows = wrapper.querySelectorAll('tr.tableRow');

            loadingApp.value = false;
            rows.forEach(element => {
                if (!element.offsetParent) return;


                element.querySelectorAll('td').forEach(td => {
                    td.style.height = `${element.offsetHeight}px`
                });
            });

        };

        if(props.scrollable){
            console.log("GELDİİ");

            // Initial check
            updateIndicators();
            // Update indicators on scroll and resize
            wrapper.addEventListener("scroll", updateIndicators);
            window.addEventListener("resize", updateIndicators);
        }else {
            loadingApp.value = false;

        }


    })
});


const toggleShowSub = (index) => {
  data.value[index].isSelected = !data.value[index].isSelected ?? true;
}
defineExpose({
  removeRowByIndex,
  removeRowData,
  search,
  addRow,
  removeRowDataFromRemote,
  editRow,
  deSelect,
  selectAll,
  selectRows,
  toggleShowSub
})
</script>
<style>
.appCheckBox {
  width: 14px;
  height: 14px;
  padding:1px;
  border-radius: 2.6px;
  background: white;
  box-shadow: 0px 2px 2px 0px #1B1C1D1F;
  border: 1px solid var(--soft-200);
  position: relative;
    display:flex;
    items:center;
    justify-content:center;
}

.appCheckBox.checked {
    background:var(--dark-green-500);
    border: 1px solid var(--dark-green-500);

}
.appCheckBox.checked .svg {
    display:block;
    width:12px;
    height:12px;
}

.appCheckBox.half:after {
  content: "";
  inset: 0;
  margin: auto;
  position: absolute;
  width: 8px;
  height: 3px;
  border-radius: 2.6px;
  background: var(--dark-green-500);
}
.side-indicator{
    position:absolute;
    width:48px;
    height:100%;
      border-radius:8px;
    justify-content:center;
    padding-top:10px;
    cursor:pointer;
}
.left-indicator{
    display:none;
    left:0px;
    top:0px;
    bottom:0px;
    margin-left:1px;
    margin-top:1px;
    background: linear-gradient(270deg, rgba(255, 255, 255, 0) 0%, #FFFFFF 100%);
}
.right-indicator{
    display:none;
    right:0px;
    top:0px;
    bottom:0px;
    margin-right:1px;
    margin-top:1px;

    background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, #FFFFFF 100%);
}
</style>

