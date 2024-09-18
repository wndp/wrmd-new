<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AnalyticsHeader from '../Partials/AnalyticsHeader.vue';
import GeneratedOn from '../Partials/GeneratedOn.vue';
import BarGraph from '@/Components/Analytics/BarGraph.vue';
import PieGraph from '@/Components/Analytics/PieGraph.vue';
import SunBurstGraph from '@/Components/Analytics/SunBurstGraph.vue';
import {__} from '@/Composables/Translate';

const category = computed(() => usePage().props.analytics.groupStudly);

const text = {
  CircumstancesOfAdmission: {
    heading: __('Circumstances of Admission Overview'),
    ClassificationTags: {
      title: __('Total of Each Circumstances of Admission'),
      subtitle: __('Excluding Circumstances of Admission With No Occurrences')
    },
    Hierarchy: {
      title: __('Circumstances of Admission by Hierarchy'),
      subtitle: __('Excluding Circumstances of Admission With No Occurrences')
    },
    Percentages: {
      title: __('Percent of Each Circumstances of Admission'),
      subtitle: __('Excluding Circumstances of Admission With No Occurrences')
    }
  },
  ClinicalClassifications: {
    heading: __('Clinical Classifications Overview'),
    ClassificationTags: {
      title: __('Total of Each Clinical Classification'),
      subtitle: __('Excluding Clinical Classifications With No Occurrences')
    },
    Hierarchy: {
      title: __('Clinical Classifications by Hierarchy'),
      subtitle: __('Excluding Clinical Classifications With No Occurrences')
    },
    Percentages: {
      title: __('Percent of Each Clinical Classification'),
      subtitle: __('Excluding Clinical Classifications With No Occurrences')
    }
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
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-4">
      <div>
        <BarGraph
          id="classification-tags"
          :title="text[category].ClassificationTags.title"
          :subtitle="text[category].ClassificationTags.subtitle"
          :urlParams="{category}"
          :height="1200"
        />
      </div>
      <div>
        <SunBurstGraph
          id="classification-tags-hierarchy"
          :title="text[category].Hierarchy.title"
          :subtitle="text[category].Hierarchy.subtitle"
          :urlParams="{category}"
          :height="572"
          class="mb-8"
        />
        <PieGraph
          id="classification-tags-percentages"
          :title="text[category].Percentages.title"
          :subtitle="text[category].Percentages.subtitle"
          :urlParams="{category}"
          :height="572"
        />
      </div>
    </div>
  </AppLayout>
</template>
