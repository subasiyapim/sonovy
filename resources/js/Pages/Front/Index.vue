<script setup lang="ts">
import {Head, Link, usePage} from '@inertiajs/vue3';
import DropdownLink from "@/Components/DropdownLink.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

defineProps<{
  canLogin?: boolean;
  canRegister?: boolean;
  laravelVersion: string;
  phpVersion: string;
}>();

function handleImageError() {
  document.getElementById('screenshot-container')?.classList.add('!hidden');
  document.getElementById('docs-card')?.classList.add('!row-span-1');
  document.getElementById('docs-card-content')?.classList.add('!flex-row');
  document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
  <Head title="Welcome"/>
  <div class="h-screen flex flex-col justify-center items-center mx-auto gap-10">

    <ApplicationLogo/>

    <template v-if="usePage().props.auth.user">
      <form method="POST" @submit.prevent="logout">
        <Link :href="route('logout')"
              method="post"
              class="btn btn-primary font-bold text-lg"
              as="button">Çıkış yap
        </Link>
      </form>

    </template>
    <template v-else>
      <Link href="/login" class="btn btn-primary font-bold text-lg">Giriş yap</Link>
    </template>


  </div>
</template>
