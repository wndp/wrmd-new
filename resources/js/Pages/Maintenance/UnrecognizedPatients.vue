<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Paginator from '@/Components/Paginator.vue';
import MaintenanceAside from './Partials/MaintenanceAside.vue';
import UnrecognizedAccountPatients from '@/Components/Unrecognized/UnrecognizedAccountPatients.vue';
import {__} from '@/Composables/Translate';

defineProps({
  admissions: {
    type: Object,
    required: true
  }
})
</script>

<template>
  <AppLayout title="Maintenance">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <MaintenanceAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <div class="shadow sm:rounded-md sm:overflow-hidden">
          <div class="bg-white py-6 px-4 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              {{ __('Unrecognized Patients') }}
            </h3>
            <p class="mt-2 text-sm text-gray-500">
              {{ __('Unrecognized patients are your patients where the common name is unfamiliar to WRMD. This could be caused by a typo or an unknown common name typed in.') }}
            </p>
            <p class="mt-2 text-sm text-gray-500">
              {{ __('It is important to use recognized common names so WRMD can properly generate reports on your patients.') }}
            </p>
            <p class="mt-2 text-sm text-gray-500">
              {{ __('To fix your unrecognized patients, write the correct common name or choose it from the dropdown list in the New Common Name field then press the Update button. You can also click the Update All button to fix all your patients with the same unrecognized common name.') }}
            </p>
          </div>
        </div>
        <UnrecognizedAccountPatients
          :admissions="admissions.data"
          class="mt-8"
        />
        <Paginator
          :properties="admissions"
          class="mt-8"
        />
      </div>
    </div>
  </AppLayout>
</template>
