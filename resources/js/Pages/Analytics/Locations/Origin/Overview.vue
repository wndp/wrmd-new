<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AnalyticsHeader from '../../Partials/AnalyticsHeader.vue';
import GeneratedOn from '../../Partials/GeneratedOn.vue';
import PieGraph from '@/Components/Analytics/PieGraph.vue';
import AnalyticsDataTable from '@/Components/Analytics/AnalyticsDataTable.vue';
import {__} from '@/Composables/Translate';
</script>

<template>
  <AppLayout title="Analytics">
    <AnalyticsHeader />
    <h2 class="text-2xl font-normal text-gray-900">
      {{ __('Location Found Overview') }}
    </h2>
    <GeneratedOn class="mb-4" />
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-4">
      <div>
        <PieGraph
          id="patients-by-city-found"
          :title="__('Most Prevalent Cities')"
        >
          <template #footing>
            <Link
              href="/analytics/origin/city"
              class="text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700"
            >
              {{ __('See Details') }}
            </Link>
          </template>
        </PieGraph>
      </div>
      <div>
        <PieGraph
          id="patients-by-subdivision-found"
          :title="__('Most Prevalent States')"
        >
          <template #footing>
            <Link
              href="/analytics/origin/subdivision"
              class="text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700"
            >
              {{ __('See Details') }}
            </Link>
          </template>
        </PieGraph>
      </div>
    </div>
    <AnalyticsDataTable id="cities-found" />
  </AppLayout>
</template>
