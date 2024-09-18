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
      {{ __('Demographics and Health Indicators Overview') }}
    </h2>
    <GeneratedOn class="mb-4" />
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <div>
        <PieGraph
          id="patients-by-age"
          :title="__('Patients')"
          :subtitle="__('By Age at Intake')"
        >
          <template #footing>
            <Link
              href="/analytics/demographics/age"
              class="text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700"
            >
              {{ __('See Details') }}
            </Link>
          </template>
        </PieGraph>
      </div>
      <div>
        <PieGraph
          id="patients-by-sex"
          :title="__('Patients')"
          :subtitle="__('By Sex at Intake')"
        >
          <template #footing>
            <Link
              href="/analytics/demographics/sex"
              class="text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700"
            >
              {{ __('See Details') }}
            </Link>
          </template>
        </PieGraph>
      </div>
      <div>
        <PieGraph
          id="patients-by-attitude"
          :title="__('Patients')"
          :subtitle="__('By Attitude at Intake')"
        >
          <template #footing>
            <Link
              href="/analytics/demographics/attitude"
              class="text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700"
            >
              {{ __('See Details') }}
            </Link>
          </template>
        </PieGraph>
      </div>
      <div>
        <PieGraph
          id="patients-by-body-condition"
          :title="__('Patients')"
          :subtitle="__('By Body Condition at Intake')"
        >
          <template #footing>
            <Link
              href="/analytics/demographics/body-condition"
              class="text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700"
            >
              {{ __('See Details') }}
            </Link>
          </template>
        </PieGraph>
      </div>
    </div>
  </AppLayout>
</template>
