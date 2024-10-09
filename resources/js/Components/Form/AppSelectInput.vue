<template>
   <div ref="selectContainer"  v-click-outside="handleClickOutside" class="relative w-full flex h-9 border-text-input flex items-center radius-8 c-white-500 px-2.5 ">
        <div v-if="hasSlot('icon')">
            <slot name="icon" />
        </div>
       <input @click="open" class="absolute inset-0 radius-8 border-none focus:ring-0">
        <div class="selectButton flex items-center border-none focus:outline-none focus:border-none focus:border-transparent focus:ring-0 h-full  w-full bg-transparent label-sm cursor-pointer">
            <div class="flex-1 pointer-events-none c-soft-400">
                {{placeholder}}
            </div>
            <ChevronRightIcon
                color="var(--soft-400)"
                :class="{
                    'transform rotate-90 transition-transform duration-300': isOpen,
                    'transform rotate-0 transition-transform duration-300': !isOpen
                }" />
        </div>

        <transition name="dropdown">
            <div v-if="isOpen"
                :class="dropdownDirection"
                class="absolute selectButton left-0 right-0 border border-soft-200 radius-8 p-3 bg-white z-10"
                :style="dropdownStyle">
                <AppTextInput v-if="config.hasSearch" class="w-full mb-2" placeholder="Yayın, Artist ara...">
                    <template #icon><SearchIcon color="var(--soft-400)" /></template>
                </AppTextInput>
                <div v-if="config.data != null && config.data.length > 0" class="mb-2">
                    <slot v-if="hasSlot('first_child')" name="first_child" />
                </div>
                <div v-if="config.data != null && config.data.length > 0">
                    <div v-for="el in config.data" :data-id="el[config.value ?? 'value']" class="p-2 cursor-pointer selectMenuItem radius-8">
                       <span class="paragraph-sm c-strong-950"> {{el[config.label ?? 'label']}}</span>

                    </div>
                </div>
                <div v-else class="flex flex-col gap-5 items-center justify-center min-h-[224px]">
                    <img src="@/assets/images/empty_state.png" class="w-16 h-16">
                    <p class="label-medium c-strong-950">Maalesef sonuç bulunamadı:(</p>
                    <slot name="empty" />
                </div>
            </div>
        </transition>
   </div>
</template>

<script setup>
import { useSlots, ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { ChevronRightIcon, SearchIcon } from '@/Components/Icons'
import AppTextInput from './AppTextInput.vue';

const slots = useSlots()
const props = defineProps({
    config: {
        default: {}
    },
    modelValue: {
        type: Object,
    },
    placeholder: { type: String }
})
const emits = defineEmits(['update:modelValue'])
const element = computed({
    get: () => props.modelValue,
    set: (value) => emits('update:modelValue'),
})
const isOpen = ref(false);
const dropdownDirection = ref('bottom');  // Direction for dropdown
const dropdownStyle = ref({});  // Inline style for dropdown (e.g., top or bottom)
const selectContainer = ref(null);  // Reference to the select container element

const open = async () => {
      isOpen.value = !isOpen.value;
    if (isOpen.value) {

        await nextTick(); // Wait until DOM has updated
        adjustDropdownDirection();

    }else {


    }
}

const hasSlot = (name) => {
    return !!slots[name];
}

const handleClickOutside = () => {
    if(isOpen.value){
        console.log("GELDİİİİİ");
        isOpen.value = false;
    }
}

// Function to adjust dropdown direction based on available space
const adjustDropdownDirection = () => {
    const dropdownHeight = 250;  // Assume dropdown max height is around 250px
    const rect = selectContainer.value.getBoundingClientRect();
    const viewportHeight = window.innerHeight;

    if (rect.bottom + dropdownHeight > viewportHeight) {
        dropdownDirection.value = 'top';  // Set to open upwards
        dropdownStyle.value = { bottom: '42px' };  // Adjust for your layout
    } else {
        dropdownDirection.value = 'bottom';  // Set to open downwards
        dropdownStyle.value = { top: '42px' };  // Adjust for your layout
    }
}

// Handle window resize event to recheck dropdown direction
const handleResize = () => {
    if (isOpen.value) {
        adjustDropdownDirection();
    }
}

// Add event listener for window resize on mounted, and remove on unmounted
onMounted(() => {
    window.addEventListener('resize', handleResize);
})

onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize);
})
</script>

<style scoped>
.selectMenuItem:hover {
    background: var(--white-500);
}
</style>
