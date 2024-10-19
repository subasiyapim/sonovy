<template>
    <div class="flex justify-between items-center  my-4">
        <input v-model="chars[0]" @input="onChange" @keyup="onKeyDown" class=" border pinField "  maxlength="1" />
        <input v-model="chars[1]" @input="onChange" @keyup="onKeyDown" class=" border pinField "  maxlength="1" />
        <input v-model="chars[2]" @input="onChange" @keyup="onKeyDown" class=" border pinField "  maxlength="1" />
        <input v-model="chars[3]" @input="onChange" @keyup="onKeyDown" class=" border pinField "  maxlength="1" />
        <input v-model="chars[4]" @input="onChange" @keyup="onKeyDown" class=" border pinField "  maxlength="1" />
        <input v-model="chars[5]" @input="onChange" @keyup="onKeyDown" class=" border pinField "  maxlength="1" />
    </div>
</template>


<script setup>

import {computed,ref,watch} from 'vue';
const props =defineProps({
    modelValue:{

    },
});

const chars = ref({
    0:"",
    1:"",
    2:"",
    3:"",
    4:"",
    5:"",
})
watch(chars.value, async (newValue, oldValue) => {

    element.value = `${newValue[0]}${newValue[1]}${newValue[2]}${newValue[3]}${newValue[4]}${newValue[5]}`
})


const emits = defineEmits(['update:modelValue','onCompleted']);
const element = computed({
    get:() => props.modelValue,
    set:(value) => emits('update:modelValue',value)
})
function findNextInput(element) {
    var nextSibling = element.nextElementSibling;
    while (nextSibling) {
        if (nextSibling.tagName.toLowerCase() === "input") {
            return nextSibling;
        }
        nextSibling = nextSibling.nextElementSibling;
    }
    return null; // If no next input element is found
}

function findPreviousSibling(element) {
    var previousSibling = element.previousElementSibling;
    while (previousSibling) {
        if (previousSibling.tagName.toLowerCase() === "input") {
            return previousSibling;
        }
        previousSibling = previousSibling.previousElementSibling;
    }
    return null; // If no next input element is found
}

const onChange = (e) => {

    if(e.inputType == 'deleteContentBackward'){
        const previousSibling = findPreviousSibling(e.target);
        if(previousSibling){
            previousSibling.focus();
        }
    }else {
         const nextSibling = findNextInput(e.target);

        if(nextSibling){
            nextSibling.focus();


        }
    }

    if(element.value.length == 6){
       emits('onCompleted',element.value)
    }


}

const onKeyDown  = (e) => {

    if(e.key == 'Backspace'){
        if(!e.target.value){
            const previousSibling = findPreviousSibling(e.target);
            if(previousSibling){
                previousSibling.focus();
            }
        }
    }
}
</script>
