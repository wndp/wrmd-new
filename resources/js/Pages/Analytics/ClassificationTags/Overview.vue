<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AnalyticsHeader from '../Partials/AnalyticsHeader.vue';
import GeneratedOn from '../Partials/GeneratedOn.vue';
import PieGraph from '@/Components/Analytics/PieGraph.vue';
import AnalyticsDataTable from '@/Components/Analytics/AnalyticsDataTable.vue';
import {__} from '@/Composables/Translate';

const category = computed(() => usePage().props.analytics.groupStudly);

const text = {
  CircumstancesOfAdmission: {
    heading: __('Circumstances of Admission Overview'),
    Frequent: {
      title: __('Most Frequent Circumstances of Admission'),
    },
    Root: {
      title: __('Root Circumstances of Admission'),
    }
  },
  ClinicalClassifications: {
    heading: __('Clinical Classifications Overview'),
    Frequent: {
      title: __('Most Frequent Clinical Classification'),
    },
    Root: {
      title: __('Root Clinical Classifications'),
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
        <PieGraph
          id="most-frequent-classification-tags"
          :title="text[category].Frequent.title"
          :urlParams="{category}"
        />
      </div>
      <div>
        <PieGraph
          id="classification-tags-roots"
          :title="text[category].Root.title"
          :urlParams="{category}"
        />
      </div>
    </div>
    <AnalyticsDataTable
      id="classification-tags"
      :urlParams="{category}"
    />
  </AppLayout>
</template>
