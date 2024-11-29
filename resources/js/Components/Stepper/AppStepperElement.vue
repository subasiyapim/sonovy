<template>
    <div>

        <div @click="onTabClicked" class="flex items-center gap-3 cursor-pointer">
            <div class="w-5 h-5 rounded-full  flex items-center justify-center label-xs " :class="activeIndex == currentIndex ? 'bg-dark-green-500 text-white' :( activeIndex > currentIndex ? (showWarning ? 'bg-error-500' : 'bg-dark-green-600' ) : 'c-sub-600 bg-white border border-soft-200') ">
                <template v-if="activeIndex > currentIndex">
                    <template v-if="showWarning">
                        <WarningIcon />
                    </template>
                    <template v-else>

                        <CheckIcon color="#fff" />
                    </template>
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
    import {ChevronRightIcon,CheckIcon,WarningIcon} from '@/Components/Icons'

const props = defineProps({
    title:{},
    activeIndex:{},
    currentIndex:{},
    showIcon:{
        default:true
    },
    showWarning:{
        default:false
    }
})
const emits = defineEmits(['change'])

const onTabClicked = () => {
    // currentIndex.value = props.activeIndex;
    emits('change',props.currentIndex);
}
</script>

<style lang="scss" scoped>

</style>
