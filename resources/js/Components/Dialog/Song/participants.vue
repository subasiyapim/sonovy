<template>
  <BaseDialog height="min-content" v-model="isDialogOn" align="center" :title="'Katılımcılar'"
              :description="'Katılımcı listesini görüntüleyin veya düzenleyin'">
    <template #icon>
      <PersonIcon color="var(--dark-green-950)"/>
    </template>

    <div class="p-5 flex flex-col gap-6">
        <div v-for="participant in song.participants" class="flex items-center justify-between">

            <div class="flex items-center gap-2 flex-1">
                <div class="w-10 h-10 rounded-full overflow-hidden">
                    <img :alt="participant.user?.name"
                        :src="defaultStore.profileImage(participant.user?.name)"
                    >
                </div>
                <div class="flex flex-col">
                    <p class="label-sm c-strong-950">
                        {{participant.user?.name}}
                    </p>
                    <p class="paragraph-xs c-neutral-500 flex-1">
                        {{participant.branch_names}}
                    </p>
                </div>
            </div>

            <p class="paragraph-sm c-neutral-500 flex-1">
                {{participant.user?.name}}
            </p>
            <p class="paragraph-sm c-neutral-500 flex-1">
               % {{participant.rate}} Pay
            </p>

        </div>
    </div>
    <div class="flex p-5 border-t border-soft-200 gap-4">
      <RegularButton @click="isDialogOn = false" class="flex-1">
       Kapat
      </RegularButton>
    </div>
  </BaseDialog>
</template>

<script setup>
import BaseDialog from '../BaseDialog.vue';
import {SectionHeader} from '@/Components/Widgets';
import {PersonIcon,AudioIcon,RingtoneIcon,MusicVideoIcon} from '@/Components/Icons'
import {RegularButton, PrimaryButton} from '@/Components/Buttons'
import {computed, ref, onMounted} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import {toast} from 'vue3-toastify';
import {useDefaultStore} from "@/Stores/default";

import {FormElement, AppFancyRadio} from '@/Components/Form'



const defaultStore = useDefaultStore();

const props = defineProps({
  modelValue: {
    default: false,
  },
  song:{

  }
})


const form = useForm({
  id: "",
  album_name: '',
  type:1

});


const emits = defineEmits(['update:modelValue', 'done']);
const isDialogOn = computed({
  get: () => props.modelValue,
  set: (value) => emits('update:modelValue', value)
})
const adding = ref(false);
const onSubmit = (e) => {
    adding.value = true;
    form.post(route('control.catalog.products.store'), {
        onFinish: () => {
            adding.value = false;
        },
        onSuccess: async (e) => {
            console.log("BŞARILIR");

        },
        onError: (e) => {
            console.log("HATAAAA", e);
        },
    });

}

const checkIfDisabled = computed(() => {

  return !form['album_name'];

})


onMounted(() => {

});
</script>

<style scoped>

</style>
