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
        <VueDatePicker @update:model-value="onPublishDateChoosen" v-model="form.physical_release_date"
                       :convertModel="false" class="radius-8" auto-apply :enable-time-picker="false"
                       placeholder="Tarih Seçiniz">
          <template #input-icon>
            <div class="p-3">
              <CalendarIcon color="var(--sub-600)"/>
            </div>
          </template>
        </VueDatePicker>
      </FormElement>
      <FormElement label-width="190px" :error="form.errors.production_year" v-model="form.production_year" type="select"
                   label="Yapım Yılı" placeholder="Yapım Yılı" :config="selectConfig">

      </FormElement>
      <FormElement label-width="190px" :error="form.errors.previously_released" v-model="form.previously_released"
                   @change="onChangeIsPublishedBefore" type="fancyCheck" label="Daha önce Yayınlandı mı?"
                   placeholder="Daha önce Yayınlandı mı?">

      </FormElement>
      <FormElement label-width="190px" :error="form.errors.previous_release_date" :disabled="form.previously_released"
                   type="custom" label="Orjinal / Fiziksel Yayın Tarihi">
        <VueDatePicker :disabled="!form.previously_released" v-model="form.previous_release_date" class="radius-8"
                       auto-apply :enable-time-picker="false" placeholder="Orjinal / Fiziksel Yayınlanma Tarihi">
          <template #input-icon>
            <div class="p-3">
              <CalendarIcon color="var(--sub-600)"/>
            </div>
          </template>
        </VueDatePicker>
      </FormElement>


      <SectionHeader title="ÜLKE VE BÖLGE TERCİHLERİ"></SectionHeader>

      <FormElement @change="onChangePublishCountryType" label-width="190px" :error="form.errors.publishing_country_type"
                   v-model="form.publishing_country_type" type="radio" label="Tercihler" :config="countryRadioConfig">

      </FormElement>
      <div class="flex">
        <div class="w-[190px] label-sm c-strong-950">Yayınlanacak Ülkeler</div>
        <div class="flex flex-col w-full gap-3">

          <div class="w-full" v-for="(value,key) in usePage().props.countriesGroupedByRegion.countries.data">
            <AppAccordion :title="key" :description="usePage().props.countriesGroupedByRegion.countries.counts[key].selected_count+' Adet Seçildi'">

              <div class="flex items-center ">
                <div class="flex-1">
                  <p class="label-medium c-strong-950 !text-start">Ülkeler</p>
                </div>
                <div class="flex items-center gap-2">
                  <button @click.stop="chooseAll(key)" class="c-blue-500 label-xs hover:underline">Tümünü Seç</button>
                  <button @click.stop="unChooseAll(key)" class="c-blue-500 label-xs hover:underline">Seçimi Kaldır
                  </button>
                </div>
              </div>
              <hr class="my-2">
              <div class="grid grid-cols-3 gap-2 ">
                <div v-for="country in value">
                  <label @click.stop class="flex items-center gap-2">
                    <input type="checkbox" @click="onCountryCheck(country)"
                           :checked="form.published_countries?.find((el) => el == country.value)"
                           class="focus:ring-0 rounded appCheckbox border border-soft-200"/>

                    {{ country.iconKey }}
                    <span class="paragraph-xs c-strong-950">{{ country.label }}</span>
                  </label>
                </div>
              </div>
            </AppAccordion>
          </div>
        </div>
      </div>
      <SectionHeader title="PLATFORM TERCİHLERİ"></SectionHeader>

      <AppTable ref="platformTable" v-model="form.platforms" @selectionChange="onPlatformSelected" :isClient="true"
                :hasSelect="true" :hasSearch="false" :showAddButton="false">

        <AppTableColumn label="Platform">
          <template #default="scope">
            <div class="flex items-center justify-center gap-2">
              <Icon :icon="scope.row.iconKey"/>

              <p class="label-sm c-solid-950">
                {{ scope.row.label }}
              </p>
            </div>

          </template>
        </AppTableColumn>

        <AppTableColumn label="İndirme Fiyatı">
          <template #default="scope">
            <FormElement :error="form.errors[`platforms.${scope.index}.price`]" direction="vertical"
                         :disabled="true" v-model="scope.row.price" placeholder="0.00"></FormElement>

          </template>
        </AppTableColumn>

        <AppTableColumn label="Ön Sipariş Tarihi">

          <template #default="scope">
            <FormElement :error="form.errors[`platforms.${scope.index}.pre_order_date`]" type="custom"
                         direction="vertical">

              <VueDatePicker :disabled="!scope.row.isSelected" v-model="scope.row.pre_order_date" class="radius-8"
                             auto-apply :enable-time-picker="false" placeholder="Tarih Giriniz (Opsiyonel)">
                <template #input-icon>
                  <div class="p-3">
                    <CalendarIcon color="var(--sub-600)"/>
                  </div>
                </template>
              </VueDatePicker>
            </FormElement>
          </template>
        </AppTableColumn>

        <AppTableColumn label="Yayın Tarihi" align="end">

          <template #default="scope">
            <FormElement :error="form.errors[`platforms.${scope.index}.publish_date`]" type="custom"
                         direction="vertical">
              <VueDatePicker :disabled="!scope.row.isSelected" v-model="scope.row.publish_date" class="radius-8"
                             auto-apply :enable-time-picker="false" placeholder="Tarih Giriniz">
                <template #input-icon>
                  <div class="p-3">
                    <CalendarIcon color="var(--sub-600)"/>
                  </div>
                </template>
              </VueDatePicker>
            </FormElement>


          </template>
        </AppTableColumn>


      </AppTable>

    </div>


  </div>
</template>

<script setup>
import {SectionHeader, AppAccordion} from '@/Components/Widgets';
import AppTable from '@/Components/Table/AppTable.vue';
import AppTableColumn from '@/Components/Table/AppTableColumn.vue';
import {computed, ref, onBeforeMount, nextTick} from 'vue';
import {FormElement, AppTextInput} from '@/Components/Form';
import {AddIcon, Icon, CalendarIcon} from '@/Components/Icons'
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

const selectConfig = computed(() => {
  return {
    hasSearch: false,
    data: getLast100Years(),
  }
})


const countryRadioConfig = computed(() => {
  return {
    optionDirection: 'vertical',
    options: usePage().props.product_publish_country_types
  }
})

const onChangeIsPublishedBefore = (e) => {
  if (e) {
    form.value.previous_release_date = null;
  }
}

const onChangePublishCountryType =  (e) => {
    console.log(e);
    if(e.value == 1){
        Object.keys(usePage().props.countriesGroupedByRegion.countries.data).forEach((key) => {
               usePage().props.countriesGroupedByRegion.countries.counts[key].selected_count = usePage().props.countriesGroupedByRegion.countries.data[key].length;

            chooseAll(key);
        })
    }else {
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
            console.log("TEK TEK",element);
            if(form.value.published_countries.includes(element.value)){
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

            if(e.selected){
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
    if(form.value.publishing_country_type == 1){
            Object.keys(usePage().props.countriesGroupedByRegion.countries.data).forEach(key => {
               chooseAll(key)

               usePage().props.countriesGroupedByRegion.countries.counts[key].selected_count = usePage().props.countriesGroupedByRegion.countries.data[key].length;
            });
    }
});


</script>

<style lang="scss" scoped>

</style>
