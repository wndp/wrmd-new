<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AnalyticsHeader from '../../Partials/AnalyticsHeader.vue';
import GeneratedOn from '../../Partials/GeneratedOn.vue';
import PieGraph from '@/Components/Analytics/PieGraph.vue';
import {__} from '@/Composables/Translate';
</script>

<template>
  <AppLayout title="Analytics">
    <AnalyticsHeader />
    <h2 class="text-2xl font-normal text-gray-900">
      {{ __('Patients By Taxa Categories') }}
    </h2>
    <GeneratedOn class="mb-4" />
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <div class="">
        <PieGraph
          id="patients-by-common-name"
          :title="__('Most Prevalent Patients')"
          :subtitle="__('By Common Name')"
        >
          <template #footing>
            <Link
              href="/analytics/taxa/prevalent-patients"
              class="text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700"
            >
              {{ __('See Details') }}
            </Link>
          </template>
        </PieGraph>
      </div>
      <div class="">
        <PieGraph
          id="patients-by-taxonomic-class"
          :title="__('Patients')"
          :subtitle="__('By Taxonomic Class')"
        >
          <template #footing>
            <Link
              href="/analytics/taxa/class"
              class="text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700"
            >
              {{ __('See Details') }}
            </Link>
          </template>
        </PieGraph>
      </div>
    </div>
    <!-- <div class="grid">
            <div class="grid__cell unit-1-2-desktop">
                <pie
                    id="patients-by-biological-group"
                    title="Most Prevalent Patients"
                    subtitle="By Biological Group">
                    <Link :to="{name: 'biological-group'}" class="btn btn-sm" slot="footer">{{ __('See Details') }}</Link>
                </pie>
            </div>
            <div class="grid__cell unit-1-2-desktop">
                <pie
                    id="patients-by-endemic-status"
                    title="Patients"
                    subtitle="By Endemic Status">
                    <Link :to="{name: 'endemic-status'}" class="btn btn-sm" slot="footer">{{ __('See Details') }}</Link>
                </pie>
            </div>
        </div> -->
  </AppLayout>
</template>
