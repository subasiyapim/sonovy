import {h,render} from 'vue';
import AppNotification from './AppNotification.vue'
const showNotification = (title,type) => {
    // const body = document.querySelector('body');
    // const existNotification =  document.querySelector('.appNotificationMessage');
    // if(existNotification){
    //     existNotification.remove();
    // }

    // render(h(AppNotification,{
    //     title:title,
    //     type:type ?? 'success'
    // }));


    // setTimeout(() => {
    //     const finded =  document.querySelector('.appNotificationMessage');
    //     if(finded) {
    //         finded.remove();
    //     }
    // },1000)
};

export  {
showNotification
}
