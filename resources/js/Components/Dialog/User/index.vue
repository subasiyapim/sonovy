<template>
  <BaseDialog v-model="isDialogOn" align="right" :title="'Yeni Kullanıcı Ekle'"
              :description="'Temel bilgileri girerek yeni kullanıcı oluşturabilirsiniz'">
    <template #icon>
      <img width="24" height="24" src="@/assets/images/mp3_active.png"/>
    </template>
    <SectionHeader :title="__('control.user.dialog.header_1')"/>
    <div class="p-5 flex flex-col gap-6">
      <FormElement label-width="190px"
                   :required="true"
                   :error="form.errors.name"
                   :config="{letter:500}"
                   v-model="form.name"
                   :label="__('control.user.fields.name')"
                   type="text"
                   :placeholder="__('control.user.fields.name_placeholder')"/>
        <FormElement  label-width="190px" v-model="form.country_id"
                   :error="form.errors.country_id" type="select"
                   :label="__('control.user.fields.country_id')"
                   :placeholder="__('control.user.fields.country_id_placeholder')" :config="countryConfig">

        </FormElement>

        <FormElement label-width="190px"
                   :required="false"
                   :error="form.errors.email"

                   v-model="form.email"
                   :label="__('control.user.fields.email')"
                   type="text"
                   :placeholder="__('control.user.fields.email_placeholder')"/>

        <FormElement  label-width="190px" v-model="form.language_id"
                   :error="form.errors.language_id" type="select"
                   :label="__('control.user.fields.language_id')"
                   :placeholder="__('control.user.fields.language_id_placeholder')" :config="countryConfig">

        </FormElement>

        <div class="flex items-center gap-2">
            <FormElement direction="vertical" label-width="190px" v-model="form.city_id"
                    :error="form.errors.city_id" type="select"
                    :label="__('control.user.fields.city_id')"
                    :placeholder="__('control.user.fields.city_id')" :config="cityConfig">

            </FormElement>
            <FormElement direction="vertical" label-width="190px" v-model="form.district_id"
                    :error="form.errors.district_id" type="select"
                    :label="__('control.user.fields.district_id')"
                    :placeholder="__('control.user.fields.district_id_placeholder')" :config="districtConfig">

            </FormElement>
        </div>


      <FormElement label-width="190px"
                   :required="false"
                   :error="form.errors.adress"
                   :config="{letter:500}"
                   v-model="form.adress"
                   :label="__('control.user.fields.adress')"
                   type="textarea"
                   :placeholder="__('control.user.fields.adress_placeholder')"/>
        <FormElement label-width="190px"
                   :required="false"
                   :error="form.errors.commission_rate"
                   v-model="form.commission_rate"
                   :label="__('control.user.fields.commission_rate')"
                   type="text"
                   :placeholder="__('control.user.fields.commission_rate_placeholder')"/>
        <FormElement direction="vertical" v-model="form.is_company" :error="form.errors.is_company"
                   type="fancyCheck"
                   :placeholder="__('control.user.fields.is_company_placeholder')">

            <div v-if="form.is_company">
                <FormElement label-width="190px"
                   :required="false"
                   :error="form.errors.company_name"
                   v-model="form.company_name"
                   :label="__('control.user.fields.company_name')"
                   type="text"
                   :placeholder="__('control.user.fields.company_name_placeholder')"/>
                <FormElement label-width="190px"
                   :required="false"
                   :error="form.errors.tax_number"
                   v-model="form.tax_number"
                   :label="__('control.user.fields.tax_number')"
                   type="text"
                   :placeholder="__('control.user.fields.tax_number_placeholder')"/>
                <FormElement label-width="190px"
                   :required="false"
                   :error="form.errors.tax_house"
                   v-model="form.tax_house"
                   :label="__('control.user.fields.tax_house')"
                   type="text"
                   :placeholder="__('control.user.fields.tax_house_placeholder')"/>

                <FormElement label-width="190px"
                   v-model="form.phone"
                   :error="form.errors.phone"
                   :label="__('control.artist.fields.phone')"
                   :config="{codes:usePage().props.countryCodes}"
                   type="phone"
                   placeholder="(555) 000-0000"></FormElement>

            </div>
        </FormElement>

    </div>
    <SectionHeader :title="__('control.user.dialog.header_2')"/>
    <div class="p-5 flex flex-col gap-6">

        <FormElement label-width="190px"
            :required="false"
            :error="form.errors.password"
            v-model="form.password"
            :label="__('control.user.fields.password')"
            type="password"
            :placeholder="__('control.user.fields.password_placeholder')"/>
        <FormElement label-width="190px"
            :required="false"
            :error="form.errors.re_password"
            v-model="form.re_password"
            :label="__('control.user.fields.re_password')"
            type="password"
            :placeholder="__('control.user.fields.re_password_placeholder')"/>

    </div>


    <div class="flex p-5 border-t border-soft-200 gap-4 sticky bottom-0 bg-white">
      <RegularButton @click="isDialogOn = false" class="flex-1">
        {{ __('control.general.cancel') }}
      </RegularButton>
      <PrimaryButton @click="onSubmit" :disabled="checkIfDisabled" class="flex-1">
        <template #icon>
          <AddIcon  color="var(--dark-green-500)"/>
        </template>
        {{ __('control.general.save') }}
      </PrimaryButton>
    </div>
  </BaseDialog>


