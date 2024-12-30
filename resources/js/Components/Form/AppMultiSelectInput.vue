<template>
    <div ref="selectContainer" v-click-outside="handleClickOutside"
        class="relative w-full flex h-9 border-text-input flex items-center radius-8 c-white-500 px-2.5 ">
        <div v-if="hasSlot('icon')">
        <slot name="icon"/>
        </div>

        <div @click="open"
            class="absolute inset-0 flex items-center px-3 radius-8 border-none focus:ring-0 appSelectInput c-strong-950">
            <span class="label-sm !font-normal " :class="disabled ? 'c-soft-300' : 'c-soft-400' "
                    v-if="element.length <= 0">{{ placeholder }}</span>
            <slot v-else-if="hasSlot('disabled')" name="disabled"/>
            <div v-else-if="hasSlot('model')" class="flex items-center w-full">
            <div class="flex-1 overflow-hidden"> <slot  name="model" :scope="choosenAll"/></div>

            <div class="bg-white w-6 flex justify-center ms-auto">
                <ChevronRightIcon

                    color="var(--soft-400)"
                    :class="{
                                    'transform rotate-90 transition-transform duration-300': isOpen,
                                'transform -rotate-90 transition-transform duration-300': !isOpen
                                }"/>
            </div>
            </div>
        <span v-else class="label-sm !font-normal">{{ getShowLabel }}</span>
        </div>
        <div
            class="selectButton bg-blue-300 flex items-center justify-end border-none focus:outline-none focus:border-none focus:border-transparent focus:ring-0 h-full  w-full bg-transparent label-sm !font-normal cursor-pointer">

        <ChevronRightIcon
            v-if="disabled"
            color="var(--soft-400)"
            :class="{
                            'transform rotate-90 transition-transform duration-300': isOpen,
                        'transform -rotate-90 transition-transform duration-300': !isOpen
                        }"/>
        </div>

        <transition name="dropdown">
        <teleport to="body">
            <div v-if="isOpen"
                :class="dropdownDirection"
                class="absolute dropdownWrapper left-0 right-0 border border-soft-200 radius-8 p-2 bg-white z-10"
                :style="dropdownStyle">
            <AppTextInput v-if="config.hasSearch" v-model="searchTerm" @change="onSearchChange" class="w-full mb-2"
                            placeholder="Yayın, Artist ara...">
                <template #icon>
                <SearchIcon color="var(--soft-400)"/>
                </template>
            </AppTextInput>
            <template v-if="config.data != null && config.data.length > 0">
                <div v-if="hasSlot('first_child')" class="mb-2">
                <slot name="first_child"/>
                </div>
            </template>

            <div class="max-h-[250px] overflow-scroll">

                <div @click="chooseValue(el)" v-for="el in getFilteredData" :data-id="el[config.value ?? 'value']"
                    :class="checkIfChecked(el[config.value ?? 'value']) ? 'bg-white-500' :  'bg-white'"
                    class="p-2 cursor-pointer selectMenuItem radius-8 flex items-center gap-2">
                <div
                    :class="checkIfChecked(el[config.value ?? 'value']) ? 'bg-dark-green-600 border-dark-green-600' : 'bg-white  border-soft-200'"
                    class="w-3 h-3 border flex items-center justify-center  rounded-sm shadow">
                    <CheckIcon v-if="checkIfChecked(el[config.value ?? 'value'])" color="#fff"/>
                </div>
                <slot v-if="hasSlot('option')" name="option" :scope="el"/>
                <span v-else class="paragraph-sm c-strong-950"> {{ el[config?.label ?? 'label'] }}</span>
                </div>
            </div>
            <div v-if="getFilteredData?.length <= 0"
                class="flex flex-col gap-5 items-center justify-center min-h-[224px]">
                <img src="@/assets/images/empty_state.png" class="w-16 h-16">
                <p class="label-medium c-strong-950">Maalesef sonuç bulunamadı:(</p>
                <slot name="empty"/>
            </div>
            </div>
        </teleport>
        </transition>
    </div>

    <div v-if="showTags && config.data" class="flex flex-wrap items-center gap-2 mt-2">
        <StatusBadge v-for="(el,index) in element" :showClose="true" @close="chooseValue(config.data?.find((option) => option[config.value ?? 'value'] == el))" :showIcon="false">
        <span
            class="label-xs c-sub-600">{{ config.data?.find((option) => option[config.value ?? 'value'] == el)[config?.label ?? 'label'] }}</span>
        </StatusBadge>
    </div>

</template>

<script setup>
import {useSlots, ref, computed, onMounted, onBeforeUnmount, nextTick, getCurrentInstance} from 'vue'
import {ChevronRightIcon, SearchIcon, CheckIcon} from '@/Components/Icons'
import AppTextInput from './AppTextInput.vue';
import {StatusBadge} from '@/Components/Badges'

const instance = getCurrentInstance()

const slots = useSlots()
const props = defineProps({
  config: {
    default: {}
  },
  modelValue: {},
  placeholder: {type: String},
  type: {},
  disabled: {},
})
const emits = defineEmits(['update:modelValue'])
const element = computed({
  get: () => props.modelValue ?? [],
  set: (value) => emits('update:modelValue', value),
})

const showTags = ref(props.config?.showTags != null ? props.config?.showTags : true)
const isOpen = ref(false);
const dropdownDirection = ref('bottom');  // Direction for dropdown
const dropdownStyle = ref({});  // Inline style for dropdown (e.g., top or bottom)
const selectContainer = ref(null);  // Reference to the select container element
const onClose = () => {

}

const choosenAll = computed({
  get: () => props.config?.data.filter((e) => element.value.includes(e.value)),
  set: (value) => element.value
});
const onChangeForSearch = (e) => {

  searchTerm.value = e;
}
const searchTerm = ref();
const remoteDatas = ref(null)
const getFilteredData = computed(() => {

  if (searchTerm.value) {
    if (remoteDatas.value) {
      return remoteDatas.value;
    } else {
      if (props.config?.remote == null) {
        return props.config?.data?.filter((el) => el[props.config.label ?? 'label'].toLocaleLowerCase('tr-TR').includes(searchTerm.value.toLocaleLowerCase('tr-TR')))
      }
    }

  } else {
    console.log("SERACH TERM YOK");
    return props.config?.data
  }
})
const getShowLabel = computed(() => {

  let finalStr = '';
  const listOfOptions = (remoteDatas.value ?? props.config?.data) ?? [];
  const filteredData = listOfOptions.filter((e) => {
    return element.value.find((checkValue) => checkValue == e[props.config.value ?? 'value']);
  });
  filteredData.forEach((e, index) => {

    finalStr += `${index != 0 ? ' , ' : ''}${e[props.config.label ?? 'label']}`
  })
  return finalStr;
})
const open = async () => {
  if (props.disabled) return;
  isOpen.value = !isOpen.value;
  if (isOpen.value) {

    await nextTick(); // Wait until DOM has updated
    adjustDropdownDirection();

  }
}

const onSearchChange = async (e) => {

  if (props.config?.remote) {
    try {
      remoteDatas.value = await props.config?.remote(e)
    } catch (error) {
      remoteDatas.value = null;
    }
    props.config.data = remoteDatas.value;
  }

}

const checkIfChecked = computed(() => {
  return (rowValue) => {
    if (rowValue)
      return element.value.find((e) => e == rowValue);
    return false;
  }
})
const hasSlot = (name) => {
  return !!slots[name];
}

const handleClickOutside = (e) => {
  if (e.target?.closest('.dropdownWrapper')) {
    return;
  } else {
    if (isOpen.value) {
      isOpen.value = false;
    }
  }
}

// Function to adjust dropdown direction based on available space
const adjustDropdownDirection = () => {
  const dropdownHeight = 300;  // Assume dropdown max height is around 250px
  const rect = selectContainer.value.getBoundingClientRect();
  const viewportHeight = window.innerHeight;

  // Check available space below the trigger
  const spaceBelow = viewportHeight - rect.bottom;

  if (spaceBelow < dropdownHeight) {
    // Not enough space below, open upwards
    dropdownDirection.value = 'top';
    dropdownStyle.value = {
      bottom: `${viewportHeight - rect.top + window.scrollY}px`,  // Adjust for viewport and element height
      left: `${rect.left}px`,
      width: `${rect.width}px`
    };
  } else {
    // Enough space below, open downwards
    dropdownDirection.value = 'bottom';
    dropdownStyle.value = {
      top: `${rect.bottom + window.scrollY}px`,  // Position below the trigger
      left: `${rect.left}px`,
      width: `${rect.width}px`
    };
  }
}

const uniqueElements = computed(() => {
  const seen = new Set();
  return choosenAll.value.filter((item) => {
    const key = item.value; // Filtering by the `id` property
    if (seen.has(key)) {
      return false;
    }
    seen.add(key);
    return true;
  });
});
const chooseValue = (val) => {

  const v = val[props.config.value ?? 'value'];
  const vIndex = element.value.findIndex((el) => el == v);

  if (vIndex < 0) {
    element.value.push(v);
    choosenAll.value.push(val);
  } else {
    element.value.splice(vIndex, 1);
    choosenAll.value.splice(vIndex, 1);
  }
  element.value = JSON.parse(JSON.stringify(element.value));

    emits('change',uniqueElements.value)
}

// Handle window resize event to recheck dropdown direction
const handleResize = () => {
  if (isOpen.value) {
    adjustDropdownDirection();
  }
}

const insertData = (e) => {

    props.config?.data.push(e);
  chooseValue(e);
  instance.update();
}

// Add event listener for window resize on mounted, and remove on unmounted
onMounted(() => {


  window.addEventListener('resize', handleResize);
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize);
})
defineExpose({
  insertData,
})
</script>

<style scoped>
.selectMenuItem:hover {
  background: var(--white-500);
}
</style>
