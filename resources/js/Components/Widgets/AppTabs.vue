


<script  setup>
    import {ref,onBeforeMount} from 'vue'
    const props = defineProps({
        tabs:{},
        slug:{}
    });
    const activetab = ref('metadata');
    const emits = defineEmits(['change'])
    const onTabClicked = (tab) => {
        emits('change',tab)
    };
    onBeforeMount(() => {
        if(props.slug){
            activetab.value = props.slug;
        }
    });
</script>

<template>
    <div class="px-8 flex items-center gap-5 cursor-pointer  border-b border-soft-200" >
        <div @click="onTabClicked(item)" class="label-sm  pb-3" :class="activetab == item.slug ? 'c-strong-950 border-b border-dark-green-500' : 'c-sub-600'" v-for="item in tabs">
            {{item.title}}
        </div>
    </div>
</template>

<style scoped>

</style>
