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
import {useCrudStore} from "@/Stores/useCrudStore";

const defaultStore = useDefaultStore();
const crudStore = useCrudStore();
const props = defineProps({
  user: {},
});
const togglePermission = (permission) => {

    permission.checked = !permission.checked;
    crudStore.post(route('control.user-management.users.togglePermissions',props.user.id),{
        permission:permission.id,
    })
};

</script>
<template>

    <div class="grid grid-cols-3 gap-5">
        <div v-for="i in Object.keys(user.tab)">
            <h1 class="label-sm c-strong-950">{{i}}</h1>
            <div @click="togglePermission(permission)" class="flex items-center gap-2" v-for="permission in user.tab[i]">
                <button class="appCheckBox" :class="permission.checked ? 'checked' : ''">
                    <CheckIcon color="#fff" />
                </button>
               <p class="paragraph-sm c-strong-950 cursor-pointer"> {{permission.name}}</p>
            </div>
        </div>


    </div>
</template>

<style scoped>

</style>
