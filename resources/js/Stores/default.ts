import {defineStore} from "pinia";
import {usePage} from '@inertiajs/vue3';

export const useDefaultStore = defineStore({
    id: 'default',
    state: () => ({}),
    getters: {},
    actions: {
        profileImage(name: string) {
            const profileImageColor = '7F9CF5'
            const profileImageBackgroundColor = 'EBF4FF'

            // Get the first letter of the first and second name
            const newNameArr = name.split(' ')

            const newName = newNameArr[0].charAt(0) + (newNameArr.length > 1 ? newNameArr[1].charAt(0) : '')

            return 'https://ui-avatars.com/api/?name=' + newName + '&color=' + profileImageColor + '&background=' + profileImageBackgroundColor
        },
    },
});