<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>

<template>
  <PublicLayout title="About Wildlife Rehabilitation MD">
    <div class="relative py-8 px-4 sm:px-6 lg:px-8 bg-white">
      <div class="prose prose-blue prose-lg mx-auto">
        <div class="sm:flex">
          <div class="mr-4 flex-shrink-0 self-end">
            <img
              src="../../../images/WNDP-logo.png"
              alt="The Wild Neighbors Database Project"
              class="h-32 sm:w-32 not-prose"
            >
          </div>
          <div>
            <h1 class="mt-2 block text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
              The Wild Neighbors Database Project
            </h1>
          </div>
        </div>
        <p class="mt-8 text-xl text-gray-500 leading-8">
          Wildlife Rehabilitation MD (WRMD) is developed by <a
            href="https://www.wildneighborsdp.org/"
            target="_blank"
          >The Wild Neighbors Database Project</a>, a US 501(c)3 non-profit organization which promotes international wildlife rehabilitation by developing online data management software and helping implement its use worldwide to gather and exchange wildlife data.
        </p>
      </div>
      <div class="mt-6 prose prose-xl prose-blue text-gray-500 mx-auto">
        <p>Our founders, Rachel Avilla and Devin Dombrowski, have been wildlife rehabilitators since 2003. They saw a need for a wildlife database that was simple for everyone to use but scalable to large facilities. Devin was a self taught programmer and set out to create the Wildlife Rehablitation Medical Database, commonly referred to as WRMD. Development of WRMD began in 2010 and beta testing took place in 2012. WRMD was officially launched in 2013 and The Wild Neighbors Database Project was incorporated as a nonprofit in order to support WRMD and our other projects.</p>
        <p>Flash forward to today and WRMD is used all over the world. As of 2025, WRMD is used in all 50 states, 51 countries, has over 1,775 accounts and over 4 million wild patients admitted. We continue to play an active role in WRMD by providing ongoing user support and continuing to improve WRMD behind the scenes with updated features and new versions.</p>
      </div>
    </div>
  </PublicLayout>
</template>
