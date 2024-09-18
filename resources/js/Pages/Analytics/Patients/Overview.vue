<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue';
import AnalyticsHeader from '../Partials/AnalyticsHeader.vue';
import GeneratedOn from '../Partials/GeneratedOn.vue';
import LineGraph from '@/Components/Analytics/LineGraph.vue';
import SparkLine from '@/Components/Analytics/SparkLine.vue';
import AnalyticNumber from '@/Components/Analytics/AnalyticNumber.vue';
import AnalyticsDataTable from '@/Components/Analytics/AnalyticsDataTable.vue';
import {__} from '@/Composables/Translate';

let featuredChartId = ref('patients-admitted');
let featuredChartHeading = ref(__('Patients Admitted'));

let swapFeaturedChart = (title, id) => {
  featuredChartId.value = id;
  featuredChartHeading.value = title;
}
</script>


<template>
  <AppLayout title="Analytics">
    <AnalyticsHeader />
    <h2 class="text-2xl font-normal text-gray-900">
      {{ __('Patients Overview') }}
    </h2>
    <GeneratedOn class="mb-4" />
    <LineGraph
      :id="featuredChartId"
      :title="featuredChartHeading"
      class="mb-4"
    />
    <div class="sm:grid sm:grid-cols-8 sm:gap-4 ">
      <div class="sm:col-span-5 grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4 sm:mb-0">
        <SparkLine
          id="patients-admitted"
          :title="__('Patients Admitted')"
          @spark-show="swapFeaturedChart"
        />
        <SparkLine
          id="patients-in-care"
          :title="__('Patients in Care')"
          @spark-show="swapFeaturedChart"
        />
        <SparkLine
          id="patients-by-class"
          :title="__('Patients by Class')"
          @spark-show="swapFeaturedChart"
        />
        <SparkLine
          id="most-prevalent-species"
          :title="__('Most Prevalent Species')"
          @spark-show="swapFeaturedChart"
        />
      </div>
      <div class="sm:col-span-3 grid grid-cols-2 sm:grid-cols-1 lg:grid-cols-2 gap-4">
        <AnalyticNumber
          id="patients-admitted"
          :title="__('Patients Admitted')"
        />
        <AnalyticNumber
          id="species-admitted"
          :title="__('Species Admitted')"
        />
      </div>
    </div>
    <AnalyticsDataTable
      id="patients-by-taxonomic-class"
      class="mt-4"
    />
  </AppLayout>
</template>
