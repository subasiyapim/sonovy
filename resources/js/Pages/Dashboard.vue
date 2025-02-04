<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {Head, usePage} from '@inertiajs/vue3';

const props = defineProps({
  auth: Object,
  tenant_id: Number,
});

window.Echo
    .private('tenant.' + props.tenant_id + '.ws-test')
    .listen('Ws', (e) => {
      console.log(e)
    });

</script>

<template>


  <AdminLayout :title="'Dashboard'">

  </AdminLayout>
</template>
