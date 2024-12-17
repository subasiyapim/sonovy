<template>

  <div ref="selectContainer" v-click-outside="handleClickOutside"
       class="relative w-full flex h-9 border-text-input flex items-center radius-8 c-white-500 px-2.5 ">
    <div v-if="hasSlot('icon')">
      <slot name="icon"/>
    </div>
    <form @submit="onSubmit">
        <input v-model="textValue" @click="open" @input="onInput" class="absolute inset-0 radius-8 border-none focus:ring-0 appSelectInput c-strong-950"  :placeholder="placeholder">
    </form>

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


  </div>
  <div v-if="showTags" class="flex flex-wrap items-center gap-2 mt-2">

    <StatusBadge v-for="(el,index) in element" :showClose="true" @close="spliceElement(index)" :showIcon="false">
      <span
          class="label-xs c-sub-600">{{ el }}</span>
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
    default: {
        data:[],
    }
  },
  modelValue: {},
  placeholder: {type: String},
  type: {},
  disabled: {},
})

const textValue = ref();
const emits = defineEmits(['update:modelValue','change'])
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
  get: () => props.config?.data.filter((e) => element.value.includes(e)),
  set: (value) => element.value
});


const onSubmit = (e) => {
    e.preventDefault();
    e.stopPropagation();
    insertData(textValue.value)
    textValue.value = "";
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

const spliceElement = (index) => {
    element.value.splice(index,1)
    instance.update();

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

const chooseValue = (val) => {

  const vIndex = element.value.findIndex((el) => el == val);
    console.log("VALL",val);
    console.log("IBNDEX",vIndex);

  if (vIndex < 0) {
    element.value.push(val);

  } else {
    element.value.splice(vIndex, 1);
  }
  element.value = JSON.parse(JSON.stringify(element.value));

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
