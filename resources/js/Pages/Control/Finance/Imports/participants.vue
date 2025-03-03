<script setup>

import {ref, onMounted, onUnmounted,computed} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {PrimaryButton} from '@/Components/Buttons'
import {UploadIcon,DownloadIcon} from '@/Components/Icons'
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {useDefaultStore} from "@/Stores/default";
import moment from 'moment';
const props = defineProps({
    earnings:{}
});
const isImportModalOn = ref(false);
const data = computed({
    get:() => props.earnings,
    set:() =>$emit('update')
});
const defaultStore = useDefaultStore();

</script>

<template>

    <AdminLayout :showGoBack="false" :showDatePicker="false" :title="'Katılımcı Raporları'" parentTitle="Finans">

        <AppTable  v-model="data"  :isClient="true" :hasSearch="false" :showAddButton="false">
                <AppTableColumn label="Kullanıcı Adı">
                    <template #default="scope">
                        <div class="flex justify-start items-center gap-3 w-full">
                            <div class="w-10 h-10 rounded-full overflow-hidden">
                            <img :alt="scope.row.name"
                                :src="defaultStore.profileImage(scope.row.user?.name)"
                            >
                            </div>
                            <div class="flex flex-col items-start ">
                                <a :href="route('control.user-management.users.show',scope.row.user?.id)"
                                    class="paragraph-xs c-blue-500 mb-0.5">{{ scope.row.user?.name }}</a>
                                <span class="c-sub-600 paragraph-xs mb-2">{{scope.row.user?.email}}</span>

                            </div>
                        </div>
                    </template>
                </AppTableColumn>

                <AppTableColumn label="Tarih">
                    <template #default="scope">
                        <p class="paragraph-xs c-strong-950">
                            {{moment(scope.row.report_date).format('YYYY - DD')}}

                        </p>
                    </template>
                </AppTableColumn>


                <AppTableColumn label="DSP">
                    <template #default="scope">
                         <p class="paragraph-xs c-strong-950">
                                {{scope.row.platform}}
                         </p>
                    </template>
                </AppTableColumn>

                <AppTableColumn label="T. Kazanç">
                    <template #default="scope">
                        <p class="paragraph-xs c-strong-950">
                               {{scope.row.total_earning}}
                        </p>
                    </template>
                </AppTableColumn>
                <AppTableColumn label="Kazancım">
                    <template #default="scope">
                        <p class="paragraph-xs c-strong-950">
                               {{scope.row.provider_earning}}

                        </p>
                    </template>
                </AppTableColumn>
                <AppTableColumn label="Katılımcı Kazancı">
                    <template #default="scope">
                        <p class="paragraph-xs c-strong-950">
                            {{scope.row.participant_earning}}
                        </p>
                    </template>
                </AppTableColumn>
                <AppTableColumn label="Katılımcı Yüzdesi">
                    <template #default="scope">

                        <p class="paragraph-xs c-strong-950">
                            {{scope.row.user?.commission_rate}} %
                        </p>

                    </template>
                </AppTableColumn>

                <AppTableColumn label="Aksiyon" align="right">
                    <template #default="scope">

                        <a target="_blank">
                            <DownloadIcon color="var(--sub-600)" />
                        </a>
                    </template>
                </AppTableColumn>

                <template #empty>
                    Henüz veri yok
                </template>
            </AppTable>
    </AdminLayout>


</template>

<style lang="postcss" scoped>

</style>
