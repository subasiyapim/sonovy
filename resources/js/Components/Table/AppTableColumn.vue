<template>
  <div class="flex items-center h-full" :class="align == 'center' ? 'justify-center' : (align == 'left' ? 'justify-start' : 'justify-end' )">
    <slot :row="row" :index="index" />
  </div>
</template>

<script setup>
import {ref,computed,onMounted} from 'vue';


const props = defineProps({
    row:{},
    index:{},
    align:{
        default:'left',
    },
    width:{

    }
})

</script>
