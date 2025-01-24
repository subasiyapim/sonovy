<template>
  <BaseDialog v-model="isDialogOn" align="right" :title="user?.id ? 'Kullanıcı Düzenle' :'Yeni Kullanıcı Ekle'"
              :description="user?.id ? 'Kullanıcı düzenleyebilirsiniz' :'Temel bilgileri girerek yeni kullanıcı oluşturabilirsiniz'">
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
        <FormElement @change="onCountryChoosen" label-width="190px" :required="true" v-model="form.country_id"
                   :error="form.errors.country_id" type="select"
                   :label="__('control.user.fields.country_id')"
                   :placeholder="__('control.user.fields.country_id_placeholder')" :config="countryConfig">
            <template #option="scope">
                <span>{{ scope.data.iconKey }}</span>
                <span class="paragraph-sm c-strong-950">{{ scope.data.label }}</span>
            </template>
            <template #model="scope">
                <div v-if="scope.data" class="flex items-center gap-2 paragraph-sm c-strong-950">
                    <span>{{ countryConfig.data.find((el) => el.value == scope.data)?.iconKey }}</span>
                    <span>{{ countryConfig.data.find((el) => el.value == scope.data)?.label }}</span>
                </div>
            </template>
        </FormElement>

        <FormElement label-width="190px"
                   :required="false"
                   :error="form.errors.email"

                   v-model="form.email"
                   :label="__('control.user.fields.email')"
                   type="text"
                   :placeholder="__('control.user.fields.email_placeholder')"/>

        <FormElement :required="true" label-width="190px" v-model="form.language_id"
                   :error="form.errors.language_id" type="select"
                   :label="__('control.user.fields.language_id')"
                   :placeholder="__('control.user.fields.language_id_placeholder')" :config="languageConfig">
            <template #option="scope">
                <span>{{ scope.data.iconKey }}</span>
                <span class="paragraph-sm c-strong-950">{{ scope.data.label }}</span>
            </template>
            <template #model="scope">
                <div v-if="scope.data" class="flex items-center gap-2 paragraph-sm c-strong-950">
                    <span>{{ languageConfig.data.find((el) => el.value == scope.data)?.iconKey }}</span>
                    <span>{{ languageConfig.data.find((el) => el.value == scope.data)?.label }}</span>
                </div>
            </template>
        </FormElement>

        <div class="flex items-center gap-2">

            <FormElement v-if="form.country_id" type="custom" :error="form.errors.city_id || form.errors.district_id" label="İl İlçe" label-width="190px" class="w-full">
                <div class="flex items-center gap-2">
                    <AppSelectInput  ref="citySelect"  @change="onCityChoosen"  class="flex-1" v-model="form.city_id"
                            :error="form.errors.city_id" type="select"

                            :placeholder="__('control.user.fields.city_id')" :config="cityConfig">
                         <template #model="scope">

                            <div  class="flex items-center gap-2 paragraph-sm c-strong-950">
                                {{cityConfig.data.find((e) => e.id == form.city_id)?.name}}
                            </div>
                        </template>
                    </AppSelectInput>
                    <AppSelectInput  ref="districtSelect" class="flex-1" v-model="form.district_id"
                            :error="form.errors.district_id" type="select"

                            :placeholder="__('control.user.fields.district_id_placeholder')" :config="districtConfig">
                        <template #model="scope">

                            <div  class="flex items-center gap-2 paragraph-sm c-strong-950">
                                {{districtConfig.data.find((e) => e.id == form.district_id)?.name}}
                            </div>
                        </template>
                    </AppSelectInput>
                </div>
            </FormElement>
        </div>


      <FormElement label-width="190px"
                   :required="false"
                   :error="form.errors.address"
                   :config="{letter:500}"
                   v-model="form.address"
                   :label="__('control.user.fields.address')"
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


        </FormElement>
        <div v-if="form.is_company" class="flex flex-col gap-6">
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
            :error="form.errors.password_confirmation"
            v-model="form.password_confirmation"
            :label="__('control.user.fields.password_confirmation')"
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
import {computed, ref, onMounted,nextTick} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import {toast} from 'vue3-toastify';
import {useCrudStore} from '@/Stores/useCrudStore';
import {FormElement, ArtistInput, AppIncrementer, AppSelectInput, AppSliderInput} from '@/Components/Form'
import InputError from "@/Components/InputError.vue";



const citySelect = ref();
const districtSelect = ref();
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
const cityLoading = ref(false);
const districtLoading = ref(false);

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
    address:'',
    commission_rate:'',
    is_company:false,
    phone:'',
    password:'',
    password_confirmation:'',
});





const emits = defineEmits(['update:modelValue', 'done','update']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})


const countryConfig = computed(() => {
  return {
        hasSearch:true,
        data: usePage().props.countries,
  };
})

const languageConfig = computed(() => {
  return {
    hasSearch:true,
    data: usePage().props.languages,
  };
})


const cityConfig = computed(() => {
  return {
    value : 'id',
    label:'name',
    hasSearch: true,
    data: [],
  }
})
const districtConfig = computed(() => {
  return {
    value : 'id',
    label:'name',
    hasSearch: true,
    data: [],
  }
})

const onSubmit = async (e) => {

    if(props.user?.id){
        form.put(route('control.user-management.users.update',props.user.id),
            {
                onFinish: () => {

                },
                onSuccess: async (e) => {
                    console.log(e.props);

                    toast.success(e.props.notification.message);
                    isDialogOn.value = false;
                    emits('update', e.props.notification.user)

                },
                onError: (e) => {
                    toast.error(Object.values(e)[0])
                }
            }
        );
    }else {
        form.post(route('control.user-management.users.store'),
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
            }
        );
    }


}

const checkIfDisabled = computed(() => {
  return false;
})

const onCountryChoosen = async (e) => {

    cityLoading.value = true;
    let response = await crudStore.post(route('control.findall.cities'),{
        country_id:e.value
    })


   nextTick(() => {
    citySelect.value.appendOptions(response);
     cityConfig.value.data = response;


     cityLoading.value = false;
   })

}
const onCityChoosen = async (e) => {

    districtLoading.value = true;
    let districtResponse = await crudStore.post(route('control.findall.districts'),{
        city_id:e.id
    })
    if(districtResponse.length > 0){
         nextTick(() => {
            districtConfig.value.data = districtResponse;
            districtSelect.value.appendOptions(districtResponse);
            districtLoading.value = false;
            console.log("DSADASD",districtResponse);

        })
    }

}
onMounted(() => {
  if (props.user) {
        form['id'] = props.user.id;
        form['name'] = props.user.name;
        form['country_id'] = props.user.country_id;
        if(props.user.country_id){
            onCountryChoosen({value:props.user.country_id})
        }
        form['city_id'] = props.user.city_id;
        if(props.user.city_id){
            console.log("PROPS:USsER CİTY",props.user.city_id);

            onCityChoosen({id:props.user.city_id})
        }

        form['district_id'] =props.user.district_id;
        form['language_id'] =props.user.language_id;
        form['company_name'] =props.user.company_name;
        form['tax_number'] =props.user.tax_number;
        form['tax_house'] =props.user.tax_house;
        form['tax_house'] =props.user.tax_house;
        form['email'] =props.user.email;
        form['address'] =props.user.address;
        form['commission_rate'] =props.user.commission_rate;
        form['is_company'] = props.user.is_company ?? false;
        form['phone'] =props.user.phone;

  }
});


</script>

<style scoped>

</style>
