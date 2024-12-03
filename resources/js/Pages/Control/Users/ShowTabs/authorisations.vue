<script setup>
import {ref,reactive} from 'vue';
import {IconButton,PrimaryButton,RegularButton} from '@/Components/Buttons';
import {FormElement} from '@/Components/Form';
import {usePage} from '@inertiajs/vue3';

import {

  EyeOnIcon,
  TrashIcon
} from '@/Components/Icons'
import {useDefaultStore} from "@/Stores/default";

const defaultStore = useDefaultStore();

const props = defineProps({
  user: {},
});

</script>
<template>
    <div>
        <button class="appCheckBox checked"
                >
                <CheckIcon color="#fff" />
        </button>
    </div>
</template>

<style scoped>

</style>
