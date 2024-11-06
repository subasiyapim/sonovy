<template>
  <div class="text-sm radius-8  flex items-center gap-2 py-1 px-2 border" :class="getBg">
    <template v-if="showIcon">

      <CheckFilledIcon v-if="type == 'success'" color="var(--dark-green-500)"/>
      <PendingIcon v-else-if="type == 'pending'" color="var(--orange-700)"/>
    </template>
    <slot name="default"/>
    <span v-if="showClose" class="cursor-pointer " @click="$emit('close')">
        <CloseIcon color="var(--soft-400)"/>
    </span>
  </div>
</template>

<script setup>
import {CheckFilledIcon, CloseIcon,PendingIcon} from '@/Components/Icons'
import {computed} from 'vue';
const props = defineProps({
  showIcon: {
    default: true // empty
  },
  showClose: {
    default: false
  },
  type:{
    default: 'success' // pending,danger etc #FFF1EB
  }
})



const getBg = computed(() => {
    if(props.type == 'pending'){
        return 'bg-orange-100';
    }
     if(props.type == 'pending'){
        return 'bg-white  border-soft-200 border ';
    }
})

</script>

<style lang="scss" scoped>

</style>
