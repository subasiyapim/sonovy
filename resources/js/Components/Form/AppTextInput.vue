<template>
   <div class="w-full flex h-9 border-text-input flex items-center radius-8 c-white-500 bg-white" :class="hasSlot('icon') || type == 'web' || type == 'phone' ? 'px-2.5' :''">
        <div v-if="hasSlot('icon')">
            <slot  name="icon" />
        </div>

        <div v-if="type == 'web'" class="border-r border-soft-200 c-soft-400 pe-2 me-2 paragraph-sm">
            https://
        </div>

         <div v-if="type == 'phone'" class="border-r border-soft-200 c-soft-400 pe-2 me-2 paragraph-sm">
            <div class="w-12 max-w-xs mx-auto">

                    <select id="options" v-model="phoneCode" @change="onChangedCode"  name="options" class="block  phoneSelect paragraph-xs border-none focus:border-none focus:ring-0 focus:outline-none  radius-8">
                        <option v-for="code in config?.codes" :value="`+${code.value}`">
                        {{code.iconKey}} +{{code.value}}
                        </option>

                    </select>
                </div>
        </div>

        <input  v-model="element" @input="onInput" @change="onChange" :disabled="disabled" v-debounce="400" ref="inputEl" class="border-none focus:outline-none focus:border-none  focus:border-transparent focus:ring-0 h-full w-full bg-transparent paragraph-sm c-strong-950  disabledBg " :type="type" :placeholder="placeholder">
        <IconButton v-if="type == 'password'" @click="onEyeClicked">
           <EyeOnIcon  v-if="isPasswordHidden" color="var(--sub-600)" />
           <EyeOffIcon v-else color="var(--sub-600)" />
        </IconButton>
    </div>

</template>

<script setup>
    import { useSlots,ref,computed } from 'vue'
    import {EyeOnIcon,EyeOffIcon} from '@/Components/Icons'
    import {IconButton} from '@/Components/Buttons'
    const slots = useSlots()
    const props = defineProps({
        type:{type:String},
        placeholder: { type: String},
        modelValue:{},
        config:{},
        disabled:{
            default:false
        }

    })
    const emits = defineEmits(['update:modelValue','change','input']);

    const element = computed({
        get:() => {

            if(props.type == 'phone'){
                if(props.modelValue){
                    const splitted = props.modelValue.split(" ");
                    if(splitted.length > 1){
                        phoneCode.value = splitted[0];

                    }
                        return splitted[1];
                }
                return props.modelValue;


            }else {
                return props.modelValue;

            }
        },
        set:(value) => {
            if(props.type == 'phone'){
                value = `${phoneCode.value} ${value}`;
            }
            return emits('update:modelValue',value);
        }
    })

    const isPasswordHidden = ref(true);
    const inputEl = ref();

    const hasSlot = (name) => {
        return !!slots[name];
    }
    const onEyeClicked = () =>{
        if(isPasswordHidden.value){
            inputEl.value.setAttribute('type','text');
            isPasswordHidden.value = false;

        }else {
             inputEl.value.setAttribute('type','password');
            isPasswordHidden.value = true;
        }
    }
    const phoneCode = ref('+90');

    const onInput = (e) => {
        emits('input',e.target.value);
    }
    const onChange = (e) => {
        emits('change',e.target.value);
    }
    const onChangedCode  = () => {
        console.log("ELEÖENT",element.value);

        emits('update:modelValue',`${phoneCode.value} ${element.value}` )
    }

</script>

<style scoped>
input::placeholder{
    color:var(--soft-400)
}
input{
    color:var(--sub-600)
}
</style>
