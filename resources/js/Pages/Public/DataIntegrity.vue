<template>
  <PublicLayout title="Data Integrity">
    <div class="relative py-8 px-4 sm:px-6 lg:px-8 bg-white">
      <div class="prose prose-lg mx-auto">
        <h1>
          <span class="mt-2 block text-3xl text-center leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">Data Integrity</span>
        </h1>
        <p class="mt-8 text-xl text-gray-500 leading-8">
          We are often asked about the integrity of the data within Wildlife Rehabilitation MD, which is an entirely fair question to ask. The data collected by Wildlife Rehabilitation MD is private and precious to each account and it is our responsibility to keep it that way. Over the course of the development of Wildlife Rehabilitation MD we have adopted many precautions to ensure that your data stays put. Below is a list of the features within Wildlife Rehabilitation MD that help to ensure just that.
        </p>
      </div>
      <div class="mt-6 prose prose-xl prose-blue text-gray-500 mx-auto">
        <ol>
          <li>Your data is stored on private servers that only we and the server administrators have access to.</li>
          <li>The database is replicated across multiple servers which allows you faster access to your data.</li>
          <li>Every day the entire database is backed up remotely. In the event of a system-wide breakdown, we can quickly restore the entire database to the last back-up cycle.</li>
          <li>Every week the entire database server is backed up. If the master database server ever failed, it could be restored back to its last stable state.</li>
          <li>We keep a revision history of every change to every patient. If you create, update or delete a record we record that change, when it occurred and who made it.</li>
          <li>At any time, you may export any of your records from Wildlife Rehabilitation MD, keeping an external backup of your data.</li>
          <li>When you share a patient (<em>print, email or export</em>), a log may be created of that sharing and can be viewed at any time allowing you to see when and how data left your account.</li>
          <li>There are other security and integrity walls in place, but in the interest of security we won't disclose them. :)</li>
        </ol>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>
