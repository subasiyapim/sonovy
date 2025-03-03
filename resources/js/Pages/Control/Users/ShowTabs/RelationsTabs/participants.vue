

<template>
    <AppTable :hasSearch="false" v-model="tableData" :showAddButton="false" :isClient="true" >
        <AppTableColumn label="Kullanıcı Adı" sortable="type">
            <template #default="scope">
            <div class="flex justify-start items-center gap-3 w-full">
                <div class="w-10 h-10 rounded-full overflow-hidden">
                <img :alt="scope.row.name"
                    :src="scope.row.image ? scope.row.image.thumb : defaultStore.profileImage(scope.row.name)"
                >
                </div>
                <div class="flex flex-col items-start ">
                    <a :href="route('control.user-management.users.show',scope.row.id)"
                        class="font-poppins table-name-text c-sub-600 mb-0.5">{{ scope.row.name }}</a>
                    <span class="c-sub-600 paragraph-xs mb-2">{{scope.row.email}}</span>

                    <button class="c-blue-500 label-xs" @click="usersTable.toggleShowSub(scope.index)" v-if="scope.row?.children?.length>0">
                        {{scope.row?.children?.length}} {{ __('control.user.view_sub_users') }}

                    </button>

                </div>
            </div>
            </template>

        </AppTableColumn>

        <AppTableColumn label="Şarkı Adı ve sanatçı">
            <template #default="scope">
                <div class="flex flex-col">
                    <p class="paragraph-xs c-strong-950">
                        {{scope.row.song?.name}} <template v-if="scope.row.song?.version">({{scope.row.song.version}})</template>
                    </p>
                    <span class="paragraph-xs c-sub-600" v-for="(artist,artistIndex) in scope.row.song?.main_artists">
                            {{artist.name}} <template v-if="artistIndex < scope.row.song?.main_artists.length-1">,</template>
                    </span>
                </div>

            </template>
        </AppTableColumn>
        <AppTableColumn label="Albüm UPC/ISRC">
            <template #default="scope">
                <div class="flex flex-col">
                    <p class="paragraph-xs c-strong-950" v-if="scope.row.song?.products.length >0"> UPC: {{scope.row.song.products[0].upc_code}}</p>
                    <p class="paragraph-xs c-strong-950"> ISRC: {{scope.row.song?.isrc}}</p>

                </div>
            </template>
        </AppTableColumn>


        <AppTableColumn label="Hakediş/Realizasyon">
            <template #default="scope">
                   <div class="flex items-center gap-1 label-sm">
                    <span class="c-soft-400">%{{scope.row.commission_rate}}/</span>
                    <span class="c-strong-950">%{{scope.row.realization}}</span>

                </div>
            </template>
        </AppTableColumn>

        <AppTableColumn label="Kullanıcı Rolü" sortable="type">
            <template #default="scope">
                <template v-for="role in scope.row.roles">
                    <div class="px-3 py-1 rounded-full" :class="role.code == 'super_admin' ? 'bg-[#CAC0FF]' : (role.code == 'admin' ? 'bg-[#D8E5ED]' : 'bg-[#C0D5FF]')">
                    <p class="label-xs" :class="role.code == 'super_admin' ? 'text-[#351A75]' : (role.code == 'admin' ? 'text-[#060E2F]' : 'text-[#122368]')">  {{role.name}}</p>
                    </div>
                </template>
            </template>
        </AppTableColumn>


        <AppTableColumn label="Durum" sortable="type" width="80">
            <template #default="scope">
                <div class="flex items-center gap-2  rounded-lg px-3 py-1" :class="scope.row.status == 'Pasif' ? 'bg-red-500 ' :' border border-soft-200'">
                    <span class="label-xs " :class="scope.row.status == 'Pasif' ? 'text-white' :'c-strong-95'">•</span>
                    <span class="label-xs " :class="scope.row.status == 'Pasif' ? 'text-white' :'c-sub-600'">{{scope.row.status}}</span>
                </div>
            </template>
        </AppTableColumn>
        <template #empty>
            {{ __('control.user.date_detail_not_found') }}
        </template>
    </AppTable>
</template>

<script  setup>
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {useDefaultStore} from "@/Stores/default";

import {computed} from 'vue';
const defaultStore = useDefaultStore();
const props = defineProps({
    modelValue:{}
});
const emits = defineEmits(['update:modelValue']);

const tableData = computed({
    get:() => props.modelValue,
    set:(val) => emits('update:modelValue',val)
})
</script>
<style  scoped>

</style>
