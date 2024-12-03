<script setup>
import {ref,reactive} from 'vue';
import {IconButton,PrimaryButton,RegularButton} from '@/Components/Buttons';
import {FormElement} from '@/Components/Form';
import {usePage} from '@inertiajs/vue3';
import {CheckIcon} from '@/Components/Icons'
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

    <div class="grid grid-cols-3 gap-5">
        <div v-for="i in 5">
            <h1 class="label-sm c-strong-950">Yeni Yayın izinleri</h1>
            <div class="flex items-center gap-2">
                <button class="appCheckBox checked">
                    <CheckIcon color="#fff" />

                </button>
               <p class="paragraph-sm c-strong-950"> Ses yayını oluştur</p>
            </div>
        </div>


    </div>
</template>

<style scoped>

</style>
