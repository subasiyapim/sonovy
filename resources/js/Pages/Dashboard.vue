<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {Head, usePage} from '@inertiajs/vue3';

const props = defineProps({
  auth: Object,
  tenant_id: Number,
});

declare global {
  interface Window {
    Echo: any;
  }
}

</script>

<template>


  <AdminLayout :title="'Dashboard'">

  </AdminLayout>
</template>
