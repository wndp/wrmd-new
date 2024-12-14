<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>

<template>
  <PublicLayout title="WildAlert">
    <div class="relative py-8 px-4 sm:px-6 lg:px-8 bg-white">
      <div class="prose prose-lg prose-blue mx-auto">
        <div class="sm:flex">
          <div class="mr-4 flex-shrink-0 self-end">
            <img
              src="../../../images/WildAlert-logo.png"
              alt="WildAlert"
              class="h-32 aspect-square not-prose"
            >
          </div>
          <div>
            <h1 class="mt-2 block text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
              WildAlert
            </h1>
            <h2 class="mt-6 text-3xl font-bold tracking-tight text-blue-600 sm:text-xl">
              A Wildlife Morbidity and Mortality Event Alert System.
            </h2>
          </div>
        </div>
        <p class="mt-8 text-xl text-gray-500 leading-8">
          <a
            href="https://wildalert.org/"
            target="_blank"
          >WildAlert</a> is a system used for early detection of wildlife morbidity and mortality events. It provides timely and accurate information on where wildlife disease events are occurring or have occurred to facilitate decision making and improve our understanding of health threats.
        </p>
      </div>
      <div class="mt-6 prose prose-xl prose-blue text-gray-500 mx-auto">
        <p>
          Wildlife Rehabilitation MD (WRMD) contributes data to WildAlert in real time. The system allows for monitoring, filtering, visualizing, and disseminating information on wildlife morbidity and mortality events, facilitating early and enhanced detection of health threats.
        </p>
        <p>Users of Wildlife Rehabilitation MD (WRMD) benefit from WildAlert by receiving notifications on their patient records, who may be part of an ongoing wildlife health event. Only essential data is used in WildAlert such as the patient common name, date and location found, diagnosis and the initial exam.</p>
        <p>
          WildAlert was developed by investigators at <Link :href="route('about.wndp')">
            The Wild Neighbors Database Project
          </Link>, University of California-Davis, EpiEcos, and the Wildlife Health Lab of the California Department of Fish and Wildlife, in partnership with a network of wildlife rehabilitation organizations in California.
        </p>
        <p>
          To learn more please visit the <a
            href="https://wildalert.org/"
            target="_blank"
          >WildAlert website</a>.
        </p>
      </div>
    </div>
  </PublicLayout>
</template>
