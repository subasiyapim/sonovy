

<template>

<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M4.5 3H15.75C15.9489 3 16.1397 3.07902 16.2803 3.21967C16.421 3.36032 16.5 3.55109 16.5 3.75V9H15V4.5H4.5V6.75L0.75 3.75L4.5 0.75V3ZM13.5 15H2.25C2.05109 15 1.86032 14.921 1.71967 14.7803C1.57902 14.6397 1.5 14.4489 1.5 14.25V9H3V13.5H13.5V11.25L17.25 14.25L13.5 17.25V15Z" :fill="color"/>
</svg>


</template>
<script setup>
const props =defineProps({
        color:{
            default:'white'
        }
});
</script>
