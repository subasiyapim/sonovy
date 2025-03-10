<template>
  <div class="flex items-start" :class="direction == 'horizontal' ? 'flex-row' : 'flex-col mb-2'">
    <div class="flex items-start gap-1" :style="`width:${labelWidth}`">
      <label v-if="label" class="label-sm c-strong-950">{{ label }}</label>
      <span v-if="required" class="c-blue-500 label-sm flex items-start">*</span>
      <tippy :interactive="true" v-if="hasSlot('tooltip')">
        <InfoFilledIcon color="var(--soft-300)"/>

        <template #content>
          <slot name="tooltip"/>
        </template>
      </tippy>

    </div>
    <div class="w-full" :class="error ? 'hasError' : ''">


      <AppTextInput v-if="type=='text' || type=='web' || type=='phone'|| type=='password'" :config="config" :type="type"
                    v-model="element" :placeholder="placeholder" :disabled="disabled"></AppTextInput>
      <AppFancyCheckInput v-if="type=='fancyCheck'" :type="type" :config="config" v-model="element"
                          :placeholder="placeholder" :disabled="disabled" @change="change"></AppFancyCheckInput>
      <AppTextareaInput v-else-if="type=='textarea'" v-model="element" :placeholder="placeholder" :config="config"
                        :disabled="disabled"></AppTextareaInput>
      <AppRadioInput v-else-if="type=='radio'" v-model="element" @change="change" :placeholder="placeholder"
                     :config="config" :disabled="disabled"></AppRadioInput>
      <AppUploadInput @onImageDelete="$emit('onImageDelete',$event)" v-else-if="type=='upload'" :config="config" v-model="element" :placeholder="placeholder"
                      :disabled="disabled"></AppUploadInput>
      <AppSliderInput v-else-if="type=='slider'" :config="config" :type="type" v-model="element"
                      :placeholder="placeholder" :disabled="disabled"></AppSliderInput>

      <AppSelectInput ref="appSelect" v-else-if="type=='select'" @change="change" :config="config" :type="type" v-model="element"
                      :placeholder="placeholder" :disabled="disabled">
        <template v-if="hasSlot('first_child')" #first_child>
          <slot name="first_child"/>
        </template>
        <template v-if="hasSlot('empty')" #empty>
          <slot name="empty"/>
        </template>
        <template v-if="hasSlot('option')" #option="scope">

          <slot name="option" :data="scope.scope"/>
        </template>
        <template v-if="hasSlot('model')" #model="scope">
          <slot name="model" :data="scope.scope"/>
        </template>
      </AppSelectInput>

      <AppHashtagInput  v-else-if="type=='hashtags'" @change="change" :config="config" :type="type" v-model="element"
                      :placeholder="placeholder" :disabled="disabled">

      </AppHashtagInput>

      <slot v-if="type=='custom'"/>

      <AppMultiSelectInput ref="appMultiSelect" v-else-if="type=='multiselect'" :config="config" :type="type"
                           v-model="element"
                            @change="change"
                           :placeholder="placeholder" :disabled="disabled">
        <template v-if="hasSlot('first_child')" #first_child>
          <slot name="first_child"/>
        </template>
        <template v-if="hasSlot('empty')" #empty>
          <slot name="empty"/>
        </template>
        <template v-if="hasSlot('option')" #option="scope">

          <slot name="option" :data="scope.scope"/>
        </template>
        <template v-if="hasSlot('model')" #model="scope">
          <slot name="model" :data="scope.scope"/>
        </template>
        <template v-if="hasSlot('disabled')" #disabled>
          <slot name="disabled"/>
        </template>
      </AppMultiSelectInput>


      <slot name="description"/>
      <span v-if="error" class="c-error-500 paragraph-xs flex items-center gap-1 mt-2">
        <InfoFilledIcon color="var(--error-500)"/>
        {{ error }}
      </span>
      <span v-if="additionError" class="c-error-500 paragraph-xs flex items-center gap-1 mt-2">
        <InfoFilledIcon color="var(--error-500)"/>
        {{ additionError }}
      </span>

    </div>

  </div>

</template>

<script setup>
import {computed, ref} from 'vue';
import {InfoFilledIcon} from '@/Components/Icons'
import AppTextInput from './AppTextInput.vue';
import AppTextareaInput from './AppTextareaInput.vue';
import AppSelectInput from './AppSelectInput.vue';
import AppMultiSelectInput from './AppMultiSelectInput.vue';
import AppUploadInput from './AppUploadInput.vue';
import AppFancyCheckInput from './AppFancyCheckInput.vue';
import AppRadioInput from './AppRadioInput.vue';
import AppSliderInput from './AppSliderInput.vue';
import AppHashtagInput from './AppHashtagInput.vue';

import {useSlots} from 'vue';

const appMultiSelect = ref();
const appSelect = ref();
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
  additionError: {
    default: null,
  },
  label: {},
  labelWidth: {
    default: '120px'
  },
  disabled: {
    default: false,
  },
  placeholder: {},
  modelValue: {},
  type: {type: String, default: "text"},
  config: {type: Object}
})
const emits = defineEmits(['update:modelValue', 'change','onImageDelete']);

const element = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})
const slots = useSlots()
const hasSlot = (name) => {
  return !!slots[name];
}
const change = (e) => {
  emits('change', e)
}

defineExpose({appMultiSelect, appSelect})
</script>

<style lang="scss" scoped>

</style>