</template>

<script setup>
import BaseDialog from '../BaseDialog.vue';
import {SectionHeader} from '@/Components/Widgets';

import {AddIcon, ISRCStarsIcon} from '@/Components/Icons'
import {RegularButton, PrimaryButton} from '@/Components/Buttons'
import {computed, ref, onMounted} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import {toast} from 'vue3-toastify';
import {useCrudStore} from '@/Stores/useCrudStore';
import {FormElement, ArtistInput, AppIncrementer, AppSelectInput, AppSliderInput} from '@/Components/Form'
import InputError from "@/Components/InputError.vue";




const createArtistDialog = ref(false);
const crudStore = useCrudStore();
const props = defineProps({
  modelValue: {
    default: false,
  },
  user: {
    default: null,
  },
})
const isUpdating = computed(() => {
  return props.user ? true : false;
});


const adding = ref(false)

const form = useForm({
    name:'',
    city_id:null,
    country_id:null,
    district_id:null,
    language_id:null,
    company_name:null,
    tax_number:null,
    tax_house:null,
    tax_house:null,
    email:'',
    adress:'',
    commission_rate:'',
    is_company:false,
    phone:'',
    password:'',
    re_password:'',
});





const emits = defineEmits(['update:modelValue', 'done']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})


const countryConfig = computed(() => {
  return {
    data: usePage().props.countries,
  };
})


const cityConfig = computed(() => {
  return {
    hasSearch: true,
    data: props.genres,
  }
})
const districtConfig = computed(() => {
  return {
    hasSearch: true,
    data: [],
  }
})

const onSubmit = async (e) => {


  form.post(route('control.user-management.users.store', props.user.id),
      {
        onFinish: () => {

        },
        onSuccess: async (e) => {

          toast.success(e.props.notification.message);
          isDialogOn.value = false;

          emits('done', e.props.notification.user)

        },
        onError: (e) => {
          toast.error(Object.values(e)[0])
        }
      });

}

const checkIfDisabled = computed(() => {
  return false;
})

const onArtistCreated = (e) => {
  let row = {
    label: e.name,
    value: e.id,

  };


  if (whichArtistConfigToAdd.value == 'featuring_artists') {
    featuringArtistMultiselect.value.appMultiSelect.insertData(row);
    featuringArtistSelectConfig.value.data.push(row);
  } else {
    mainArtistSelect.value.appSelect.insertData(row);
    mainArtistSelectConfig.value.data.push(row);
  }

}
const roleConfig = computed(() => {
  return {
    data: usePage().props.artistBranches,
  }
})
onMounted(() => {
  if (props.user) {

    form.featuring_artists = (props.user.featuring_artists ?? []).map((e) => e.id);


    form.main_artists = props.user.main_artists?.length > 0 ? props.user.main_artists[0].id : null;


    form.musicians = (props.user.musicians ?? []).map((e) => {
      return {name: e.name, role_id: e.role_id}
    }) ?? [{}];

    form.participants = (props.user.participants ?? []).map((e) => {
      return {id: e.user_id, tasks: e.tasks, rate: e.rate}
    }) ?? [{}];


    props.user?.writers?.forEach(element => {
      if (element.name) {
        form.lyrics_writers.push(element.name)
      }
    });
    props.user?.composers?.forEach(element => {
      if (element.name) {
        form.composers.push(element.name)
      }
    });
    if (form.composers.length == 0)
      form.composers = [''];
    if (form.lyrics_writers.length == 0)
      form.lyrics_writers = [''];
    if (form.musicians.length == 0)
      form.musicians = [{name: ''}];
    if (form.participants.length == 0)
      form.participants = [{}]
  }
});
const openArtistCreateDialog = (whichArtistConfigToAdString) => {
  whichArtistConfigToAdd.value = whichArtistConfigToAdString;
  createArtistDialog.value = true;
}

</script>

<style scoped>

</style>
