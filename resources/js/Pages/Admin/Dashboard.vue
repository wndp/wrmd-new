<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AdminNavigation from './Partials/AdminNavigation.vue';
import AnalyticNumber from '@/Components/Analytics/AnalyticNumber.vue';
import LineGraph from '@/Components/Analytics/LineGraph.vue';
import LeafletMap from '@/Components/Analytics/LeafletMap.vue';

defineProps({
  analyticFiltersForAllYears: Object,
  analyticFiltersForThisWeek: Object,
  analyticFiltersForToday: Object,
})
</script>

<template>
  <AppLayout title="Admin">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <AdminNavigation class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <div class="mt-5 grid grid-cols-2 md:grid-cols-3 gap-4">
          <AnalyticNumber
            id="active-accounts"
            title="Active Accounts"
          />
          <AnalyticNumber
            id="active-us-states"
            title="Active US States"
          />
          <AnalyticNumber
            id="active-countries"
            title="Active Countries"
          />
          <AnalyticNumber
            id="all-patients-admitted"
            title="Total Patients Admitted"
            :urlParams="analyticFiltersForAllYears"
          />
          <AnalyticNumber
            id="all-patients-admitted"
            title="Total Patients This Week"
            :urlParams="analyticFiltersForThisWeek"
          />
          <AnalyticNumber
            id="all-patients-admitted"
            title="Total Patients Today"
            :urlParams="analyticFiltersForToday"
          />
        </div>
        <LineGraph
          id="all-patients-admitted"
          title="All Patients Admitted"
          class="mt-5"
          :urlParams="analyticFiltersForAllYears"
        />
        <LeafletMap
          id="wrmd-accounts"
          title="WRMD Accounts"
          class="mt-5"
        />
      </div>
    </div>
  </AppLayout>
</template>
