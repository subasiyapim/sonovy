<template>

   <div class="w-full flex h-9 border-text-input flex items-center radius-8 c-white-500 relative">


        <input v-model="element" @input="onInput" @change="onChange" v-debounce="400" class="border-none focus:outline-none focus:border-none  focus:border-transparent focus:ring-0 h-full w-full bg-transparent label-sm c-strong-950" :type="type" :placeholder="placeholder">
        <div class="flex gap-1 pe-3">
            <button :class="choosenSpotify ? '' : 'grayscale'" class="w-5 h-5" @click="onClicked">
                <SpotifyIcon class="w-full h-full" color="var(--sub-600)" />
            </button>
            <button :class="choosenSpotify ? '' : 'grayscale'" class="w-5 h-5" @click="onClicked">
                <ItunesIcon  class="w-full h-full" color="var(--sub-600)" />
            </button>
        </div>
         <div v-if="openSearchPlatform" class="absolute max-h-[300px] top-10 bg-white z-10 border rounded-lg overflow-scroll w-full py-2 px-1">
            <div v-for="item in artists" @click="chooseValue(item)"  :class="checkIfChecked ? 'active' :  ''" class="p-2 cursor-pointer selectMenuItem radius-8 flex items-center gap-2">

                <div class="w-4 h-4 flex items-center justify-center border border-soft-200 rounded-full bg-white shadow">
                    <div v-if="checkIfChecked" class="bg-dark-green-600 w-3 h-3 rounded-full border-dark-green-600">
                    </div>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-300 flex items-center justify-center overflow-hidden">
                     <img :src="item?.image?.thumb">
                </div>
                <div class="flex flex-col flex-1">
                    <span class="label-sm c-strong-950">{{item.name}}</span>
                    <span class="paragraph-xs c-sub-600">123123123</span>

                </div>
                <ItunesIcon  class="w-5 h-5" color="var(--sub-600)" />
            </div>
             <AppDivider title="VEYA" />
           <div class="px-3 flex flex-col gap-2">

                <FormElement label="Link" direction="verital"  placeholder="Spotify linkini ekleyebilirsiniz">
                    <template #tooltip>
                            adsd
                    </template>
                </FormElement>
                <div class="flex items-center gap-2 mb-3">
                    <RegularButton class="flex-1">İptla</RegularButton>
                    <PrimaryButton class="flex-1">
                        <template #icon>
                                <AddIcon color="var(--dark-green-500)" />
                        </template>
                        Apple Profilini Tanımla
                    </PrimaryButton>
                </div>
           </div>
        </div>
    </div>

</template>

<script setup>
    import { useSlots,ref,computed } from 'vue'
    import {SpotifyIcon,ItunesIcon,AddIcon} from '@/Components/Icons'
    import {IconButton} from '@/Components/Buttons'
    import {useQueryStore} from '@/Stores/useQueryStore';
    import {AppDivider} from '@/Components/Widgets';
    import {FormElement} from '@/Components/Form';
    import {RegularButton,PrimaryButton} from '@/Components/Buttons';
    const openSearchPlatform = ref(false)
    const artists = ref([
        {
            image:{
                thumb:"https://picsum.photos/200",
            },
            name:"sdasas sadas"
        },
        {
            image:{
                thumb:"https://picsum.photos/200",
            },
            name:"sdasas sadas"
        },
    ]);

    const choosenSpotify = ref();
    const choosenItunes = ref();
    const queryStore = useQueryStore();
    const slots = useSlots()
    const props = defineProps({
        type:{type:String},
        placeholder: { type: String},
        modelValue:{}

    })
    const emits = defineEmits(['update:modelValue','change','input']);

    const element = computed({
        get:() => props.modelValue,
        set:(value) => emits('update:modelValue',value)
    })

    const data = ref([]);

    const hasSlot = (name) => {
        return !!slots[name];
    }
    const checkIfChecked = computed(() => {
        return false;
    })
    const chooseValue = () => {

    }
    const onClicked = () => {

    }
    const onInput = (e) => {

        emits('input',e.target.value);
    }
    const onChange = async (e) => {

        emits('change',e.target.value);

        // const artistRepsonse = await queryStore.search(e.target.value, route('control.search.artists'));
        // artists.value = artistRepsonse;
    }

</script>

<style scope>

.selectMenuItem:hover{
    background: var(--white-600);
}
</style>
