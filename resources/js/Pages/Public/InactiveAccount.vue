<template>
  <PublicLayout title="Inactive Account Policy">
    <div class="relative py-8 px-4 sm:px-6 lg:px-8 bg-white">
      <div class="prose prose-lg mx-auto">
        <h1>
          <span class="mt-2 block text-3xl text-center leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">Inactive Account Policy</span>
        </h1>
      </div>
      <div class="mt-6 prose max-w-none prose-blue text-gray-500 mx-auto">
        <h4>Last updated: Sep 10, 2023</h4>
        <p>This Inactive Account Policy describes Our policies and procedures on ....</p>
        ...
        <h2>Changes to this Inactive Account Policy</h2>
        <p>We may update Our Inactive Account Policy from time to time. We will notify You of any changes by posting the new Inactive Account Policy on this page.</p>
        <p>We will let You know via email and/or a prominent notice on Our Service, prior to the change becoming effective and update the &quot;Last updated&quot; date at the top of this Inactive Account Policy.</p>
        <p>You are advised to review this Inactive Account Policy periodically for any changes. Changes to this Inactive Account Policy are effective when they are posted on this page.</p>
        <h2>Contact Us</h2>
        <p>If you have any questions about this Inactive Account Policy, You can contact us:</p>
        <ul>
          <li>
            By email: <a href="mailto:support@wildneighborsdp.org?subject=WRMD%20Inactive%20Account%20Policy%20">support@wildneighborsdp.org</a>
          </li>
          <li>
            By visiting this page on our website: <Link :href="route('contact.create')">
              https://www.wrmd.org/contact
            </Link>
          </li>
        </ul>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>
