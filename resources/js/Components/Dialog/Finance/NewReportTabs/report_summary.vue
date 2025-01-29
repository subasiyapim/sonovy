<script setup>
import {computed} from 'vue';
import moment from 'moment';
import  'moment/dist/locale/tr';
moment.locale('tr');

import { usePage} from '@inertiajs/vue3';


const props = defineProps({
    modelValue:{},
    formattedDates:{},
})

const emits = defineEmits(['update:modelValue']);

const element = computed({
    get:() => props.modelValue,
    set:(value) => emits('update:modelValue',value)
});
const getFormatedDate = computed(() => {
    if (element.value.date && Array.isArray(element.value.date) && element.value.date.length === 2) {
    const [startDate, endDate] = element.value.date;
    const formattedStartDate = moment(startDate).format('MMM DD, YYYY').replace('Dec', 'Ara').replace('Jan', 'Oca');
    const formattedEndDate = moment(endDate).format('MMM DD, YYYY').replace('Dec', 'Ara').replace('Jan', 'Oca');
    return `${formattedStartDate} - ${formattedEndDate}`;
  }else {
        return 'Tarih seçimi yapınız';
    }
    return element.value.date;
});


const reportContentMap = {
  1: 'Tam Rapor',
  2: 'Sanatçı Seç',
  3: 'Albüm Seç',
  4: 'Parça Seç',
  5: 'Platform Seç',
  6: 'Ülke Seç',
  7: "Label'e Göre",
  8: 'Sanatçıya Göre',
  9: 'Albüme Göre',
  10: 'Mağazaya Göre',
  11: 'Ülkeye Göre',
  12: 'Label Seç',
};
</script>

<template>
      <div class="flex flex-col items-start gap-3">
            <p class="label-medium c-strong-950 mb-3">Rapor Özeti:</p>
            <div class="flex items-center justify-between gap-2 w-full">
                <p class="paragraph-sm c-sub-600">Seçilen Dönem:</p>
                <p class="label-sm c-strong-950">{{getFormatedDate}}</p>
            </div>
            <div class="flex items-center justify-between gap-2 w-full">
                <p class="paragraph-sm c-sub-600">Rapor Tipi:</p>
                <p class="label-sm c-strong-950">{{reportContentMap[element.report_content_type]}}</p>
            </div>

            <div v-if="element.type == 1 && element.report_content_type != 1" class="flex items-center justify-between gap-2 w-full">
                <p class="paragraph-sm c-sub-600">
                    <template v-if="element.report_content_type == 2">
                        Sanatçılar:
                    </template>
                    <template v-if="element.report_content_type == 12">
                        Plak Şirketleri:
                    </template>
                    <template v-if="element.report_content_type == 3">
                        Albümler:
                    </template>
                    <template v-if="element.report_content_type == 4">
                        Parçalar:
                    </template>
                    <template v-if="element.report_content_type == 5">
                        Platformlar:
                    </template>
                    <template v-if="element.report_content_type == 6">
                        Ülkeler:
                    </template>

                </p>
                <!-- {{usePage().props.labels}} -->
                <p class="label-sm c-strong-950">
                    <template v-for="(e,index) in element.choosenValues">
                        <template v-if="element.report_content_type == 12">
                            {{usePage().props.labels.find((el) => el.id == e)?.name}} <template v-if="index != element.choosenValues.length - 1">, </template>
                        </template>
                        <template v-if="element.report_content_type == 2">
                            {{usePage().props.artists.find((el) => el.id == e)?.name}} <template v-if="index != element.choosenValues.length - 1">, </template>
                        </template>
                        <template v-if="element.report_content_type == 3">
                            {{usePage().props.products.find((el) => el.id == e)?.album_name}} <template v-if="index != element.choosenValues.length - 1">, </template>
                        </template>
                        <template v-if="element.report_content_type == 4">
                            {{usePage().props.songs.find((el) => el.id == e)?.name}} <template v-if="index != element.choosenValues.length - 1">, </template>
                        </template>
                        <template v-if="element.report_content_type == 5">
                            {{usePage().props.platforms.find((el) => el.id == e)?.name}} <template v-if="index != element.choosenValues.length - 1">, </template>
                        </template>

                    </template>
                    <template v-if="element.report_content_type == 6">
                            {{element.choosenValues.length}} adet ülke seçildi
                    </template>

                </p>
            </div>
        </div>

</template>

<style scoped>

</style>
