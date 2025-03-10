<template>

  <div ref="selectContainer" v-click-outside="handleClickOutside"
       class="relative w-full flex h-9 border-text-input flex items-center radius-8 c-white-500 px-2.5 ">
    <div v-if="hasSlot('icon')">
      <slot name="icon"/>
    </div>

    <!-- <input v-if="!hasSlot('model')" @click="open" class="absolute inset-0 radius-8 border-none focus:ring-0 appSelectInput c-strong-950" :value="getShowLabel" :placeholder="placeholder"> -->
    <div @click="open"
         :class="disabled ? 'bg-weak-50' : '' "
         class="absolute inset-0 flex items-center px-3 radius-8 border-none focus:ring-0 appSelectInput c-strong-950">
      <span class="label-sm !font-normal " :class="disabled ? 'c-soft-300' : 'c-soft-400' "
            v-if="!element">{{ placeholder }}</span>
      <slot v-else-if="hasSlot('model')" name="model" :scope="element"/>
      <span v-else class="label-sm !font-normal">{{ getShowLabel }}</span>
    </div>
    <div
        class="selectButton bg-blue-300 flex items-center justify-end border-none focus:outline-none focus:border-none focus:border-transparent focus:ring-0 h-full  w-full bg-transparent label-sm !font-normal cursor-pointer">
      <!-- <div  class="flex-1 pointer-events-none c-soft-400">
          <span v-if="!element">{{placeholder}}</span>
      </div> -->
      <ChevronRightIcon

          color="var(--soft-400)"
          :class="{
                    'transform rotate-90 transition-transform duration-300': isOpen,
                    'transform -rotate-90 transition-transform duration-300': !isOpen
                }"/>
    </div>

    <transition name="dropdown">
      <teleport to="body">
        <div v-if="isOpen"
            class="absolute dropdownWrapper left-0 right-0 border border-soft-200 radius-8 p-2 bg-white z-[100000]"
            :style="dropdownStyle">
          <AppTextInput @click.stop v-if="config.hasSearch" v-model="searchTerm" @change="onSearchChange" class="w-full mb-2"
                        :placeholder="config?.searchPlaceholder">
            <template #icon>
              <SearchIcon color="var(--soft-400)"/>
            </template>
          </AppTextInput>
          <template v-if="config.data != null && config.data.length > 0">
            <div v-if="hasSlot('first_child')" class="mb-2">
              <slot @click="onClickFirst" name="first_child"/>
            </div>
          </template>

          <div class="max-h-[250px] overflow-scroll">
            <div v-for="el in getFilteredData" :data-id="el[config.value ?? 'value']" @click="chooseValue(el)"
                 :class="checkIfChecked(el[config.value ?? 'value']) ? 'bg-white-500' :  'bg-white'"
                 class="p-2 cursor-pointer selectMenuItem radius-8 flex items-center gap-2 my-0.5">


              <slot v-if="hasSlot('option')" name="option" :scope="el"/>
              <span v-else class="paragraph-sm c-strong-950"> {{ el[config?.label ?? 'label'] }}</span>
            </div>
          </div>
          <div v-if="getFilteredData?.length <= 0"
               class="flex flex-col gap-5 items-center justify-center min-h-[224px] ">
            <img src="@/assets/images/empty_state.png" class="w-16 h-16">
            <p class="label-medium c-strong-950">Maalesef sonuç bulunamadı:(</p>
            <slot name="empty"/>
          </div>
        </div>
      </teleport>
    </transition>

  </div>

</template>

<script setup>
import {useSlots, ref, computed, onMounted, onBeforeUnmount, nextTick,getCurrentInstance} from 'vue'
import {ChevronRightIcon, SearchIcon} from '@/Components/Icons'
import AppTextInput from './AppTextInput.vue';
import {StatusBadge} from '@/Components/Badges'

const instance = getCurrentInstance()

const searchTerm = ref(null);
const slots = useSlots()
const props = defineProps({
  config: {
    default: {}
  },
  modelValue: {},
  placeholder: {type: String},
  type: {},
  disabled: {
    default: false
  }
})
const emits = defineEmits(['update:modelValue', 'change'])
const element = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value),
})
const isOpen = ref(false);
const dropdownDirection = ref('bottom');  // Direction for dropdown
const dropdownStyle = ref({});  // Inline style for dropdown (e.g., top or bottom)
const selectContainer = ref(null);  // Reference to the select container element

const getShowLabel = computed(() => {
  const listOfOptions = remoteDatas.value ?? props.config?.data;


  const findedElement = listOfOptions?.find((e) => e[props.config.value ?? 'value'] == element.value);
  return findedElement == null ? '' : findedElement[props.config.label ?? 'label'];
});

const onClickFirst = () => {
    isOpen.value = false;
}
const dropdownWrapper = ref(null);

const open = async () => {
  if (props.disabled) return;
  isOpen.value = true;
  if (isOpen.value) {

    await nextTick(); // Wait until DOM has updated
    adjustDropdownDirection();

  }
    // Attach scroll event listener when dropdown opens
    window.addEventListener("scroll", handleScroll, true);


}
const handleScroll = () => {

  if (isOpen.value) {
    adjustDropdownDirection();
  }
};

const remoteDatas = ref(null)

const localData = ref(props.config?.data || []);
const getFilteredData = computed(() => {

  if (searchTerm.value) {
    return localData.value.filter((el) =>
      el[props.config.label ?? 'label']
        ?.toLocaleLowerCase('tr-TR')
        .includes(searchTerm.value.toLocaleLowerCase('tr-TR'))
    );
  } else {
    return localData.value;
  }
});
const appendOptions = (list) => {
    console.log("BURASI ÇALIŞTI",list);

    // props.config.data = list;
    localData.value = [ ...list];
    instance.update();

}
const checkIfChecked = computed(() => {
  return (rowValue) => {
    return rowValue == element.value;
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
      window.removeEventListener("scroll", handleScroll, true);
    }
  }
}

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
  element.value = val[props.config.value ?? 'value'];
  isOpen.value = false;
  emits('change', val)


}

// Handle window resize event to recheck dropdown direction
const handleResize = () => {
  if (isOpen.value) {
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



// Add event listener for window resize on mounted, and remove on unmounted
onMounted(() => {
  window.addEventListener('resize', handleResize);
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize);
})

const insertData = (e) => {
    props.config?.data.push(e);
    chooseValue(e);
    instance.update();
}


const observer = ref(null);

onMounted(() => {
  observer.value = new IntersectionObserver(
    ([entry]) => {
      if (entry.isIntersecting) {
        adjustDropdownDirection();
      }
    },
    { threshold: 1.0 }
  );

  if (selectContainer.value) {
    observer.value.observe(selectContainer.value);
  }

  window.addEventListener("scroll", handleScroll, true);
});

onBeforeUnmount(() => {
  if (observer.value && selectContainer.value) {
    observer.value.unobserve(selectContainer.value);
  }
  window.removeEventListener("scroll", handleScroll, true);
});


const closeDropdown = () => {
     isOpen.value = false;
}
defineExpose({
  insertData,
  appendOptions,
  closeDropdown
});

</script>



<style scoped>
.selectMenuItem:hover {
  background: var(--white-500);
}


</style>
