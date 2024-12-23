

<template>

<svg width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M8.75 5.5H12.5L8 10L3.5 5.5H7.25V0.25H8.75V5.5ZM2 12.25H14V7H15.5V13C15.5 13.1989 15.421 13.3897 15.2803 13.5303C15.1397 13.671 14.9489 13.75 14.75 13.75H1.25C1.05109 13.75 0.860322 13.671 0.71967 13.5303C0.579018 13.3897 0.5 13.1989 0.5 13V7H2V12.25Z" :fill="color"/>
</svg>



</template>
<script setup>
const props =defineProps({
        color:{
            default:'white'
        }
});
</script>
