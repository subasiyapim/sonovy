<template>
    <div>

        <a :href="path" class="flex items-center gap-2 menuItem" :class="checkIsActive ? 'active' : ''"  @click.prevet="onClick">
            <component :is="icon" />
            <div class="flex-1">
                <p>{{title}}</p>
            </div>
            <div v-if="hasSlot('sub')">
                 <ChevronRightIcon
                :class="{
                    'transform rotate-90 transition-transform duration-300': isSubMenuOpen || checkIsActive,
                    'transform rotate-0 transition-transform duration-300': !isSubMenuOpen
                }"
        />
            </div>
        </a>
       <div v-if="hasSlot('sub')" class=" border-l border-[#C8C7C6]  ms-4 ">
        <transition name="dropdown">
            <div v-show="isSubMenuOpen || checkIsActive" class="ps-3 my-3 flex flex-col">
                <slot name="sub" />
            </div>
        </transition>

       </div>
    </div>
</template>

<script setup>
    import {DashboardIcon,ChevronRightIcon,ChevronDownIcon} from '@/Components/Icons'
    import { useSlots ,ref,computed} from 'vue'

    const props = defineProps({
        wrapper:{},
        title:{type:String},
        active:{type:Boolean},
        path:{type:String,default:'#'},
        icon:{},
        active:{
            default:false,
        }

    })

    const slots = useSlots()
    const hasSlot = (name) => {
        return !!slots[name];
    }

    const isSubMenuOpen = ref(false);
    const onClick = (e) => {

            isSubMenuOpen.value = !isSubMenuOpen.value;
    }

    const checkIsActive =  computed(() => {
       return route().current().split(".").includes(props.wrapper);

    })
</script>



<style scoped>

</style>
