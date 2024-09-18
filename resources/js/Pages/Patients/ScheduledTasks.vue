<script setup>
// import { computed } from 'vue';
// import { usePage } from '@inertiajs/vue3';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import DailyTasksTabs from './Partials/DailyTasksTabs.vue';
import ScheduledTaskRow from './Partials/ScheduledTaskRow.vue';
import {__} from '@/Composables/Translate';

defineProps({
  tasks: {
    type: Array,
    required: true
  }
});

//const patient = computed(() => usePage().props.admission.patient);
</script>

<template>
  <PatientLayout title="Daily Tasks">
    <DailyTasksTabs />
    <div class="shadow rounded-lg bg-white mt-8 md:mt-0 md:rounded-t-none px-4 sm:px-6">
      <div class="pt-5">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          {{ __('All Scheduled Tasks') }}
        </h3>
        <p class="mt-1 text-sm text-gray-500">
          {{ __('Below are all tasks scheduled for this patient with an audit log of their activity.') }}
        </p>
      </div>
      <div class="flow-root">
        <div class="-my-2 -mx-4 sm:-mx-6">
          <div class="inline-block min-w-full py-2 mt-8 align-middle border-t border-gray-300">
            <table class="min-w-full border-separat border-spacing-0">
              <tbody class="divide-y divide-gray-200">
                <ScheduledTaskRow
                  v-for="task in tasks"
                  :key="task.id"
                  :task="task"
                />
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </PatientLayout>
</template>
