<template>
  <PatientLayout title="WRAPP">
    <template #tabs>
      <OiledPatientTabs />
    </template>
    <Alert
      v-if="wildlifeRecoveryData.length === 0"
      class="mt-4"
    >
      {{ __('Wildlife Recovery Application data is not available for this patient.') }}
    </Alert>
    <div
      v-else
      class="overflow-hidden bg-white shadow sm:rounded-lg"
    >
      <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900">
          Wildlife Recovery Application Data
        </h3>
        <p class="mt-1 text-sm text-gray-500">
          {{ __('The data listed below is un-edited information collected in the field using the OSPR Wildlife Recovery Application.') }}
        </p>
      </div>
      <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
        <dl class="sm:divide-y sm:divide-gray-200">
          <div
            v-for="(value, key) in wildlifeRecoveryData"
            :key="key"
            class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6"
          >
            <dt class="text-sm font-medium text-gray-500">
              {{ key }}
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
              {{ value }}
            </dd>
          </div>
        </dl>
      </div>
    </div>
  </PatientLayout>
</template>

<script setup>
import PatientLayout from '@/Layouts/PatientLayout.vue';
import OiledPatientTabs from '@/Pages/Oiled/Partials/OiledPatientTabs.vue';
import Alert from '@/Components/Alert.vue';

defineProps({
  wildlifeRecoveryData: {
    type: Object,
    default: () => ({})
  }
})
</script>
