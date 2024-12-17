<template>

  <div class="pb-6">
    <p class="label-xl c-strong-950">
      Harika, yayınlama ve dağıtım bilgilerini dolduralım. 🌍
    </p>
  </div>

    <div class="">

        <div class="flex flex-col gap-6">
            <SectionHeader title="YAYINLANMA TARİHLERİ"></SectionHeader>

            <FormElement label-width="190px" :error="form.errors.physical_release_date" type="custom" label="Genel Yayın Tarihi">
                <VueDatePicker  v-model="form.physical_release_date"
                            :convertModel="false" class="radius-8" auto-apply :enable-time-picker="false"
                            placeholder="Tarih Seçiniz">
                <template #input-icon>
                    <div class="p-3">
                    <CalendarIcon color="var(--sub-600)"/>
                    </div>
                </template>
                </VueDatePicker>
            </FormElement>
            <SectionHeader title="PLATFORM TERCİHLERİ"></SectionHeader>

            <div v-for="platform in form.platforms" class="flex items-start gap-24">

                    <div @click="platform.isChecked = !platform.isChecked" class="flex items-center gap-2 w-32 cursor-pointer">
                        <button class="w-4 h-4 focus:ring-0 rounded appCheckbox border border-soft-200 p-0.5 flex items-center justify-center" :class="platform.isChecked ? 'bg-dark-green-500' :'bg-white'">
                            <CheckIcon v-if="platform.isChecked" color="#fff" />
                        </button>
                        <Icon :icon="platform.iconKey"/>
                        <p class="label-sm c-strong-950">{{platform.label}}</p>
                    </div>
                <div class="flex-1">
                    <FormElement direction="vertical" type="textarea" v-model="platform.description" placeholder="Tanım"></FormElement>
                    <div class="flex items-center gap-2">
                        <FormElement direction="vertical" v-model="platform.content_id" class="flex-1" type="text" placeholder="Content Id"></FormElement>
                        <FormElement direction="vertical" v-model="platform.privacy" class="flex-1" type="select" :config="youtubeConfig" placeholder="Gizlilik seçiniz"></FormElement>
                    </div>
                    <div class="flex items-center gap-2">
                        <FormElement direction="vertical" v-model="platform.hashtags" class="flex-1" type="hashtags" placeholder="Etiket giriniz"></FormElement>
                    </div>
                    <div class="flex items-center gap-2">

                        <VueDatePicker  v-model="platform.date" class="radius-8" auto-apply :enable-time-picker="false" placeholder="Tarih Giriniz">
                                <template #input-icon>
                                <div class="p-3">
                                        <CalendarIcon color="var(--sub-600)"/>
                                </div>
                                </template>
                            </VueDatePicker>
                        <VueDatePicker    v-model="platform.time" class="radius-8" time-picker auto-apply :enable-time-picker="false" placeholder="Tarih Giriniz">
                                <template #input-icon>
                                <div class="p-3">
                                        <CalendarIcon color="var(--sub-600)"/>
                                </div>
                                </template>
                            </VueDatePicker>

                    </div>
                </div>


            </div>
        </div>
    </div>
</template>

<script setup>
import {SectionHeader, AppAccordion} from '@/Components/Widgets';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {computed, ref, onBeforeMount, nextTick} from 'vue';
import {FormElement, AppTextInput} from '@/Components/Form';
import {AddIcon, Icon, CalendarIcon,CheckIcon} from '@/Components/Icons'
import {usePage} from '@inertiajs/vue3';

const props = defineProps({
  product: {},
  modelValue: {},
})

const platformTable = ref();
const emits = defineEmits(['update:modelValue']);

const form = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})

// const platforms = ref(usePage().props.platforms);

function getLast100Years() {
  const currentYear = new Date().getFullYear();
  const years = [];

  for (let i = 0; i < 100; i++) {
    years.push({
      "value": currentYear - i,
      "label": currentYear - i
    });
  }

  return years;
}

const youtubeConfig = computed(() => {
  return {
    hasSearch: false,
    data: [
      {
        "label": "Herkese Açık",
        value: 1,
      },
      {
        "label": "Gizli",
        value: 2,
      }
    ]
  }
})


const countryRadioConfig = computed(() => {
  return {
    optionDirection: 'vertical',
    options: usePage().props.product_publish_country_types
  }
})

const onChangeIsPublishedBefore = (e) => {
  if (!e) {
    form.value.publish_year = null;
  }
}

