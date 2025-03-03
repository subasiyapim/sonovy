<template>
  <div class="my-0.5">

    <a :href="path" class="flex items-center gap-2 menuItem" :class="checkIsActive ? 'active' : ''"
       @click="onClick">
      <component :is="icon"/>
      <div v-if="isSidebarOpen" class="flex-1">
        <p class="label-sm">{{ title }}</p>
      </div>
      <div v-if="hasSlot('sub') && isSidebarOpen">
        <ChevronRightIcon
            :class="{
                    'transform rotate-90 transition-transform duration-300': isSubMenuOpen || checkIsActive,
                    'transform rotate-0 transition-transform duration-300': !isSubMenuOpen
                }"
        />
      </div>
    </a>
    <div v-if="hasSlot('sub')" class="  " :class="isSidebarOpen ? 'ms-4 border-l border-[#C8C7C6]' :''">
      <transition name="dropdown">
        <div v-show="isSubMenuOpen || checkIsActive" :class="isSidebarOpen ? 'ps-3' : ''" class=" my-3 flex flex-col">
          <slot name="sub"/>
        </div>
      </transition>

    </div>
  </div>
</template>

<script setup>
import {DashboardIcon, ChevronRightIcon, ChevronDownIcon} from '@/Components/Icons'
import {useSlots, ref, computed} from 'vue'

const props = defineProps({
  wrapper: {},
  title: {type: String},
  active: {type: Boolean},
  path: {type: String, default: '#'},
  icon: {},
  active: {
    default: false,
  },
  isSidebarOpen: { type: Boolean, default: true }


})

const slots = useSlots()
const hasSlot = (name) => {
  return !!slots[name];
}

const isSubMenuOpen = ref(false);
const onClick = (e) => {

  isSubMenuOpen.value = !isSubMenuOpen.value;
}

const checkIsActive = computed(() => {
  return route().current().split(".").includes(props.wrapper);

})
</script>


<style scoped>

</style>
