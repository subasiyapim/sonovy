<template>

  <span v-html="icon">

  </span>


</template>
<script setup>
const props = defineProps({
  icon: {
    required: true
  }
});
</script>
