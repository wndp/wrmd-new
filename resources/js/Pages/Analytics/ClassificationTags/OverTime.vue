<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AnalyticsHeader from '../Partials/AnalyticsHeader.vue';
import GeneratedOn from '../Partials/GeneratedOn.vue';
import StreamGraph from '@/Components/Analytics/StreamGraph.vue';
import {__} from '@/Composables/Translate';

const category = computed(() => usePage().props.analytics.groupStudly);

const text = {
  CircumstancesOfAdmission: {
    heading: __('Circumstances of Admission Over Time'),
    title: __('Circumstances of Admission Over Time'),
    subtitle: __('Excluding Circumstances of Admission With No Occurrences')
  },
  ClinicalClassifications: {
    heading: __('Clinical Classifications Over Time'),
    title: __('Clinical Classifications Over Time'),
    subtitle: __('Excluding Clinical Classifications With No Occurrences')
  }
};
</script>

<template>
  <AppLayout title="Analytics">
    <AnalyticsHeader />
    <h2 class="text-2xl font-normal text-gray-900">
      {{ text[category].heading }}
    </h2>
    <GeneratedOn class="mb-4" />
    <StreamGraph
      id="classification-tags-over-time"
      :title="text[category].title"
      :subtitle="text[category].subtitle"
      :urlParams="{category}"
      :height="515"
    />
  </AppLayout>
</template>
