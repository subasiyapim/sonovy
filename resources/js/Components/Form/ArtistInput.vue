<template>

   <div v-click-outside="handleClickOutside" class="w-full flex h-9 border-text-input flex items-center radius-8 c-white-500 relative">
        <input v-model="element" @input="onInput" @change="onChange" v-debounce="400" class="border-none focus:outline-none focus:border-none  focus:border-transparent focus:ring-0 h-full w-full bg-transparent label-sm !font-normal c-strong-950" :type="type" :placeholder="placeholder">
        <div class="flex gap-1 pe-3">

             <tippy :allowHtml="true" :sticky="true" :interactive="true">
                <button :class="choosenSpotify ? '' : 'grayscale'" class="w-5 h-5" @click="onClicked('spotify')">
                    <SpotifyIcon class="w-full h-full" color="var(--sub-600)" />
                </button>
                <template #content>
                      <a :href="choosenSpotify?.url" target="_blank" class="hover:underline "  v-if="choosenSpotify?.artistId">{{choosenSpotify?.artistId}}</a>
                       <p v-else>Lütfen seçim yapın</p>
                </template>
            </tippy>

             <tippy interactive>
                <button :class="choosenItunes ? '' : 'grayscale'" class="w-5 h-5" @click="onClicked('itunes')">
                    <ItunesIcon  class="w-full h-full" color="var(--sub-600)" />
                </button>

                <template #content>
                      <a :href="choosenItunes?.url" target="_blank" class="underline "  v-if="choosenItunes?.artistId">{{choosenItunes?.artistId}}</a>

                    <p v-else>Lütfen seçim yapın</p>
                </template>
            </tippy>


        </div>
         <div v-if="openSearchPlatform" class="absolute  top-10 bg-white z-10 border rounded-lg overflow-hidden w-full py-2 px-1">
           <div v-if="!searchingPlatformArtists" class="max-h-[300px] overflow-scroll">
                 <div v-for="item in artists" @click="chooseValue(item)"  :class="checkIfChecked(item) ? 'active' :  ''" class="p-2 cursor-pointer selectMenuItem radius-8 flex items-center gap-2">

                    <div class="w-4 h-4 flex items-center justify-center border border-soft-200 rounded-full bg-white shadow">
                        <div class="bg-dark-green-600 w-3 h-3 rounded-full border-dark-green-600 green-dot">
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-300 flex items-center justify-center overflow-hidden">
                        <img :src="item?.image">
                    </div>
                    <div class="flex flex-col flex-1">
                        <span class="label-sm c-strong-950">{{item.name}}</span>
                        <span class="paragraph-xs c-sub-600">{{item.artistId}}</span>
                    </div>
                    <ItunesIcon v-if="choosenPlatform == 'itunes'" class="w-5 h-5" color="var(--sub-600)" />
                    <SpotifyIcon v-if="choosenPlatform == 'spotify'" class="w-5 h-5" color="var(--sub-600)" />
                </div>
           </div>
           <div v-else class="flex items-center justify-center h-12">
                <AppLoadingIcon color="var(--dark-green-500)" />
           </div>
             <AppDivider title="VEYA" />
           <div class="px-3 flex flex-col gap-2">
                <FormElement label="Link" @input="onInputLink"  direction="verital"  :placeholder=" (choosenPlatform == 'itunes' ?  'Apple' : 'Spotify')+' linkini ekleyebilirsiniz'">
                    <template #tooltip>

                    </template>
                </FormElement>
                <div class="flex items-center gap-2 mb-3">
                    <RegularButton class="flex-1">İptal</RegularButton>
                    <PrimaryButton @click="submit" class="flex-1">
                        <template #icon>
                                <AddIcon color="var(--dark-green-500)" />
                        </template>
                       <span v-if="choosenPlatform == 'itunes'"> Apple</span>  <span v-else> Spotify</span> Profilini Tanımla
                    </PrimaryButton>
                </div>
           </div>
        </div>
    </div>

</template>

