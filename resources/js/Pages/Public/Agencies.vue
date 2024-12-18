<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
  annualReports: {
    type: Array,
    required: true
  },
})
</script>

<template>
  <PublicLayout title="Regulatory Agencies">
    <div class="relative py-8 px-4 sm:px-6 lg:px-8 bg-white">
      <div class="prose prose-lg mx-auto">
        <h1>
          <span class="mt-2 block text-3xl text-center leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">Regulatory Agencies</span>
        </h1>
        <p class="mt-8 text-xl text-gray-500 leading-8">
          We recognize that wildlife rehabilitation has many legal regulations that must be observed both federally and locally. Wildlife Rehabilitation MD strives to advance the wildlife rehabilitation profession as well as standardize reporting to agencies. If you are an entity that oversees wildlife rehabilitators, help us fill in the legal holes.
        </p>
      </div>
      <div class="mt-6 prose prose-lg prose-blue text-gray-500 mx-auto">
        <h4>Annual Reports</h4>
        <p>Part of what makes Wildlife Rehabilitation MD so special is it's ability to produce customized annual reports at the push of a button. The customization is the real trick. Each state, province or subdivision has their own idea of what should be reported on at the end of the year and how it should be organized.</p>
        <p>
          View the list below of all the annual reports that Wildlife Rehabilitation MD is currently able to create. If your state, province or subdivision is not listed, then don't fret. Send us the actual report as created by your government and we'll recreate it as soon as possible.
        </p>
        <ol class="md:columns-2">
          <li
            v-for="report in annualReports"
            :key="report"
            class="first:mt-0"
          >
            {{ report }}
          </li>
        </ol>
        <h4>Missing or Updated Annual Reports</h4>
        <p>Send missing or updated annual reports to <a href="mailto:support@wildneighborsdp.org?subject=Annual Report">support@wildneighborsdp.org</a> and please indicate in the subject that you are including an annual report.</p>
      </div>
    </div>
  </PublicLayout>
</template>
