<template>
    <div>

        <div @click="onTabClicked" class="flex items-center gap-3 cursor-pointer">
            <div class="w-5 h-5 rounded-full  flex items-center justify-center label-xs " :class="activeIndex == currentIndex ? 'bg-dark-green-500 text-white' :(activeIndex > currentIndex ? 'bg-dark-green-600' : 'c-sub-600 bg-white border border-soft-200') ">
                <template v-if="activeIndex > currentIndex">
                    <CheckIcon color="#fff" />
                </template>
                <template v-else>
                    {{props.currentIndex+1}}
                </template>

            </div>
            <span class="paragpraph-sm c-strong-950">{{title}} </span>
        </div>
    </div>
    <ChevronRightIcon v-if="showIcon" color="var(--soft-400)" />
</template>

<script setup>
import {ref} from 'vue';
    import {ChevronRightIcon,CheckIcon} from '@/Components/Icons'

const props = defineProps({
    title:{},
    activeIndex:{},
    currentIndex:{},
    showIcon:{
        default:true
    },
})
const emits = defineEmits(['change'])

const onTabClicked = () => {
    // currentIndex.value = props.activeIndex;
    emits('change',props.currentIndex);
}
</script>

<style lang="scss" scoped>

</style>