const onChangePublishCountryType = (e) => {
  console.log(e);
  if (e.value == 1) {
    Object.keys(usePage().props.countriesGroupedByRegion.countries.data).forEach((key) => {
      usePage().props.countriesGroupedByRegion.countries.counts[key].selected_count = usePage().props.countriesGroupedByRegion.countries.data[key].length;

      chooseAll(key);
    })
  } else {
    Object.keys(usePage().props.countriesGroupedByRegion.countries.data).forEach((key) => {
      usePage().props.countriesGroupedByRegion.countries.counts[key].selected_count = 0;

      unChooseAll(key);
    })
  }

}
const onCountryCheck = (e) => {
  const findedIndex = form.value.published_countries.findIndex((el) => el == e.value);
  if (findedIndex >= 0) {
    form.value.published_countries.splice(findedIndex, 1);
  } else {
    form.value.published_countries.push(e.value);
  }

  Object.keys(usePage().props.countriesGroupedByRegion.countries.data).forEach((key) => {

    let total = 0;
    usePage().props.countriesGroupedByRegion.countries.data[key].forEach(element => {
      console.log("TEK TEK", element);
      if (form.value.published_countries.includes(element.value)) {
        total++;
      }

    });

    usePage().props.countriesGroupedByRegion.countries.counts[key].selected_count = total;

  })
}

const chooseAll = (key) => {


  usePage().props.countriesGroupedByRegion.countries.data[key].forEach((e) => {
    const findedIndex = form.value.published_countries.findIndex((el) => el == e.value);
    if (findedIndex < 0) {
      form.value.published_countries.push(e.value);
    }
  });
  usePage().props.countriesGroupedByRegion.countries.counts[key].selected_count = usePage().props.countriesGroupedByRegion.countries.data[key].length;

}
const unChooseAll = (key) => {
  usePage().props.countriesGroupedByRegion.countries.data[key].forEach((e) => {
    const findedIndex = form.value.published_countries.findIndex((el) => el == e.value);
    if (findedIndex >= 0) {
      form.value.published_countries.splice(findedIndex, 1);
    }
  });
  usePage().props.countriesGroupedByRegion.countries.counts[key].selected_count = 0;

}

const onPlatformSelected = (rows) => {
  form.value.platforms.forEach(element => {
    element.isSelected = null;
  });
  rows.forEach(element => {

    const finded = form.value.platforms.find((el) => el == element);
    if (finded)
      finded.isSelected = true;

  });
//    form.value.platforms = rows.map((row) =>  { return {id:row.value,price:0,pre_order_date:null,publish_date:null}});
}

const onPublishDateChoosen = (e) => {


  if (e) {
    platformTable.value.selectAll();
    for (let index = 0; index < form.value.platforms.length; index++) {
      const element = form.value.platforms[index];
      element.publish_date = e;

    }
  }

}
onBeforeMount(() => {
  form.value.platforms = usePage().props.platforms;
  if (form.value.publishing_country_type) {
    form.value.published_countries = [];
    Object.keys(usePage().props.countriesGroupedByRegion.countries.data).forEach((key) => {

      usePage().props.countriesGroupedByRegion.countries.data[key].forEach((e) => {

        if (e.selected) {
          const findedIndex = form.value.published_countries.findIndex((el) => el == e.value);
          if (findedIndex < 0) {
            form.value.published_countries.push(e.value);
          }
        }

      });
    })
  }
  if (usePage().props.product.download_platforms) {
    let tempPlatformsTofill = [];
    usePage().props.product.download_platforms.forEach(element => {


      const findedIndex = form.value.platforms.findIndex((e) => e.value == element.id);
      if (findedIndex >= 0) {

        form.value.platforms[findedIndex].isSelected = true;
        form.value.platforms[findedIndex].price = element.pivot?.price;
        form.value.platforms[findedIndex].pre_order_date = new Date(element.pivot?.pre_order_date);
        form.value.platforms[findedIndex].publish_date = new Date(element.pivot?.publish_date);

        tempPlatformsTofill.push(findedIndex);

      }
    });

    nextTick(() => {

      platformTable.value?.selectRows(tempPlatformsTofill);
    })
  }
  if (form.value.publishing_country_type == 1) {
    Object.keys(usePage().props.countriesGroupedByRegion.countries.data).forEach(key => {
      chooseAll(key)

      usePage().props.countriesGroupedByRegion.countries.counts[key].selected_count = usePage().props.countriesGroupedByRegion.countries.data[key].length;
    });
  }
});


</script>

<style lang="scss" scoped>

</style>
