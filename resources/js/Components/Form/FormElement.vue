<template>
  <div class="flex items-start" :class="direction == 'horizontal' ? 'flex-row' : 'flex-col mb-2'">
    <div class="flex items-center gap-1" :style="`width:${labelWidth}`">
      <label class="label-sm c-strong-950">{{ label }}</label>
      <span v-if="required" class="c-blue-500 label-sm">*</span>
      <tippy v-if="hasSlot('tooltip')">
        <InfoFilledIcon color="var(--soft-300)"/>

        <template #content>
          <slot name="tooltip"/>
        </template>
      </tippy>

    </div>
    <div class="w-full" :class="error ? 'hasError' : ''">


        <AppTextInput v-if="type=='text' || type=='web' || type=='phone'|| type=='password'" :type="type" v-model="element" :placeholder="placeholder"></AppTextInput>
        <AppFancyCheckInput v-if="type=='fancyCheck'" :type="type" :config="config" v-model="element" :placeholder="placeholder"></AppFancyCheckInput>
        <AppTextareaInput v-else-if="type=='textarea'" v-model="element" :placeholder="placeholder" :config="config"></AppTextareaInput>
        <AppRadioInput v-else-if="type=='radio'" v-model="element" :placeholder="placeholder" :config="config"></AppRadioInput>
        <AppUploadInput v-else-if="type=='upload'" :label="config.label" :note="config.note" v-model="element" :placeholder="placeholder"></AppUploadInput>
        <AppSelectInput v-else-if="type=='select'" :config="config" :type="type" v-model="element" :placeholder="placeholder">
        <template v-if="hasSlot('first_child')" #first_child>
                <slot  name="first_child" />
        </template>
        <template v-if="hasSlot('empty')" #empty>
                <slot name="empty" />
        </template>
        <template v-if="hasSlot('option')" #option="scope">

                <slot name="option" :data="scope.scope" />
        </template>
        <template v-if="hasSlot('model')" #model="scope">
                <slot name="model" :data="scope.scope" />
        </template>
        </AppSelectInput>

        <slot v-if="type=='custom'" />

      <AppMultiSelectInput v-else-if="type=='multiselect'" :config="config" :type="type" v-model="element"
                           :placeholder="placeholder">
        <template v-if="hasSlot('first_child')" #first_child>
          <slot name="first_child"/>
        </template>
        <template v-if="hasSlot('empty')" #empty>
          <slot name="empty"/>
        </template>
      </AppMultiSelectInput>


      <slot name="description"/>
      <span v-if="error" class="c-error-500 paragraph-xs flex items-center gap-1 mt-2">
                <InfoFilledIcon color="var(--error-500)"/>
                {{ error }}
            </span>
    </div>

  </div>

</template>

<script setup>
import {computed} from 'vue';
import {InfoFilledIcon} from '@/Components/Icons'
import AppTextInput from './AppTextInput.vue';
import AppTextareaInput from './AppTextareaInput.vue';
import AppSelectInput from './AppSelectInput.vue';
import AppMultiSelectInput from './AppMultiSelectInput.vue';
import AppUploadInput from './AppUploadInput.vue';
import AppFancyCheckInput from './AppFancyCheckInput.vue';
import AppRadioInput from './AppRadioInput.vue';

import {useSlots} from 'vue';


const props = defineProps({
  direction: {
    default: 'horizontal', //vertical
  },
  required: {
    default: false,
  },
  error: {
    default: null,
  },
  label: {},
  labelWidth: {
    default: '120px'
  },
  placeholder: {},
  modelValue: {},
  type: {type: String, default: "text"},
  config: {type: Object}
})
const emits = defineEmits(['update:modelValue']);

const element = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})
const slots = useSlots()
const hasSlot = (name) => {
  return !!slots[name];
}
</script>

<style lang="scss" scoped>

</style>
