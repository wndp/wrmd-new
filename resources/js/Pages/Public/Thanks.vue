<template>
  <PublicLayout title="Thank You!">
    <div class="relative py-8 px-4 sm:px-6 lg:px-8 bg-white">
      <div class="prose prose-blue mx-auto">
        <h3 v-if="firstName">
          Dear {{ firstName }} {{ lastName }},
        </h3>
        <h2>Thank You For Supporting<br>Wildlife Rehabilitation MD (WRMD)!</h2>
        <p>
          Thank you for your generous gift to Wildlife Rehabilitation MD<template v-if="amount">
            of {{ amount }} {{ currency }}</template>. We are honored to have your support. Through your donation we will be able to continue growing Wildlife Rehabilitation MD and supporting our users around the world. You're truly making the difference for us, <strong class="font-bold">and we are extremely grateful</strong>!
        </p>
        <p>Each year the number of organizations we support grows by nearly 30% and to-date {{ patientsCount }} wildlife patients have been admitted from across the world. Without you, none of the work we do would be possible. And for that we owe you the world's largest thank you.</p>
        <p class="text-sm text-gray-500 italic">
          Wildlife Rehabilitation MD is managed by <a
            href="http://wildneighborsdp.org"
            target="_blank"
          >The Wild Neighbors Database Project</a>, a 501(c)(3) tax-exempt not-for-profit organization based in the United States (Tax ID: 46-3151740). If you have specific questions about how your gift is being used or our organization as a whole, please don’t hesitate to contact us at <a href="mailto:support@wildneighborsdp.org">support@wildneighborsdp.org</a>.
        </p>
        <img
          loading="lazy"
          width="838"
          height="420"
          style="max-width: 100%;"
          src="../../../images/devin-and-rachel.png"
          alt="Devin and Rachel"
        >
        <p class="text-foot-note">
          &mdash; Devin Dombrowski and Rachel Avilla
        </p>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
  import PublicLayout from '@/Layouts/PublicLayout.vue';

  defineProps({
    firstName: {
      type: String,
      default: null
    },
    lastName: {
      type: String,
      default: null
    },
    amount: {
      type: Number,
      default: null
    },
    currency: {
      type: String,
      default: null
    },
    patientsCount: {
      type: Number,
      required: true
    },
  })
</script>
