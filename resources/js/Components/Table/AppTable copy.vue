<template>
    <div class="appTable">
        <div class="flex flex-col md:flex-row c-text-tertiary items-center my-3">
            <div>
                <h1 v-if="!hasSlot('title')" class="display-sm ">{{ title }}</h1>
                <slot v-else name="title"/>

            </div>
            <div class="flex flex-col md:flex-row flex-1 ms-auto w-full">
                <div class="flex flex-1 justify-end my-3 md:my-0">
                    <slot name="header"/>
                </div>
                <div v-if="hasSearch" class="relative flex items-center flex-1 md:flex-none">
                    <input  @input="onSearchStart" v-model="term"
                           class="termInput defaultInput w-full my-3 md:my-0 rounded-lg ps-9 "
                           @change="onSearch" :placeholder="__('panel.general.search')" v-debounce="400"></input>
                    <div class="absolute left-0 top-0 bottom-0 flex items-center ps-3">
                        <svg width="14" height="14" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M16.5 16.5L13.5834 13.5833M15.6667 8.58333C15.6667 12.4954 12.4954 15.6667 8.58333 15.6667C4.67132 15.6667 1.5 12.4954 1.5 8.58333C1.5 4.67132 4.67132 1.5 8.58333 1.5C12.4954 1.5 15.6667 4.67132 15.6667 8.58333Z"
                                stroke="currentColor" stroke-width="1.66667" stroke-linecap="round"
                                stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <el-table
            @selection-change="$emit('selection-change',$event)"
            v-loading="searching"
            @sort-change="onSortCalled"
            :data="!hasPaginate ? data : data.data"
            :rowClassName="rowClassName"
            :default-sort="{ prop: currentSortProp, order: currentSortOrder+'ending' }"
            class="w-full rounded-lg">


            <slot/>

            <template v-if="hasPaginate" #append>
                <div class="flex justify-between p-3">
                    <span>
                        {{ data.total }}
                        {{ __('panel.datatable.from_item') }}
                        {{ data.from }}
                        {{ __('panel.datatable.with') }}
                        {{ data.to }}
                        {{ __('panel.datatable.you_see_elements_between') }}
                    </span>
                    <el-pagination :default-current-page="data.current_page ?? 1" @change="onChange"
                                   :prev-text="__('panel.datatable.previous')"
                                   :next-text="__('panel.datatable.next')" background layout="prev, pager, next"
                                   :page-size="data.per_page"
                                   :total="data.total ?? 0"/>
                </div>
            </template>
        </el-table>
    </div>

</template>

<!--
-->

<script setup>
import {computed, ref, useSlots, nextTick, onMounted} from 'vue';

const slots = useSlots()
import {usePage, router} from '@inertiajs/vue3';


const term = ref()
const currentSortProp = ref();
const currentSortOrder = ref();
const slotNames = Object.keys(slots);
const elementsCount = slotNames.reduce((acc, slotName) => {
    // If the slot is not empty, count its elements
    if (slots[slotName]) {
        acc += slots[slotName].length;
    }
    return acc;
}, 0);
const searching = ref(false);



const onSearchStart = () => {
    if (!searching.value) {
        searching.value = true;
    }


}

const hasSlot = (name) => {
    return !!slots[name];
}
const props = defineProps({
    modelValue: {
        default: {}
    },
    listKey: {},
    title: {
        default: ''
    },
    slug: {
        default: '',
    },
    extraQuery: {},
    rowClassName: {},
    hasPaginate: {
        default:true,
    },
    hasSearch:{
        default:true,
    }
});

const emits = defineEmits(['update:modelValue','selection-change']);


const data = computed({
    get: () => props.modelValue,
    set: (value) => emits('update:modelValue', value)
})


let params = new URLSearchParams(window.location.search)


if (params.get('sort')) {
    currentSortOrder.value = params.get('order')
    currentSortProp.value = params.get('sort');
}


const query = ref({
    'page': params.get('page') ?? 1,
    'limit': params.get('limit') ?? null,
    'sort': params.get('sort') ?? null,
    'date': params.get('date') ?? null,
    'order': params.get('order') ?? null,
    'mine': params.get('mine') ?? null,
    's': params.get('s') ?? null,


})


if (params.get('s')) {
    term.value = params.get('s');
    nextTick(() => {
        const appTable = document.querySelector('.appTable');
        if (appTable) {
            const termInput = appTable.querySelector('.termInput');

            if (termInput) {
                termInput.focus();
            }
        }

    });
}

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


const getTableData = () => {
    deleteNullProperties(query.value);
    router.visit(props.slug, {
        data: query.value,

        preserveScroll: true,
    });

}

const onChange = (e) => {
    query.value.page = e;
    getTableData();
}

const onSortCalled = (e) => {

    console.log(e.order);
    if (e.order) {
        query.value.sort = e.prop;
        query.value.order = e.order == "ascending" ? "asc" : "desc";
    } else {
        query.value.sort = null;
        query.value.order = null;
    }

    deleteNullProperties(query.value);
    getTableData();
}

const onSearch = (e) => {
    searching.value = false;

    query.value.s = e.target.value;
    deleteNullProperties(query.value);
    getTableData();
}
onMounted(() => {
    if (props.extraQuery) {
        Object.keys(props.extraQuery).map((key) => {
            if (props.extraQuery[key]) {
                query.value[key] = props.extraQuery[key]
            } else {
                query.value[key] = params.get(key) ?? null;
            }
        });
    }


});

defineExpose({
    getTableData,
    setQuery
});

</script>


<style lang="scss">



</style>
