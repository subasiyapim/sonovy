
import {ref} from 'vue';
import {defineStore} from 'pinia';

export const useUiStore = defineStore('uiStore', () => {
    const isAdminViewOn = ref(localStorage.getItem("account-to-switch-back") ?? false)

    return {

        isAdminViewOn
    };
});