<script setup>
    import { useSlots,ref,computed ,onMounted,nextTick} from 'vue'
    import {SpotifyIcon,ItunesIcon,AddIcon,AppLoadingIcon} from '@/Components/Icons'
    import {IconButton} from '@/Components/Buttons'
    import {useQueryStore} from '@/Stores/useQueryStore';
    import {AppDivider} from '@/Components/Widgets';
    import {FormElement} from '@/Components/Form';
    import {useCrudStore} from '@/Stores/useCrudStore';
    import {RegularButton,PrimaryButton} from '@/Components/Buttons';
    const openSearchPlatform = ref(false)
    const artists = ref([]);
    const choosenPlatform = ref(null);
    const choosenSpotify = ref();
    const choosenItunes = ref();
    const queryStore = useQueryStore();
    const crudStore = useCrudStore();
    const searchingPlatformArtists = ref(false);
    const slots = useSlots()
    const props = defineProps({
        type:{type:String},
        placeholder: { type: String},
        modelValue:{},
        choosenSpotifyField:{
            default:null,
        },
        choosenItunesField:{
            default:null,
        },
    })
    const emits = defineEmits(['update:modelValue','change','input','onPlatformsChoosen']);

    const element = computed({
        get:() => props.modelValue,
        set:(value) => emits('update:modelValue',value)
    })

    const data = ref([]);

    const hasSlot = (name) => {
        return !!slots[name];
    }
    const checkIfChecked = computed(() => {
        return (row) => {
            if(choosenPlatform.value){
                if(choosenPlatform.value == 'spotify') return choosenSpotify.value?.artistId == row?.artistId;
                else return choosenItunes.value?.artistId == row?.artistId;
            }
            return false;
        }

    })
    const chooseValue = async (item) => {
        if(choosenPlatform.value == 'spotify'){ choosenSpotify.value = item}
        if(choosenPlatform.value == 'itunes'){ choosenItunes.value = item}

        // if(props.artistId){

            // const response = await crudStore.post(route('control.artist-platform-detach',props.artistId));
        // }
        submit();
    }
    const onClicked = (platform) => {
        if(element.value){
             openSearchPlatform.value = false;

            choosenPlatform.value = platform;
            openSearchPlatform.value = true;

            searchPlatformData();
        }

    }
    const onInput = (e) => {

        emits('input',e.target.value);
    }
    const onChange = async (e) => {

        emits('change',e.target.value);


    }
    const handleClickOutside = () => {
        openSearchPlatform.value = false;

    }
    const onInputLink = (e) => {
        choosenPlatform.value == 'spotify' ? choosenSpotify.value = {url:e.target.value} : choosenItunes.value = {url:e.target.value}
    }
    const submit = () => {
        let data = {};
        if(choosenPlatform.value == 'spotify')
            data ={...choosenSpotify.value,platform:'Spotify'};
        else
            data = {...choosenItunes.value,platform:'Apple'};

        emits('onPlatformsChoosen',data)
        openSearchPlatform.value = false;

    }

const searchPlatformData = async () => {
    searchingPlatformArtists.value = true;
        artists.value = [];
        const response =  await queryStore.search(element.value,route('control.search.artists-platform-search'));
        if(choosenPlatform.value == 'spotify'){
            // platforms.value = response.spotify.artists.items;
            response?.spotify?.artists?.items?.forEach((ar) => {
                artists.value.push({
                    name: ar.name,
                    url:ar.external_urls.spotify,
                    image:ar.images?.slice(-1)?.pop()?.url,
                    artistId:ar.id,
                });
            })

        }else {
            if(response.itunes != 'No results found.'){
                response.itunes.results.forEach((ar) => {
                    artists.value.push({
                        name: ar.artistName,
                        url:ar.artistLinkUrl,
                        image:null,
                        artistId:ar.artistId,
                    });
                })
            }


        }

         searchingPlatformArtists.value = false;

}

onMounted(() => {
    nextTick(() => {
        if(props.choosenSpotifyField){
            choosenSpotify.value = {
                 artistId:props.choosenSpotifyField.split("/").pop(),
                url:props.choosenSpotifyField,
            }
        }
        if(props.choosenItunesField){
            let finalString = props.choosenItunesField.split("/").pop();
            if(finalString.includes('?')){
                finalString = finalString.split('?')[0];
            }
            choosenItunes.value = {
                artistId:finalString,
                url:props.choosenItunesField,
            }
        }
    })


});
</script>

<style scoped>

.green-dot{
    display:none;
}
.selectMenuItem.active .green-dot {
    display:block;
}
.selectMenuItem:hover,.selectMenuItem.active{
    background: var(--white-600);
}
</style>
