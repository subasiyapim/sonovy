<template>


    <div :class="element ? 'border-green-700 bg-white-500' :'border-soft-200 bg-white'" class="w-full flex flex-col  p-4 border flex items-start radius-8 c-white-500 p-3 cursor-pointer" @click="onChange">
        <p class="label-sm c-strong-950" v-if="config?.title">{{config?.title}}</p>
        <div class="flex w-full">
            <div class="flex-1 c-sub-600 paragraph-xs" >
                 {{placeholder}}
                </div>
            <div class="relative flex justify-center pointer-events-none">
                    <label class="appSwitch">
                        <input type="checkbox" :checked="element">
                        <span class="appSwitchSlider round"></span>
                    </label>
            </div>
        </div>
    </div>


</template>

<script setup>
    import { useSlots,ref,computed } from 'vue'
    const isChecked = ref(false)
    const slots = useSlots()
    const props = defineProps({
        type:{type:String},
        placeholder: { type: String},
        modelValue:{},
        config:{},

    })
    const emits = defineEmits(['update:modelValue','change','input']);

    const element = computed({
        get:() => props.modelValue,
        set:(value) => emits('update:modelValue',value)
    })
    const onChange = () => {
        element.value = !element.value;
        emits('change',element.value);
    }


</script>

<style scoped>


.appSwitch {
  position: relative;
  display: inline-block;
  width: 28px;
  height: 16px;
}

.appSwitch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.appSwitchSlider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: var(--soft-200);
  -webkit-transition: .4s;
  transition: .4s;

}

.appSwitchSlider:before {
  position: absolute;
  content: "";
  height: 12px;
  width: 12px;
  left: 2px;
  bottom: 2px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}
.appSwitchSlider:after {
  position: absolute;

  content: "";
  height: 4px;
  width: 4px;
  left: 6px;
  top: 6px;
  border-radius:50%;
  background-color: var(--soft-200);
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .appSwitchSlider {
  background-color: var(--dark-green-700);
}
input:checked + .appSwitchSlider:after {
  background-color: var(--dark-green-700);
}

input:focus + .appSwitchSlider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .appSwitchSlider:before {
  -webkit-transform: translateX(12px);
  -ms-transform: translateX(12px);
  transform: translateX(12px);
}
input:checked + .appSwitchSlider:after {
  -webkit-transform: translateX(12px);
  -ms-transform: translateX(12px);
  transform: translateX(12px);
}



/* Rounded sliders */
.appSwitchSlider.round {
  border-radius: 34px;
}

.appSwitchSlider.round:before {
  border-radius: 50%;
}
</style>
