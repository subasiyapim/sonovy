<script setup>
import {Head, Link, usePage} from '@inertiajs/vue3';
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

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
