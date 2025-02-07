<template>

  <span :style="{'width':width, 'height' : height}" v-html="icon">

  </span>


</template>
<script setup>
const props = defineProps({
  icon: {
    required: true
  },
  width:{},
  height:{}
});
</script>
