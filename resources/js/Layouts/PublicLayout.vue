<script setup>
import {Head} from '@inertiajs/vue3';
import PublicFooter from '@/Layouts/Partials/PublicFooter.vue';
import PublicHeader from '@/Layouts/Partials/PublicHeader.vue';

defineProps({
  title: {
      type: String,
      default: 'WRMD'
  }
});
</script>

<template>
  <Head :title="title" />
  <div class="flex flex-col h-screen">
    <PublicHeader />

    <!-- Page Content -->
    <main
      id="main"
      class="flex-grow relative"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 sm:mt-16 mb-8">
        <slot />
      </div>
    </main>

    <!-- Footer Content -->
    <PublicFooter />
  </div>
</template>
