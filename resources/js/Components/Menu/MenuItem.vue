<template>
    <div>
        <a :href="route" class="flex items-center gap-2 menuItem" :class="active ? 'active' : ''"  @click.prevet="onClick">
            <component :is="icon" />
            <div class="flex-1">
                <p>{{title}}</p>
            </div>
            <div v-if="hasSlot('sub')">
                 <ChevronRightIcon
                :class="{
                    'transform rotate-90 transition-transform duration-300': isSubMenuOpen,
                    'transform rotate-0 transition-transform duration-300': !isSubMenuOpen
                }"
        />
            </div>
        </a>
       <div v-if="hasSlot('sub')" class=" border-l border-[#C8C7C6]  ms-4 ">
        <transition name="dropdown">
            <div v-show="isSubMenuOpen" class="ps-3 my-3 flex flex-col">
                <slot name="sub" />
            </div>
        </transition>

       </div>
    </div>
</template>

<script setup>
    import {DashboardIcon,ChevronRightIcon,ChevronDownIcon} from '@/Components/Icons'
    import { useSlots ,ref} from 'vue'

    defineProps({
        title:{type:String},
        active:{type:Boolean},
        route:{type:String,default:'#'},
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
</script>



<style scoped>

</style>
