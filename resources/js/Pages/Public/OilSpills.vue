<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>

<template>
  <PublicLayout title="Oil Spills">
    <div class="relative py-8 px-4 sm:px-6 lg:px-8 bg-white">
      <div class="prose prose-lg prose-blue mx-auto">
        <h1 class="text-center text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
          WRMD and Oil Spills
        </h1>
        <p class="mt-8 text-xl text-gray-500 leading-8">
          In 2013 <Link :href="route('about.wndp')">
            The Wild Neighbors Database Project
          </Link> designed an oil spill database (O-WRMD) with the <a
            href="https://owcn.vetmed.ucdavis.edu/"
            target="_blank"
          >Oiled Wildlife Care Network</a> (based at the One Health Institute at UC Davis School of Veterinary Medicine). This database is specifically for animal care during an oil spill response in California. Interest around the world grew quickly on this feature and now, we're happy to share that an oil spill extension in WRMD is available for use by any organization.
        </p>
      </div>
      <div class="mt-6 prose prose-xl prose-blue text-gray-500 mx-auto">
        <h3>A Breakdown of Some of the Essential Features</h3>
        <h4>Processing</h4>
        <p>A critical component of oiled wildlife response is collection of data about the contaminant and how it has impacted patients. The processing module contains fields with standardized options for oiling percentage, oiling depth, location and type. This module also contains an easy upload feature for evidence photos.</p>
        <h4>Wash</h4>
        <p>Wash module fields can capture all aspects of the cleaning process, including pre-treatments, detergents, type of wash, wash team members and the duration of each step.</p>
        <h4>Waterproof Assessment</h4>
        <p>The waterproof assessment module provides an easy way to record conditioning notes using fields for buoyancy, preening and grooming behaviors, use of haul-outs, and feeding notes.</p>
        <h3>Oil Spill Extension FAQs</h3>
        <h4>How Can My Organization Use The Oil Spill Extension?</h4>
        <p>To use the WRMD oil spill extension please <a href="mailto:support@wildneighborsdp.org?subject=WRMD%20Oil%20Spill%20Extension">contact us via email</a> so we can help create your master account which will be used to activate individual oil spill accounts.</p>
        <h4>Is there a cost to use the oil spill extension?</h4>
        <p>Yes. There is a one-time activation fee of $500. No further charge until the extension is used during a response, then $1 / per patient billed annually.</p>
        <h4>How does the oil spill extension relate to WRMD Pro?</h4>
        <p>Along with the oil spill extension, users will also receive all the benefits of WRMD Pro.</p>
      </div>
    </div>
  </PublicLayout>
</template>
